<?php

namespace App\Http\Controllers;

use App\Models\GroupMenu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupMenuController extends Controller
{
    public function index()
    {
        return GroupMenu::with('optionMenus')->get();
    }

    public function store(Request $request)
    {
//        Validate data
        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('groupmenu')->whereNull('deleted_at'),
            ],
            'icon' => 'required|string',
        ]);

        $data = [
            'name' => $request->input('name'),
            'icon' => $request->input('icon'),
            'order' => GroupMenu::max('order') + 1,
        ];

//        Create a new Group Menu
        return GroupMenu::create($data);
    }

    public function show(int $id)
    {
        //        Find the Group Menu
        $groupMenu = GroupMenu::find($id);

//        Error when not found
        if (!$groupMenu) {
            return response()->json(
                ['message' => 'Group Menu not found'], 404
            );
        }

//        Return the Group Menu
//        return $groupMenu->load('optionMenus');
        return $groupMenu;
    }

    public function update(Request $request, int $id)
    {
        $groupMenu = GroupMenu::find($id);

//        Error when not found
        if (!$groupMenu) {
            return response()->json(
                ['message' => 'Group Menu not found'], 404
            );
        }

//        Validate data
        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('groupmenu')->whereNull('deleted_at')->ignore($id),
            ],
            'icon' => 'required|string',
        ]);

        $data = [
            'name' => $request->input('name'),
            'icon' => $request->input('icon'),
            'order' => $request->input('order') ?? $groupMenu->order,
        ];

//        Update the Group Menu
        $groupMenu->update($data);
        return $groupMenu;
    }

    public function destroy(int $id)
    {
//        Find the Group Menu
        $groupMenu = GroupMenu::find($id);

//        Error when not found
        if (!$groupMenu) {
            return response()->json(
                ['message' => 'Group Menu not found'], 404
            );
        }

//        VALIDATE IF GROUPMENU HAS ANY OPTIONMENUS ASSOCIATED
        if ($groupMenu->optionMenus()->count() > 0) {
            return response()->json(
                ['message' => 'Group Menu has option menus associated'], 409
            );
        }

//        Delete the Group Menu
        $groupMenu->delete();
        return response()->json(
            ['message' => 'Option Menu deleted successfully']
        );
    }
}
