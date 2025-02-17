<?php

namespace App\Http\Middleware;

use App\Trait\Menu;
use Illuminate\Support\Arr;
use Modules\MenuBuilder\Models\MenuBuilder;

class GenerateMenus
{
    use Menu;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($menuname, $type, $arraymenu)
    {
        \Menu::make($menuname, function ($menu) use ($type, $arraymenu) {
            $menuArray = MenuBuilder::getAllMenu()->where('menu_type', $type);

            if (count($menuArray) == 0) {
                $arr = [];
                foreach (config('menubuilder.'.$arraymenu) as $key => $value) {
                    // code...
                    $arr[] = array_merge(config('menubuilder.MENU'), $value);
                }
                foreach ($arr as $key => $value) {
                    $this->saveMenu($value);
                }

                $menuArray = MenuBuilder::getAllMenu()->where('menu_type', $type);
            }

            foreach ($menuArray as $key => $value) {
                if ($value->status) {
                    $this->makeMenu($menu, $value);
                }
            }

            // Access Permission Check
            $menu->filter(function ($item) {
                if ($item->data('permission')) {
                    if (auth()->check()) {
                        if (auth()->user()->hasRole('admin')) {
                            return true;
                        }
                        if (auth()->user()->hasAnyPermission($item->data('permission'))) {
                            return true;
                        }
                    }

                    return false;
                } else {
                    return true;
                }
            });
            // Set Active Menu
            $menu->filter(function ($item) {
                if ($item->activematches) {
                    $activematches = (is_string($item->activematches)) ? [$item->activematches] : $item->activematches;
                    foreach ($activematches as $pattern) {
                        if (request()->is($pattern)) {
                            $item->active();
                            $item->link->active();
                            if ($item->hasParent() && $item->parent()) {
                                $item->parent()->active();
                            }
                        }
                    }
                }

                return true;
            });
        })->sortBy('order');

        return \Menu::get($menuname);
    }

    protected function saveMenu($menu)
    {
        $menuChildren = $menu['children'] ?? null;
        $menu = Arr::except($menu, ['children']);
        $savedMenu = MenuBuilder::create($menu);
        if (isset($menuChildren) && count($menuChildren) > 0) {
            foreach ($menuChildren as $key => $value) {
                $value['parent_id'] = $savedMenu->id;
                $this->saveMenu($value);
            }
        }
    }

    protected function makeMenu($menu, $value)
    {
        if ($value->menu_item_type == 'static') {
            $this->staticMenu($menu, ['title' => __($value->title), 'order' => $value->order, 'permission' => $value->permission]);
        } else {
            if (count($value->children) > 0) {
                $parentMenuArr = [
                    'icon' => $value->start_icon,
                    'title' => __($value->title),
                    'active' => $value->active,
                    'nickname' => $value->nickname ?? \Str::slug($value->title),
                    'order' => $value->order,
                    'permission' => $value->permission,
                ];
                if (isset($value->parent)) {
                    $parentMenuArr['parent'] = $value->parent->nickname;
                }
                $parentMenu = $this->parentMenu($menu, $parentMenuArr);
                foreach ($value->children as $key => $childValue) {
                    $childArr = [
                        'title' => __($childValue->title),
                        'active' => $childValue->active,
                        'order' => $childValue->order,
                    ];

                    if (isset($childValue->start_icon)) {
                        $childArr['icon'] = $childValue->start_icon;
                    }

                    if ($childValue->is_route) {
                        if (isset($childValue->route)) {
                            $childArr['route'] = $childValue->route;
                        }
                    } else {
                        if (isset($childValue->url)) {
                            $childArr['url'] = $childValue->url;
                        }
                    }
                    if (isset($childValue['permission']) && count($childValue['permission']) > 0) {
                        $childArr['permission'] = $childValue->permission;
                    }
                    if (isset($childValue['target_type'])) {
                        $childArr['target'] = $childValue->target_type;
                    }
                    if (isset($childValue->children) && count($childValue->children) > 0) {
                        $this->makeMenu($parentMenu, $childValue);
                    } else {
                        switch ($childValue->menu_item_type) {
                            case 'static':
                                $this->staticMenu($parentMenu, ['title' => __($childValue->title), 'order' => $childValue->order, 'permission' => $childValue->permission]);
                                break;

                            case 'parent':
                                $this->makeMenu($parentMenu, $childValue);
                                break;
                            default:
                                $this->childMain($parentMenu, $childArr);
                                break;
                        }
                    }
                }
            } else {
                $arr = [
                    'icon' => $value->start_icon,
                    'title' => __($value->title),
                    'active' => $value->active,
                    'order' => $value->order,
                ];
                if ($value->is_route) {
                    if (isset($value->route)) {
                        $arr['route'] = $value->route;
                    }
                } else {
                    if (isset($value->url)) {
                        $arr['url'] = $value->url;
                    }
                }
                if (isset($value['permission']) && count($value['permission']) > 0) {
                    $arr['permission'] = $value->permission;
                }
                if (isset($value['target_type'])) {
                    $arr['target'] = $value->target_type;
                }
                $this->mainRoute($menu, $arr);
            }
        }
    }
}
