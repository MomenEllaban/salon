<?php

namespace Modules\MenuBuilder\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\MenuBuilder\Models\MenuBuilder;

class MenuBuildersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_menu_builder'])->only('index');
        $this->middleware(['permission:edit_menu_builder'])->only('edit', 'update');
        $this->middleware(['permission:add_menu_builder'])->only('store');
        $this->middleware(['permission:delete_menu_builder'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $type = $request->type;

        $data = MenuBuilder::with('children')->whereNull('parent_id')->where('menu_type', $type)->orderBy('order', 'ASC')->get();

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $data = MenuBuilder::create($request->all());

        $message = 'New Menu Added';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = MenuBuilder::findOrFail($id);

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = MenuBuilder::findOrFail($id);

        $data->update($request->all());

        $message = 'Menu Updated Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = MenuBuilder::findOrFail($id);

        $data->delete();

        $message = 'Menu Deleted Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function route_list(Request $request)
    {
        $query = config('menubuilder.menu_routes');

        return response()->json($query);
    }

    public function update_sequance(Request $request)
    {
        $menuArr = $request->menu;
        $menuType = $request->type;

        if (count($menuArr) > 0) {
            foreach ($menuArr as $key => $value) {
                $this->updateMenu($value['id'], $key, 0, $menuType);
                if (count($value['children']) > 0) {
                    foreach ($value['children'] as $childKey => $childValue) {
                        $this->updateMenu($childValue['id'], $childKey, 1, $menuType, $value['id']);
                        if (count($childValue['children']) > 0) {
                            foreach ($childValue['children'] as $subChildKey => $subChildValue) {
                                $this->updateMenu($subChildValue['id'], $subChildKey, 2, $menuType, $childValue['id']);
                            }
                        } // else {
                        //     // $this->updateMenu($childValue['id'], $childKey, 1, $menuType, $value['id']);
                        // }
                    }
                }
            }

            return response()->json(['message' => 'Menu Updated!', 'status' => true]);
        }

        return response()->json(['message' => 'Menu Updated!', 'status' => true]);
    }

    protected function updateMenu($id, $order, $level, $menuType, $parent = null)
    {
        $data = MenuBuilder::find($id);
        if (isset($data)) {
            $data->update(['order' => $order, 'parent_id' => $parent, 'menu_level' => $level, 'menu_type' => $menuType]);
        }
    }

    public function menu_titles(Request $request)
    {
        $data = $request->all();

        $file_id = $data['file_id'];

        $langauge_id = $data['language_id'];

        $langFolderPath = base_path("lang/{$langauge_id}/{$file_id}.php");

        $fileContent = file_get_contents($langFolderPath);

        $langArray = include $langFolderPath;
        $file_value = collect([]);
        foreach ($langArray as $key => $value) {
            $file_value[] = [
                'key' => 'sidebar.'.$key,
                'value' => $value,
            ];
        }

        $menuTitles = MenuBuilder::all()->pluck('title');

        foreach ($menuTitles as $key => $value) {
            if (count($file_value->where('key', $value)) == 0) {
                $file_value[] = [
                    'key' => $value,
                    'value' => $value,
                ];
            }
        }

        return response()->json($file_value);
    }
}
