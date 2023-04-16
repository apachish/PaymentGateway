<?php

namespace Armanbroker\Auth\App\Http\Requests;

use App\Http\Requests\JsonRequest;
use App\Rules\Codemeli;
use Balea\Auth\Models\Profile;
use Illuminate\Validation\Rule;

class AcceptRule extends JsonRequest
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
            "rule" => 'required|boolean',
        ];

    }
}
