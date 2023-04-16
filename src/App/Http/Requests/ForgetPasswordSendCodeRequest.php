<?php

namespace Armanbroker\Auth\App\Http\Requests;

use App\Http\Requests\JsonRequest;
use Illuminate\Validation\Rule;

class ForgetPasswordSendCodeRequest extends JsonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', Rule::in(['email', 'mobile'])],
            'email' => 'required_if:type,email|email',
            'mobile' => 'required_if:type,mobile|string|phone:flag',
            'flag' => 'required_with:mobile|string|max:2',
        ];
    }
}
