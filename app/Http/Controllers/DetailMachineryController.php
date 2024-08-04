<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexDetailMachineryRequest;
use App\Http\Resources\DetailMachineryResource;
use App\Models\DetailMachinery;
use App\Http\Requests\StoreDetailMachineryRequest;
use App\Http\Requests\UpdateDetailMachineryRequest;
use App\Traits\Filterable;

class DetailMachineryController extends Controller
{
    use Filterable;

    public function index(IndexDetailMachineryRequest $request)
    {
        return $this->getFilteredResults(
            DetailMachinery::class,
            $request,
            DetailMachinery::filters,
            DetailMachinery::sorts,
            DetailMachineryResource::class
        );
    }

    public function store(StoreDetailMachineryRequest $request)
    {
        //
    }

    public function show(DetailMachinery $detailMachinery)
    {
        //
    }

    public function update(UpdateDetailMachineryRequest $request, DetailMachinery $detailMachinery)
    {
        //
    }

    public function destroy(DetailMachinery $detailMachinery)
    {
        //
    }
}
