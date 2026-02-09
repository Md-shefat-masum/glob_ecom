<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralInfoResource extends JsonResource
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
            'logo' => $this->logo,
            'logo_dark' => $this->logo_dark,
            'fav_icon' => $this->fav_icon,
            'tab_title' => $this->tab_title,
            'company_name' => $this->company_name,
            'short_description' => $this->short_description,
            'contact' => $this->contact,
            'email' => $this->email,
            'address' => $this->address,
            'google_map_link' => $this->google_map_link,
            'play_store_link' => $this->play_store_link,
            'app_store_link' => $this->app_store_link,
            'footer_copyright_text' => $this->footer_copyright_text,
            'payment_banner' => $this->payment_banner,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'tertiary_color' => $this->tertiary_color,
            'title_color' => $this->title_color,
            'paragraph_color' => $this->paragraph_color,
            'border_color' => $this->border_color,
            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'custom_css' => $this->custom_css,
            'custom_js' => $this->custom_js,
            'header_script' => $this->header_script,
            'footer_script' => $this->footer_script,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'twitter' => $this->twitter,
            'linkedin' => $this->linkedin,
            'youtube' => $this->youtube,
            'messenger' => $this->messenger,
            'whatsapp' => $this->whatsapp,
            'telegram' => $this->telegram,
            'tiktok' => $this->tiktok,
            'pinterest' => $this->pinterest,
            'viber' => $this->viber,
            'google_analytic_tracking_id' => $this->google_analytic_status == 1 ? $this->google_analytic_tracking_id : null,
            'fb_pixel_app_id' => $this->fb_pixel_status == 1 ? $this->fb_pixel_app_id : null,
            'tawk_chat_status' => $this->tawk_chat_status,
            'tawk_chat_link' => $this->tawk_chat_link,
            'crisp_chat_status' => $this->crisp_chat_status,
            'crisp_website_id' => $this->crisp_website_id,
        ];

    }
}
