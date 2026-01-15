<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rental_id' => $this->rental_id,
            'tenant_id' => $this->tenant_id,
            'landlord_id' => $this->landlord_id,
            'property_id' => $this->property_id,
            'amount' => (float) $this->amount,
            'payment_month' => $this->payment_month->format('Y-m-d'),
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'is_advance' => (bool) $this->is_advance,
            'note' => $this->note,
            'transaction_id' => $this->transaction_id,
            'confirmed_at' => $this->confirmed_at?->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),

            'tenant' => $this->when($this->relationLoaded('tenant'), fn() => new UserResource($this->tenant)),
            'landlord' => $this->when($this->relationLoaded('landlord'), fn() => new UserResource($this->landlord)),
            'property' => $this->when($this->relationLoaded('property'), fn() => new PropertyResource($this->property)),
            'receipt' => $this->when($this->relationLoaded('receipt'), fn() => $this->receipt),
        ];
    }
}
