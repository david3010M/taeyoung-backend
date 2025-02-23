<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexTypeUserRequest;
use App\Http\Resources\CurrencyResource;
use App\Http\Resources\TypeUserResource;
use App\Models\TypeUser;
use App\Traits\Filterable;
use Illuminate\Http\Request;

class TypeUserController extends Controller
{
    use Filterable;

    public function index(IndexTypeUserRequest $request)
    {
        return $this->getFilteredResults(
            TypeUser::class,
            $request,
            TypeUser::filters,
            TypeUser::sorts,
            TypeUserResource::class
        );
    }

    public function setAccess(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'typeUser_id' => 'required|integer|exists:type_users,id',
            'accesses' => 'required|array',
            'accesses.*' => 'integer|exists:option_menus,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $typeUserId = $request->input('typeUser_id');
        $accesses = $request->input('accesses');

        $typeUser = TypeUser::find($typeUserId);
        $typeUser->setAccess($typeUserId, $accesses);

        $typeUser->optionMenuAccess = $typeUser->access()->pluck('optionmenu_id')->toArray();
        return response()->json($typeUser);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(int $id)
    {
        //
    }

    public function update(Request $request, int $id)
    {
        //
    }

    public function destroy(int $id)
    {
        //
    }
}
