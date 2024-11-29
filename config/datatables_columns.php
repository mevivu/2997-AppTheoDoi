<?php

return [


    'notifications' => [
        'checkbox' => [
            'title' => 'choose',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'align-middle text-center',
            'footer' => '<input type="checkbox" class="form-check-input check-all" />',
            'visible' => false,
        ],

        'title' => [
            'title' => 'Tiêu đề',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],

        'user_id' => [
            'title' => 'Nhân viên nhận',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],

        'admin_id' => [
            'title' => 'Admin nhận',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],

        'message' => [
            'title' => 'Nội dung',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],

        'status' => [
            'title' => 'status',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],

        'created_at' => [
            'title' => 'Ngày thông báo',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
        ],

        'action' => [
            'title' => 'action',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle',
        ],

    ],

    'module' => [
        'checkbox' => [
            'title' => 'choose',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'align-middle text-center w-25',
            'footer' => '<input type="checkbox" class="form-check-input check-all" />',
        ],
        'id' => [
            'title' => 'ID',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'name' => [
            'title' => 'Tên Module',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'role' => [
        'id' => [
            'title' => 'ID',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'title' => [
            'title' => 'Tên vai trò',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'name' => [
            'title' => 'Slug ( role_name )',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'guard_name' => [
            'title' => 'Vai trò của nhóm ( Guard Name )',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'permission' => [
        'checkbox' => [
            'title' => 'choose',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'align-middle text-center',
            'footer' => '<input type="checkbox" class="form-check-input check-all" />',
        ],
        'id' => [
            'title' => 'ID',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'title' => [
            'title' => 'Tên quyền',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'name' => [
            'title' => 'Slug ( Permission_name )',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'module_id' => [
            'title' => 'Thuộc Module',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'guard_name' => [
            'title' => 'Nhóm quyền ( Guard Name )',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'type' => [
            'title' => 'Loại quyền',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'admin' => [

        'fullname' => [
            'title' => 'Họ tên',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        'phone' => [
            'title' => 'Số điện thoại',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        'email' => [
            'title' => 'Email',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'roles' => [
            'title' => 'Vai trò',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'orderable' => false,
            'visible' => false
        ],
        'action' => [
            'title' => 'Thao tác',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle',
        ],
    ],

    'user' => [
        'checkbox' => [
            'title' => 'choose',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'align-middle text-center',
            'footer' => '<input type="checkbox" class="form-check-input check-all" />',
            'visible' => false,
        ],
        'code' => [
            'title' => 'Mã nhân viên',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'fullname' => [
            'title' => 'Họ tên',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        'email' => [
            'title' => 'Email',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'phone' => [
            'title' => 'Số điện thoại',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        'status' => [
            'title' => 'status',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        'active' => [
            'title' => 'Admin xác nhận',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        'action' => [
            'title' => 'Thao tác',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'children' => [
        'checkbox' => [
            'title' => 'choose',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'align-middle text-center',
            'footer' => '<input type="checkbox" class="form-check-input check-all" />',
            'visible' => false,
        ],
        'fullname' => [
            'title' => 'Họ tên trẻ',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        'user_id' => [
            'title' => 'Cha/Mẹ',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        
        'birthday' => [
            'title' => 'birth-day',
            'addClass' => 'text-center align-middle',
            'orderable' => false,
        ],
        'gender' => [
            'title' => 'gender',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        'status' => [
            'title' => 'status',
            'addClass' => 'text-center align-middle',
            'orderable' => false
        ],
        'action' => [
            'title' => 'Thao tác',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],

    'slider' => [
        'name' => [
            'title' => 'Tên',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'plain_key' => [
            'title' => 'Key',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'items' => [
            'title' => 'Slider Item',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'orderable' => false,
            'visible' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'slider_item' => [
        'title' => [
            'title' => 'Tên',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle',
        ],
        'image' => [
            'title' => 'Hình ảnh',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'position' => [
            'title' => 'Vị trí',
            'orderable' => false,
            'width' => '150px',
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'orderable' => false,
            'visible' => false,
            'addClass' => 'text-center align-middle'
        ],
        'action' => [
            'title' => 'Thao tác',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'post_category' => [
        'avatar' => [
            'title' => 'avatar',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'name' => [
            'title' => 'Tên danh mục',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => false
        ],
        'action' => [
            'title' => 'Thao tác',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'text-center align-middle'
        ],
    ],
    'post' => [
        'checkbox' => [
            'title' => 'choose',
            'orderable' => false,
            'exportable' => false,
            'printable' => false,
            'addClass' => 'align-middle text-center',
            'footer' => '<input type="checkbox" class="form-check-input check-all" />',
            'visible' => false,
        ],
        'image' => [
            'title' => 'Ảnh',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'title' => [
            'title' => 'Tiêu đề',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'status' => [
            'title' => 'Trạng thái',
            'orderable' => false,
            'addClass' => 'text-center align-middle'
        ],
        'is_featured' => [
            'title' => 'Nổi bật',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => false
        ],
        'created_at' => [
            'title' => 'Ngày tạo',
            'orderable' => false,
            'addClass' => 'text-center align-middle',
            'visible' => false
        ],
    ],

];
