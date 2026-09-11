<?php

namespace App\Traits;

class RouteAdminSystem
{
    /** AUTHENTICATION & DASHBOARD */
    const ADMIN_LOGIN = 'admin.login.index';
    const ADMIN_LOGIN_POST = 'admin.login.post';
    const ADMIN_LOGOUT = 'admin.logout';
    const ADMIN_DASHBOARD = 'admin.dashboard';

    /** PWA */
    const PWA_MANIFEST = 'admin.manifest';
    const PWA_SERVICE_WORKER = 'admin.service-worker';
    const PWA_OFFLINE = 'admin.offline';

    /** PROFILE & PASSWORD */
    const PROFILE_INDEX = 'admin.profile.index';
    const PROFILE_UPDATE = 'admin.profile.update';
    const PASSWORD_INDEX = 'admin.password.index';
    const PASSWORD_UPDATE = 'admin.password.update';

    /** FIREBASE REPORT */
    const FIREBASE_REPORT = 'admin.firebase.report';

    /** FEATURE STATISTICS */
    const FEATURE_STATISTICS_INDEX = 'admin.feature.statistics.index';
    const FEATURE_STATISTICS_EXPORT = 'admin.feature.statistics.export';

    /** AFFILIATE STATISTICS */
    const AFFILIATE_STATISTICS_INDEX = 'admin.affiliate.statistics.index';
    const AFFILIATE_STATISTICS_PARTNER_DETAILS = 'admin.affiliate.statistics.partner.details';
    const AFFILIATE_STATISTICS_SYNC = 'admin.affiliate.statistics.sync';

    /** TRANSACTION */
    const TRANSACTION_INDEX = 'admin.transaction.index';
    const TRANSACTION_WITHDRAW = 'admin.transaction.withdraw';
    const TRANSACTION_APPROVE_WITHDRAW = 'admin.transaction.approveWithdraw';
    const TRANSACTION_REJECT_WITHDRAW = 'admin.transaction.rejectWithdraw';
    const TRANSACTION_CREATE = 'admin.transaction.create';
    const TRANSACTION_EDIT = 'admin.transaction.edit';
    const TRANSACTION_STORE = 'admin.transaction.store';
    const TRANSACTION_UPDATE = 'admin.transaction.update';
    const TRANSACTION_DELETE = 'admin.transaction.delete';

    /** KYC CCCD APPROVAL */
    const KYC_INDEX = 'admin.kyc.index';
    const KYC_APPROVE = 'admin.kyc.approve';
    const KYC_REJECT = 'admin.kyc.reject';

    /** DEVELOP GUIDE */
    const DEVELOP_INDEX = 'admin.develop.index';
    const DEVELOP_CREATE = 'admin.develop.create';
    const DEVELOP_EDIT = 'admin.develop.edit';
    const DEVELOP_STORE = 'admin.develop.store';
    const DEVELOP_UPDATE = 'admin.develop.update';
    const DEVELOP_DELETE = 'admin.develop.delete';

    /** STEP */
    const STEP_INDEX = 'admin.step.index';
    const STEP_CREATE = 'admin.step.create';
    const STEP_EDIT = 'admin.step.edit';
    const STEP_STORE = 'admin.step.store';
    const STEP_UPDATE = 'admin.step.update';
    const STEP_DELETE = 'admin.step.delete';

    /** GPA */
    const GPA_INDEX = 'admin.gpa.index';
    const GPA_CREATE = 'admin.gpa.create';
    const GPA_EDIT = 'admin.gpa.edit';
    const GPA_STORE = 'admin.gpa.store';
    const GPA_UPDATE = 'admin.gpa.update';
    const GPA_DELETE = 'admin.gpa.delete';

