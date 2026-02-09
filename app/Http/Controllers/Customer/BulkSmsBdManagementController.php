<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Customer\Models\Customer;
use App\Models\User;

class BulkSmsBdManagementController extends Controller
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
        
        return view('backend.crm.bulk_sms_bd_management', compact('customers'));
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
     * Send single SMS (uses helper function)
     */
    public function sendSingle(Request $request)
    {
        $request->validate([
            'number' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $number = $request->number;
        $message = $request->message;

        $result = sms_send_single($number, $message, 'bulksmsbd');

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
     * Implementation moved from helper to controller
     */
    public function sendOneToMany(Request $request)
    {
        $request->validate([
            'numbers' => 'required|array|min:1',
            'numbers.*' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $numbers = $request->numbers;
        $message = $request->message;

        // Validate all numbers
        $validNumbers = [];
        foreach ($numbers as $number) {
            if (validateBDPhone($number)) {
                $validNumbers[] = normalizeBDPhone($number);
            }
        }

        if (empty($validNumbers)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid phone numbers provided.',
                'code' => 1001
            ], 400);
        }

        // Implementation from helper
        $apiKey = env('BULKSMS_API_KEY', 'RBAJvEih5U5SmqJfJC2d');
        $senderId = env('BULKSMS_SENDER_ID', 'Wardah Life');
        
        // Join numbers with comma
        $numberString = implode(',', $validNumbers);

        try {
            $response = Http::post('http://bulksmsbd.net/api/smsapi', [
                'api_key' => $apiKey,
                'senderid' => $senderId,
                'number' => $numberString,
                'message' => $message,
            ]);

            $result = $response->object();
            
            if (isset($result->response_code) && $result->response_code == 202) {
                return response()->json([
                    'success' => true,
                    'message' => $this->getErrorMessage(202) . ' to ' . count($validNumbers) . ' recipient(s)',
                    'code' => 202,
                    'data' => $result
                ]);
            }

            $errorCode = $result->response_code ?? 1005;
            return response()->json([
                'success' => false,
                'message' => $this->getErrorMessage($errorCode),
                'code' => $errorCode,
                'data' => $result
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 1005
            ], 400);
        }
    }

    /**
     * Send many-to-many SMS (different messages to different numbers)
     * Implementation moved from helper to controller
     */
    public function sendManyToMany(Request $request)
    {
        $request->validate([
            'messages' => 'required|array|min:1',
            'messages.*.to' => 'required|string',
            'messages.*.message' => 'required|string|max:1000',
        ]);

        $messages = $request->messages;

        // Validate and normalize all messages
        $validMessages = [];
        foreach ($messages as $msg) {
            if (!isset($msg['to']) || !isset($msg['message'])) {
                continue;
            }
            
            if (validateBDPhone($msg['to'])) {
                $validMessages[] = [
                    'to' => normalizeBDPhone($msg['to']),
                    'message' => $msg['message']
                ];
            }
        }

        if (empty($validMessages)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid messages provided.',
                'code' => 1001
            ], 400);
        }

        // Implementation from helper
        $apiKey = env('BULKSMS_API_KEY', 'RBAJvEih5U5SmqJfJC2d');
        $senderId = env('BULKSMS_SENDER_ID', 'Wardah Life');
        
        $messagesJson = json_encode($validMessages);

        try {
            $response = Http::asForm()->post('http://bulksmsbd.net/api/smsapimany', [
                'api_key' => $apiKey,
                'senderid' => $senderId,
                'messages' => $messagesJson,
            ]);

            $result = $response->object();
            
            if (isset($result->response_code) && $result->response_code == 202) {
                return response()->json([
                    'success' => true,
                    'message' => $this->getErrorMessage(202) . ' to ' . count($validMessages) . ' recipient(s)',
                    'code' => 202,
                    'data' => $result
                ]);
            }

            $errorCode = $result->response_code ?? 1005;
            return response()->json([
                'success' => false,
                'message' => $this->getErrorMessage($errorCode),
                'code' => $errorCode,
                'data' => $result
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => 1005
            ], 400);
        }
    }

    /**
     * Get SMS balance (uses helper function)
     */
    public function getBalance()
    {
        $result = sms_get_balance('bulksmsbd');
        return response()->json($result);
    }
}

