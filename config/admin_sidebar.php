<?php

use App\Traits\RouteAdminSystem;

return [
    [
        'title' => 'Dashboard',
        'routeName' => RouteAdminSystem::ADMIN_DASHBOARD,
        'icon' => '<i class="ti ti-home"></i>',
        'roles' => [],
        'permissions' => ['mevivuDev'],
        'sub' => []
    ],
    [
        'title' => 'Thống kê Firebase',
        'routeName' => RouteAdminSystem::FIREBASE_REPORT,
        'icon' => '<i class="ti ti-chart-bar"></i>',
        'roles' => [],
        'permissions' => [],
        'sub' => []
    ],
    [
        'title' => 'Thống kê Đối tác',
        'routeName' => RouteAdminSystem::AFFILIATE_STATISTICS_INDEX,
        'icon' => '<i class="ti ti-award"></i>',
        'roles' => [],
         'permissions' => ['mevivuDev'],
        'sub' => []
    ],
    [
        'title' => 'Giao dịch',
        'routeName' => null,
        'icon' => '<i class="ti ti-calendar-dollar"></i>',
        'roles' => [],
        'permissions' => ['viewTransaction'],
        'sub' => [

            [
                'title' => 'Thanh toán mua gói',
                'routeName' => RouteAdminSystem::TRANSACTION_INDEX,
                'icon' => '<i class="ti ti-receipt"></i>',
                'roles' => [],
                'permissions' => ['viewTransaction'],
            ],
            [
                'title' => 'Yêu cầu rút tiền',
                'routeName' => RouteAdminSystem::TRANSACTION_WITHDRAW,
                'icon' => '<i class="ti ti-wallet"></i>',
                'roles' => [],
                'permissions' => ['viewTransaction'],
            ],


        ]
    ],
    [
        'title' => 'Quá trình phát triển',
        'routeName' => null,
        'icon' => '<i class="ti ti-writing"></i>',
        'roles' => [],
        'permissions' => ['createDevelopGuide', 'viewDevelopGuide', 'updateDevelopGuide', 'deleteDevelopGuide'],
        'sub' => [
            [
                'title' => 'Thêm Quá trình',
                'routeName' => RouteAdminSystem::DEVELOP_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createDevelopGuide'],
            ],
            [
                'title' => 'DS Bài phát triển',
                'routeName' => RouteAdminSystem::DEVELOP_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewDevelopGuide'],
            ],
        ]
    ],

    [
        'title' => 'Học lực (GPA)',
        'routeName' => null,
        'icon' => '<i class="ti ti-bell-school"></i>',
        'roles' => [],
        'permissions' => ['viewGPA'],
        'sub' => [
            [
                'title' => 'DS Đánh giá',
                'routeName' => RouteAdminSystem::GPA_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewGPA'],
            ],
        ]
    ],
    [
        'title' => 'Thể chất (PQ)',
        'routeName' => null,
        'icon' => '<i class="ti ti-writing"></i>',
        'roles' => [],
        'permissions' => ['createGuide', 'viewGuide', 'updateGuide', 'deleteGuide', 'viewPQ', 'deletePQ'],
        'sub' => [
            [
                'title' => 'DS Đánh giá',
                'routeName' => RouteAdminSystem::RATING_PQ_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewPQ'],
            ],
            [
                'title' => 'DS Hướng dẫn',
                'routeName' => RouteAdminSystem::GUIDE_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewGuide'],
            ],
        ]
    ],
    [
        'title' => 'Chỉ số (EQ, IQ, AQ)',
        'routeName' => null,
        'icon' => '<i class="ti ti-writing"></i>',
        'roles' => [],
        'permissions' => ['viewEQ', 'viewAQ', 'viewIQ',],
        'sub' => [
            [
                'title' => 'DS Đánh giá EQ',
                'routeName' => RouteAdminSystem::RATING_EQ,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewEQ'],
            ],
            [
                'title' => 'DS Đánh giá IQ',
                'routeName' => RouteAdminSystem::RATING_IQ,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewIQ'],
            ],
            [
                'title' => 'DS Đánh giá AQ',
                'routeName' => RouteAdminSystem::RATING_AQ,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewAQ'],
            ],
        ]
    ],
    [
        'title' => 'Nhật ký',
        'routeName' => null,
        'icon' => '<i class="ti ti-external-link"></i>',
        'roles' => [],
        'permissions' => ['createJournal', 'viewJournal'],
        'sub' => [
            [
                'title' => 'Thêm Nhật ký',
                'routeName' => RouteAdminSystem::JOURNAL_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createJournal'],
            ],
            [
                'title' => 'Hồ sơ y tế',
                'routeName' => RouteAdminSystem::JOURNAL_PRESCRIPTION,
                'icon' => '<i class="ti ti-pill"></i>',
                'roles' => [],
                'permissions' => ['viewJournal'],
            ],
            [
                'title' => 'Nhật ký Khoảng khắc',
                'routeName' => RouteAdminSystem::JOURNAL_MOMENT,
                'icon' => '<i class="ti ti-comet"></i>',
                'roles' => [],
                'permissions' => ['viewJournal'],
            ]

        ]
    ],
    [
        'title' => 'Thai kì',
        'routeName' => null,
        'icon' => '<i class="ti ti-pennant"></i>',
        'roles' => [],
        'permissions' => ['createPregnancy', 'viewPregnancy', 'viewPregnancy', 'updatePregnancy'],
        'sub' => [
            [
                'title' => 'Thêm Thai kì',
                'routeName' => RouteAdminSystem::PREGNANCY_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createPregnancy'],
            ],
            [
                'title' => 'DS Thai kì',
                'routeName' => RouteAdminSystem::PREGNANCY_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewPregnancy'],
            ],


        ]
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
                'routeName' => RouteAdminSystem::NOTIFICATION_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createNotification'],
            ],
            [
                'title' => 'Thông báo ADMIN',
                'routeName' => RouteAdminSystem::NOTIFICATION_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewNotification'],
            ],

            [
                'title' => 'Thông báo Khách hàng',
                'routeName' => RouteAdminSystem::NOTIFICATION_USER,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewNotification'],
            ],

            [
                'title' => 'Yêu cầu xác nhận',
                'routeName' => RouteAdminSystem::NOTIFICATION_PACKAGE,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewNotification'],
            ],
        ]
    ],
    [
        'title' => 'Sliders',
        'routeName' => null,
        'icon' => '<i class="ti ti-slideshow"></i>',
        'roles' => [],
        'permissions' => ['createSlider', 'viewSlider', 'updateSlider', 'deleteSlider'],
        'sub' => [
            [
                'title' => 'Thêm Sliders',
                'routeName' => RouteAdminSystem::SLIDER_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createSlider'],
            ],
            [
                'title' => 'DS Sliders',
                'routeName' => RouteAdminSystem::SLIDER_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewSlider'],
            ],
        ]
    ],
    [
        'title' => 'package',
        'routeName' => null,
        'icon' => '<i class="ti ti-package-import"></i>',
        'roles' => [],
        'permissions' => ['createPackage', 'viewPackage', 'updatePackage', 'deletePackage'],
        'sub' => [
            [
                'title' => 'Thêm gói',
                'routeName' => RouteAdminSystem::PACKAGE_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createPackage'],
            ],
            [
                'title' => 'DS thông tin gói',
                'routeName' => RouteAdminSystem::PACKAGE_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewPackage'],
            ],
        ]
    ],
