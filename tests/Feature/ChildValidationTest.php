<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use App\Enums\Child\BornStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rules\Enum;
use App\Api\V1\Http\Requests\Child\ChildRequest;
use App\Api\V1\Http\Requests\Child\ChildSyncRequest;

class ChildValidationTest extends TestCase
{
    private function validate($data)
    {
        $request = new ChildRequest();
        $request->merge($data);

        $refMethod = new \ReflectionMethod(ChildRequest::class, 'methodPost');
        $refMethod->setAccessible(true);
        $rules = $refMethod->invoke($request);

        $messages = $request->messages();

        return Validator::make($data, $rules, $messages);
    }

    private function validateSync($data)
    {
        $request = new ChildSyncRequest();
        $request->merge($data);

        $refMethod = new \ReflectionMethod(ChildSyncRequest::class, 'methodPost');
        $refMethod->setAccessible(true);
        $rules = $refMethod->invoke($request);

        $messages = $request->messages();

        return Validator::make($data, $rules, $messages);
    }

    public function test_born_child_birthday_cannot_be_in_future()
    {
        $validator = $this->validate([
            'fullname' => 'John Doe',
            'gender' => Gender::Male->value,
            'is_born' => BornStatus::Born->value,
            'birthday' => '2030-01-01',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertEquals('Ngày sinh không được lớn hơn ngày hiện tại.', $validator->errors()->first('birthday'));
    }

    public function test_born_child_birthday_can_be_in_past()
    {
        $validator = $this->validate([
            'fullname' => 'John Doe',
            'gender' => Gender::Male->value,
            'is_born' => BornStatus::Born->value,
            'birthday' => '2020-01-01',
        ]);

        $this->assertTrue($validator->passes());
    }

    public function test_unborn_child_due_date_cannot_be_in_past()
    {
        $validator = $this->validate([
            'fullname' => 'Unborn Child',
            'gender' => Gender::Female->value,
            'is_born' => BornStatus::Unborn->value,
            'birthday' => '2020-01-01',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertEquals('Ngày dự sinh không được nhỏ hơn ngày hiện tại.', $validator->errors()->first('birthday'));
    }

    public function test_unborn_child_due_date_can_be_in_future()
    {
        $validator = $this->validate([
            'fullname' => 'Unborn Child',
            'gender' => Gender::Female->value,
            'is_born' => BornStatus::Unborn->value,
            'birthday' => '2030-01-01',
        ]);

        $this->assertTrue($validator->passes());
    }

    public function test_sync_validation_detects_invalid_birthdays()
    {
        $validator = $this->validateSync([
            'children' => [
                [
                    'id' => 1,
                    'fullname' => 'John Doe',
                    'gender' => Gender::Male->value,
                    'is_born' => BornStatus::Born->value,
                    'birthday' => '2030-01-01', // Future (invalid for born)
                ],
                [
                    'id' => 2,
                    'fullname' => 'Unborn Child',
                    'gender' => Gender::Female->value,
                    'is_born' => BornStatus::Unborn->value,
                    'birthday' => '2020-01-01', // Past (invalid for unborn)
                ],
            ]
        ]);

        $this->assertTrue($validator->fails());
        $this->assertEquals('Ngày sinh không được lớn hơn ngày hiện tại.', $validator->errors()->first('children.0.birthday'));
        $this->assertEquals('Ngày dự sinh không được nhỏ hơn ngày hiện tại.', $validator->errors()->first('children.1.birthday'));
    }

    public function test_sync_validation_passes_valid_payload()
    {
        $validator = $this->validateSync([
            'children' => [
                [
                    'id' => 1,
                    'fullname' => 'John Doe',
                    'gender' => Gender::Male->value,
                    'is_born' => BornStatus::Born->value,
                    'birthday' => '2020-01-01', // Past (valid)
                ],
                [
                    'id' => 2,
                    'fullname' => 'Unborn Child',
                    'gender' => Gender::Female->value,
                    'is_born' => BornStatus::Unborn->value,
                    'birthday' => '2030-01-01', // Future (valid)
                ],
            ]
        ]);

        $this->assertTrue($validator->passes());
    }
}
