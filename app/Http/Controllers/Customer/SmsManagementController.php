<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Customer\Models\Customer;
use App\Models\User;

class SmsManagementController extends Controller
{
    /**
     * Display SMS management page
     */
    public function index()
    {
        $customers = Customer::where('status', 'active')
            ->select('id', 'name', 'phone', 'email')
            ->orderBy('name', 'asc')
            ->get();
        
        return view('backend.crm.sms_management', compact('customers'));
    }

    /**
     * Get customers list for dropdown (AJAX)
     */
    public function getCustomers()
    {
        $customers = Customer::where('status', 'active')
            ->select('id', 'name', 'phone', 'email')
            ->orderBy('name', 'asc')
            ->get();
        
        return response()->json($customers);
    }

    /**
     * Get error message from error code
     */
    private function getErrorMessage($code)
    {
        $errorMessages = [
            202 => 'SMS Submitted Successfully',
            1001 => 'Invalid Phone Number',
            1002 => 'Sender ID not correct or disabled',
            1003 => 'Required fields missing. Please contact system administrator',
            1005 => 'Internal Error',
            1006 => 'Balance Validity Not Available',
            1007 => 'Insufficient Balance',
            1011 => 'User ID not found',
            1012 => 'Masking SMS must be sent in Bengali',
            1013 => 'Sender ID has not found Gateway by API key',
            1014 => 'Sender Type Name not found using this sender by API key',
            1015 => 'Sender ID has not found Any Valid Gateway by API key',
            1016 => 'Sender Type Name Active Price Info not found by this sender id',
            1017 => 'Sender Type Name Price Info not found by this sender id',
            1018 => 'The Owner of this Account is disabled',
            1019 => 'The Price of this Account is disabled',
            1020 => 'The parent of this account is not found',
            1021 => 'The parent active price of this account is not found',
            1031 => 'Your Account Not Verified, Please Contact Administrator',
            1032 => 'IP Not whitelisted. Please whitelist your IP from Phonebook',
        ];

        return $errorMessages[$code] ?? 'Unknown error occurred';
    }

    /**
     * Send single SMS
     */
    public function sendSingle(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:bulksmsbd,twilio',
            'number' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $provider = $request->provider;
        $number = $request->number;
        $message = $request->message;

        $result = sms_send_single($number, $message, $provider);

        if ($result['status'] ?? false) {
            return response()->json([
                'success' => true,
                'message' => $this->getErrorMessage($result['code'] ?? 202),
                'data' => $result
            ]);
        }

        $errorCode = $result['code'] ?? 1005;
        return response()->json([
            'success' => false,
            'message' => $this->getErrorMessage($errorCode),
            'code' => $errorCode,
            'data' => $result
        ], 400);
    }

    /**
     * Send one-to-many SMS (same message to multiple numbers)
     */
    public function sendOneToMany(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:bulksmsbd,twilio',
            'numbers' => 'required|array|min:1',
            'numbers.*' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $provider = $request->provider;
        $numbers = $request->numbers;
        $message = $request->message;

        $result = sms_send_one_to_many($numbers, $message, $provider);

        if ($result['status'] ?? false) {
            return response()->json([
                'success' => true,
                'message' => $this->getErrorMessage($result['code'] ?? 202) . ' to ' . count($numbers) . ' recipient(s)',
                'data' => $result
            ]);
        }

        $errorCode = $result['code'] ?? 1005;
        return response()->json([
            'success' => false,
            'message' => $this->getErrorMessage($errorCode),
            'code' => $errorCode,
            'data' => $result
        ], 400);
    }

    /**
     * Send many-to-many SMS (different messages to different numbers)
     */
    public function sendManyToMany(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:bulksmsbd,twilio',
            'messages' => 'required|array|min:1',
            'messages.*.to' => 'required|string',
            'messages.*.message' => 'required|string|max:1000',
        ]);

        $provider = $request->provider;
        $messages = $request->messages;

        $result = sms_send_many_to_many($messages, $provider);

        if ($result['status'] ?? false) {
            return response()->json([
                'success' => true,
                'message' => $this->getErrorMessage($result['code'] ?? 202) . ' to ' . count($messages) . ' recipient(s)',
                'data' => $result
            ]);
        }

        $errorCode = $result['code'] ?? 1005;
        return response()->json([
            'success' => false,
            'message' => $this->getErrorMessage($errorCode),
            'code' => $errorCode,
            'data' => $result
        ], 400);
    }

    /**
     * Get SMS balance
     */
    public function getBalance(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:bulksmsbd,twilio',
        ]);

        $provider = $request->provider;
        $result = sms_get_balance($provider);

        return response()->json($result);
    }
}

