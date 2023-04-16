<?php

namespace Armanbroker\Auth\App\Http\Requests;



use App\Http\Requests\JsonRequest;
use App\Rules\Codemeli;

class RegisterRequest extends JsonRequest
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
            'name' => 'required|string|max:255|min:2',
            'family' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'required|string|unique:users|phone:flag',
            'flag' => 'required_with:mobile|string|max:2',
            'password' => 'required|string|min:8|same:confirmed',
            'national_code' => ['required', 'string', 'max:10', 'unique:users', new Codemeli()],
            'reagent_code' => ['nullable', 'string', 'exists:profile,uniq_code'],
        ];
    }
}
