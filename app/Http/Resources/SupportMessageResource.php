<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportMessageResource extends JsonResource
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
        $userInfo = User::where('id', $this->sender_id)->first();

        return [
            'id' => $this->id,
            'support_ticket_id' => $this->support_ticket_id,
            'sender_id' => $this->sender_id,
            'sender_name' => $userInfo ? $userInfo->name : '',
            'sender_type' => $this->sender_type,
            'sender_type_text' => $this->sender_type == 1 ? 'Support Agent' : 'Customer',
            'message' => $this->message,
            'attachment' => $this->attachment,
            'created_at' => $this->created_at,
            'created_at_formatted' => date("h:i:s A, jS F Y", strtotime($this->created_at)),
        ];
    }
}
