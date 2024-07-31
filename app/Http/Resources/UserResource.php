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
            'names' => $this->names,
            'lastnames' => $this->lastnames,
            'username' => $this->username,
            'typeuser_id' => $this->typeuser_id,
            'typeuser' => $this->typeuser,
        ];
    }
}
