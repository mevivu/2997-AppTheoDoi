@php 
use App\Enums\Question\QuestionType; 
$isIq = request()->routeIs('admin.question.iq') 
    || (isset($question_type) && ($question_type == QuestionType::IQ || (is_object($question_type) && $question_type->value == 'iq') || $question_type == 'iq'));
@endphp
@if ($isIq)
    <x-link :href="route('admin.question.editIq', $id)" :title="$code"/>
@else
    <x-link :href="route('admin.question.editEqAq', $id)" :title="$code"/>
@endif

<i class="ti ti-copy copy-btn"  style="font-size: 18px; cursor: pointer;" data-value="{{ $code }}"></i>
<i class="ti ti-check check-icon" style="display:none;font-size: 18px"></i>
