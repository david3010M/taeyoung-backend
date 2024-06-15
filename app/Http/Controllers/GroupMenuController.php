<?php

namespace App\Http\Controllers;

use App\Models\GroupMenu;
use Illuminate\Http\Request;

class GroupMenuController extends Controller
{
    public function index()
    {
        return GroupMenu::all();
    }

    public function store(Request $request)
    {

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
