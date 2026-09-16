@if(isset($questionGroups) && isset($question))
    <div class="d-inline-flex align-items-center gap-1 question-group-select-wrapper" style="min-width: 185px;">
        <select class="form-select form-select-sm select-change-question-group shadow-none {{ empty($question->question_group_id) ? 'border-warning text-warning-emphasis bg-warning-subtle' : 'border-primary-subtle text-dark' }}" 
                data-id="{{ $question->id }}"
                data-original="{{ $question->question_group_id ?? '' }}"
                title="Thay đổi nhóm câu hỏi trực tiếp"
                style="font-size: 12.5px; font-weight: 500; border-radius: 6px; cursor: pointer; padding: 4px 8px;">
            <option value="" {{ empty($question->question_group_id) ? 'selected' : '' }}>-- Chưa chọn nhóm --</option>
            @foreach($questionGroups as $id => $name)
                <option value="{{ $id }}" {{ $question->question_group_id == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        <span class="status-indicator" id="status-indicator-{{ $question->id }}" style="width: 18px; display: inline-flex; justify-content: center; align-items: center;"></span>
    </div>
@elseif($question_group)
    <x-link target="_blank" :href="route('admin.question-group.edit', $question_group->id)" :title="$question_group->name" />
@else
    <span class="badge bg-secondary-subtle text-secondary">N/A</span>
@endif
