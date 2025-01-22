<?php

namespace App\Http\Requests;
/**
 * @OA\Schema(
 *     schema="ImageRequest",
 *     title="ImageRequest",
 *     required={"images[]"},
 *     @OA\Property(property="images[]", type="array", @OA\Items(type="file", format="binary")),
 * )
 */
class ImageRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            "images" => "required|array",
            "images.*" => "required|file",
        ];
    }
}
