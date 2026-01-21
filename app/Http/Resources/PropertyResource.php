<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Helper function to generate full URLs for images
        $getImageUrl = function($path) {
            if (!$path) return null;
            // If already a full URL, return as is
            if (Str::startsWith($path, 'http')) return $path;

            // Use Laravel's asset() helper with proper encoding
            $url = asset($path);

            // Ensure the path component is properly encoded
            $parts = parse_url($url);
            if (isset($parts['path'])) {
                // Encode the path but preserve the directory structure
                $encodedPath = implode('/', array_map('rawurlencode', explode('/', $parts['path'])));
                $url = str_replace($parts['path'], $encodedPath, $url);
            }

            return $url;
        };

        return [
            'id' => $this->id,
            'property_name' => $this->property_name,
            'location' => $this->location,
            'rent' => (int) $this->rent,
            'landlord' => $this->landlord,
            'landlord_id' => $this->landlord_id,
            'image' => $getImageUrl($this->image),
            'thumbnail' => $getImageUrl($this->thumbnail),
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

            // Utility Bills & Charges
            'utility_bills' => [
                'electricity_rate' => $this->electricity_rate ? (float) $this->electricity_rate : null,
                'meter_rent' => $this->meter_rent ? (float) $this->meter_rent : null,
                'water_bill' => $this->water_bill ? (float) $this->water_bill : null,
                'has_water_bill' => (bool) $this->has_water_bill,
                'gas_bill' => $this->gas_bill ? (float) $this->gas_bill : null,
                'has_gas_bill' => (bool) $this->has_gas_bill,
                'service_charge' => $this->service_charge ? (float) $this->service_charge : null,
                'has_service_charge' => (bool) $this->has_service_charge,
                'other_charges' => $this->other_charges ? (float) $this->other_charges : null,
                'has_other_charges' => (bool) $this->has_other_charges,
            ],

            // Relationships
            'landlord_data' => $this->when($this->landlord_id && $this->relationLoaded('landlord'), function () {
                $landlord = $this->getRelation('landlord');
                return $landlord ? new UserResource($landlord) : null;
            }),
            'units' => PropertyUnitResource::collection($this->whenLoaded('units')),
            'gallery' => $this->whenLoaded('gallery', function() use ($getImageUrl) {
                return $this->gallery->map(function($image) use ($getImageUrl) {
                    return [
                        'id' => $image->id,
                        'image_path' => $getImageUrl($image->image_path),
                    ];
                });
            }),
            'amenities' => $this->whenLoaded('amenities'),
            'reviews' => $this->whenLoaded('reviews'),
            'favourited' => isset($this->favourited) ? (bool) $this->favourited :
                          (isset($this->favourited_count) ? ($this->favourited_count > 0) : false),
            'rented_by_me' => isset($this->rented_by_me) ? (bool) $this->rented_by_me :
                            (isset($this->rented_by_me_count) ? ($this->rented_by_me_count > 0) : false),
        ];
    }
}
