<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function pendingSupportTickets(Request $request){
        if ($request->ajax()) {

            $data = DB::table('support_tickets')
                        ->join('users', 'support_tickets.support_taken_by', '=', 'users.id')
                        ->select('support_tickets.*', 'users.name')
                        ->where('support_tickets.status', 0)
                        ->orWhere('support_tickets.status', 1)
                        ->orderBy('id', 'desc')
                        ->get();

            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 0){
                            return 'Pending';
                        } elseif($data->status == 1) {
                            return 'In Progress';
                        } elseif($data->status == 2) {
                            return 'Solved';
                        } elseif($data->status == 3) {
                            return 'Rejected';
                        } elseif($data->status == 4) {
                            return 'On Hold';
                        }else {
                            return '';
                        }
                    })
                    ->editColumn('attachment', function($data) {
                        if($data->attachment){
                            return "<a href=".url('/')."/".$data->attachment." stream target='_blank'>Download Attachment</a>";
                        } else {
                            return "No Attachment Found";
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('view/support/messages').'/'.$data->slug.'" title="Edit" class="mb-1 btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                        if($data->status == 0){
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="On Hold" data-id="'.$data->slug.'" data-original-title="On Hold" class="btn-sm btn-secondary rounded onHoldBtn"><i class="fa fa-pause"></i></a>';
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Reject" data-id="'.$data->slug.'" data-original-title="Reject" class="btn-sm btn-danger rounded rejectBtn"><i class="fa fa-thumbs-down"></i></a>';
                        }
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Approve For Next Level" data-id="'.$data->slug.'" data-original-title="Status" class="btn-sm btn-info rounded statusBtn"><i class="fas fa-check"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'attachment', 'status'])
                    ->make(true);
        }
        return view('backend.support_ticket.pending');
    }

    public function solvedSupportTickets(Request $request){
        if ($request->ajax()) {

            $data = DB::table('support_tickets')
                        ->join('users', 'support_tickets.support_taken_by', '=', 'users.id')
                        ->select('support_tickets.*', 'users.name')
                        ->where('support_tickets.status', 2)
                        ->orderBy('id', 'desc')
                        ->get();

            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 0){
                            return 'Pending';
                        } elseif($data->status == 1) {
                            return 'In Progress';
                        } elseif($data->status == 2) {
                            return 'Solved';
                        } elseif($data->status == 3) {
                            return 'Rejected';
                        } elseif($data->status == 4) {
                            return 'On Hold';
                        }else {
                            return '';
                        }
                    })
                    ->editColumn('attachment', function($data) {
                        if($data->attachment){
                            return "<a href=".url('/')."/".$data->attachment." stream target='_blank'>Download Attachment</a>";
                        } else {
                            return "No Attachment Found";
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('view/support/messages').'/'.$data->slug.'" class="mb-1 btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'attachment'])
                    ->make(true);
        }
        return view('backend.support_ticket.solved');
    }

    public function onHoldSupportTickets(Request $request){
        if ($request->ajax()) {

            $data = DB::table('support_tickets')
                        ->join('users', 'support_tickets.support_taken_by', '=', 'users.id')
                        ->select('support_tickets.*', 'users.name')
                        ->where('support_tickets.status', 4)
                        ->orderBy('id', 'desc')
                        ->get();

            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 0){
                            return 'Pending';
                        } elseif($data->status == 1) {
                            return 'In Progress';
                        } elseif($data->status == 2) {
                            return 'Solved';
                        } elseif($data->status == 3) {
                            return 'Rejected';
                        } elseif($data->status == 4) {
                            return 'On Hold';
                        }else {
                            return '';
                        }
                    })
                    ->editColumn('attachment', function($data) {
                        if($data->attachment){
                            return "<a href=".url('/')."/".$data->attachment." stream target='_blank'>Download Attachment</a>";
                        } else {
                            return "No Attachment Found";
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('view/support/messages').'/'.$data->slug.'" title="Edit" class="mb-1 btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="In Progress" data-id="'.$data->slug.'" data-original-title="In Progress" class="btn-sm btn-info rounded inProgressBtn"><i class="fas fa-check"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'attachment'])
                    ->make(true);
        }
        return view('backend.support_ticket.hold');
    }

    public function rejectedSupportTickets(Request $request){
        if ($request->ajax()) {

            $data = DB::table('support_tickets')
                        ->join('users', 'support_tickets.support_taken_by', '=', 'users.id')
                        ->select('support_tickets.*', 'users.name')
                        ->where('support_tickets.status', 3)
                        ->orderBy('id', 'desc')
                        ->get();

            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 0){
                            return 'Pending';
                        } elseif($data->status == 1) {
                            return 'In Progress';
                        } elseif($data->status == 2) {
                            return 'Solved';
                        } elseif($data->status == 3) {
                            return 'Rejected';
                        } elseif($data->status == 4) {
                            return 'On Hold';
                        }else {
                            return '';
                        }
                    })
                    ->editColumn('attachment', function($data) {
                        if($data->attachment){
                            return "<a href=".url('/')."/".$data->attachment." stream target='_blank'>Download Attachment</a>";
                        } else {
                            return "No Attachment Found";
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('view/support/messages').'/'.$data->slug.'" title="Edit" class="mb-1 btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'attachment'])
                    ->make(true);
        }
        return view('backend.support_ticket.rejected');
    }

    public function deleteSupportTicket($slug){
        $data = SupportTicket::where('slug', $slug)->first();
        if($data->attachment){
            if(file_exists(public_path($data->attachment))){
                unlink(public_path($data->attachment));
            }
        }
        SupportMessage::where('support_ticket_id', $data->id)->delete();
        $data->delete();
        return response()->json(['success' => 'Support deleted successfully.']);
    }

    public function changeStatusSupport($slug){
        $data = SupportTicket::where('slug', $slug)->first();
        $data->status = $data->status + 1;
        $data->save();
        return response()->json(['success' => 'Status Changed successfully.']);
    }

    public function changeStatusSupportOnHold($slug){
        $data = SupportTicket::where('slug', $slug)->first();
        $data->status = 4;
        $data->save();
        return response()->json(['success' => 'Status Changed successfully.']);
    }

    public function changeStatusSupportRejected($slug){
        $data = SupportTicket::where('slug', $slug)->first();
        $data->status = 3;
        $data->save();
        return response()->json(['success' => 'Status Changed successfully.']);
    }

    public function changeStatusSupportInProgress($slug){
        $data = SupportTicket::where('slug', $slug)->first();
        $data->status = 1;
        $data->save();
        return response()->json(['success' => 'Status Changed successfully.']);
    }

    public function viewSupportMessage($slug){
        $data = SupportTicket::where('slug', $slug)->first();
        $messages = SupportMessage::where('support_ticket_id', $data->id)->orderBy('id', 'asc')->get();
        return view('backend.support_ticket.messages', compact('data', 'messages'));
    }

    public function sendSupportMessage(Request $request){
        $request->validate([
            'support_ticket_id' => 'required',
            'message' => 'required',
        ]);

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
            'sender_id' => Auth::user()->id,
            'sender_type' => 1, //Support Agent
            'message' => $request->message,
            'attachment' => $attachment,
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Message has been Send', 'Success');
        return back();
    }
}