    /** RATING PQ & GUIDE */
    const RATING_PQ_INDEX = 'admin.ratingPQ.index';
    const RATING_PQ_CREATE = 'admin.ratingPQ.create';
    const RATING_PQ_EDIT = 'admin.ratingPQ.edit';
    const RATING_PQ_STORE = 'admin.ratingPQ.store';
    const RATING_PQ_UPDATE = 'admin.ratingPQ.update';
    const RATING_PQ_DELETE = 'admin.ratingPQ.delete';

    const GUIDE_INDEX = 'admin.guide.index';
    const GUIDE_CREATE = 'admin.guide.create';
    const GUIDE_EDIT = 'admin.guide.edit';
    const GUIDE_STORE = 'admin.guide.store';
    const GUIDE_UPDATE = 'admin.guide.update';
    const GUIDE_DELETE = 'admin.guide.delete';

    /** RATING EQ, IQ, AQ */
    const RATING_EQ = 'admin.rating.eq';
    const RATING_IQ = 'admin.rating.iq';
    const RATING_AQ = 'admin.rating.aq';

    /** JOURNAL */
    const JOURNAL_INDEX = 'admin.journal.index';
    const JOURNAL_CREATE = 'admin.journal.create';
    const JOURNAL_EDIT = 'admin.journal.edit';
    const JOURNAL_STORE = 'admin.journal.store';
    const JOURNAL_UPDATE = 'admin.journal.update';
    const JOURNAL_DELETE = 'admin.journal.delete';
    const JOURNAL_PRESCRIPTION = 'admin.journal.prescription';
    const JOURNAL_MOMENT = 'admin.journal.moment';

    /** PREGNANCY */
    const PREGNANCY_INDEX = 'admin.pregnancy.index';
    const PREGNANCY_CREATE = 'admin.pregnancy.create';
    const PREGNANCY_EDIT = 'admin.pregnancy.edit';
    const PREGNANCY_STORE = 'admin.pregnancy.store';
    const PREGNANCY_UPDATE = 'admin.pregnancy.update';
    const PREGNANCY_DELETE = 'admin.pregnancy.delete';

    /** NOTIFICATION */
    const NOTIFICATION_INDEX = 'admin.notification.index';
    const NOTIFICATION_CREATE = 'admin.notification.create';
    const NOTIFICATION_EDIT = 'admin.notification.edit';
    const NOTIFICATION_STORE = 'admin.notification.store';
    const NOTIFICATION_UPDATE = 'admin.notification.update';
    const NOTIFICATION_DELETE = 'admin.notification.delete';
    const NOTIFICATION_USER = 'admin.notification.user';
    const NOTIFICATION_PACKAGE = 'admin.notification.package';

    /** SLIDERS */
    const SLIDER_INDEX = 'admin.slider.index';
    const SLIDER_CREATE = 'admin.slider.create';
    const SLIDER_EDIT = 'admin.slider.edit';
    const SLIDER_STORE = 'admin.slider.store';
    const SLIDER_UPDATE = 'admin.slider.update';
    const SLIDER_DELETE = 'admin.slider.delete';

    /** SLIDER ITEMS */
    const SLIDER_ITEM_INDEX = 'admin.slider.item.index';
    const SLIDER_ITEM_CREATE = 'admin.slider.item.create';
    const SLIDER_ITEM_EDIT = 'admin.slider.item.edit';
    const SLIDER_ITEM_STORE = 'admin.slider.item.store';
    const SLIDER_ITEM_UPDATE = 'admin.slider.item.update';
    const SLIDER_ITEM_DELETE = 'admin.slider.item.delete';

    /** PACKAGE */
    const PACKAGE_INDEX = 'admin.package.index';
    const PACKAGE_CREATE = 'admin.package.create';
    const PACKAGE_EDIT = 'admin.package.edit';
    const PACKAGE_STORE = 'admin.package.store';
    const PACKAGE_UPDATE = 'admin.package.update';
    const PACKAGE_DELETE = 'admin.package.delete';

