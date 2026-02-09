<?php

namespace App\Http\Controllers;

use App\Models\EmailConfigure;
use App\Models\EmailTemplate;
use App\Models\PaymentGateway;
use App\Models\SmsGateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use DataTables;

class SystemController extends Controller
{
    public function viewEmailCredentials(Request $request)
    {
        if ($request->ajax()) {

            $data = EmailConfigure::orderBy('id', 'desc')->get();

            return Datatables::of($data)
                ->editColumn('status', function ($data) {
                    if ($data->status == 0) {
                        return '<button class="btn btn-sm btn-danger rounded">Inactive</button>';
                    } else {
                        return '<button class="btn btn-sm btn-success rounded">Active</button>';
                    }
                })
                ->editColumn('encryption', function ($data) {
                    if ($data->encryption == 0) {
                        return 'None';
                    } elseif ($data->encryption == 1) {
                        return 'TLS';
                    } else {
                        return 'SSL';
                    }
                })
                // ->editColumn('password', function($data) {

                //     $ciphering = "AES-128-CTR";
                //     $options = 0;

                //     $decryption_iv = '1234567891011121';
                //     $decryption_key = "GenericCommerceV1";
                //     return $decryption = openssl_decrypt ($data->password, $ciphering, $decryption_key, $options, $decryption_iv);
                // })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Edit" class="mb-1 btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('backend.system.email_config');
    }

    public function viewEmailTemplates()
    {
        $orderPlacedTemplates = EmailTemplate::where('type', 'order_placed')->orderBy('serial', 'asc')->get();
        return view('backend.system.email_template', compact('orderPlacedTemplates'));
    }

    public function changeMailTemplateStatus($templateId)
    {
        $template = EmailTemplate::where('id', $templateId)->first();
        if ($template && $template->status == 1) {
            $template->status = 0;
            $template->save();
            EmailTemplate::where('type', $template->type)->where('id', '!=', $template->id)->update(['status' => 1]);
        } else {
            $template->status = 1;
            $template->save();
            EmailTemplate::where('type', $template->type)->where('id', '!=', $template->id)->update(['status' => 0]);
        }
        return response()->json(['success' => 'Saved successfully.']);
    }

    public function saveEmailCredential(Request $request)
    {

        DB::table('email_configures')->update([
            'status' => 0
        ]);

        // $simple_string = $request->password;
        // $ciphering = "AES-128-CTR";
        // $options = 0;

        // $encryption_iv = '1234567891011121';
        // $encryption_key = "GenericCommerceV1";
        // $encryption = openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv);

        // $decryption_iv = '1234567891011121';
        // $decryption_key = "GenericCommerceV1";
        // $decryption=openssl_decrypt ($encryption, $ciphering, $decryption_key, $options, $decryption_iv);

        EmailConfigure::insert([
            'host' => $request->host,
            'port' => $request->port,
            'email' => $request->email,
            'password' => $request->password,
            // 'password' => $encryption,
            'mail_from_name' => $request->mail_from_name,
            'mail_from_email' => $request->mail_from_email,
            'encryption' => $request->encryption,
            'status' => 1,
            'slug' => time() . str::random(5),
            'created_at' => Carbon::now()
        ]);

        return response()->json(['success' => 'Saved successfully.']);
    }

    public function deleteEmailCredential($slug)
    {
        EmailConfigure::where('slug', $slug)->delete();
        return response()->json(['success' => 'Deleted Successfully.']);
    }

    public function getEmailCredentialInfo($slug)
    {
        $data = EmailConfigure::where('slug', $slug)->first();
        return response()->json($data);
    }

    public function updateEmailCredentialInfo(Request $request)
    {

        if ($request->status == 1) {
            DB::table('email_configures')->update([
                'status' => 0,
            ]);
        }

        EmailConfigure::where('slug', $request->email_config_slug)->update([
            'host' => $request->host,
            'port' => $request->port,
            'email' => $request->email,
            'mail_from_name' => $request->mail_from_name,
            'mail_from_email' => $request->mail_from_email,
            'encryption' => $request->encryption,
            'status' => $request->status,
            'updated_at' => Carbon::now()
        ]);

        return response()->json(['success' => 'Updated Successfully.']);
    }

    public function viewSmsGateways()
    {
        $gateways = SmsGateway::orderBy('id', 'asc')->get();
        return view('backend.system.sms_gateway', compact('gateways'));
    }

    public function updateSmsGatewayInfo(Request $request)
    {
        $provider = $request->provider;

        DB::table('sms_gateways')->update([
            'status' => 0,
            'updated_at' => Carbon::now()
        ]);

        if ($provider == 'elitbuzz') { //ID 1 => Elitbuzz
            SmsGateway::where('id', 1)->update([
                'api_endpoint' => $request->api_endpoint,
                'api_key' => $request->api_key,
                'sender_id' => $request->sender_id,
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);
        }

        if ($provider == 'revesms') { //ID 2 => Revesms
            SmsGateway::where('id', 2)->update([
                'api_endpoint' => $request->api_endpoint,
                'api_key' => $request->api_key,
                'secret_key' => $request->secret_key,
                'sender_id' => $request->sender_id,
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);
        }

        Toastr::success('Info Updated', 'Success');
        return back();
    }

    public function changeGatewayStatus($provider)
    {

        DB::table('sms_gateways')->update([
            'status' => 0,
            'updated_at' => Carbon::now()
        ]);

        if ($provider == 'elitbuzz') { //ID 1 => Elitbuzz
            SmsGateway::where('id', 1)->update([
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);
        }

        if ($provider == 'revesms') { //ID 2 => Revesms
            SmsGateway::where('id', 2)->update([
                'status' => 1,
                'updated_at' => Carbon::now()
            ]);
        }

        return response()->json(['success' => 'Updated Successfully.']);
    }


    // payment gateway
    public function viewPaymentGateways()
    {
        $gateways = PaymentGateway::orderBy('id', 'asc')->get();
        return view('backend.system.payment_gateway', compact('gateways'));
    }

    public function updatePaymentGatewayInfo(Request $request)
    {
        $provider = $request->provider_name;

        if ($provider == 'ssl_commerz') {
            PaymentGateway::where('id', 1)->update([
                'api_key' => $request->api_key,
                'secret_key' => $request->secret_key,
                'username' => $request->username,
                'password' => $request->password,
                'live' => $request->live == '' ? 0 : $request->live,
                'status' => $request->status,
                'updated_at' => Carbon::now()
            ]);
        }

        if ($provider == 'stripe') {
            PaymentGateway::where('id', 2)->update([
                'api_key' => $request->api_key,
                'secret_key' => $request->secret_key,
                'username' => $request->username,
                'password' => $request->password,
                'live' => $request->live == '' ? 0 : $request->live,
                'status' => $request->status,
                'updated_at' => Carbon::now()
            ]);
        }

        if ($provider == 'bkash') {
            PaymentGateway::where('id', 3)->update([
                'api_key' => $request->api_key,
                'secret_key' => $request->secret_key,
                'username' => $request->username,
                'password' => $request->password,
                'live' => $request->live == '' ? 0 : $request->live,
                'status' => $request->status,
                'updated_at' => Carbon::now()
            ]);
        }

        if ($provider == 'amar_pay') {
            PaymentGateway::where('id', 4)->update([
                'api_key' => $request->api_key,
                'secret_key' => $request->secret_key,
                'username' => $request->username,
                'password' => $request->password,
                'live' => $request->live == '' ? 0 : $request->live,
                'status' => $request->status,
                'updated_at' => Carbon::now()
            ]);
        }

        Toastr::success('Payment Gateway Info Updated', 'Updated Successfully');
        return back();
    }

    public function changePaymentGatewayStatus($provider)
    {

        if ($provider == 'ssl_commerz') { //ID 1 => ssl_commerz
            $info = PaymentGateway::where('id', 1)->first();

            PaymentGateway::where('id', 1)->update([
                'status' => $info->status == 1 ? 0 : 1,
                'updated_at' => Carbon::now()
            ]);
        }

        if ($provider == 'stripe') { //ID 2 => stripe
            $info = PaymentGateway::where('id', 2)->first();

            PaymentGateway::where('id', 2)->update([
                'status' => $info->status == 1 ? 0 : 1,
                'updated_at' => Carbon::now()
            ]);
        }

        if ($provider == 'bkash') { //ID 3 => bkash
            $info = PaymentGateway::where('id', 3)->first();

            PaymentGateway::where('id', 3)->update([
                'status' => $info->status == 1 ? 0 : 1,
                'updated_at' => Carbon::now()
            ]);
        }

        if ($provider == 'amar_pay') { //ID 4 => amar_pay
            $info = PaymentGateway::where('id', 4)->first();

            PaymentGateway::where('id', 4)->update([
                'status' => $info->status == 1 ? 0 : 1,
                'updated_at' => Carbon::now()
            ]);
        }

        return response()->json(['success' => 'Updated Successfully.']);
    }
}
