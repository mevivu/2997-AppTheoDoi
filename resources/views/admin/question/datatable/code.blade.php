@php use App\Enums\Question\QuestionType; @endphp
@if ($question_type == QuestionType::IQ->value)
    <x-link :href="route('admin.question.editIq', $id)" :title="$code"/>
@elseif($question_type == QuestionType::AQ->value || $question_type == QuestionType::EQ->value)
    <x-link :href="route('admin.question.editEqAq', $id)" :title="$code"/>
@endif

<i class="ti ti-copy copy-btn"  style="font-size: 18px; cursor: pointer;" data-value="{{ $code }}"></i>
<i class="ti ti-check check-icon" style="display:none;font-size: 18px"></i>