//    [
//        'title' => 'Bài tập',
//        'routeName' => null,
//        'icon' => '<i class="ti ti-book"></i>',
//        'roles' => [],
//        'permissions' => ['createExercise', 'viewExercise', 'updateExercise', 'deleteExercise'],
//        'sub' => [
//            [
//                'title' => 'Thêm Bài tập',
//                'routeName' => RouteAdminSystem::EXERCISE_CREATE,
//                'icon' => '<i class="ti ti-plus"></i>',
//                'roles' => [],
//                'permissions' => ['createExercise'],
//            ],
//            [
//                'title' => 'Bài tập thể chất',
//                'routeName' => RouteAdminSystem::EXERCISE_PHYSICAL,
//                'icon' => '<i class="ti ti-swimming"></i>',
//                'roles' => [],
//                'permissions' => ['viewExercise'],
//            ],
//            [
//                'title' => 'Bài tập sức mạnh',
//                'routeName' => RouteAdminSystem::EXERCISE_POWER,
//                'icon' => '<i class="ti ti-barbell"></i>',
//                'roles' => [],
//                'permissions' => ['viewExercise'],
//            ],
//        ]
//    ],
    [
        'title' => 'Bài viết',
        'routeName' => null,
        'icon' => '<i class="ti ti-article"></i>',
        'roles' => [],
        'permissions' =>
            [
                'createPost',
                'viewPost',
                'updatePost',
                'deletePost',
                'viewPostCategory',
                'createPostCategory',
                'updatePostCategory'
            ],
        'sub' => [
            [
                'title' => 'Thêm bài viết',
                'routeName' => RouteAdminSystem::POST_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createPost'],
            ],
            [
                'title' => 'DS bài viết',
                'routeName' => RouteAdminSystem::POST_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewPost'],
            ],
            [
                'title' => 'DS chuyên mục',
                'routeName' => RouteAdminSystem::POST_CATEGORY_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewPostCategory'],
            ]
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
                'routeName' => RouteAdminSystem::BMI_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createBMI'],
            ],
            [
                'title' => 'DS thông tin BMI',
                'routeName' => RouteAdminSystem::BMI_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewBMI'],
            ],
        ]
    ],
    [
        'title' => 'Thông tin Who',
        'routeName' => RouteAdminSystem::MODULE_SUMMARY,
        'icon' => '<i class="ti ti-woman"></i>',
        'roles' => [],
        'permissions' => ['createHeightWeight', 'viewHeightWeight', 'updateHeightWeight', 'deleteHeightWeight'],
        'sub' => [
            [
                'title' => 'Thêm',
                'routeName' => RouteAdminSystem::WHO_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createHeightWeight'],
            ],
            [
                'title' => 'DS Thông tin',
                'routeName' => RouteAdminSystem::WHO_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewHeightWeight'],
            ]
        ]
    ],
