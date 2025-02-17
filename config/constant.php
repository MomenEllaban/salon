<?php

return [

    'SUBSCRIPTION_STATUS' => [
        'PENDING' => 'pending',
        'ACTIVE' => 'active',
        'INACTIVE' => 'inactive',
    ],
    'USER_PERMISSION_ALLOW' => [
        //
    ],
    'MODULES' => [
        [
            'module_name' => 'Branch',
            'more_permission' => ['gallery'],
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Booking',
            'more_permission' => ['booking_tableview'],
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Service',
            'more_permission' => ['gallery'],
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Category',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Subcategory',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Staff',
            'more_permission' => ['password'],
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Customer',
            'more_permission' => ['password'],
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Page',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Tax',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Notification',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'App Banner',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Notification Template',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Review',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Menu Builder',
            'more_permission' => ['sidebar', 'header'],
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Tax',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Commission',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Custom Field',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Setting',
            'more_permission' => ['general', 'misc', 'quick_booking', 'custom_code', 'customization', 'mail', 'notification', 'intigrations', 'custom_fields', 'currency', 'commission', 'holiday', 'bussiness_hours', 'payment_method', 'language', 'menu_builder', 'invoice'],
            'is_custom_permission' => 1,
        ],
        [
            'module_name' => 'Product',
            'more_permission' => ['stock', 'gallary'],
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Product Variations',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Product Category',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Product Brand',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Product Units',
            'is_custom_permission' => 0,
        ],
        [
            'module_name' => 'Product Orders',
            'is_custom_permission' => 0,
        ],
    ],
];
