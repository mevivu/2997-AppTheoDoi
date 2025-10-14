<?php

namespace App\Admin\Http\Requests\User;

use App\Admin\Http\Requests\BaseRequest;
use App\Admin\Rules\EmailUnique;
use App\Admin\Rules\PhoneUnique;
use App\Enums\User\Gender;
use App\Enums\User\UserStatus;
use Illuminate\Validation\Rules\Enum;

class UserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [

            'fullname' => ['required', 'string'],
            'phone' => [
                'required',
                new PhoneUnique(),
            ],
            'email' => [
                'required',
                'email',
                new EmailUnique(),
            ],
            'password' => ['required', 'string', 'confirmed'],
            'address' => ['nullable'],
            'name' => ['nullable', 'string'],
            'gender' => ['required', new Enum(Gender::class)],
            'lng' => ['nullable'],
            'lat' => ['nullable'],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'avatar' => ['nullable'],
            'father_name' => ['nullable', 'string'],
            'father_height' => ['nullable', 'integer', 'min:0'],
            'father_birthday' => ['nullable', 'date_format:Y-m-d'],
            'mother_name' => ['nullable', 'string'],
            'mother_height' => ['nullable', 'integer', 'min:0'],
            'mother_birthday' => ['nullable', 'date_format:Y-m-d'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\User,id'],
            'fullname' => ['required', 'string'],
            'email' => [
                'required',
                'email',
                new EmailUnique($this->id),
            ],
            'phone' => [
                'required',
                new PhoneUnique($this->id),
            ],
            'password' => ['nullable', 'string', 'confirmed'],
            'gender' => ['nullable', new Enum(Gender::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'avatar' => ['nullable'],
            'status' => ['required', new Enum(UserStatus::class)],
            'lng' => ['nullable'],
            'lat' => ['nullable'],
            'address' => ['nullable'],
            'father_name' => ['nullable', 'string'],
            'father_height' => ['nullable', 'integer', 'min:0'],
            'father_birthday' => ['nullable', 'date_format:Y-m-d'],
            'mother_name' => ['nullable', 'string'],
            'mother_height' => ['nullable', 'integer', 'min:0'],
            'mother_birthday' => ['nullable', 'date_format:Y-m-d'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required' => 'Họ và tên là bắt buộc.',
            'fullname.string' => 'Họ và tên phải là chuỗi ký tự.',

            'phone.required' => 'Số điện thoại là bắt buộc.',

            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email phải có định dạng hợp lệ.',

            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',

            'gender.required' => 'Giới tính là bắt buộc.',

            'birthday.date_format' => 'Ngày sinh phải có định dạng YYYY-MM-DD.',

            'father_height.integer' => 'Chiều cao bố phải là số nguyên.',
            'father_height.min' => 'Chiều cao bố phải lớn hơn hoặc bằng 0.',

            'father_birthday.date_format' => 'Ngày sinh bố phải có định dạng YYYY-MM-DD.',

            'mother_height.integer' => 'Chiều cao mẹ phải là số nguyên.',
            'mother_height.min' => 'Chiều cao mẹ phải lớn hơn hoặc bằng 0.',

            'mother_birthday.date_format' => 'Ngày sinh mẹ phải có định dạng YYYY-MM-DD.',

            'package_id.exists' => 'Gói dịch vụ được chọn không tồn tại.',

            'start_date.date_format' => 'Ngày bắt đầu phải có định dạng YYYY-MM-DD.',

            'end_date.date_format' => 'Ngày kết thúc phải có định dạng YYYY-MM-DD.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ];
    }
}
