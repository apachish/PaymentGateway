<?php

namespace Apachish\Auth\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Jenssegers\Optimus\Optimus;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => app(Optimus::class)->encode($this->id),
            "fullName" => $this->getFullName(),
            "name" => $this->name,
            "lastname" => $this->lastname,
            "email" => $this->email,
            "national_code" => $this->national_code,
            "mobile" => $this->mobile,
            'mobile_verified_at'=>$this->mobile_verified_at,
            'rule_verified_at'=>$this->rule_verified_at,
            'score'=> $this->profile?$this->profile->score:0,
            'uniq_code'=> $this->profile?$this->profile->uniq_code:""
        ];
    }
}
