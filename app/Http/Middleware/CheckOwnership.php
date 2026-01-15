<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $resource): Response
    {
        $user = $request->user();
        $resourceId = $request->route('id') ?? $request->route('propertyId') ?? $request->route('contractId');

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Admin can access everything
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check ownership based on resource type
        switch ($resource) {
            case 'property':
                $property = \App\Models\Property::find($resourceId);
                if (!$property || $property->landlord_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized. You do not own this property.'
                    ], 403);
                }
                break;

            case 'rental_request':
                $rentalRequest = \App\Models\RentalRequest::find($resourceId);
                if (!$rentalRequest) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found'
                    ], 404);
                }

                // Tenant can view their own requests
                // Landlord can view requests for their properties
                if ($user->role === 'tenant' && $rentalRequest->tenant_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized. This is not your rental request.'
                    ], 403);
                }

                if ($user->role === 'landlord') {
                    $property = \App\Models\Property::find($rentalRequest->property_id);
                    if (!$property || $property->landlord_id !== $user->id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized. This request is not for your property.'
                        ], 403);
                    }
                }
                break;

            case 'contract':
                $contract = \App\Models\Contract::find($resourceId);
                if (!$contract) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found'
                    ], 404);
                }

                if ($contract->tenant_id !== $user->id && $contract->landlord_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized. You are not part of this contract.'
                    ], 403);
                }
                break;

            case 'payment':
                $payment = \App\Models\RentPayment::find($resourceId);
                if (!$payment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found'
                    ], 404);
                }

                if ($payment->tenant_id !== $user->id && $payment->landlord_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized. You are not part of this payment.'
                    ], 403);
                }
                break;

            case 'review':
                // Reviews can be viewed by anyone, but only created by authorized users
                if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
                    return $next($request);
                }

                $review = \App\Models\PropertyReview::find($resourceId);
                if ($review && $review->tenant_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized. You can only modify your own reviews.'
                    ], 403);
                }
                break;

            case 'ticket':
                $ticket = \App\Models\SupportTicket::find($resourceId);
                if (!$ticket || $ticket->user_id !== $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized. This is not your support ticket.'
                    ], 403);
                }
                break;

            case 'wallet':
                // Wallet access is restricted to the owner
                $walletUserId = $request->route('userId');
                if ($walletUserId && $walletUserId != $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized. You can only access your own wallet.'
                    ], 403);
                }
                break;

            default:
                // Default: deny access if resource type is not recognized
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Unknown resource type.'
                ], 403);
        }

        return $next($request);
    }
}
