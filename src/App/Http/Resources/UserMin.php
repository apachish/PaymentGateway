<?php

namespace Apachish\Auth\App\Http\Resources;

use Armanbroker\Auth\Services\UserService;
use Illuminate\Http\Resources\Json\JsonResource;
use Jenssegers\Optimus\Optimus;

class UserMin extends JsonResource
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
            "name" => $this->name,
            "lastname" => $this->lastname,
            "national_code" => $this->national_code,
            "mobile" => $this->mobile,
            'updated_at' => toJalali($this->updated_at,"%Y %B %d"),
            'created_at' => toJalali($this->created_at),

        ];
    }
}
