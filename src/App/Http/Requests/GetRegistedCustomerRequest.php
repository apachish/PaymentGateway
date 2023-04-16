<?php

namespace Armanbroker\Auth\App\Http\Requests;



use App\Http\Requests\JsonRequest;
use App\Rules\Codemeli;
use Armanbroker\Auth\Services\UserService;
use Illuminate\Validation\Rule;

class GetRegistedCustomerRequest extends JsonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() ? auth()->user()->hasPermissionTo('getRegistedCustomer') : false;
    }

    protected function prepareForValidation()
    {
       if($this->national_code) $this->merge(['national_code' => convertNumber($this->national_code)]);
       if($this->mobile) $this->merge(['national_code' => convertNumber($this->mobile)]);
       if($this->updated_at_from) $this->merge(['updated_at_from' =>  toGregorian($this->updated_at_from, "Y/m/d")]);
       if($this->updated_at_to) $this->merge(['updated_at_to' =>  toGregorian($this->updated_at_to, "Y/m/d")]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255|min:2',
            'family' => 'nullable|string|max:255|min:2',
            'national_code' => ['nullable', 'numbric', 'digits_between:2,11'],
            'mobile' => 'nullable|numbric|digits_between:2,10',
            'score' => 'nullable|numbric|digits_between:1,15',
            'tbs' => [Rule::in(["true","false"]),'nullable'],
            'updated_at' => 'date|date_format:Y/m/d|before:tomorrow',
            'status_customer' => ['nullable',Rule::in(["0","1","2","3","4","5","6"])],
            'reagent' => 'nullable|string|max:255|min:2',
            'branch' => 'nullable|exists:works,id',
        ];
    }
}
