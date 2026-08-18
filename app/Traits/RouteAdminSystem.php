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

    /** TRANSACTION */
    const TRANSACTION_INDEX = 'admin.transaction.index';
    const TRANSACTION_CREATE = 'admin.transaction.create';
    const TRANSACTION_EDIT = 'admin.transaction.edit';
    const TRANSACTION_UPDATE = 'admin.transaction.update';
    const TRANSACTION_DELETE = 'admin.transaction.delete';

    /** DEVELOP GUIDE */
    const DEVELOP_INDEX = 'admin.develop.index';
    const DEVELOP_CREATE = 'admin.develop.create';
    const DEVELOP_EDIT = 'admin.develop.edit';
    const DEVELOP_UPDATE = 'admin.develop.update';
    const DEVELOP_DELETE = 'admin.develop.delete';

    /** GPA */
    const GPA_INDEX = 'admin.gpa.index';
    const GPA_CREATE = 'admin.gpa.create';
    const GPA_EDIT = 'admin.gpa.edit';
    const GPA_UPDATE = 'admin.gpa.update';
    const GPA_DELETE = 'admin.gpa.delete';

    /** RATING PQ & GUIDE */
    const RATING_PQ_INDEX = 'admin.ratingPQ.index';
    const GUIDE_INDEX = 'admin.guide.index';

    /** RATING EQ, IQ, AQ */
    const RATING_EQ = 'admin.rating.eq';
    const RATING_IQ = 'admin.rating.iq';
    const RATING_AQ = 'admin.rating.aq';

    /** JOURNAL */
    const JOURNAL_INDEX = 'admin.journal.index';
    const JOURNAL_CREATE = 'admin.journal.create';
    const JOURNAL_PRESCRIPTION = 'admin.journal.prescription';
    const JOURNAL_MOMENT = 'admin.journal.moment';

    /** PREGNANCY */
    const PREGNANCY_INDEX = 'admin.pregnancy.index';
    const PREGNANCY_CREATE = 'admin.pregnancy.create';

    /** NOTIFICATION */
    const NOTIFICATION_INDEX = 'admin.notification.index';
    const NOTIFICATION_CREATE = 'admin.notification.create';
    const NOTIFICATION_USER = 'admin.notification.user';
    const NOTIFICATION_PACKAGE = 'admin.notification.package';

    /** SLIDERS */
    const SLIDER_INDEX = 'admin.slider.index';
    const SLIDER_CREATE = 'admin.slider.create';

    /** PACKAGE */
    const PACKAGE_INDEX = 'admin.package.index';
    const PACKAGE_CREATE = 'admin.package.create';

    /** POSTS & CATEGORIES */
    const POST_INDEX = 'admin.post.index';
    const POST_CREATE = 'admin.post.create';
    const POST_CATEGORY_INDEX = 'admin.post_category.index';

    /** BMI & WHO */
    const BMI_INDEX = 'admin.bmi.index';
    const BMI_CREATE = 'admin.bmi.create';
    const WHO_INDEX = 'admin.weight-height-who.index';
    const WHO_CREATE = 'admin.weight-height-who.create';

    /** PRODUCT & BRAND & CATEGORY */
    const PRODUCT_INDEX = 'admin.product.index';
    const PRODUCT_CREATE = 'admin.product.create';
    const BRAND_INDEX = 'admin.brand.index';
    const CATEGORY_INDEX = 'admin.category.index';

    /** QUESTION & QUIZ */
    const QUESTION_GROUP_INDEX = 'admin.question-group.index';
    const QUESTION_IQ = 'admin.question.iq';
    const QUESTION_EQ = 'admin.question.eq';
    const QUESTION_AQ = 'admin.question.aq';
    const QUIZ_IQ = 'admin.quiz.iq';

    /** USERS & CHILDREN */
    const USER_INDEX = 'admin.user.index';
    const USER_CREATE = 'admin.user.create';
    const CHILDREN_INDEX = 'admin.children.index';
    const CHILDREN_CREATE = 'admin.children.create';

    /** VACCINATION */
    const VACCINATION_ADMIN = 'admin.vaccination.admin';
    const VACCINATION_USER = 'admin.vaccination.user';
    const VACCINATION_TYPE_INDEX = 'admin.vaccinationType.index';

    /** EDUCATION FRAMEWORK */
    const QUALITY_INDEX = 'admin.quality.index';
    const CAPABILITY_INDEX = 'admin.capability.index';
    const CLASSES_INDEX = 'admin.classes.index';
    const SUBJECT_INDEX = 'admin.subject.index';

    /** CLINIC */
    const CLINIC_INDEX = 'admin.clinic.index';
    const CLINIC_CREATE = 'admin.clinic.create';
    const CLINIC_TYPE_INDEX = 'admin.clinicType.index';

    /** ROLES & ADMINS */
    const ROLE_INDEX = 'admin.role.index';
    const ROLE_CREATE = 'admin.role.create';
    const ADMIN_INDEX = 'admin.admin.index';
    const ADMIN_CREATE = 'admin.admin.create';

    /** SUPPORT */
    const SUPPORT_INDEX = 'admin.support.index';
    const SUPPORT_CREATE = 'admin.support.create';
    const SUPPORT_HELP_CENTER = 'admin.support.help-center';
    const SUPPORT_GUIDE = 'admin.support.guide';

    /** SETTING & APP VERSION */
    const SETTING_GENERAL = 'admin.setting.general';
    const SETTING_SYSTEM = 'admin.setting.system';
    const APP_VERSION_INDEX = 'admin.app-version.index';
}
