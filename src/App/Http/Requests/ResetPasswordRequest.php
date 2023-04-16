<?php

namespace Armanbroker\Auth\App\Http\Requests;


use App\Http\Requests\JsonRequest;
use Jenssegers\Optimus\Optimus;

class ResetPasswordRequest extends JsonRequest
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

    protected function prepareForValidation()
    {
        if($this->refer) $this->merge(['refer' => app(Optimus::class)->decode($this->refer)]);

    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'refer' => 'required|exists:users,id',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ];
    }
}
