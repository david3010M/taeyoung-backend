<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;

class UnitController extends Controller
{
    public function index(IndexUnitRequest $request)
    {
        return $this->getFilteredResults(
            Unit::class,
            $request,
            Unit::filters,
            Unit::sorts,
            UnitResource::class
        );
    }

    public function store(StoreUnitRequest $request)
    {
        //
    }

    public function show(Unit $unit)
    {
        //
    }

    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        //
    }

    public function destroy(Unit $unit)
    {
        //
    }
}
