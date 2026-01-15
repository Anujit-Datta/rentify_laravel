<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PropertyReview;
use App\Models\TenantReview;
use App\Models\Property;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Get property reviews.
     */
    public function getPropertyReviews($propertyId)
    {
        $property = Property::find($propertyId);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        $reviews = PropertyReview::where('property_id', $propertyId)
            ->with('tenant')
            ->orderBy('created_at', 'desc')
            ->get();

        $averageRating = $reviews->avg('rating');

        return response()->json([
            'success' => true,
            'data' => [
                'reviews' => $reviews,
                'average_rating' => $averageRating ? round($averageRating, 1) : 0,
                'total_reviews' => $reviews->count(),
            ]
        ]);
    }

    /**
     * Create property review.
     */
    public function createPropertyReview(Request $request, $propertyId)
    {
        $user = Auth::user();

        if (!$user->isTenant()) {
            return response()->json([
                'success' => false,
                'message' => 'Only tenants can review properties'
            ], 403);
        }

        $property = Property::find($propertyId);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        // Check if tenant has an active or past contract for this property
        $contract = Contract::where('tenant_id', $user->id)
            ->where('property_id', $propertyId)
            ->where('status', 'fully_signed')
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'You can only review properties you have rented'
            ], 403);
        }

        // Check if already reviewed
        $existingReview = PropertyReview::where('property_id', $propertyId)
            ->where('tenant_id', $user->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this property'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $review = PropertyReview::create([
            'property_id' => $propertyId,
            'tenant_id' => $user->id,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review created successfully',
            'data' => $review->load('tenant')
        ], 201);
    }

    /**
     * Get tenant reviews.
     */
    public function getTenantReviews($tenantId)
    {
        $reviews = TenantReview::where('tenant_id', $tenantId)
            ->with(['tenant', 'landlord'])
            ->orderBy('created_at', 'desc')
            ->get();

        $averageRating = $reviews->avg('rating');

        return response()->json([
            'success' => true,
            'data' => [
                'reviews' => $reviews,
                'average_rating' => $averageRating ? round($averageRating, 1) : 0,
                'total_reviews' => $reviews->count(),
            ]
        ]);
    }

    /**
     * Create tenant review.
     */
    public function createTenantReview(Request $request, $tenantId)
    {
        $user = Auth::user();

        if (!$user->isLandlord()) {
            return response()->json([
                'success' => false,
                'message' => 'Only landlords can review tenants'
            ], 403);
        }

        // Check if landlord has an active or past contract with this tenant
        $contract = Contract::where('landlord_id', $user->id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'fully_signed')
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'You can only review tenants you have rented to'
            ], 403);
        }

        // Check if already reviewed
        $existingReview = TenantReview::where('tenant_id', $tenantId)
            ->where('landlord_id', $user->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this tenant'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $review = TenantReview::create([
            'tenant_id' => $tenantId,
            'landlord_id' => $user->id,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review created successfully',
            'data' => $review->load(['tenant', 'landlord'])
        ], 201);
    }
}
