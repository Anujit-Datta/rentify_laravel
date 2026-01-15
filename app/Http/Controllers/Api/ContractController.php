<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use App\Models\ContractTerms;
use App\Models\RentalRequest;
use App\Models\SignatureLog;
use App\Models\ContractVerification;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ContractController extends Controller
{
    /**
     * Display a listing of contracts.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isTenant()) {
            $query = Contract::where('tenant_id', $user->id)
                ->with(['property', 'landlord', 'terms']);
        } elseif ($user->isLandlord()) {
            $query = Contract::where('landlord_id', $user->id)
                ->with(['property', 'tenant', 'terms']);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $contracts = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => ContractResource::collection($contracts),
            'pagination' => [
                'total' => $contracts->total(),
                'per_page' => $contracts->perPage(),
                'current_page' => $contracts->currentPage(),
                'last_page' => $contracts->lastPage(),
            ]
        ]);
    }

    /**
     * Display the specified contract.
     */
    public function show($id)
    {
        $user = Auth::user();
        $contract = Contract::with(['property', 'tenant', 'landlord', 'terms'])->find($id);

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'Contract not found'
            ], 404);
        }

        // Check authorization
        if ($user->isTenant() && $contract->tenant_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($user->isLandlord() && $contract->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new ContractResource($contract)
        ]);
    }

    /**
     * Generate contract from rental request.
     */
    public function generate(Request $request, $rentalRequestId)
    {
        $user = Auth::user();

        if (!$user->isLandlord()) {
            return response()->json([
                'success' => false,
                'message' => 'Only landlords can generate contracts'
            ], 403);
        }

        $rentalRequest = RentalRequest::with('property')->find($rentalRequestId);

        if (!$rentalRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Rental request not found'
            ], 404);
        }

        if ($rentalRequest->property->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($rentalRequest->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Can only generate contract for approved requests'
            ], 400);
        }

        // Check if contract already exists
        $existingContract = Contract::where('rental_request_id', $rentalRequestId)->first();
        if ($existingContract) {
            return response()->json([
                'success' => false,
                'message' => 'Contract already exists for this request'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'monthly_rent' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'start_date' => 'required|date|after:today',
            'duration_months' => 'required|integer|min:1',
            'payment_day' => 'required|integer|min:1|max:31',
            'late_fee_per_day' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate unique contract ID
        $contractId = 'CON-' . strtoupper(Str::random(12));

        // Calculate end date
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addMonths($request->duration_months);

        // Create contract data
        $contractData = [
            'property_id' => $rentalRequest->property_id,
            'property_name' => $rentalRequest->property_name,
            'tenant_name' => $rentalRequest->tenant_name,
            'tenant_email' => $rentalRequest->tenant_email,
            'tenant_phone' => $rentalRequest->tenant_phone,
            'landlord_name' => $user->name,
            'landlord_email' => $user->email,
            'landlord_phone' => $user->phone,
        ];

        // Create contract
        $contract = Contract::create([
            'contract_id' => $contractId,
            'tenant_id' => $rentalRequest->tenant_id,
            'landlord_id' => $user->id,
            'property_id' => $rentalRequest->property_id,
            'rental_request_id' => $rentalRequest->id,
            'contract_data' => $contractData,
            'contract_hash' => hash('sha256', json_encode($contractData)),
            'pdf_filename' => "contract_{$contractId}.pdf",
            'pdf_filepath' => storage_path("app/contracts/contract_{$contractId}.pdf"),
            'verification_url' => url("/api/contracts/{$contractId}/verify"),
            'status' => 'pending_signatures',
        ]);

        // Create contract terms
        ContractTerms::create([
            'contract_id' => $contractId,
            'monthly_rent' => $request->monthly_rent,
            'security_deposit' => $request->security_deposit,
            'advance_payment' => $request->advance_payment ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $endDate->toDateString(),
            'duration_months' => $request->duration_months,
            'payment_day' => $request->payment_day,
            'late_fee_per_day' => $request->late_fee_per_day,
            'utilities_included' => $request->utilities_included ?? 'none',
            'electricity_included' => $request->boolean('electricity_included', false),
            'water_included' => $request->boolean('water_included', false),
            'gas_included' => $request->boolean('gas_included', false),
            'internet_included' => $request->boolean('internet_included', false),
            'maintenance_by' => $request->maintenance_by ?? 'landlord',
            'major_repairs_by' => $request->major_repairs_by ?? 'landlord',
            'pets_allowed' => $request->boolean('pets_allowed', false),
            'smoking_allowed' => $request->boolean('smoking_allowed', false),
            'subletting_allowed' => $request->boolean('subletting_allowed', false),
            'guests_allowed' => $request->boolean('guests_allowed', true),
            'max_occupants' => $request->max_occupants ?? 2,
            'special_terms' => $request->special_terms,
            'additional_clauses' => $request->additional_clauses,
        ]);

        // Generate QR code
        $qrCodeData = QrCode::format('png')->size(300)->generate($contract->verification_url);
        $qrPath = "qr-codes/{$contractId}.png";
        \Storage::disk('public')->put($qrPath, $qrCodeData);
        $contract->update(['qr_code_data' => $qrPath]);

        return response()->json([
            'success' => true,
            'message' => 'Contract generated successfully',
            'data' => new ContractResource($contract->load(['property', 'tenant', 'landlord', 'terms']))
        ], 201);
    }

    /**
     * Sign contract.
     */
    public function sign(Request $request, $id)
    {
        $user = Auth::user();
        $contract = Contract::find($id);

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'Contract not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'signature_data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userRole = $user->isTenant() ? 'tenant' : 'landlord';

        // Check if user is part of this contract
        if ($userRole === 'tenant' && $contract->tenant_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($userRole === 'landlord' && $contract->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Update signature
        if ($userRole === 'tenant') {
            $contract->update([
                'tenant_signature' => $request->signature_data,
                'tenant_signed_at' => now(),
            ]);
        } else {
            $contract->update([
                'landlord_signature' => $request->signature_data,
                'landlord_signed_at' => now(),
            ]);
        }

        // Log signature
        SignatureLog::create([
            'contract_id' => $contract->contract_id,
            'user_id' => $user->id,
            'user_role' => $userRole,
            'signature_data' => $request->signature_data,
            'public_key_used' => $user->public_key ?? '',
            'ip_address' => $request->ip(),
            'device_info' => $request->userAgent(),
            'signature_valid' => true,
            'verified_at' => now(),
        ]);

        // Update contract status if both signed
        if ($contract->tenant_signature && $contract->landlord_signature) {
            $contract->update([
                'status' => 'fully_signed',
                'activated_at' => now(),
            ]);
        } else {
            $contract->update(['status' => 'partially_signed']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contract signed successfully',
            'data' => new ContractResource($contract->load(['property', 'tenant', 'landlord', 'terms']))
        ]);
    }

    /**
     * Verify contract.
     */
    public function verify($contractId)
    {
        $contract = Contract::where('contract_id', $contractId)
            ->with(['tenant', 'landlord', 'property'])
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'Contract not found',
                'verification_result' => 'not_found'
            ], 404);
        }

        // Verify contract hash
        $expectedHash = hash('sha256', json_encode($contract->contract_data));
        $hashMatch = $contract->contract_hash === $expectedHash;

        $verificationResult = 'valid';
        if (!$hashMatch) {
            $verificationResult = 'tampered';
        }

        // Log verification
        ContractVerification::create([
            'contract_id' => $contractId,
            'verification_result' => $verificationResult,
            'verification_type' => 'api',
            'hash_match' => $hashMatch,
            'tenant_signature_valid' => !empty($contract->tenant_signature),
            'landlord_signature_valid' => !empty($contract->landlord_signature),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'verification_result' => $verificationResult,
            'data' => [
                'contract_id' => $contract->contract_id,
                'hash_match' => $hashMatch,
                'tenant_signed' => !empty($contract->tenant_signature),
                'landlord_signed' => !empty($contract->landlord_signature),
                'status' => $contract->status,
                'property' => $contract->property ? $contract->property->property_name : null,
                'tenant' => $contract->tenant ? $contract->tenant->name : null,
                'landlord' => $contract->landlord ? $contract->landlord->name : null,
            ]
        ]);
    }

    /**
     * Download contract PDF.
     */
    public function downloadPdf($id)
    {
        $user = Auth::user();
        $contract = Contract::find($id);

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'Contract not found'
            ], 404);
        }

        // Check authorization
        if ($user->isTenant() && $contract->tenant_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($user->isLandlord() && $contract->landlord_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $filePath = $contract->pdf_filepath;

        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'PDF file not found. Please generate the contract first.'
            ], 404);
        }

        return response()->download($filePath, $contract->pdf_filename);
    }

    /**
     * Get QR code for contract.
     */
    public function getQrCode($contractId)
    {
        $contract = Contract::where('contract_id', $contractId)->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'Contract not found'
            ], 404);
        }

        if (!$contract->qr_code_data) {
            return response()->json([
                'success' => false,
                'message' => 'QR code not available'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'qr_code_url' => Storage::url($contract->qr_code_data),
                'verification_url' => $contract->verification_url,
            ]
        ]);
    }
}
