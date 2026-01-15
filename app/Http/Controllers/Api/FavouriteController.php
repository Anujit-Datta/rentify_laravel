<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Favourite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    /**
     * Display a listing of favourite properties.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->isTenant()) {
            return response()->json([
                'success' => false,
                'message' => 'Only tenants can have favourites'
            ], 403);
        }

        $favourites = Favourite::where('tenant_id', $user->id)
            ->with(['property.landlord', 'property.units', 'property.gallery'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        $properties = $favourites->map(function ($favourite) {
            $property = $favourite->property;
            $property->favourited = true;
            return new PropertyResource($property);
        });

        return response()->json([
            'success' => true,
            'data' => $properties,
            'pagination' => [
                'total' => $favourites->total(),
                'per_page' => $favourites->perPage(),
                'current_page' => $favourites->currentPage(),
                'last_page' => $favourites->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created favourite.
     */
    public function store(Request $request, $propertyId)
    {
        $user = Auth::user();

        if (!$user->isTenant()) {
            return response()->json([
                'success' => false,
                'message' => 'Only tenants can add favourites'
            ], 403);
        }

        $property = Property::find($propertyId);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        // Check if already favourited
        $existing = Favourite::where('tenant_id', $user->id)
            ->where('property_id', $propertyId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Property already in favourites'
            ], 400);
        }

        $favourite = Favourite::create([
            'tenant_id' => $user->id,
            'property_id' => $propertyId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Property added to favourites',
            'data' => [
                'id' => $favourite->id,
                'property_id' => $propertyId,
            ]
        ], 201);
    }

    /**
     * Remove the specified favourite.
     */
    public function destroy(Request $request, $propertyId)
    {
        $user = Auth::user();

        if (!$user->isTenant()) {
            return response()->json([
                'success' => false,
                'message' => 'Only tenants can remove favourites'
            ], 403);
        }

        $favourite = Favourite::where('tenant_id', $user->id)
            ->where('property_id', $propertyId)
            ->first();

        if (!$favourite) {
            return response()->json([
                'success' => false,
                'message' => 'Favourite not found'
            ], 404);
        }

        $favourite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Property removed from favourites'
        ]);
    }
}
