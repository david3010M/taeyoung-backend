<?php

namespace App\Http\Controllers;

use App\Models\OptionMenu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OptionMenuController extends Controller
{
    public function index()
    {
        return response()->json(OptionMenu::all());
    }

    public function store(Request $request)
    {

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                Rule::unique('option_menus')->whereNull('deleted_at'),
            ],
            'route' => [
                'required',
                Rule::unique('option_menus')->whereNull('deleted_at'),
            ],
            'groupmenu_id' => 'required|integer|exists:group_menus,id',
            'icon' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $lastOrder = Optionmenu::max('order');

        $data = [
            'name' => $request->input('name'),
            'route' => $request->input('route'),
            'icon' => $request->input('icon'),
            'groupmenu_id' => $request->input('groupmenu_id'),
            'order' => $lastOrder + 1,
        ];

        $object = Optionmenu::create($data);
        $object = Optionmenu::find($object->id);
        return response()->json($object, 200);
    }

    public function show(int $id)
    {
        $object = Optionmenu::find($id);
        if ($object) {
            return response()->json($object, 200);
        }
        return response()->json(
            ['message' => 'Option Menu not found'], 404
        );
    }

    public function update(Request $request, int $id)
    {
        $object = OptionMenu::find($id);

        if (!$object) {
            return response()->json(
                ['message' => 'Option Menu not found'], 404
            );
        }
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                Rule::unique('optionmenus')->ignore($id)->whereNull('deleted_at'),
            ],
            'route' => [
                'required',
                Rule::unique('optionmenus')->ignore($id)->whereNull('deleted_at'),
            ],
            'icon' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'route' => $request->input('route'),
            'icon' => $request->input('icon'),
            'order' => $request->input('order') ?? $object->order,
        ];

        $object->update($data);
        $object = Optionmenu::find($object->id);
        return response()->json($object, 200);
    }

    public function destroy(int $id)
    {
        $object = Optionmenu::find($id);
        if (!$object) {
            return response()->json(
                ['message' => 'Option Menu not found'], 404
            );
        }
        $object->delete();
    }
}