    /** POSTS & CATEGORIES */
    const POST_INDEX = 'admin.post.index';
    const POST_CREATE = 'admin.post.create';
    const POST_EDIT = 'admin.post.edit';
    const POST_STORE = 'admin.post.store';
    const POST_UPDATE = 'admin.post.update';
    const POST_DELETE = 'admin.post.delete';

    const POST_CATEGORY_INDEX = 'admin.post_category.index';
    const POST_CATEGORY_CREATE = 'admin.post_category.create';
    const POST_CATEGORY_EDIT = 'admin.post_category.edit';
    const POST_CATEGORY_STORE = 'admin.post_category.store';
    const POST_CATEGORY_UPDATE = 'admin.post_category.update';
    const POST_CATEGORY_DELETE = 'admin.post_category.delete';

    /** BMI & WHO */
    const BMI_INDEX = 'admin.bmi.index';
    const BMI_CREATE = 'admin.bmi.create';
    const BMI_EDIT = 'admin.bmi.edit';
    const BMI_STORE = 'admin.bmi.store';
    const BMI_UPDATE = 'admin.bmi.update';
    const BMI_DELETE = 'admin.bmi.delete';

    const WHO_INDEX = 'admin.weight-height-who.index';
    const WHO_CREATE = 'admin.weight-height-who.create';
    const WHO_EDIT = 'admin.weight-height-who.edit';
    const WHO_STORE = 'admin.weight-height-who.store';
    const WHO_UPDATE = 'admin.weight-height-who.update';
    const WHO_DELETE = 'admin.weight-height-who.delete';

    /** EXPECTED */
    const EXPECTED_INDEX = 'admin.expected.index';
    const EXPECTED_CREATE = 'admin.expected.create';
    const EXPECTED_EDIT = 'admin.expected.edit';
    const EXPECTED_STORE = 'admin.expected.store';
    const EXPECTED_UPDATE = 'admin.expected.update';
    const EXPECTED_DELETE = 'admin.expected.delete';

    /** PRODUCT & BRAND & CATEGORY */
    const PRODUCT_INDEX = 'admin.product.index';
    const PRODUCT_CREATE = 'admin.product.create';
    const PRODUCT_EDIT = 'admin.product.edit';
    const PRODUCT_STORE = 'admin.product.store';
    const PRODUCT_UPDATE = 'admin.product.update';
    const PRODUCT_DELETE = 'admin.product.delete';

    const BRAND_INDEX = 'admin.brand.index';
    const BRAND_CREATE = 'admin.brand.create';
    const BRAND_EDIT = 'admin.brand.edit';
    const BRAND_STORE = 'admin.brand.store';
    const BRAND_UPDATE = 'admin.brand.update';
    const BRAND_DELETE = 'admin.brand.delete';

    const CATEGORY_INDEX = 'admin.category.index';
    const CATEGORY_CREATE = 'admin.category.create';
    const CATEGORY_EDIT = 'admin.category.edit';
    const CATEGORY_STORE = 'admin.category.store';
    const CATEGORY_UPDATE = 'admin.category.update';
    const CATEGORY_DELETE = 'admin.category.delete';

    /** QUESTION & QUIZ */
    const QUESTION_GROUP_INDEX = 'admin.question-group.index';
    const QUESTION_GROUP_CREATE = 'admin.question-group.create';
    const QUESTION_GROUP_EDIT = 'admin.question-group.edit';
    const QUESTION_GROUP_STORE = 'admin.question-group.store';
    const QUESTION_GROUP_UPDATE = 'admin.question-group.update';
    const QUESTION_GROUP_DELETE = 'admin.question-group.delete';

    const QUESTION_IQ = 'admin.question.iq';
    const QUESTION_EQ = 'admin.question.eq';
    const QUESTION_AQ = 'admin.question.aq';
    const QUIZ_IQ = 'admin.quiz.iq';

