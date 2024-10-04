<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/dgush-backend/public/api/province",
     *     tags={"Province"},
     *     summary="Get all provinces",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent( type="array", @OA\Items(ref="#/components/schemas/ProvinceResource") )
     *     )
     * )
     */
    public function index()
    {
        return Province::all();
    }

    public function store(Request $request)
    {

    }

    /**
     * @OA\Get(
     *     path="/dgush-backend/public/api/province/{id}",
     *     tags={"Province"},
     *     summary="Get province by id",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID of province", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProvinceResource")
     *     )
     * )
     */
    public function show(int $id)
    {
        return Province::find($id);
    }

    public function update(Request $request, int $id)
    {

    }

    public function destroy(int $id)
    {

    }
}
