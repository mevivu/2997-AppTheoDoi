<?php

namespace App\Admin\Http\Requests\Classes;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\ActiveStatus;
use App\Enums\Class\EducationLevel;
use App\Enums\Class\LevelGroup;
use App\Enums\ReportCard\EvaluationMethod;
use App\Models\SchoolClass;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class ClassesRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string'],
            'subject_id' => ['required', 'array'],
            'subject_id.*' => 'required|exists:subjects,id',
            'status' => ['required', new Enum(ActiveStatus::class)],
            'level_group' => ['nullable', new Enum(LevelGroup::class)],
            'education_level' => ['required', new Enum(EducationLevel::class)],
            ...$this->subjectConfigRules(),
        ];
    }
    protected function methodPut(): array
    {
        return [
            'id'=>['required', 'integer', 'exists:App\Models\SchoolClass,id'],
            'name' => ['required', 'string'],
            'status' => ['required', new Enum(ActiveStatus::class)],
            'subject_id' => ['required', 'array'],
            'subject_id.*' => 'required|exists:subjects,id',
            'level_group' => ['nullable', new Enum(LevelGroup::class)],
            'education_level' => ['nullable', new Enum(EducationLevel::class)],
            ...$this->subjectConfigRules(),
        ];
    }

    private function subjectConfigRules(): array
    {
        return [
            'subject_config' => ['nullable', 'array'],
            'subject_config.*.evaluation_method' => ['nullable', new Enum(EvaluationMethod::class)],
            'subject_config.*.is_required' => ['nullable', 'boolean'],
            'subject_config.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    /**
     * Không cho phép phương thức đánh giá sai cấp học (vd: "Đạt/Chưa đạt" cho lớp tiểu học).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $level = $this->resolveEducationLevel();
            if (!$level) {
                return;
            }

            $allowed = array_map(fn (EvaluationMethod $m) => $m->value, EvaluationMethod::allowedFor($level));
            $selected = array_map('strval', (array) $this->input('subject_id', []));

            foreach ((array) $this->input('subject_config', []) as $subjectId => $cfg) {
                $method = $cfg['evaluation_method'] ?? null;
                if ($method === null || $method === '' || !in_array((string) $subjectId, $selected, true)) {
                    continue;
                }
                if (!in_array($method, $allowed, true)) {
                    $validator->errors()->add(
                        "subject_config.{$subjectId}.evaluation_method",
                        __('Cách đánh giá ":method" không áp dụng cho cấp :level.', [
                            'method' => EvaluationMethod::tryFrom($method)?->getTranslatedName() ?? $method,
                            'level' => $level->getTranslatedName(),
                        ])
                    );
                }
            }
        });
    }

    public function resolveEducationLevel(): ?EducationLevel
    {
        $input = $this->input('education_level');
        if ($input && ($level = EducationLevel::tryFrom($input))) {
            return $level;
        }

        $id = (int) $this->input('id');
        if ($id > 0) {
            return SchoolClass::find($id)?->resolvedEducationLevel();
        }

        return null;
    }
}
