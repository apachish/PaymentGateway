<?php

namespace Armanbroker\Auth\App\Http\Requests;

use App\Http\Requests\JsonRequest;

class VerificationConfirmRequest extends JsonRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|string|max:6',
        ];
    }
}
