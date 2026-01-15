<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'property_name' => $this->property_name,
            'location' => $this->location,
            'rent' => (int) $this->rent,
            'landlord' => $this->landlord,
            'landlord_id' => $this->landlord_id,
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,
            'bedrooms' => (int) $this->bedrooms,
            'bathrooms' => (int) $this->bathrooms,
            'property_type' => $this->property_type,
            'description' => $this->description,
            'available' => (bool) $this->available,
            'posted_date' => $this->posted_date?->format('Y-m-d'),
            'featured' => (bool) $this->featured,
            'size' => $this->size,
            'floor' => $this->floor,
            'parking' => (bool) $this->parking,
            'furnished' => (bool) $this->furnished,
            'map_embed' => $this->map_embed,
            'floor_plan' => $this->floor_plan,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'status' => $this->status,
            'rental_type' => $this->rental_type,
            'is_building' => (bool) $this->is_building,
            'total_floors' => (int) $this->total_floors,
            'is_verified' => (bool) $this->is_verified,

            // Relationships
            'landlord_data' => $this->when($this->landlord_id && $this->relationLoaded('landlord'), function () {
                $landlord = $this->getRelation('landlord');
                return $landlord ? new UserResource($landlord) : null;
            }),
            'units' => PropertyUnitResource::collection($this->whenLoaded('units')),
            'gallery' => $this->whenLoaded('gallery'),
            'amenities' => $this->whenLoaded('amenities'),
            'reviews' => $this->whenLoaded('reviews'),
            'favourited' => isset($this->favourited) ? (bool) $this->favourited :
                          (isset($this->favourited_count) ? ($this->favourited_count > 0) : null),
            'rented_by_me' => isset($this->rented_by_me) ? (bool) $this->rented_by_me :
                            (isset($this->rented_by_me_count) ? ($this->rented_by_me_count > 0) : null),
        ];
    }
}
