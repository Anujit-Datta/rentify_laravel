<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalRequestResource;
use App\Models\Notification;
use App\Models\Property;
use App\Models\RentalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RentalRequestController extends Controller
{
    /**
     * Display a listing of rental requests.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isTenant()) {
            // Tenants see their own requests
            $query = RentalRequest::where('tenant_id', $user->id)
                ->with(['property', 'unit', 'tenant']);
        } elseif ($user->isLandlord()) {
            // Landlords see requests for their properties
            $query = RentalRequest::whereHas('property', function ($q) use ($user) {
                $q->where('landlord_id', $user->id);
            })->with(['property', 'unit', 'tenant']);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $query->orderBy('request_date', 'desc');

        // Paginate
        $perPage = $request->get('per_page', 15);
        $requests = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => RentalRequestResource::collection($requests),
            'pagination' => [
                'total' => $requests->total(),
                'per_page' => $requests->perPage(),
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created rental request.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            if (! $user->isTenant()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only tenants can submit rental requests',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'property_id' => 'required|integer|exists:properties,id',
                'unit_id' => 'nullable|integer|exists:property_units,id',
                'move_in_date' => 'required|date',
                'payment_method' => 'required|in:bank_transfer,hand_cash,mobile_banking,check',
                'has_pets' => 'nullable|in:yes,no',
                'current_address' => 'nullable|string',
                'num_occupants' => 'nullable|integer|min:1',
                'occupation' => 'nullable|string|max:100',
                'emergency_contact' => 'nullable|string|max:255',
                'emergency_phone' => 'nullable|string|max:20',
                'notes' => 'nullable|string|max:1000',
                'message_to_landlord' => 'nullable|string|max:1000',
                'documents' => 'nullable|array',
                'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
                'terms' => 'required|accepted',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $property = Property::find($request->property_id);

            if (! $property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found',
                ], 404);
            }

            // Check if tenant already has a pending request for this property
            $existingRequest = RentalRequest::where('tenant_id', $user->id)
                ->where('property_id', $request->property_id)
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active request for this property',
                ], 400);
            }

            $data = [
                'property_id' => $request->property_id,
                'unit_id' => $request->unit_id,
                'tenant_id' => $user->id,
                'property_name' => $property->property_name,
                'tenant_name' => $user->name,
                'tenant_email' => $user->email,
                'tenant_phone' => $user->phone ?? '',
                'national_id' => $user->nid_number,
                'move_in_date' => $request->move_in_date,
                'payment_method' => $request->payment_method,
                'has_pets' => $request->has_pets ? 'yes' : 'no',
                'current_address' => $request->current_address,
                'num_occupants' => $request->num_occupants ?? 1,
                'occupation' => $request->occupation,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'notes' => $request->notes,
                'message_to_landlord' => $request->message_to_landlord,
                'status' => 'pending',
                'terms' => $request->terms ? 1 : 0,
            ];

            // Handle document uploads
            if ($request->hasFile('documents')) {
                $documentPaths = [];
                foreach ($request->file('documents') as $document) {
                    $filename = time().'_'.Str::random(10).'.'.$document->getClientOriginalExtension();
                    $path = $document->storeAs('rental-documents', $filename, 'public');
                    $documentPaths[] = $path;
                }
                $data['documents'] = json_encode($documentPaths);
                $data['document_path'] = $documentPaths[0] ?? null;
            }

            $rentalRequest = RentalRequest::create($data);

            // Create notification for landlord
            \App\Models\Notification::create([
                'user_id' => $property->landlord_id,
                'type' => 'request',
                'title' => 'New Rental Request',
                'message' => "New rental request from {$user->name} for your property: {$property->property_name}",
                'related_id' => $rentalRequest->id,
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rental request submitted successfully',
                'data' => new RentalRequestResource($rentalRequest->load(['property', 'unit', 'tenant'])),
            ], 201);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Rental request creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ] : null,
            ], 500);
        }
    }

    /**
     * Display the specified rental request.
     */
    public function show($id)
    {
        $user = Auth::user();
        $request = RentalRequest::with(['property', 'unit', 'tenant'])->find($id);

        if (! $request) {
            return response()->json([
                'success' => false,
                'message' => 'Rental request not found',
            ], 404);
        }

        // Check authorization
        if ($user->isTenant() && $request->tenant_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($user->isLandlord() && $request->property->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new RentalRequestResource($request),
        ]);
    }

    /**
     * Approve rental request.
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();

        if (! $user->isLandlord()) {
            return response()->json([
                'success' => false,
                'message' => 'Only landlords can approve requests',
            ], 403);
        }

        $rentalRequest = RentalRequest::with('property')->find($id);

        if (! $rentalRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Rental request not found',
            ], 404);
        }

        // Check ownership
        if ($rentalRequest->property->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($rentalRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Request can only be approved if pending',
            ], 400);
        }

        $rentalRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Create notification for tenant
        Notification::create([
            'user_id' => $rentalRequest->tenant_id,
            'type' => 'request',
            'title' => 'Rental Request Approved',
            'message' => "Your rental request for {$rentalRequest->property_name} has been approved",
            'related_id' => $rentalRequest->id,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rental request approved successfully',
            'data' => new RentalRequestResource($rentalRequest->load(['property', 'unit', 'tenant'])),
        ]);
    }

    /**
     * Reject rental request.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        if (! $user->isLandlord()) {
            return response()->json([
                'success' => false,
                'message' => 'Only landlords can reject requests',
            ], 403);
        }

        $rentalRequest = RentalRequest::with('property')->find($id);

        if (! $rentalRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Rental request not found',
            ], 404);
        }

        // Check ownership
        if ($rentalRequest->property->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($rentalRequest->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Request is already rejected',
            ], 400);
        }

        $rentalRequest->update([
            'status' => 'rejected',
        ]);

        // Create notification for tenant
        Notification::create([
            'user_id' => $rentalRequest->tenant_id,
            'type' => 'request',
            'title' => 'Rental Request Rejected',
            'message' => "Your rental request for {$rentalRequest->property_name} has been rejected",
            'related_id' => $rentalRequest->id,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rental request rejected successfully',
            'data' => new RentalRequestResource($rentalRequest->load(['property', 'unit', 'tenant'])),
        ]);
    }
}
