<?php

namespace App\Admin\Http\Controllers\Dashboard;

use App\Admin\Http\Controllers\Controller;
use App\Enums\ActiveStatus;
use App\Enums\DeleteStatus;
use App\Enums\Journal\JournalType;
use App\Enums\Question\QuestionType;
use App\Enums\User\UserStatus;
use App\Models\Admin;
use App\Models\Bmi;
use App\Models\Capability;
use App\Models\Child;
use App\Models\Clinic;
use App\Models\Exercise;
use App\Models\Expected;
use App\Models\Guide;
use App\Models\Journal;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Post;
use App\Models\Pregnancy;
use App\Models\Product;
use App\Models\Quality;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Slider;
use App\Models\Subject;
use App\Models\Support;
use App\Models\Transaction;
use App\Models\ClassGrade;
use App\Models\User;
use App\Models\VaccinationSchedule;

class DashboardController extends Controller
{
    //

    public function getView()
    {
        return [
            'index' => 'admin.dashboard.index'
        ];
    }
    public function index(){
        $rowCountTransaction = Transaction::where('is_deleted', DeleteStatus::NotDeleted)->count();
        $rowCountGPA = ClassGrade::where('status', ActiveStatus::Draft)->count();
        $rowCountGuide = Guide::where('status', ActiveStatus::Active)->count();
        $rowCountJournal = Journal::where('type', JournalType::Prescription)->count() + Journal::where('type', JournalType::Moment)->count();
        $rowCountPregnancy = Pregnancy::where('status', ActiveStatus::Active)->count();
        $rowCountNotification = Notification::count();
        $rowCountSlider = Slider::count();
        $rowCountPackage = Package::where('status', ActiveStatus::Active)->count();
        $rowCountExercise = Exercise::where('status', ActiveStatus::Active)->count();
        $rowCountPost = Post::count();
        $rowCountBMI = BMI::where('status', ActiveStatus::Active)->count();
        $rowCountExpected = Expected::where('status', ActiveStatus::Active)->count();
        $rowCountProduct = Product::where('status', ActiveStatus::Active)->count();
        $rowCountQuestion = Question::where('question_type', QuestionType::IQ)->count() + Question::where('question_type', QuestionType::AQ)->count() +Question::where('question_type', QuestionType::EQ)->count();
        $rowCountQuiz = Quiz::count();
        $rowCountUser = User::where('status', UserStatus::Active)->count();
        $rowCountVaccinationSchedule = VaccinationSchedule::count();
        $rowCountChildren = Child::where('status', ActiveStatus::Active)->count();
        $rowCountEducation = Quality::where('status', ActiveStatus::Active)->count() + SchoolClass::where('status', ActiveStatus::Active)->count() + Capability::where('status', ActiveStatus::Active)->count() + Subject::where('status', ActiveStatus::Active)->count();
        $rowCountClinic = Clinic::where('status', ActiveStatus::Active)->count();
        $rowCountRole = Role::count();
        $rowCountSupport = Support::where('status', ActiveStatus::Active)->count();
        $rowCountAdmin = Admin::count();

        return view($this->view['index'],[
            'rowCountTransaction' => $rowCountTransaction,
            'rowCountGPA' => $rowCountGPA,
            'rowCountGuide' => $rowCountGuide,
            'rowCountJournal' => $rowCountJournal,
            'rowCountPregnancy' => $rowCountPregnancy,
            'rowCountNotification' => $rowCountNotification,
            'rowCountSlider' => $rowCountSlider,
            'rowCountPackage' => $rowCountPackage,
            'rowCountExercise' => $rowCountExercise,
            'rowCountPost' => $rowCountPost,
            'rowCountBMI' => $rowCountBMI,
            'rowCountExpected' => $rowCountExpected,
            'rowCountProduct' => $rowCountProduct,
            'rowCountQuestion' => $rowCountQuestion,
            'rowCountQuiz' => $rowCountQuiz,
            'rowCountUser' => $rowCountUser,
            'rowCountVaccinationSchedule' => $rowCountVaccinationSchedule,
            'rowCountChildren' => $rowCountChildren,
            'rowCountEducation' => $rowCountEducation,
            'rowCountClinic' => $rowCountClinic,
            'rowCountRole' => $rowCountRole,
            'rowCountSupport' => $rowCountSupport,
            'rowCountAdmin' => $rowCountAdmin,
            ]);

    }

}
