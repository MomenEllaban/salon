<?php

return [
    'ARRAY_MENU' => [
        [
            'start_icon' => 'fa-solid fa-globe',
            'title' => 'sidebar.location',
            'menu_item_type' => 'parent',
            'permission' => [],
            'order' => 20,
            'children' => [
                [
                    'title' => 'sidebar.city',
                    'route' => 'backend.city.index',
                    'active' => 'app/city',
                    'order' => 1,
                ],
                [
                    'title' => 'sidebar.state',
                    'route' => 'backend.state.index',
                    'active' => 'app/state',
                    'order' => 2,
                ],
                [
                    'title' => 'sidebar.country',
                    'route' => 'backend.country.index',
                    'active' => 'app/country',
                    'order' => 3,
                ],
            ],
        ],
    ],
];
