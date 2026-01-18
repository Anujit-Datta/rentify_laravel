<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'unit_id' => $this->unit_id,
            'tenant_id' => $this->tenant_id,
            'property_name' => $this->property_name,
            'tenant_name' => $this->tenant_name,
            'tenant_email' => $this->tenant_email,
            'tenant_phone' => $this->tenant_phone,
            'national_id' => $this->national_id,
            'move_in_date' => $this->move_in_date?->format('Y-m-d'),
            'payment_method' => $this->payment_method,
            'has_pets' => $this->has_pets,
            'current_address' => $this->current_address,
            'num_occupants' => $this->num_occupants ? (int) $this->num_occupants : null,
            'occupation' => $this->occupation,
            'emergency_contact' => $this->emergency_contact,
            'emergency_phone' => $this->emergency_phone,
            'notes' => $this->notes,
            'documents' => $this->documents ? json_decode($this->documents, true) : null,
            'document_path' => $this->document_path,
            'status' => $this->status,
            'terms' => (bool) $this->terms,
            'pdf_file' => $this->pdf_file,
            'contract_file' => $this->contract_file,
            'contract_id' => $this->contract_id,
            'request_date' => $this->request_date?->toDateTimeString(),
            'approved_at' => $this->approved_at?->toDateTimeString(),

            'property' => $this->when($this->relationLoaded('property'), fn () => new PropertyResource($this->property)),
            'unit' => $this->when($this->relationLoaded('unit'), fn () => $this->unit),
            'tenant' => $this->when($this->relationLoaded('tenant'), fn () => new UserResource($this->tenant)),
        ];
    }
}
