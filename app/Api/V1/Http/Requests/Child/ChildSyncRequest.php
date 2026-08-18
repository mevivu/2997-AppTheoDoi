<?php

namespace App\Api\V1\Http\Requests\Child;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Child\BornStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;

class ChildSyncRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        $rules = [
            'children' => 'required|array',
            'children.*.id' => 'required',
            'children.*.fullname' => 'required|string|max:255',
            'children.*.gender' => ['required', new Enum(Gender::class)],
            'children.*.is_born' => ['required', new Enum(BornStatus::class)],
            'children.*.avatar' => 'nullable',
        ];

        $children = $this->input('children', []);
        if (is_array($children)) {
            foreach ($children as $index => $child) {
                $isBorn = $child['is_born'] ?? null;
                $birthdayRules = ['nullable', 'date'];

                if ($isBorn === BornStatus::Born->value) {
                    $birthdayRules[] = 'before_or_equal:today';
                } elseif ($isBorn === BornStatus::Unborn->value) {
                    $birthdayRules[] = 'after_or_equal:today';
                }

                $rules["children.{$index}.birthday"] = $birthdayRules;
            }
        } else {
            $rules['children.*.birthday'] = 'nullable|date';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'children.*.birthday.before_or_equal' => __('Ngày sinh không được lớn hơn ngày hiện tại.'),
            'children.*.birthday.after_or_equal' => __('Ngày dự sinh không được nhỏ hơn ngày hiện tại.'),
        ];
    }
}