//    [
//        'title' => 'Thông tin dự kiến',
//        'routeName' => RouteAdminSystem::EXPECTED_INDEX,
//        'icon' => '<i class="ti ti-award"></i>',
//        'roles' => [],
//        'permissions' => ['createExpected', 'viewExpected', 'updateExpected', 'deleteExpected'],
//        'sub' => [
//            [
//                'title' => 'Thêm',
//                'routeName' => RouteAdminSystem::EXPECTED_CREATE,
//                'icon' => '<i class="ti ti-plus"></i>',
//                'roles' => [],
//                'permissions' => ['createExpected'],
//            ],
//            [
//                'title' => 'DS Thông tin',
//                'routeName' => RouteAdminSystem::EXPECTED_INDEX,
//                'icon' => '<i class="ti ti-list"></i>',
//                'roles' => [],
//                'permissions' => ['viewExpected'],
//            ]
//        ]
//    ],

    [
        'title' => 'product',
        'routeName' => RouteAdminSystem::EXPECTED_INDEX,
        'icon' => '<i class="ti ti-brand-producthunt"></i>',
        'roles' => [],
        'permissions' => [
            'createProductCatalog',
            'viewProductCatalog',
            'updateProductCatalog',
            'deleteProductCatalog',
            'createProduct',
            'viewProduct',
            'updateProduct',
            'deleteProduct',
            'createBrand',
            'viewBrand',
            'updateBrand',
            'deleteBrand',
        ],
        'sub' => [
            [
                'title' => 'Tạo sản phẩm',
                'routeName' => RouteAdminSystem::PRODUCT_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createProduct'],
            ],
            [
                'title' => 'DS sản phẩm',
                'routeName' => RouteAdminSystem::PRODUCT_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewProduct'],
            ],
            [
                'title' => 'DS thông tin thương hiệu',
                'routeName' => RouteAdminSystem::BRAND_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewBrand'],
            ],
            [
                'title' => 'Danh mục sản phẩm',
                'routeName' => RouteAdminSystem::CATEGORY_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewProductCatalog'],
            ],
        ]
    ],
    [
        'title' => 'Câu hỏi',
        'routeName' => null,
        'icon' => '<i class="ti ti-help-hexagon"></i>',
        'roles' => [],
        'permissions' => [
            'createQuestionGroup',
            'viewQuestionGroup',
            'updateQuestionGroup',
            'deleteQuestionGroup',
            'viewExpected'
        ],
        'sub' => [
            [
                'title' => 'Nhóm câu hỏi',
                'routeName' => RouteAdminSystem::QUESTION_GROUP_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewExpected'],
            ],
            [
                'title' => 'Câu hỏi IQ',
                'routeName' => RouteAdminSystem::QUESTION_IQ,
                'icon' => '<i class="ti ti-brain"></i>',
                'roles' => [],
                'permissions' => ['viewQuestionGroup'],
            ],
            [
                'title' => 'Câu hỏi EQ',
                'routeName' => RouteAdminSystem::QUESTION_EQ,
                'icon' => '<i class="ti ti-heart"></i>',
                'roles' => [],
                'permissions' => ['viewQuestionGroup'],
            ],
            [
                'title' => 'Câu hỏi AQ',
                'routeName' => RouteAdminSystem::QUESTION_AQ,
                'icon' => '<i class="ti ti-leaf"></i>',
                'roles' => [],
                'permissions' => ['viewQuestionGroup'],
            ],
        ]
    ],
    [
        'title' => 'Bài kiểm tra IQ',
        'routeName' => RouteAdminSystem::QUIZ_IQ,
        'icon' => '<i class="ti ti-brain"></i>',
        'roles' => [],
        'permissions' => ['createQuiz', 'viewQuiz', 'updateQuiz', 'deleteQuiz'],
        'sub' => [],
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
                'routeName' => RouteAdminSystem::USER_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createUser'],
            ],
            [
                'title' => 'list',
                'routeName' => RouteAdminSystem::USER_INDEX,
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
        'permissions' => [
            'createVaccinationSchedule',
            'viewVaccinationSchedule',
            'updateVaccinationSchedule',
            'deleteVaccinationSchedule',
            'createTypeVaccination',
            'viewTypeVaccination',
            'updateTypeVaccination',
            'deleteTypeVaccination'
        ],
        'sub' => [
//            [
//                'title' => 'add',
//                'routeName' => RouteAdminSystem::VACCINATION_CREATE,
//                'icon' => '<i class="ti ti-plus"></i>',
//                'roles' => [],
//                'permissions' => ['createVaccinationSchedule'],
//            ],
            [
                'title' => 'Quản trị viên',
                'routeName' => RouteAdminSystem::VACCINATION_ADMIN,
                'icon' => '<i class="ti ti-ad"></i>',
                'roles' => [],
                'permissions' => ['viewVaccinationSchedule'],
            ],
            [
                'title' => 'Người dùng',
                'routeName' => RouteAdminSystem::VACCINATION_USER,
                'icon' => '<i class="ti ti-user"></i>',
                'roles' => [],
                'permissions' => ['viewVaccinationSchedule'],
            ],
            [
                'title' => 'DS loại Tiêm chủng',
                'routeName' => RouteAdminSystem::VACCINATION_TYPE_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewTypeVaccination'],
            ],
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
                'routeName' => RouteAdminSystem::CHILDREN_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createChildren'],
            ],
            [
                'title' => 'list',
                'routeName' => RouteAdminSystem::CHILDREN_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewChildren'],
            ]
        ]
    ],
    [
        'title' => "Khung giáo dục",
        'routeName' => null,
        'icon' => '<i class="ti ti-star"></i>',
        'roles' => [],
        'permissions' => [
            'createQuality',
            'viewQuality',
            'updateQuality',
            'deleteQuality',
            'createCapability',
            'viewCapability',
            'updateCapability',
            'deleteCapability',
            'createClasses',
            'viewClasses',
            'updateClasses',
            'deleteClasses',
            'createSubject',
            'viewSubject',
            'updateSubject',
            'deleteSubject'

        ],
        'sub' => [
            [
                'title' => 'DS phẩm chất',
                'routeName' => RouteAdminSystem::QUALITY_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewQuality'],
            ],
            [
                'title' => 'DS năng lực',
                'routeName' => RouteAdminSystem::CAPABILITY_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewCapability'],
            ],
            [
                'title' => 'DS lớp',
                'routeName' => RouteAdminSystem::CLASSES_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewClasses'],
            ],
            [
                'title' => 'DS môn học',
                'routeName' => RouteAdminSystem::SUBJECT_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewSubject'],
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
                'routeName' => RouteAdminSystem::CLINIC_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createClinic'],
            ],
            [
                'title' => 'DS  phòng khám',
                'routeName' => RouteAdminSystem::CLINIC_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewClinic'],
            ],
            [
                'title' => 'DS loại phòng khám',
                'routeName' => RouteAdminSystem::CLINIC_TYPE_INDEX,
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
                'routeName' => RouteAdminSystem::ROLE_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createRole'],
            ],
            [
                'title' => 'DS Vai trò',
                'routeName' => RouteAdminSystem::ROLE_INDEX,
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewRole'],
            ]
        ]
    ],
    [
        'title' => 'Hỗ trợ khách hàng',
        'routeName' => null,
        'icon' => '<i class="ti ti-lifebuoy"></i>',
        'roles' => [],
        'permissions' => ['viewSupport'],
        'sub' => [
            [
                'title' => 'Thêm mới',
                'routeName' => RouteAdminSystem::SUPPORT_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['viewSupport'],
            ],
            [
                'title' => 'Câu hỏi thường gặp',
                'routeName' => RouteAdminSystem::SUPPORT_HELP_CENTER,
                'icon' => '<i class="ti ti-help-square-rounded"></i>',
                'roles' => [],
                'permissions' => ['viewSupport'],
            ],
            [
                'title' => 'Hướng dẫn sử dụng',
                'routeName' => RouteAdminSystem::SUPPORT_GUIDE,
                'icon' => '<i class="ti ti-help-square-rounded"></i>',
                'roles' => [],
                'permissions' => ['viewSupport'],
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
                'routeName' => RouteAdminSystem::ADMIN_CREATE,
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createAdmin'],
            ],
            [
                'title' => 'DS Admin',
                'routeName' => RouteAdminSystem::ADMIN_INDEX,
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
                'routeName' => RouteAdminSystem::SETTING_GENERAL,
                'icon' => '<i class="ti ti-tool"></i>',
                'roles' => [],
                'permissions' => ['settingGeneral'],
            ],
            [
                'title' => 'system_revenue',
                'routeName' => RouteAdminSystem::SETTING_SYSTEM,
                'icon' => '<i class="ti ti-server-cog"></i>',
                'permissions' => ['settingGeneral'],
            ],
            [
                'title' => 'Affiliate',
                'routeName' => RouteAdminSystem::SETTING_AFFILIATE,
                'icon' => '<i class="ti ti-affiliate"></i>',
                'roles' => [],
                'permissions' => ['settingGeneral'],
            ],
        ]
    ],
    [
        'title' => 'Quản lý Phiên bản',
        'routeName' => RouteAdminSystem::APP_VERSION_INDEX,
        'icon' => '<i class="ti ti-versions"></i>',
        'roles' => [],
        'permissions' => ['mevivuDev'],
        'sub' => []
    ],