    /** USERS & CHILDREN */
    const USER_INDEX = 'admin.user.index';
    const USER_CREATE = 'admin.user.create';
    const USER_EDIT = 'admin.user.edit';
    const USER_STORE = 'admin.user.store';
    const USER_UPDATE = 'admin.user.update';
    const USER_DELETE = 'admin.user.delete';
    const USER_FORCE_DELETE = 'admin.user.forceDelete';
    const USER_DEPOSIT = 'admin.user.deposit';
    const USER_WITHDRAW = 'admin.user.withdraw';

    const CHILDREN_INDEX = 'admin.children.index';
    const CHILDREN_CREATE = 'admin.children.create';
    const CHILDREN_EDIT = 'admin.children.edit';
    const CHILDREN_STORE = 'admin.children.store';
    const CHILDREN_UPDATE = 'admin.children.update';
    const CHILDREN_DELETE = 'admin.children.delete';

    /** VACCINATION */
    const VACCINATION_CREATE = 'admin.vaccination.create';
    const VACCINATION_ADMIN = 'admin.vaccination.admin';
    const VACCINATION_USER = 'admin.vaccination.user';
    const VACCINATION_SCHEDULE_INDEX = 'admin.vaccinationSchedule.index';
    const VACCINATION_SCHEDULE_CREATE = 'admin.vaccinationSchedule.create';
    const VACCINATION_SCHEDULE_EDIT = 'admin.vaccinationSchedule.edit';
    const VACCINATION_SCHEDULE_STORE = 'admin.vaccinationSchedule.store';
    const VACCINATION_SCHEDULE_UPDATE = 'admin.vaccinationSchedule.update';
    const VACCINATION_SCHEDULE_DELETE = 'admin.vaccinationSchedule.delete';

    const VACCINATION_TYPE_INDEX = 'admin.vaccinationType.index';
    const VACCINATION_TYPE_CREATE = 'admin.vaccinationType.create';
    const VACCINATION_TYPE_EDIT = 'admin.vaccinationType.edit';
    const VACCINATION_TYPE_STORE = 'admin.vaccinationType.store';
    const VACCINATION_TYPE_UPDATE = 'admin.vaccinationType.update';
    const VACCINATION_TYPE_DELETE = 'admin.vaccinationType.delete';

    /** EDUCATION FRAMEWORK */
    const QUALITY_INDEX = 'admin.quality.index';
    const QUALITY_CREATE = 'admin.quality.create';
    const QUALITY_EDIT = 'admin.quality.edit';
    const QUALITY_STORE = 'admin.quality.store';
    const QUALITY_UPDATE = 'admin.quality.update';
    const QUALITY_DELETE = 'admin.quality.delete';

    const CAPABILITY_INDEX = 'admin.capability.index';
    const CAPABILITY_CREATE = 'admin.capability.create';
    const CAPABILITY_EDIT = 'admin.capability.edit';
    const CAPABILITY_STORE = 'admin.capability.store';
    const CAPABILITY_UPDATE = 'admin.capability.update';
    const CAPABILITY_DELETE = 'admin.capability.delete';

    const CLASSES_INDEX = 'admin.classes.index';
    const CLASSES_CREATE = 'admin.classes.create';
    const CLASSES_EDIT = 'admin.classes.edit';
    const CLASSES_STORE = 'admin.classes.store';
    const CLASSES_UPDATE = 'admin.classes.update';
    const CLASSES_DELETE = 'admin.classes.delete';

    const SUBJECT_INDEX = 'admin.subject.index';
    const SUBJECT_CREATE = 'admin.subject.create';
    const SUBJECT_EDIT = 'admin.subject.edit';
    const SUBJECT_STORE = 'admin.subject.store';
    const SUBJECT_UPDATE = 'admin.subject.update';
    const SUBJECT_DELETE = 'admin.subject.delete';

    const EXERCISE_INDEX = 'admin.exercise.index';
    const EXERCISE_CREATE = 'admin.exercise.create';
    const EXERCISE_PHYSICAL = 'admin.exercise.physical';
    const EXERCISE_POWER = 'admin.exercise.power';
    const EXERCISE_EDIT = 'admin.exercise.edit';
    const EXERCISE_STORE = 'admin.exercise.store';
    const EXERCISE_UPDATE = 'admin.exercise.update';
    const EXERCISE_DELETE = 'admin.exercise.delete';

