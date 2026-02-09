<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SmsGateway;
use App\Models\SmsHistory;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SmsServiceController extends Controller
{
    public function viewSmsTemplates(Request $request){
        if ($request->ajax()) {

            $data = SmsTemplate::orderBy('id', 'desc')->get();

            return Datatables::of($data)
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d h:i:s a", strtotime($data->created_at));
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="javascript:void(0)" data-id="'.$data->id.'" class="mb-1 btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('backend.sms.sms_template');
    }

    public function createSmsTemplate(){
        return view('backend.sms.create_template');
    }

    public function saveSmsTemplate(Request $request){
        SmsTemplate::insert([
            'title' => $request->title,
            'description' => $request->description,
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Template has save', 'Successfully');
        return redirect('view/sms/templates');
    }

    public function deleteSmsTemplate($id){
        SmsTemplate::where('id', $id)->delete();
        return response()->json(['success' => 'Tempalte Deleted Successfully.']);
    }

    public function getTemplateDescription(Request $request){
        $data = SmsTemplate::where('id', $request->template_id)->first();
        return response()->json($data);
    }

    public function getSmsTemplateInfo($id){
        $data = SmsTemplate::where('id', $id)->first();
        return response()->json($data);
    }

    public function updateSmsTemplate(Request $request){
        SmsTemplate::where('id', $request->template_id)->update([
            'title' => $request->template_title,
            'description' => $request->template_description,
            'updated_at' => Carbon::now()
        ]);
        return response()->json(['success' => 'Tempalte Updated Successfully.']);
    }

    public function sendSmsPage(){
        $data = array();
        $index = 0;
        $customers = DB::table('users')->select('name', 'phone')->where('phone', '!=', '')->whereNotNull('phone')->whereRaw('LENGTH(phone) >= 11')->get();
        foreach($customers as $customer){
            $contactNo = trim(str_replace(" ", '', $customer->phone));
            $contactNo = str_replace("+", '', $contactNo);
            $contactNo = str_replace("-", '', $contactNo);

            if (strpos($contactNo, "@") === false && strpos($contactNo, ".") === false) {
                $regex = '/^880/';
                if(!preg_match($regex, $contactNo)) {
                    $contactNo = "88".$contactNo;
                }
                $data[$index]['name'] = $customer->name;
                $data[$index]['contact'] = $contactNo;
                $index++;
            }
        }
        return view('backend.sms.send_sms', compact('data'));
    }

    public function sendSms(Request $request){

        $template_id = $request->template_id;
        if($template_id){
            $template_title = SmsTemplate::where('id', $request->template_id)->first() ?  SmsTemplate::where('id', $request->template_id)->first()->title : '';
        }
        $template_description = $request->template_description;

        $sending_type = $request->sending_type;
        $individual_contacts = array();
        $individual_contacts = $request->individual_contact;

        $sms_receivers = $request->sms_receivers;
        $min_order = $request->min_order ? $request->min_order : 0;
        $max_order = $request->max_order ? $request->max_order : 0;
        $min_order_value = $request->min_order_value ? $request->min_order_value : 0;
        $max_order_value = $request->max_order_value ? $request->max_order_value : 0;
        $data = array();

        if($sending_type == 1){ //individual sms

            if($individual_contacts && count($individual_contacts) >= 1){

                $smsGateway = SmsGateway::where('status', 1)->first();
                if(!$smsGateway){
                    Toastr::error('No SMS Api is Active', 'Failed');
                    return back();
                }

                foreach($individual_contacts as $individual_contact){
                    if($smsGateway && $smsGateway->provider_name == 'Reve'){
                        // $response = Http::get($smsGateway->api_endpoint, [
                        //     'apikey' => $smsGateway->api_key,
                        //     'secretkey' => $smsGateway->secret_key,
                        //     "callerID" => $smsGateway->sender_id,
                        //     "toUser" => $individual_contact,
                        //     "messageContent" => $template_description
                        // ]);
                    } elseif($smsGateway && $smsGateway->provider_name == 'ElitBuzz'){
                        // $response = Http::get($smsGateway->api_endpoint, [
                        //     'api_key' => $smsGateway->api_key,
                        //     "type" => "text",
                        //     "contacts" => $individual_contact, //“88017xxxxxxxx,88018xxxxxxxx”
                        //     "senderid" => $smsGateway->sender_id,
                        //     "msg" => $template_description
                        // ]);
                    } else {
                        Toastr::error('No SMS Api is Active', 'Failed');
                        return back();
                    }

                    // if($response->status() == 200){
                        SmsHistory::insert([
                            'template_id' => $template_id,
                            'template_title' => $template_title,
                            'template_description' => $template_description,
                            'sending_type' => $sending_type,
                            'individual_contact' => $individual_contact,
                            'sms_receivers' => $sms_receivers,
                            'min_order' => $min_order,
                            'max_order' => $max_order,
                            'min_order_value' => $min_order_value,
                            'max_order_value' => $max_order_value,
                            'created_at' => Carbon::now()
                        ]);
                    // }
                }
            }

            Toastr::success('Sms has sent', 'Successfully');
            return back();

        } else {

            $index = 0;
            $customers = DB::table('users')->where('phone', '!=', '')->whereNotNull('phone')->whereRaw('LENGTH(phone) >= 11')->groupBy('phone')->get();
            foreach($customers as $customer){

                $contactNo = trim(str_replace(" ", '', $customer->phone));
                $contactNo = str_replace("+", '', $contactNo);
                $contactNo = str_replace("-", '', $contactNo);

                if (strpos($contactNo, "@") === false && strpos($contactNo, ".") === false) {
                    $regex = '/^880/';
                    if(!preg_match($regex, $contactNo)) {
                        $contactNo = "88".$contactNo;
                    }

                    if($sms_receivers == 1){ //having no order
                        if(!Order::where('user_id', $customer->id)->exists()){
                            $data[$index]['user_id'] = $customer->id;
                            $data[$index]['contact'] = $contactNo;
                            $index++;
                        }
                    } else {

                        if(Order::where('user_id', $customer->id)->exists()){
                            $totalOrderCount = Order::where('user_id', $customer->id)->count();
                            $totalOrderValue = Order::where('user_id', $customer->id)->sum('total');

                            if($min_order > 0 && $totalOrderCount < $min_order){
                                continue;
                                // skipping the loop as it failes to meet the criteria
                            }
                            if($max_order > 0 && $totalOrderCount > $max_order){
                                continue;
                                // skipping the loop as it failes to meet the criteria
                            }
                            if($min_order_value > 0 && $min_order_value < $totalOrderValue){
                                continue;
                                // skipping the loop as it failes to meet the criteria
                            }
                            if($max_order_value > 0 && $min_order_value > $totalOrderValue){
                                continue;
                                // skipping the loop as it failes to meet the criteria
                            }

                            $data[$index]['user_id'] = $customer->id;
                            $data[$index]['contact'] = $contactNo;
                            $index++;
                        }
                    }
                }
            }

            foreach($data as $user){

                $smsGateway = SmsGateway::where('status', 1)->first();
                if($smsGateway && $smsGateway->provider_name == 'Reve'){
                    // $response = Http::get($smsGateway->api_endpoint, [
                    //     'apikey' => $smsGateway->api_key,
                    //     'secretkey' => $smsGateway->secret_key,
                    //     "callerID" => $smsGateway->sender_id,
                    //     "toUser" => $individual_contact,
                    //     "messageContent" => $template_description
                    // ]);
                } elseif($smsGateway && $smsGateway->provider_name == 'ElitBuzz'){
                    // $response = Http::get($smsGateway->api_endpoint, [
                    //     'api_key' => $smsGateway->api_key,
                    //     "type" => "text",
                    //     "contacts" => $individual_contact, //“88017xxxxxxxx,88018xxxxxxxx”
                    //     "senderid" => $smsGateway->sender_id,
                    //     "msg" => $template_description
                    // ]);
                } else {
                    Toastr::error('No SMS Api is Active', 'Failed');
                    return back();
                }

                // if($response->status() == 200){
                    SmsHistory::insert([
                        'template_id' => $template_id,
                        'template_title' => $template_title,
                        'template_description' => $template_description,
                        'sending_type' => $sending_type,
                        'individual_contact' => $user['contact'],
                        'sms_receivers' => $sms_receivers,
                        'min_order' => $min_order,
                        'max_order' => $max_order,
                        'min_order_value' => $min_order_value,
                        'max_order_value' => $max_order_value,
                        'created_at' => Carbon::now()
                    ]);
                // }
            }

        }

        Toastr::success('Sms has sent', 'Successfully');
        return back();
    }

    public function viewSmsHistory(Request $request){
        if ($request->ajax()) {

            $data = SmsHistory::orderBy('id', 'desc')->get();

            return Datatables::of($data)
                    ->editColumn('sending_type', function($data) {
                        if($data->sending_type == 1)
                            return "Individual";
                        else
                            return "Everyone";
                    })
                    ->editColumn('sms_receivers', function($data) {
                        if($data->sms_receivers == 1)
                            return "No Order";
                        elseif($data->sms_receivers == 2)
                            return "Have Order";
                        else
                            return "";
                    })
                    ->editColumn('min_order', function($data) {
                        if($data->min_order > 0)
                            return "<b>Min:</b> ".$data->min_order. ($data->max_order > 0 ? "<b>; Max:</b> ".$data->max_order : '');
                        if($data->min_order <= 0 && $data->max_order > 0)
                            return " <b>Max:</b> ".$data->max_order;
                    })
                    ->editColumn('min_order_value', function($data) {
                        if($data->min_order_value > 0)
                            return "<b>Min:</b> ".$data->min_order_value. ($data->max_order_value > 0 ? "<b>; Max:</b> ".$data->max_order_value : '');
                        if($data->min_order_value <= 0 && $data->max_order_value > 0)
                            return " <b>Max:</b> ".$data->max_order_value;
                    })
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d h:i:s a", strtotime($data->created_at));
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status', 'min_order', 'min_order_value'])
                    ->make(true);
        }

        return view('backend.sms.sms_history');
    }

    public function deleteSmsHistoryRange(){
        $currentDate = date("Y-m-d H:i:s");
        $prevDate = date('Y-m-d', strtotime('-15 day', strtotime($currentDate)));
        SmsHistory::where('created_at', '<=', $prevDate)->delete();
        Toastr::error('SMS Histories are Deleted', 'Successful');
        return back();
    }

    public function deleteSmsHistory($id){
        SmsHistory::where('id', $id)->delete();
        return response()->json(['success' => 'SMS History has Deleted Successfully.']);
    }
}
