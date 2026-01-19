<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\PropertyUnitResource;
use App\Models\Property;
use App\Models\PropertyFloor;
use App\Models\PropertyUnit;
use App\Models\PropertyGallery;
use App\Models\PropertyAmenity;
use App\Models\Favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    /**
     * Display a listing of properties with filters.
     */
    public function index(Request $request)
    {
        $query = Property::with(['landlord', 'units', 'gallery']);

        // Filter by availability
        if ($request->has('available')) {
            $query->where('available', $request->boolean('available'));
        }

        // Filter by rental type
        if ($request->has('rental_type')) {
            $query->where('rental_type', $request->rental_type);
        }

        // Filter by property type
        if ($request->has('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->searchLocation($request->location);
        }

        // Filter by rent range
        if ($request->has('min_rent') && $request->has('max_rent')) {
            $query->rentRange($request->min_rent, $request->max_rent);
        }

        // Filter by bedrooms
        if ($request->has('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        // Filter by landlord
        if ($request->has('landlord_id')) {
            $query->where('landlord_id', $request->landlord_id);
        }

        // Featured properties
        if ($request->has('featured')) {
            $query->featured();
        }

        // Check if favourited by current user AND if already rented/booked by user
        if (Auth::check()) {
            $userId = Auth::id();

            \Log::info("Loading favourites for user", [
                'user_id' => $userId,
                'expected_favourite' => \DB::table('favourites')->where('tenant_id', $userId)->where('property_id', 22)->exists()
            ]);

            $query->withCount(['favourites as favourited' => function ($q) use ($userId) {
                $q->where('tenant_id', $userId);
                \Log::info("Favourite query SQL", ['sql' => $q->toSql()]);
            }]);

            $query->withCount(['rentals as rented_by_me' => function ($q) use ($userId) {
                $q->where('tenant_id', $userId)
                  ->where('status', 'active');
            }]);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('property_name', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 15);
        $properties = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => PropertyResource::collection($properties),
            'pagination' => [
                'total' => $properties->total(),
                'per_page' => $properties->perPage(),
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created property.
     */
    public function store(Request $request)
    {
        \Log::info('Property creation request received', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'request_data' => $request->except(['image']),
        ]);

        $validator = Validator::make($request->all(), [
            'property_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'rent' => 'required|integer|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'property_type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'rental_type' => 'required|in:sublet,commercial,family,bachelor,roommate,all',
            'size' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'parking' => 'boolean',
            'furnished' => 'boolean',
            'featured' => 'boolean',
            'available' => 'boolean',
            'image' => 'nullable|image|max:5120',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_building' => 'boolean',
            'total_floors' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            \Log::warning('Property validation failed', [
                'user_id' => Auth::id(),
                'errors' => $validator->errors()->toArray(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['image']);
        $data['landlord_id'] = Auth::id();
        $data['landlord'] = Auth::user()->name;
        $data['posted_date'] = now();

        // Set default available if not provided (but don't override if sent)
        if (!isset($data['available'])) {
            $data['available'] = true;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

            // Save to uploads folder (same as raw PHP project)
            $destinationPath = public_path('uploads/properties');
            $image->move($destinationPath, $filename);

            $data['image'] = 'uploads/properties/' . $filename;

            \Log::info('Property image uploaded', [
                'user_id' => Auth::id(),
                'filename' => $filename,
                'path' => $data['image'],
            ]);
        }

        try {
            $property = Property::create($data);

            \Log::info('Property created successfully', [
                'property_id' => $property->id,
                'user_id' => Auth::id(),
                'property_name' => $property->property_name,
                'rent' => $property->rent,
                'featured' => $property->featured ?? 'not set',
                'available' => $property->available ?? 'not set',
                'parking' => $property->parking ?? 'not set',
                'furnished' => $property->furnished ?? 'not set',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully',
                'data' => new PropertyResource($property->load('landlord'))
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Property creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create property: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified property.
     */
    public function show(Request $request, $id)
    {
        $property = Property::with(['landlord', 'units', 'gallery', 'amenities', 'reviews'])
            ->find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        // Check if favourited by current user AND if already rented/booked by user
        if (Auth::check()) {
            $userId = Auth::id();
            $property->favourited = Favourite::where('tenant_id', $userId)
                ->where('property_id', $id)
                ->exists();

            $property->rented_by_me = $property->rentals()
                ->where('tenant_id', $userId)
                ->where('status', 'active')
                ->exists();
        }

        return response()->json([
            'success' => true,
            'data' => new PropertyResource($property)
        ]);
    }

    /**
     * Update the specified property.
     */
    public function update(Request $request, $id)
    {
        \Log::info('Property update request received', [
            'user_id' => Auth::id(),
            'property_id' => $id,
            'request_data' => $request->except(['image']),
        ]);

        $property = Property::find($id);

        if (!$property) {
            \Log::warning('Property update failed - property not found', [
                'user_id' => Auth::id(),
                'property_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        // Check ownership
        if ($property->landlord_id !== Auth::id()) {
            \Log::warning('Property update failed - unauthorized', [
                'user_id' => Auth::id(),
                'property_id' => $id,
                'property_owner' => $property->landlord_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'property_name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'rent' => 'sometimes|required|integer|min:0',
            'bedrooms' => 'sometimes|required|integer|min:0',
            'bathrooms' => 'sometimes|required|integer|min:0',
            'property_type' => 'sometimes|required|string|max:50',
            'description' => 'nullable|string',
            'rental_type' => 'sometimes|required|in:sublet,commercial,family,bachelor,roommate,all',
            'size' => 'nullable|string|max:50',
            'floor' => 'nullable|string|max:50',
            'parking' => 'boolean',
            'furnished' => 'boolean',
            'image' => 'nullable|image|max:5120',
            'available' => 'boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            \Log::warning('Property update validation failed', [
                'user_id' => Auth::id(),
                'property_id' => $id,
                'errors' => $validator->errors()->toArray(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['image']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($property->image && file_exists(public_path($property->image))) {
                unlink(public_path($property->image));
            }

            $image = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

            // Save to uploads folder
            $destinationPath = public_path('uploads/properties');
            $image->move($destinationPath, $filename);

            $data['image'] = 'uploads/properties/' . $filename;

            \Log::info('Property image updated', [
                'user_id' => Auth::id(),
                'property_id' => $id,
                'filename' => $filename,
            ]);
        }

        try {
            $property->update($data);

            \Log::info('Property updated successfully', [
                'property_id' => $id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully',
                'data' => new PropertyResource($property->load('landlord'))
            ]);
        } catch (\Exception $e) {
            \Log::error('Property update failed', [
                'user_id' => Auth::id(),
                'property_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update property: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified property.
     */
    public function destroy($id)
    {
        \Log::info('Property deletion request', [
            'property_id' => $id,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
        ]);

        $property = Property::find($id);

        if (!$property) {
            \Log::warning('Property not found for deletion', ['property_id' => $id, 'user_id' => Auth::id()]);
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        // Check ownership
        if ($property->landlord_id !== Auth::id()) {
            \Log::warning('Unauthorized deletion attempt', [
                'property_id' => $id,
                'user_id' => Auth::id(),
                'property_landlord_id' => $property->landlord_id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete images
        if ($property->image && file_exists(public_path($property->image))) {
            \Log::info('Deleting property image', ['image_path' => $property->image]);
            unlink(public_path($property->image));
        }

        foreach ($property->gallery as $image) {
            $imagePath = public_path($image->image_path);
            if (file_exists($imagePath)) {
                \Log::info('Deleting gallery image', ['image_path' => $image->image_path]);
                unlink($imagePath);
            }
        }

        $property->delete();

        \Log::info('Property deleted successfully', [
            'property_id' => $id,
            'property_name' => $property->property_name,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Property deleted successfully'
        ]);
    }

    /**
     * Upload gallery images.
     */
    public function uploadGallery(Request $request, $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        // Check ownership
        if ($property->landlord_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'images' => 'required|array|max:10',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $uploadedImages = [];
        $destinationPath = public_path('uploads/properties/' . $property->id);

        // Create directory if it doesn't exist
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        foreach ($request->file('images') as $image) {
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $filename);

            $relativePath = 'uploads/properties/' . $property->id . '/' . $filename;

            $gallery = PropertyGallery::create([
                'property_id' => $property->id,
                'image_path' => $relativePath,
            ]);

            $uploadedImages[] = [
                'id' => $gallery->id,
                'image_path' => url($relativePath),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully',
            'data' => $uploadedImages
        ]);
    }

    /**
     * Delete gallery image.
     */
    public function deleteGalleryImage($imageId)
    {
        $image = PropertyGallery::find($imageId);

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        }

        $property = Property::find($image->property_id);

        // Check ownership
        if ($property->landlord_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $imagePath = public_path($image->image_path);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }

    /**
     * Get property units.
     */
    public function getUnits($id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        $units = PropertyUnit::where('property_id', $id)
            ->with('floor')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PropertyUnitResource::collection($units)
        ]);
    }

    /**
     * Add unit to property.
     */
    public function addUnit(Request $request, $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        // Check ownership
        if ($property->landlord_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'floor_id' => 'required|exists:property_floors,id',
            'unit_name' => 'required|string|max:100',
            'unit_number' => 'required|integer',
            'rent' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'size' => 'nullable|string|max:50',
            'rental_type' => 'required|string',
            'is_sublet' => 'boolean',
            'max_tenants' => 'required|integer|min:1',
            'is_available' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $unit = PropertyUnit::create([
            'property_id' => $id,
            'floor_id' => $request->floor_id,
            'unit_name' => $request->unit_name,
            'unit_number' => $request->unit_number,
            'rent' => $request->rent,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'size' => $request->size,
            'rental_type' => $request->rental_type,
            'is_sublet' => $request->boolean('is_sublet', false),
            'max_tenants' => $request->max_tenants,
            'is_available' => $request->boolean('is_available', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Unit added successfully',
            'data' => new PropertyUnitResource($unit->load('floor'))
        ], 201);
    }
}