    /** CLINIC */
    const CLINIC_INDEX = 'admin.clinic.index';
    const CLINIC_CREATE = 'admin.clinic.create';
    const CLINIC_EDIT = 'admin.clinic.edit';
    const CLINIC_STORE = 'admin.clinic.store';
    const CLINIC_UPDATE = 'admin.clinic.update';
    const CLINIC_DELETE = 'admin.clinic.delete';

    const CLINIC_TYPE_INDEX = 'admin.clinicType.index';
    const CLINIC_TYPE_CREATE = 'admin.clinicType.create';
    const CLINIC_TYPE_EDIT = 'admin.clinicType.edit';
    const CLINIC_TYPE_STORE = 'admin.clinicType.store';
    const CLINIC_TYPE_UPDATE = 'admin.clinicType.update';
    const CLINIC_TYPE_DELETE = 'admin.clinicType.delete';

    /** ROLES & ADMINS */
    const ROLE_INDEX = 'admin.role.index';
    const ROLE_CREATE = 'admin.role.create';
    const ROLE_EDIT = 'admin.role.edit';
    const ROLE_STORE = 'admin.role.store';
    const ROLE_UPDATE = 'admin.role.update';
    const ROLE_DELETE = 'admin.role.delete';

    const ADMIN_INDEX = 'admin.admin.index';
    const ADMIN_CREATE = 'admin.admin.create';
    const ADMIN_EDIT = 'admin.admin.edit';
    const ADMIN_STORE = 'admin.admin.store';
    const ADMIN_UPDATE = 'admin.admin.update';
    const ADMIN_DELETE = 'admin.admin.delete';

    const PERMISSION_INDEX = 'admin.permission.index';
    const PERMISSION_CREATE = 'admin.permission.create';
    const PERMISSION_EDIT = 'admin.permission.edit';
    const PERMISSION_STORE = 'admin.permission.store';
    const PERMISSION_UPDATE = 'admin.permission.update';
    const PERMISSION_DELETE = 'admin.permission.delete';

    const MODULE_INDEX = 'admin.module.index';
    const MODULE_CREATE = 'admin.module.create';
    const MODULE_EDIT = 'admin.module.edit';
    const MODULE_STORE = 'admin.module.store';
    const MODULE_UPDATE = 'admin.module.update';
    const MODULE_DELETE = 'admin.module.delete';
    const MODULE_SUMMARY = 'admin.module.summary';

    /** SUPPORT */
    const SUPPORT_INDEX = 'admin.support.index';
    const SUPPORT_CREATE = 'admin.support.create';
    const SUPPORT_EDIT = 'admin.support.edit';
    const SUPPORT_STORE = 'admin.support.store';
    const SUPPORT_UPDATE = 'admin.support.update';
    const SUPPORT_DELETE = 'admin.support.delete';
    const SUPPORT_HELP_CENTER = 'admin.support.help-center';
    const SUPPORT_GUIDE = 'admin.support.guide';

    /** SETTING & APP VERSION */
    const SETTING_GENERAL = 'admin.setting.general';
    const SETTING_SYSTEM = 'admin.setting.system';
    const SETTING_AFFILIATE = 'admin.setting.affiliate';
    const SETTING_UPDATE = 'admin.setting.update';
    const APP_VERSION_INDEX = 'admin.app-version.index';
    const APP_VERSION_CREATE = 'admin.app-version.create';
    const APP_VERSION_EDIT = 'admin.app-version.edit';
    const APP_VERSION_STORE = 'admin.app-version.store';
    const APP_VERSION_UPDATE = 'admin.app-version.update';
    const APP_VERSION_DELETE = 'admin.app-version.delete';
}
