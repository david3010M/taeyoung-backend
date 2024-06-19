<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'ruc' => $this->ruc,
            'businessName' => $this->businessName,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => (new CountryResource($this->country))->name,
        ];

    }
}
