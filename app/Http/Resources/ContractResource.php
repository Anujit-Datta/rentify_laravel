<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contract_id' => $this->contract_id,
            'tenant_id' => $this->tenant_id,
            'landlord_id' => $this->landlord_id,
            'property_id' => $this->property_id,
            'rental_request_id' => $this->rental_request_id,
            'contract_data' => $this->contract_data,
            'contract_hash' => $this->contract_hash,
            'pdf_filename' => $this->pdf_filename,
            'pdf_filepath' => $this->pdf_filepath,
            'tenant_signature' => $this->tenant_signature,
            'tenant_signed_at' => $this->tenant_signed_at?->toDateTimeString(),
            'landlord_signature' => $this->landlord_signature,
            'landlord_signed_at' => $this->landlord_signed_at?->toDateTimeString(),
            'verification_url' => $this->verification_url,
            'qr_code_data' => $this->qr_code_data,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'activated_at' => $this->activated_at?->toDateTimeString(),
            'expires_at' => $this->expires_at?->format('Y-m-d'),

            'tenant' => $this->when($this->relationLoaded('tenant'), fn() => new UserResource($this->tenant)),
            'landlord' => $this->when($this->relationLoaded('landlord'), fn() => new UserResource($this->landlord)),
            'property' => $this->when($this->relationLoaded('property'), fn() => new PropertyResource($this->property)),
            'terms' => $this->when($this->relationLoaded('terms'), fn() => $this->terms),
        ];
    }
}
