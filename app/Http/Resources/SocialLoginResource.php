<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SocialLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'fb_app_id' => $this->fb_login_status == 1 ? $this->fb_app_id : null,
            'fb_app_secret' => $this->fb_login_status == 1 ? $this->fb_app_secret : null,
            'fb_redirect_url' => $this->fb_login_status == 1 ? $this->fb_redirect_url : null,

            'gmail_client_id' => $this->gmail_login_status == 1 ? $this->gmail_client_id : null,
            'gmail_secret_id' => $this->gmail_login_status == 1 ? $this->gmail_secret_id : null,
            'gmail_redirect_url' => $this->gmail_login_status == 1 ? $this->gmail_redirect_url : null,
        ];
    }
}
