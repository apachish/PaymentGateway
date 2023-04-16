<?php

namespace Apachish\Auth\App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Jenssegers\Optimus\Optimus;

class Device extends JsonResource
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
            "device" => $this->getType($this->type),
            "os" => $this->platform,
            "version_os" => $this->version_platform,
            "browser" => $this->browser,
            "last" => toAgo($this->updated_at)
        ];
    }

    protected function gettype($type){
        switch($type){
            case "mobile":
                return "موبایل";
            case "robot":
                    return "ربات";
            case "descktop":
                    return "کامپیوتر";
            default:
              return "ناشناخته";
        }
    }
}
