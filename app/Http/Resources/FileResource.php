<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="FileResource",
 *     title="FileResource",
 *     description="File resource",
 *     @OA\Property (property="id", type="integer", example="1"),
 *     @OA\Property (property="path", type="string", example="http://develop.garzasoft.com/storage/quotation/20210819120000_file.jpg"),
 *     @OA\Property (property="quotation_id", type="integer", example="1"),
 *     @OA\Property (property="order_id", type="integer", example="1"),
 *     @OA\Property (property="created_at", type="string", format="date-time", example="2024-08-19 12:00:00")
 * )
 *
 * @OA\Schema (
 *     schema="FileCollection",
 *     title="FileCollection",
 *     description="File resource collection",
 *     @OA\Property (property="data", type="array", @OA\Items(ref="#/components/schemas/FileResource")),
 *     @OA\Property (property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property (property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 * )
 */
class FileResource extends JsonResource
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
            'path' => $this->path,
            'quotation_id' => $this->quotation_id,
            'order_id' => $this->order_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
