<?php

namespace App\Http\Requests;

/**
 * @OA\Schema(
 *     schema="FileRequest",
 *     title="FileRequest",
 *     required={"files[]"},
 *     @OA\Property(property="files[]", type="array", @OA\Items(type="file", format="binary")),
 * )
 */
class FileRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            "files" => "required|array",
            "files.*" => "required|file",
        ];
    }
}
