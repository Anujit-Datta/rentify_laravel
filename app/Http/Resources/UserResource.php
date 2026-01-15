<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phoneVerified' => (bool) $this->phoneVerified,
            'is_verified' => (bool) $this->is_verified,
            'role' => $this->role,
            'nid_number' => $this->nid_number,
            'nid_front' => $this->nid_front,
            'nid_back' => $this->nid_back,
            'is_landlord_verified' => (bool) $this->is_landlord_verified,
            'status' => $this->status,
            'last_seen' => $this->last_seen ? $this->last_seen->toDateTimeString() : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'tenant' => $this->when($this->role === 'tenant', $this->tenant),
            'landlord' => $this->when($this->role === 'landlord', $this->landlord),
        ];
    }
}
