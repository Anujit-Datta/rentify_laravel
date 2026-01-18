<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Get search suggestions for properties.
     */
    public function suggestions(Request $request)
    {
        $query = $request->query('q', '');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        // Search properties by name, location, or type
        $properties = Property::where('property_name', 'LIKE', '%' . $query . '%')
            ->orWhere('location', 'LIKE', '%' . $query . '%')
            ->orWhere('property_type', 'LIKE', '%' . $query . '%')
            ->select('id', 'property_name', 'location', 'rent', 'image', 'thumbnail')
            ->limit(10)
            ->get();

        // Extract unique locations
        $locations = Property::where('location', 'LIKE', '%' . $query . '%')
            ->select('location')
            ->distinct()
            ->limit(5)
            ->pluck('location');

        // Extract unique property types
        $propertyTypes = Property::where('property_type', 'LIKE', '%' . $query . '%')
            ->select('property_type')
            ->distinct()
            ->limit(5)
            ->pluck('property_type');

        return response()->json([
            'success' => true,
            'data' => [
                'properties' => $properties,
                'locations' => $locations,
                'property_types' => $propertyTypes,
            ]
        ]);
    }

    /**
     * Get featured properties.
     */
    public function featuredProperties(Request $request)
    {
        $limit = $request->query('limit', 10);
        $propertyType = $request->query('property_type');
        $rentalType = $request->query('rental_type');

        $query = Property::where('featured', 1)
            ->where('available', 1);

        if ($propertyType) {
            $query->where('property_type', $propertyType);
        }

        if ($rentalType) {
            $query->where('rental_type', $rentalType);
        }

        $properties = $query->with(['landlord', 'units', 'gallery'])
            ->limit($limit)
            ->orderBy('posted_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $properties,
            'count' => $properties->count()
        ]);
    }

    /**
     * Get recent/new properties.
     */
    public function recentProperties(Request $request)
    {
        $limit = $request->query('limit', 10);

        $properties = Property::where('available', 1)
            ->with(['landlord', 'units', 'gallery'])
            ->orderBy('posted_date', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $properties,
            'count' => $properties->count()
        ]);
    }

    /**
     * Advanced search with multiple filters.
     */
    public function advanced(Request $request)
    {
        $query = Property::query();

        // Search query
        if ($request->has('query') && !empty($request->query)) {
            $search = $request->query;
            $query->where(function ($q) use ($search) {
                $q->where('property_name', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%")
                  ->orWhere('property_type', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Location
        if ($request->has('location') && !empty($request->location)) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }

        // Rent range
        if ($request->has('min_rent')) {
            $query->where('rent', '>=', $request->min_rent);
        }
        if ($request->has('max_rent')) {
            $query->where('rent', '<=', $request->max_rent);
        }

        // Bedrooms
        if ($request->has('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        // Bathrooms
        if ($request->has('bathrooms')) {
            $query->where('bathrooms', '>=', $request->bathrooms);
        }

        // Property type
        if ($request->has('property_type') && !empty($request->property_type)) {
            $query->where('property_type', $request->property_type);
        }

        // Rental type
        if ($request->has('rental_type') && !empty($request->rental_type)) {
            $query->where('rental_type', $request->rental_type);
        }

        // Amenities
        if ($request->has('amenities') && !empty($request->amenities)) {
            $amenities = explode(',', $request->amenities);
            foreach ($amenities as $amenity) {
                $query->whereHas('amenities', function ($q) use ($amenity) {
                    $q->where('amenity', 'like', '%' . trim($amenity) . '%');
                });
            }
        }

        // Featured only
        if ($request->has('featured') && $request->boolean('featured')) {
            $query->where('featured', 1);
        }

        // Available only
        if ($request->has('available') && $request->boolean('available')) {
            $query->where('available', 1);
        }

        // Pagination
        $perPage = $request->query('per_page', 15);
        $page = $request->query('page', 1);

        $properties = $query->with(['landlord', 'units', 'gallery'])
            ->orderBy('posted_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $properties->items(),
            'pagination' => [
                'total' => $properties->total(),
                'per_page' => $properties->perPage(),
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'from' => $properties->firstItem(),
                'to' => $properties->lastItem(),
            ]
        ]);
    }

    /**
     * Get search filters metadata.
     */
    public function filters()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'property_types' => [
                    'Apartment',
                    'House',
                    'Condo',
                    'Studio',
                    'Duplex',
                    'Penthouse',
                    'Villa',
                ],
                'rental_types' => [
                    'family',
                    'bachelor',
                    'sublet',
                    'commercial',
                    'roommate',
                    'all',
                ],
                'amenities' => [
                    'WiFi',
                    'Parking',
                    'AC',
                    'Gym',
                    'Swimming Pool',
                    'Security',
                    'Elevator',
                    'Balcony',
                ],
            ]
        ]);
    }
}
