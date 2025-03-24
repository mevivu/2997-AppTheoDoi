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
        'title' => 'Giao dịch',
        'routeName' => null,
        'icon' => '<i class="ti ti-calendar-dollar"></i>',
        'roles' => [],
        'permissions' => ['viewTransaction'],
        'sub' => [

            [
                'title' => 'DS giao dịch',
                'routeName' => 'admin.transaction.index',
                'icon' => '<i class="ti ti-list"></i>',
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
                'routeName' => 'admin.develop.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createDevelopGuide'],
            ],
            [
                'title' => 'DS Bài phát triển',
                'routeName' => 'admin.develop.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewDevelopGuide'],
            ],
        ]
    ],

    [
        'title' => 'Đánh giá học lực (GPA)',
        'routeName' => null,
        'icon' => '<i class="ti ti-bell-school"></i>',
        'roles' => [],
        'permissions' => ['viewGPA'],
        'sub' => [
            [
                'title' => 'DS Đánh giá',
                'routeName' => 'admin.gpa.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewGPA'],
            ],
        ]
    ],
    [
        'title' => 'Đánh giá thể chất (PQ)',
        'routeName' => null,
        'icon' => '<i class="ti ti-writing"></i>',
        'roles' => [],
        'permissions' => ['createGuide', 'viewGuide', 'updateGuide', 'deleteGuide', 'viewPQ', 'deletePQ'],
        'sub' => [
            [
                'title' => 'DS Đánh giá',
                'routeName' => 'admin.ratingPQ.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewPQ'],
            ],
            [
                'title' => 'DS Hướng dẫn',
                'routeName' => 'admin.guide.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewGuide'],
            ],
        ]
    ],
    [
        'title' => 'Đánh giá (EQ,IQ,AQ)',
        'routeName' => null,
        'icon' => '<i class="ti ti-writing"></i>',
        'roles' => [],
        'permissions' => ['viewEQ', 'viewAQ', 'viewIQ',],
        'sub' => [
            [
                'title' => 'DS Đánh giá EQ',
                'routeName' => 'admin.rating.eq',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewEQ'],
            ],
            [
                'title' => 'DS Đánh giá IQ',
                'routeName' => 'admin.rating.iq',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewIQ'],
            ],
            [
                'title' => 'DS Đánh giá AQ',
                'routeName' => 'admin.rating.aq',
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
                'routeName' => 'admin.journal.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createJournal'],
            ],
            [
                'title' => 'Nhật ký đơn thuốc',
                'routeName' => 'admin.journal.prescription',
                'icon' => '<i class="ti ti-pill"></i>',
                'roles' => [],
                'permissions' => ['viewJournal'],
            ],
            [
                'title' => 'Nhật ký Khoảng khắc',
                'routeName' => 'admin.journal.moment',
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
                'routeName' => 'admin.pregnancy.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createPregnancy'],
            ],
            [
                'title' => 'DS Thai kì',
                'routeName' => 'admin.pregnancy.index',
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
                'routeName' => 'admin.notification.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createNotification'],
            ],
            [
                'title' => 'Thông báo ADMIN',
                'routeName' => 'admin.notification.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewNotification'],
            ],

            [
                'title' => 'Thông báo Khách hàng',
                'routeName' => 'admin.notification.user',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewNotification'],
            ],

            [
                'title' => 'Yêu cầu xác nhận',
                'routeName' => 'admin.notification.package',
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
                'routeName' => 'admin.slider.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createSlider'],
            ],
            [
                'title' => 'DS Sliders',
                'routeName' => 'admin.slider.index',
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
                'routeName' => 'admin.package.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createPackage'],
            ],
            [
                'title' => 'DS thông tin gói',
                'routeName' => 'admin.package.index',
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
//                'routeName' => 'admin.exercise.create',
//                'icon' => '<i class="ti ti-plus"></i>',
//                'roles' => [],
//                'permissions' => ['createExercise'],
//            ],
//            [
//                'title' => 'Bài tập thể chất',
//                'routeName' => 'admin.exercise.physical',
//                'icon' => '<i class="ti ti-swimming"></i>',
//                'roles' => [],
//                'permissions' => ['viewExercise'],
//            ],
//            [
//                'title' => 'Bài tập sức mạnh',
//                'routeName' => 'admin.exercise.power',
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
                'routeName' => 'admin.post.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createPost'],
            ],
            [
                'title' => 'DS bài viết',
                'routeName' => 'admin.post.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewPost'],
            ],
            [
                'title' => 'DS chuyên mục',
                'routeName' => 'admin.post_category.index',
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
        'title' => 'Thông tin Who',
        'routeName' => 'admin.module.summary',
        'icon' => '<i class="ti ti-woman"></i>',
        'roles' => [],
        'permissions' => ['createHeightWeight', 'viewHeightWeight', 'updateHeightWeight', 'deleteHeightWeight'],
        'sub' => [
            [
                'title' => 'Thêm',
                'routeName' => 'admin.weight-height-who.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createHeightWeight'],
            ],
            [
                'title' => 'DS Thông tin',
                'routeName' => 'admin.weight-height-who.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewHeightWeight'],
            ]
        ]
    ],
    [
        'title' => 'Thông tin dự kiến',
        'routeName' => 'admin.expected.index',
        'icon' => '<i class="ti ti-award"></i>',
        'roles' => [],
        'permissions' => ['createExpected', 'viewExpected', 'updateExpected', 'deleteExpected'],
        'sub' => [
            [
                'title' => 'Thêm',
                'routeName' => 'admin.expected.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createExpected'],
            ],
            [
                'title' => 'DS Thông tin',
                'routeName' => 'admin.expected.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewExpected'],
            ]
        ]
    ],

    [
        'title' => 'product',
        'routeName' => 'admin.expected.index',
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
                'routeName' => 'admin.product.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createProduct'],
            ],
            [
                'title' => 'DS sản phẩm',
                'routeName' => 'admin.product.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewProduct'],
            ],
            [
                'title' => 'DS thông tin thương hiệu',
                'routeName' => 'admin.brand.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewBrand'],
            ],
            [
                'title' => 'Danh mục sản phẩm',
                'routeName' => 'admin.category.index',
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
                'routeName' => 'admin.question-group.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewExpected'],
            ],
            [
                'title' => 'Câu hỏi IQ',
                'routeName' => 'admin.question.iq',
                'icon' => '<i class="ti ti-brain"></i>',
                'roles' => [],
                'permissions' => ['viewQuestionGroup'],
            ],
            [
                'title' => 'Câu hỏi EQ',
                'routeName' => 'admin.question.eq',
                'icon' => '<i class="ti ti-heart"></i>',
                'roles' => [],
                'permissions' => ['viewQuestionGroup'],
            ],
            [
                'title' => 'Câu hỏi AQ',
                'routeName' => 'admin.question.aq',
                'icon' => '<i class="ti ti-leaf"></i>',
                'roles' => [],
                'permissions' => ['viewQuestionGroup'],
            ],
        ]
    ],
    [
        'title' => 'quiz',
        'routeName' => 'admin.expected.index',
        'icon' => '<i class="ti ti-award"></i>',
        'roles' => [],
        'permissions' => ['createQuiz', 'viewQuiz', 'updateQuiz', 'deleteQuiz'],
        'sub' => [
            [
                'title' => 'Bài kiểm tra IQ',
                'routeName' => 'admin.quiz.iq',
                'icon' => '<i class="ti ti-brain"></i>',
                'roles' => [],
                'permissions' => ['viewQuiz'],
            ],
//            [
//                'title' => 'Bài kiểm tra EQ',
//                'routeName' => 'admin.quiz.eq',
//                'icon' => '<i class="ti ti-heart"></i>',
//                'roles' => [],
//                'permissions' => ['viewQuiz'],
//            ],
//            [
//                'title' => 'Bài kiểm tra AQ',
//                'routeName' => 'admin.quiz.aq',
//                'icon' => '<i class="ti ti-leaf"></i>',
//                'roles' => [],
//                'permissions' => ['viewQuiz'],
//            ],
//            [
//                'title' => 'Bài kiểm tra PQ',
//                'routeName' => 'admin.quiz.pq',
//                'icon' => '<i class="ti ti-switch"></i>',
//                'roles' => [],
//                'permissions' => ['viewQuiz'],
//            ]
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
            [
                'title' => 'add',
                'routeName' => 'admin.vaccination.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['createVaccinationSchedule'],
            ],
            [
                'title' => 'Quản trị viên',
                'routeName' => 'admin.vaccination.admin',
                'icon' => '<i class="ti ti-ad"></i>',
                'roles' => [],
                'permissions' => ['viewVaccinationSchedule'],
            ],
            [
                'title' => 'Người dùng',
                'routeName' => 'admin.vaccination.user',
                'icon' => '<i class="ti ti-user"></i>',
                'roles' => [],
                'permissions' => ['viewVaccinationSchedule'],
            ],
            [
                'title' => 'DS loại Tiêm chủng',
                'routeName' => 'admin.vaccinationType.index',
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
                'routeName' => 'admin.quality.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewQuality'],
            ],
            [
                'title' => 'DS năng lực',
                'routeName' => 'admin.capability.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewCapability'],
            ],
            [
                'title' => 'DS lớp',
                'routeName' => 'admin.classes.index',
                'icon' => '<i class="ti ti-list"></i>',
                'roles' => [],
                'permissions' => ['viewClasses'],
            ],
            [
                'title' => 'DS môn học',
                'routeName' => 'admin.subject.index',
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
        'title' => 'Hỗ trợ khách hàng',
        'routeName' => null,
        'icon' => '<i class="ti ti-lifebuoy"></i>',
        'roles' => [],
        'permissions' => ['viewSupport'],
        'sub' => [
            [
                'title' => 'Thêm mới',
                'routeName' => 'admin.support.create',
                'icon' => '<i class="ti ti-plus"></i>',
                'roles' => [],
                'permissions' => ['viewSupport'],
            ],
            [
                'title' => 'Trung tâm trợ giúp',
                'routeName' => 'admin.support.help-center',
                'icon' => '<i class="ti ti-help-square-rounded"></i>',
                'roles' => [],
                'permissions' => ['viewSupport'],
            ],
            [
                'title' => 'Hướng dẫn sử dụng',
                'routeName' => 'admin.support.guide',
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
//    [
//        'title' => 'Dev: Quyền',
//        'routeName' => null,
//        'icon' => '<i class="ti ti-code"></i>',
//        'roles' => [],
//        'permissions' => ['mevivuDev'],
//        'sub' => [
//            [
//                'title' => 'Thêm Quyền',
//                'routeName' => 'admin.permission.create',
//                'icon' => '<i class="ti ti-plus"></i>',
//                'roles' => [],
//                'permissions' => ['mevivuDev'],
//            ],
//            [
//                'title' => 'DS Quyền',
//                'routeName' => 'admin.permission.index',
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
//                'routeName' => 'admin.module.create',
//                'icon' => '<i class="ti ti-plus"></i>',
//                'roles' => [],
//                'permissions' => ['mevivuDev'],
//            ],
//            [
//                'title' => 'DS Module',
//                'routeName' => 'admin.module.index',
//                'icon' => '<i class="ti ti-list"></i>',
//                'roles' => [],
//                'permissions' => ['mevivuDev'],
//            ]
//        ]
//    ],
//    [
//        'title' => 'Dev: Nghiệm thu',
//        'routeName' => 'admin.module.summary',
//        'icon' => '<i class="ti ti-code"></i>',
//        'roles' => [],
//        'permissions' => ['mevivuDev'],
//        'sub' => []
//    ],


];
