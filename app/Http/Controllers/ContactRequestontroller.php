<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use App\Mail\ContactRequestReply;
use Illuminate\Support\Facades\Mail;

class ContactRequestontroller extends Controller
{
    public function viewAllContactRequests(Request $request){
        if ($request->ajax()) {

            $data = ContactRequest::orderBy('id', 'desc')->get();

            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 0){
                            return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Chnage Status" class="btn btn-sm btn-warning changeStatus">Not Served</a>';
                        } else {
                            return '<a href="javascript:void(0)" class="btn btn-sm btn-success">Served</a>';
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('backend.contact_request');
    }

    public function deleteContactRequests($id){
        ContactRequest::where('id', $id)->delete();
        return response()->json(['success' => 'Deleted successfully.']);
    }

    public function changeRequestStatus($id, Request $request){
        // Update status
        ContactRequest::where('id', $id)->update([
            'status' => 1,
            'updated_at' => Carbon::now()
        ]);

        // Send email if email, subject, and message are present
        $email = $request->query('email');
        $subject = $request->query('subject');
        $message = $request->query('message');
        if ($email && $subject && $message) {
            Mail::to($email)->queue(new ContactRequestReply($subject, $message));
        }
        return response()->json(['success' => 'Changed successfully and email sent if provided.']);
    }
}
