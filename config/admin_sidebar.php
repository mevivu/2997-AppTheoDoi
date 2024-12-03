<?php

return [
    [
        'title' => 'Dashboard',
        'routeName' => 'admin.dashboard',
        'icon' => '<i class="ti ti-home"></i>',
        'roles' => [],
        'permissions' => ['mevivuDev'],
        'sub' => []
    ],
    [
        'title' => 'notification',
        'routeName' => null,
        'icon' => '<i class="ti ti-bell-check"></i>',
        'roles' => [],
        'permissions' => ['createNotification', 'viewNotification', 'updateNotification', 'deleteNotification'],
        'sub' => [
            [
                'title' => 'Thêm thông báo',
                'routeName' => 'admin.notification.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createNotification'],
            ],
            [
                'title' => 'DS thông báo',
                'routeName' => 'admin.notification.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewNotification'],
            ],
        ]
    ],
    [
        'title' => 'Bài tập',
        'routeName' => null,
        'icon' => '<i class="ti ti-book"></i>',
        'roles' => [],
        'permissions' => ['createExercise', 'viewExercise', 'updateExercise', 'deleteExercise'],
        'sub' => [
            [
                'title' => 'Thêm Bài tập',
                'routeName' => 'admin.exercise.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createExercise'],
            ],
            [
                'title' => 'Bài tập thể chất',
                'routeName' => 'admin.exercise.physical',
                'icon' => '<i class="ti ti-swimming"></i>',
                'roles' => [],
                'permissions' => ['viewExercise'],
            ],
            [
                'title' => 'Bài tập sức mạnh',
                'routeName' => 'admin.exercise.power',
                'icon' => '<i class="ti ti-barbell"></i>',
                'roles' => [],
                'permissions' => ['viewExercise'],
            ],
        ]
    ],
    [
        'title' => 'Thông tin BMI',
        'routeName' => null,
        'icon' => '<i class="ti ti-info-circle"></i>',
        'roles' => [],
        'permissions' => ['createBMI', 'viewBMI', 'updateBMI', 'deleteBMI'],
        'sub' => [
            [
                'title' => 'Thêm thông tin BMI',
                'routeName' => 'admin.bmi.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createBMI'],
            ],
            [
                'title' => 'DS thông tin BMI',
                'routeName' => 'admin.bmi.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewBMI'],
            ],
        ]
    ],
    [
        'title' => 'Câu hỏi',
        'routeName' => null,
        'icon' => '<i class="ti ti-help-hexagon"></i>',
        'roles' => [],
        'permissions' => ['createQuestionGroup', 'viewQuestionGroup', 'updateQuestionGroup', 'deleteQuestionGroup'],
        'sub' => [
            [
                'title' => 'Nhóm câu hỏi',
                'routeName' => 'admin.question-group.index',
                'icon' => '<i class="ti ti-category"></i>',
                'roles' => [],
                'permissions' => ['viewQuestionGroup'],
            ],
            [
                'title' => 'Câu hỏi IQ',
                'routeName' => 'admin.question.iq',
                'icon' => '<i class="ti ti-brain"></i>',
                'roles' => [],
                'permissions' => ['viewQuestion'],
            ],
            [
                'title' => 'Câu hỏi EQ',
                'routeName' => 'admin.question.eq',
                'icon' => '<i class="ti ti-heart"></i>',
                'roles' => [],
                'permissions' => ['viewQuestion'],
            ],
            [
                'title' => 'Câu hỏi AQ',
                'routeName' => 'admin.question.aq',
                'icon' => '<i class="ti ti-leaf"></i>',
                'roles' => [],
                'permissions' => ['viewQuestion'],
            ],
        ]
    ],
    [
        'title' => 'Khách hàng',
        'routeName' => null,
        'icon' => '<i class="ti ti-users"></i>',
        'roles' => [],
        'permissions' => ['createUser', 'viewUser', 'updateUser', 'deleteUser'],
        'sub' => [
            [
                'title' => 'add',
                'routeName' => 'admin.user.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createUser'],
            ],
            [
                'title' => 'list',
                'routeName' => 'admin.user.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewUser'],
            ]
        ]
    ],
    [
        'title' => 'vaccination_schedule',
        'routeName' => null,
        'icon' => '<i class="ti ti-calendar-bolt"></i>',
        'roles' => [],
        'permissions' => ['createVaccinationSchedule', 'viewVaccinationSchedule', 'updateVaccinationSchedule', 'deleteVaccinationSchedule'],
        'sub' => [
            [
                'title' => 'add',
                'routeName' => 'admin.vaccination.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createVaccinationSchedule'],
            ],
            [
                'title' => 'list',
                'routeName' => 'admin.vaccination.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewVaccinationSchedule'],
            ]
        ]
    ],
    [
        'title' => 'Trẻ em',
        'routeName' => null,
        'icon' => '<i class="ti ti-baby-carriage"></i>',
        'roles' => [],
        'permissions' => ['createChildren', 'viewChildren', 'updateChildren', 'deleteChildren'],
        'sub' => [
            [
                'title' => 'add',
                'routeName' => 'admin.children.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createChildren'],
            ],
            [
                'title' => 'list',
                'routeName' => 'admin.children.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewChildren'],
            ]
        ]
    ],
    [
        'title' => 'clinic',
        'routeName' => null,
        'icon' => '<i class="ti ti-mushroom"></i>',
        'roles' => [],
        'permissions' => [
            'createClinicType',
            'viewClinicType',
            'updateClinicType',
            'deleteClinicType',
            'createClinic',
            'viewClinic',
            'updateClinic',
            'deleteClinic',
        ],
        'sub' => [
            [
                'title' => 'Thêm phòng khám',
                'routeName' => 'admin.clinic.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createClinic'],
            ],
            [
                'title' => 'DS  phòng khám',
                'routeName' => 'admin.clinic.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewClinic'],
            ],
            [
                'title' => 'DS loại phòng khám',
                'routeName' => 'admin.clinicType.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewClinicType'],
            ]
        ]
    ],
    [
        'title' => 'Vai trò',
        'routeName' => null,
        'icon' => '<i class="ti ti-user-check"></i>',
        'roles' => [],
        'permissions' => ['createRole', 'viewRole', 'updateRole', 'deleteRole'],
        'sub' => [
            [
                'title' => 'Thêm Vai trò',
                'routeName' => 'admin.role.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createRole'],
            ],
            [
                'title' => 'DS Vai trò',
                'routeName' => 'admin.role.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewRole'],
            ]
        ]
    ],
    [
        'title' => 'Admin',
        'routeName' => null,
        'icon' => '<i class="ti ti-user-shield"></i>',
        'roles' => [],
        'permissions' => ['createAdmin', 'viewAdmin', 'updateAdmin', 'deleteAdmin'],
        'sub' => [
            [
                'title' => 'Thêm Admin',
                'routeName' => 'admin.admin.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createAdmin'],
            ],
            [
                'title' => 'DS Admin',
                'routeName' => 'admin.admin.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewAdmin'],
            ],
        ]
    ],
    [
        'title' => 'Cài đặt',
        'routeName' => null,
        'icon' => '<i class="ti ti-settings"></i>',
        'roles' => [],
        'permissions' => ['settingGeneral'],
        'sub' => [
            [
                'title' => 'Chung',
                'routeName' => 'admin.setting.general',
                'icon' => '<i class="ti ti-tool"></i>',
                'roles' => [],
                'permissions' => ['settingGeneral'],
            ],
            [
                'title' => 'system_revenue',
                'routeName' => 'admin.setting.system',
                'icon' => '<i class="ti ti-server-cog"></i>',
                'permissions' => ['settingGeneral'],
            ],

        ]
    ],
    [
        'title' => 'Dev: Quyền',
        'routeName' => null,
        'icon' => '<i class="ti ti-code"></i>',
        'roles' => [],
        'permissions' => ['mevivuDev'],
        'sub' => [
            [
                'title' => 'Thêm Quyền',
                'routeName' => 'admin.permission.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['mevivuDev'],
            ],
            [
                'title' => 'DS Quyền',
                'routeName' => 'admin.permission.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['mevivuDev'],
            ]
        ]
    ],
    [
        'title' => 'Dev: Module',
        'routeName' => null,
        'icon' => '<i class="ti ti-code"></i>',
        'roles' => [],
        'permissions' => ['mevivuDev'],
        'sub' => [
            [
                'title' => 'Thêm Module',
                'routeName' => 'admin.module.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['mevivuDev'],
            ],
            [
                'title' => 'DS Module',
                'routeName' => 'admin.module.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['mevivuDev'],
            ]
        ]
    ],
    [
        'title' => 'Dev: Nghiệm thu',
        'routeName' => 'admin.module.summary',
        'icon' => '<i class="ti ti-code"></i>',
        'roles' => [],
        'permissions' => ['mevivuDev'],
        'sub' => []
    ],

];
