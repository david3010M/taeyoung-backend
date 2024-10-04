<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/department",
     *     tags={"Department"},
     *     summary="Get all departments",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent( type="array", @OA\Items(ref="#/components/schemas/DepartmentResource") )
     *     )
     * )
     *
     */
    public function index()
    {
        return Department::with('provinces')->get();
    }

    /**
     * @OA\Get(
     *     path="/taeyoung-backend/public/api/department/{id}",
     *     tags={"Department"},
     *     summary="Get department by id",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID of district", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/DepartmentResource") )
     * )
     */
    public function show(int $id)
    {
        return Department::with('provinces')->find($id);
    }
}
