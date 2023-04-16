<?php

namespace Armanbroker\Auth\App\Http\Requests;


use App\Http\Requests\JsonRequest;
use Jenssegers\Optimus\Optimus;

class ChangePasswordRequest extends JsonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    protected function prepareForValidation()
    {

    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password' => 'required|min:8',
            'password' => 'required|string|min:8|same:password_confirmation|different:old_password',
        ];
    }
}
