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
