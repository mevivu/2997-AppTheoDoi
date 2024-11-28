<?php

namespace App\Api\V1\Http\Requests\AUth;
use App\Api\V1\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Exception;

class UpdateEmailRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws Exception
     */

     public function rules(): array
     {   
         return [
             'email' => [
                 'required',
                 'email',
                 Rule::unique('users', 'email')->ignore($this->user()->id)
             ],
         ];
     }
     
}
