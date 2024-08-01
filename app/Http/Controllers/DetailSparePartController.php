<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequest;
use App\Http\Resources\DetailSparePartResource;
use App\Models\DetailSparePart;
use Illuminate\Http\Request;

class DetailSparePartController extends Controller
{
    public function index(IndexRequest $request)
    {
        $this->getFilteredResults(
            DetailSparePart::class,
            $request,
            DetailSparePart::filters,
            DetailSparePartResource::class
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
