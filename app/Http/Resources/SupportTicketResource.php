<?php

namespace App\Http\Resources;

use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResource extends JsonResource
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
            'id' => $this->id,
            'ticket_no' => $this->ticket_no,
            'support_taken_by_id' => $this->support_taken_by,
            'support_taken_by_name' => User::where('id', $this->support_taken_by)->first() ? User::where('id', $this->support_taken_by)->first()->name : '',
            'subject' => $this->subject,
            'message' => $this->message,
            'attachment' => $this->attachment,
            'status' => $this->status,
            'status_text' => $this->status == 0 ? "Pending" : ($this->status == 1 ? "In Progress" : ($this->status == 2 ? "Solved" : ($this->status == 3 ? "Rejected" : 'On Hold'))),
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'created_at_formatted' => date("h:i:s A, jS F Y", strtotime($this->created_at)),
            'chats' => SupportMessageResource::collection(SupportMessage::where('support_ticket_id', $this->id)->orderBy('id', 'asc')->get()),
        ];
    }
}
