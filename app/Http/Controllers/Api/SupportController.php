<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportMessageResource;
use App\Http\Resources\SupportTicketResource;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    const AUTHORIZATION_TOKEN = 'GenericCommerceV1-SBW7583837NUDD82';
    public function submitSupportTicket(Request $request){

        $subject = $request->subject;
        $message = $request->message;
        $supportTakenBy = auth()->user()->id;
        $attachment = NULL;

        if ($request->hasFile('attachment')){
            $get_attachment = $request->file('attachment');
            $attachment_name = str::random(5) . time() . '.' . $get_attachment->getClientOriginalExtension();
            $location = public_path('support_ticket_attachments/');
            $get_attachment->move($location, $attachment_name);
            $attachment = "support_ticket_attachments/" . $attachment_name;
        }

        $insertedId = SupportTicket::insertGetId([
            'ticket_no' => time(). str::random(3). rand(100, 999),
            'support_taken_by' => $supportTakenBy,
            'subject' => $subject,
            'message' => $message,
            'attachment' => $attachment,
            'status' => 0,
            'slug' => time(). str::random(5),
            'created_at' => Carbon::now()
        ]);

        return response()->json([
            'success'=> true,
            'message'=> 'Support Ticket Submitted',
            'data' => new SupportTicketResource(SupportTicket::where('id', $insertedId)->first())
        ]);

    }

    public function sendSupportTicketMessage(Request $request){

        $attachment = NULL;
        if ($request->hasFile('attachment')){
            $get_attachment = $request->file('attachment');
            $attachment_name = str::random(5) . time() . '.' . $get_attachment->getClientOriginalExtension();
            $location = public_path('support_ticket_attachments/');
            $get_attachment->move($location, $attachment_name);
            $attachment = "support_ticket_attachments/" . $attachment_name;
        }

        SupportMessage::insert([
            'support_ticket_id' => $request->support_ticket_id,
            'sender_id' => auth()->user()->id,
            'sender_type' => 2, //customer
            'message' => $request->message,
            'attachment' => $attachment,
            'created_at' => Carbon::now()
        ]);

        return response()->json([
            'success'=> true,
            'message'=> 'Message Has Sent',
            'data' => new SupportTicketResource(SupportTicket::where('id', $request->support_ticket_id)->first())
        ]);
    }

    public function getAllSupportTickets(){
        $data = SupportTicket::where('support_taken_by', auth()->user()->id)->get();
        return response()->json([
            'success'=> true,
            'message'=> 'Message Has Sent',
            'data' => SupportTicketResource::collection($data)
        ]);
    }

    public function getAllSupportTicketMessages(Request $request){
        $data = SupportMessage::where('support_ticket_id', $request->support_ticket_id)->orderBy('id', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => SupportMessageResource::collection($data),
        ], 200);
    }

    public function uploadSupportTicketFile(Request $request){
        if ($request->hasFile('attachment')){
            $get_attachment = $request->file('attachment');
            $attachment_name = $get_attachment->getClientOriginalName();
            $location = public_path('support_ticket_attachments/');
            $get_attachment->move($location, $attachment_name);
        }
        return response()->json([
            'success' => true,
            'message' => 'File Uploaded Successfully',
        ], 200);
    }
}