//    [
//        'title' => 'Dev: Quyền',
//        'routeName' => null,
//        'icon' => '<i class="ti ti-code"></i>',
//        'roles' => [],
//        'permissions' => ['mevivuDev'],
//        'sub' => [
//            [
//                'title' => 'Thêm Quyền',
//                'routeName' => RouteAdminSystem::PERMISSION_CREATE,
//                'icon' => '<i class="ti ti-plus"></i>',
//                'roles' => [],
//                'permissions' => ['mevivuDev'],
//            ],
//            [
//                'title' => 'DS Quyền',
//                'routeName' => RouteAdminSystem::PERMISSION_INDEX,
//                'icon' => '<i class="ti ti-list"></i>',
//                'roles' => [],
//                'permissions' => ['mevivuDev'],
//            ]
//        ]
//    ],
//    [
//        'title' => 'Dev: Module',
//        'routeName' => null,
//        'icon' => '<i class="ti ti-code"></i>',
//        'roles' => [],
//        'permissions' => ['mevivuDev'],
//        'sub' => [
//            [
//                'title' => 'Thêm Module',
//                'routeName' => RouteAdminSystem::MODULE_CREATE,
//                'icon' => '<i class="ti ti-plus"></i>',
//                'roles' => [],
//                'permissions' => ['mevivuDev'],
//            ],
//            [
//                'title' => 'DS Module',
//                'routeName' => RouteAdminSystem::MODULE_INDEX,
//                'icon' => '<i class="ti ti-list"></i>',
//                'roles' => [],
//                'permissions' => ['mevivuDev'],
//            ]
//        ]
//    ],
//    [
//        'title' => 'Dev: Nghiệm thu',
//        'routeName' => RouteAdminSystem::MODULE_SUMMARY,
//        'icon' => '<i class="ti ti-code"></i>',
//        'roles' => [],
//        'permissions' => ['mevivuDev'],
//        'sub' => []
//    ],


];
