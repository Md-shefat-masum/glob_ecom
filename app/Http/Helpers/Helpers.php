<?php

use App\Models\ProductStockLog;
use App\Models\User;
use App\Models\Webpage\WebPageMenu;
use App\Models\Webpage\WebPages;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\OrderPayment;
use App\Http\Controllers\Account\Models\DbCustomerPayment;
use App\Http\Controllers\Account\Models\AcTransaction;
use App\Http\Controllers\Account\Models\AcAccount;
use App\Http\Controllers\Account\Models\DbPaymentType;
use App\Models\AcEventMapping;
use App\Http\Controllers\Customer\Models\Customer;
use App\Models\ProductOrder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

if (!function_exists('normalizeBDPhone')) {
    /**
     * Normalize Bangladeshi phone number to 8801XXXXXXXXX format
     */
    function normalizeBDPhone(string $number): string
    {
        // Remove all non-digits
        $number = preg_replace('/\D/', '', $number);
        
        // Normalize → always 8801XXXXXXXXX
        if (str_starts_with($number, '+880')) {
            return substr($number, 1); // remove "+"
        } elseif (str_starts_with($number, '880')) {
            return $number;
        } elseif (str_starts_with($number, '01')) {
            return '88' . $number;
        } elseif (str_starts_with($number, '1')) {
            return '880' . $number;
        }
        
        return $number;
    }
}

if (!function_exists('validateBDPhone')) {
    /**
     * Validate Bangladeshi phone number format
     */
    function validateBDPhone(string $number): bool
    {
        // Only allow: +8801XXXXXXXXX, 8801XXXXXXXXX, 01XXXXXXXXX
        return preg_match('/^(?:\+8801\d{9}|8801\d{9}|01\d{9})$/', $number) === 1;
    }
}

if (!function_exists('sms_send')) {
    /**
     * Send SMS to single number (backward compatibility)
     * Uses default provider from env
     */
    function sms_send(string $number, string $message, string $provider = null)
    {
        $provider = $provider ?? env('SMS_PROVIDER', 'bulksmsbd');
        return sms_send_single($number, $message, $provider);
    }
}

if (!function_exists('sms_send_single')) {
    /**
     * Send SMS to a single number
     * 
     * @param string $number Phone number
     * @param string $message SMS message
     * @param string $provider Provider name (bulksmsbd, twilio)
     * @return array|object Response from provider
     */
    function sms_send_single(string $number, string $message, string $provider = 'bulksmsbd')
    {
        if (!validateBDPhone($number)) {
            return [
                'status' => false,
                'message' => 'Invalid Bangladeshi phone number format.',
                'code' => 1001
            ];
        }

        $formattedNumber = normalizeBDPhone($number);

        if ($provider === 'bulksmsbd') {
            return sms_send_single_bulksmsbd($formattedNumber, $message);
        } elseif ($provider === 'twilio') {
            return sms_send_single_twilio($formattedNumber, $message);
        }

        return [
            'status' => false,
            'message' => 'Invalid SMS provider.',
            'code' => 1003
        ];
    }
}

if (!function_exists('sms_send_one_to_many')) {
    /**
     * Send same message to multiple numbers
     * 
     * @param array $numbers Array of phone numbers
     * @param string $message SMS message
     * @param string $provider Provider name (bulksmsbd, twilio)
     * @return array|object Response from provider
     */
    function sms_send_one_to_many(array $numbers, string $message, string $provider = 'bulksmsbd')
    {
        // Validate all numbers
        $validNumbers = [];
        foreach ($numbers as $number) {
            if (validateBDPhone($number)) {
                $validNumbers[] = normalizeBDPhone($number);
            }
        }

        if (empty($validNumbers)) {
            return [
                'status' => false,
                'message' => 'No valid phone numbers provided.',
                'code' => 1001
            ];
        }

        if ($provider === 'bulksmsbd') {
            return sms_send_one_to_many_bulksmsbd($validNumbers, $message);
        } elseif ($provider === 'twilio') {
            return sms_send_one_to_many_twilio($validNumbers, $message);
        }

        return [
            'status' => false,
            'message' => 'Invalid SMS provider.',
            'code' => 1003
        ];
    }
}

if (!function_exists('sms_send_many_to_many')) {
    /**
     * Send different messages to different numbers
     * 
     * @param array $messages Array of ['to' => 'number', 'message' => 'text']
     * @param string $provider Provider name (bulksmsbd, twilio)
     * @return array|object Response from provider
     */
    function sms_send_many_to_many(array $messages, string $provider = 'bulksmsbd')
    {
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
            return [
                'status' => false,
                'message' => 'No valid messages provided.',
                'code' => 1001
            ];
        }

        if ($provider === 'bulksmsbd') {
            return sms_send_many_to_many_bulksmsbd($validMessages);
        } elseif ($provider === 'twilio') {
            return sms_send_many_to_many_twilio($validMessages);
        }

        return [
            'status' => false,
            'message' => 'Invalid SMS provider.',
            'code' => 1003
        ];
    }
}

if (!function_exists('sms_get_balance')) {
    /**
     * Get SMS balance from provider
     * 
     * @param string $provider Provider name (bulksmsbd, twilio)
     * @return array|object Balance information
     */
    function sms_get_balance(string $provider = 'bulksmsbd')
    {
        if ($provider === 'bulksmsbd') {
            return sms_get_balance_bulksmsbd();
        } elseif ($provider === 'twilio') {
            return sms_get_balance_twilio();
        }

        return [
            'status' => false,
            'message' => 'Invalid SMS provider.',
            'code' => 1003
        ];
    }
}

// ==================== Bulk SMS BD Provider Functions ====================

if (!function_exists('sms_send_single_bulksmsbd')) {
    function sms_send_single_bulksmsbd(string $number, string $message)
    {
        $apiKey = env('BULKSMS_API_KEY', 'RBAJvEih5U5SmqJfJC2d');
        $senderId = env('BULKSMS_SENDER_ID', 'Wardah Life');

        try {
            $response = Http::get('http://bulksmsbd.net/api/smsapi', [
                'api_key' => $apiKey,
                'type' => 'text',
                'number' => $number,
                'senderid' => $senderId,
                'message' => $message,
            ]);

            $result = $response->object();
            
            // Map response to standard format
            if (isset($result->response_code) && $result->response_code == 202) {
                return [
                    'status' => true,
                    'message' => 'SMS Submitted Successfully',
                    'code' => 202,
                    'data' => $result
                ];
            }

            return [
                'status' => false,
                'message' => $result->error_message ?? 'SMS sending failed',
                'code' => $result->response_code ?? 1005,
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 1005
            ];
        }
    }
}

if (!function_exists('sms_send_one_to_many_bulksmsbd')) {
    function sms_send_one_to_many_bulksmsbd(array $numbers, string $message)
    {
        $apiKey = env('BULKSMS_API_KEY', 'RBAJvEih5U5SmqJfJC2d');
        $senderId = env('BULKSMS_SENDER_ID', 'Wardah Life');
        
        // Join numbers with comma
        $numberString = implode(',', $numbers);

        try {
            $response = Http::post('http://bulksmsbd.net/api/smsapi', [
                'api_key' => $apiKey,
                'senderid' => $senderId,
                'number' => $numberString,
                'message' => $message,
            ]);

            $result = $response->object();
            
            if (isset($result->response_code) && $result->response_code == 202) {
                return [
                    'status' => true,
                    'message' => 'SMS Submitted Successfully',
                    'code' => 202,
                    'data' => $result
                ];
            }

            return [
                'status' => false,
                'message' => $result->error_message ?? 'SMS sending failed',
                'code' => $result->response_code ?? 1005,
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 1005
            ];
        }
    }
}

if (!function_exists('sms_send_many_to_many_bulksmsbd')) {
    function sms_send_many_to_many_bulksmsbd(array $messages)
    {
        $apiKey = env('BULKSMS_API_KEY', 'RBAJvEih5U5SmqJfJC2d');
        $senderId = env('BULKSMS_SENDER_ID', 'Wardah Life');
        
        $messagesJson = json_encode($messages);

        try {
            $response = Http::asForm()->post('http://bulksmsbd.net/api/smsapimany', [
                'api_key' => $apiKey,
                'senderid' => $senderId,
                'messages' => $messagesJson,
            ]);

            $result = $response->object();
            
            if (isset($result->response_code) && $result->response_code == 202) {
                return [
                    'status' => true,
                    'message' => 'SMS Submitted Successfully',
                    'code' => 202,
                    'data' => $result
                ];
            }

            return [
                'status' => false,
                'message' => $result->error_message ?? 'SMS sending failed',
                'code' => $result->response_code ?? 1005,
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'code' => 1005
            ];
        }
    }
}

if (!function_exists('sms_get_balance_bulksmsbd')) {
    function sms_get_balance_bulksmsbd()
    {
        $apiKey = env('BULKSMS_API_KEY', 'RBAJvEih5U5SmqJfJC2d');

        try {
            $response = Http::get('http://bulksmsbd.net/api/getBalanceApi', [
                'api_key' => $apiKey,
            ]);

            $result = $response->object();
            
            return [
                'status' => true,
                'balance' => $result->balance ?? 0,
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'balance' => 0
            ];
        }
    }
}

// ==================== Twilio Provider Functions (Placeholder) ====================

if (!function_exists('sms_send_single_twilio')) {
    function sms_send_single_twilio(string $number, string $message)
    {
        // TODO: Implement Twilio SMS sending
        return [
            'status' => false,
            'message' => 'Twilio provider not yet implemented. Please configure Twilio credentials.',
            'code' => 1003
        ];
    }
}

if (!function_exists('sms_send_one_to_many_twilio')) {
    function sms_send_one_to_many_twilio(array $numbers, string $message)
    {
        // TODO: Implement Twilio bulk SMS
        return [
            'status' => false,
            'message' => 'Twilio provider not yet implemented. Please configure Twilio credentials.',
            'code' => 1003
        ];
    }
}

if (!function_exists('sms_send_many_to_many_twilio')) {
    function sms_send_many_to_many_twilio(array $messages)
    {
        // TODO: Implement Twilio many-to-many SMS
        return [
            'status' => false,
            'message' => 'Twilio provider not yet implemented. Please configure Twilio credentials.',
            'code' => 1003
        ];
    }
}

if (!function_exists('sms_get_balance_twilio')) {
    function sms_get_balance_twilio()
    {
        // TODO: Implement Twilio balance check
        return [
            'status' => false,
            'message' => 'Twilio provider not yet implemented. Please configure Twilio credentials.',
            'balance' => 0
        ];
    }
}

if (! function_exists('entityResponse')) {
    function entityResponse($data = null, $statusCode = 200, $status = 'success', $message = null, $cache_time_sec = 5)
    {
        $payload = ['status' => $status, 'statusCode' => $statusCode, 'data' => $data];
        if ($message) {
            $payload['message'] = $message;
        }

        return response()
            ->json($payload, $statusCode)
            ->header('Cache-Control', "public, max-age=$cache_time_sec") // Cache seconds
            ->header('Expires', now()->addSeconds($cache_time_sec)->toRfc7231String());
    }
}

if (! function_exists('format_asset_url')) {
    function format_asset_url(string $path): array
    {
        $items = explode(',', $path);
        $result = [];

        foreach ($items as $item) {
            $item = trim($item);
            if (!preg_match('/^https?:\/\//i', $item)) {
                $item = '/' . ltrim($item, '/');
            }

            $result[] = $item;
        }

        return $result;
    }
}

if (! function_exists('get_setting_value')) {
    function get_setting_value($title = null)
    {
        if ($title) {
            try {
                return $GLOBALS['app_settings']->where('title', $title)->first()['value'];
            } catch (\Throwable $th) {
                return '';
            }
        }
    }
}

if (! function_exists('validateBDPhone')) {
    function validateBDPhone($number)
    {
        // Remove all non-digits
        $number = preg_replace('/\D/', '', $number);

        // Remove leading +880 or 880 if present
        if (strpos($number, '880') === 0) {
            $number = substr($number, 3);
        }

        // If starts with 0 and has 11 digits, remove the first 0
        if (strlen($number) === 11 && $number[0] === '0') {
            $number = substr($number, 1);
        }

        // Valid BD mobile prefixes (without leading 0)
        $validPrefixes = ['13', '14', '15', '16', '17', '18', '19'];

        // Must be exactly 10 digits now
        if (strlen($number) !== 10) {
            return false;
        }

        // Check if prefix is valid
        $prefix = substr($number, 0, 2);
        if (!in_array($prefix, $validPrefixes)) {
            return false;
        }

        // Return in full +880 format
        return '+880' . $number;
    }
}

if (! function_exists('delete_storage_file')) {
    function delete_storage_file($path, $disk)
    {
        // $media = new MediaUpController();
        // try {
        //     return $media->delete(['path' => $path, 'disk' => $disk]);
        // } catch (\Exception $e) {
        // }
    }
}

if (! function_exists('asset_url')) {
    function asset_url($src)
    {
        $url =  env('IMAGE_URL');
        $cleanPath = ltrim($src, '/');
        return $url . '/' . $cleanPath;
    }
}

if (! function_exists('versioned_asset')) {
    /**
     * Generate a versioned asset URL with query parameter
     * 
     * @param string $path Asset path
     * @return string URL with version query parameter
     */
    function versioned_asset($path)
    {
        $version = env('APP_VERSION', '1.0.0');
        $url = asset($path);
        
        // Check if URL already has query parameters
        $separator = strpos($url, '?') !== false ? '&' : '?';
        
        return $url . $separator . 'v=' . $version;
    }
}

if (! function_exists('versioned_url')) {
    /**
     * Generate a versioned URL with query parameter
     * 
     * @param string $path URL path
     * @return string URL with version query parameter
     */
    function versioned_url($path)
    {
        $version = config('app.version', '1.0.0');
        $url = url($path);
        
        // Check if URL already has query parameters
        $separator = strpos($url, '?') !== false ? '&' : '?';
        
        return $url . $separator . 'v=' . $version;
    }
}

if (! function_exists('class_info')) {
    function class_info(string|object $class)
    {
        $reflection = new \ReflectionClass($class);
        return $reflection->getNamespaceName();
        // return [
        //     'namespace'  => $reflection->getNamespaceName(),
        //     'class_name' => $reflection->getShortName(),
        //     'full_class' => $reflection->getName(),
        // ];
    }
}

if (!function_exists('insert_stock_log')) {
    function insert_stock_log($data = [])
    {
        $payload = [
            'warehouse_id'      => $data['warehouse_id'] ?? null,
            'product_id'        => $data['product_id'] ?? null,
            'product_name'      => $data['product_name'] ?? null,
            'product_sales_id'  => $data['product_sales_id'] ?? null,
            'product_purchase_id'  => $data['product_purchase_id'] ?? null,
            'product_return_id' => $data['product_return_id'] ?? null,
            'quantity'          => $data['quantity'] ?? 0,
            'type'              => $data['type'] ?? null, //purchase / sales / return / initial / transfer
            'creator'           => auth()->id(),
            'slug'              => (($data['product_sales_id'] ?? '') || ($data['product_purchase_id']  ?? '') || ($data['product_return_id']  ?? '')).uniqid(),
        ];

        if (Schema::hasColumn('product_stock_logs', 'has_variant')) {
            $payload['has_variant'] = (bool)($data['has_variant'] ?? false);
        }

        if (Schema::hasColumn('product_stock_logs', 'variant_combination_key')) {
            $payload['variant_combination_key'] = $data['variant_combination_key'] ?? null;
        }

        if (Schema::hasColumn('product_stock_logs', 'variant_sku')) {
            $payload['variant_sku'] = $data['variant_sku'] ?? null;
        }

        if (Schema::hasColumn('product_stock_logs', 'variant_data')) {
            $variantData = $data['variant_data'] ?? null;
            if (is_array($variantData) || is_object($variantData)) {
                $variantData = json_encode($variantData);
            }
            $payload['variant_data'] = $variantData;
        }

        if (Schema::hasColumn('product_stock_logs', 'variant_combination_id')) {
            $payload['variant_combination_id'] = $data['variant_combination_id'] ?? null;
        }

        return ProductStockLog::create($payload);
    }
}

if (!function_exists('record_sales_accounting')) {
    /**
     * Record accounting entries for sales orders
     * 
     * @param object $order ProductOrder object
     * @param string $action 'create', 'update', 'delete', 'return_full', 'return_partial'
     * @param array $options Additional data for specific actions
     * @return array Status and message
     */
    function record_sales_accounting($order, $action = 'create', $options = [])
    {
        try {
            if ($action === 'create') {
                return record_sales_accounting_create($order);
            } elseif ($action === 'update') {
                return record_sales_accounting_update($order, $options);
            } elseif ($action === 'delete') {
                return record_sales_accounting_delete($order);
            } elseif ($action === 'return_full' || $action === 'return_partial') {
                return record_sales_accounting_return($order, $action, $options);
            }
            
            return ['success' => false, 'message' => 'Invalid action'];
        } catch (\Exception $e) {
            logger()->error('Sales Accounting Error: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
                'action' => $action,
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

if (!function_exists('record_sales_accounting_create')) {
    function record_sales_accounting_create($order)
    {
        $user = auth()->user();
        $payments = is_array($order->payments) ? $order->payments : json_decode($order->payments, true);
        $advanceAdjustment = request()->advance_adjustment ?? 0;
        
        // Step 1: Record Order Payments
        $orderPaymentIds = [];
        foreach ($payments as $method => $amount) {
            if ($amount > 0) {
                $orderPayment = OrderPayment::create([
                    'order_id' => $order->id,
                    'payment_through' => strtoupper($method),
                    'amount' => $amount,
                    'tran_date' => $order->sale_date ?? now(),
                    'store_id' => $user->store_id ?? null,
                    'status' => 'VALID',
                    'currency' => 'BDT',
                    'created_at' => Carbon::now()
                ]);
                $orderPaymentIds[$method] = $orderPayment->id;
            }
        }
        
        // Step 2: Record Customer Payments (db_customer_payments)
        foreach ($payments as $method => $amount) {
            if ($amount > 0) {
                DbCustomerPayment::create([
                    'orderpayment_id' => $orderPaymentIds[$method] ?? null,
                    'customer_id' => $order->customer_id,
                    'payment_date' => $order->sale_date ?? now(),
                    'payment_type' => 'received',
                    'payment' => $amount,
                    'payment_note' => "Payment via {$method} for order {$order->order_code}",
                    'creator' => $user->id,
                    'slug' => Str::orderedUuid() . uniqid(),
                    'status' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
        
        // Step 2.1: Record Advance Adjustment if applicable
        if ($advanceAdjustment > 0) {
            DbCustomerPayment::create([
                'orderpayment_id' => null,
                'customer_id' => $order->customer_id,
                'payment_date' => $order->sale_date ?? now(),
                'payment_type' => 'adjustment',
                'payment' => -$advanceAdjustment, // Negative to deduct from advance
                'payment_note' => "Advance adjustment for order {$order->order_code}",
                'creator' => $user->id,
                'slug' => Str::orderedUuid() . uniqid(),
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
        
        // Step 3: Update Customer Balance
        $customer = Customer::find($order->customer_id);
        if ($customer) {
            // Calculate total due from all orders
            $totalDue = DB::table('product_orders')
                ->where('customer_id', $order->customer_id)
                ->where('status', 'active')
                ->sum('due_amount');
            
            // Calculate total paid from all orders
            $totalPaid = DB::table('product_orders')
                ->where('customer_id', $order->customer_id)
                ->where('status', 'active')
                ->sum('paid_amount');
            
            $customer->due = $totalDue;
            $customer->paid = $totalPaid;
            $customer->balance = $totalDue - $totalPaid;
            $customer->last_buy = now();
            $customer->last_transaction = now();
            $customer->save();
        }
        
        // Step 4: Create Accounting Transactions
        // Get event mappings
        $customerPaymentEvent = AcEventMapping::getByEventName('customer_payment');
        $salesEvent = $order->due_amount > 0 
            ? AcEventMapping::getByEventName('sales_credit') 
            : AcEventMapping::getByEventName('sales');
        
        // 4.1: Record payment transactions for each payment method
        foreach ($payments as $method => $amount) {
            if ($amount > 0 && $customerPaymentEvent) {
                $paymentAccountId = getPaymentAccountId($method);
                
                AcTransaction::create([
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $order->order_code,
                    'transaction_date' => $order->sale_date ?? now(),
                    'transaction_type' => 'customer_payment',
                    'event_type' => 'customer_payment',
                    'debit_account_id' => $paymentAccountId, // Cash/Bank account
                    'credit_account_id' => $customerPaymentEvent->credit_account_id, // Accounts Receivable
                    'debit_amt' => $amount,
                    'credit_amt' => $amount,
                    'note' => "Payment received via {$method} for order {$order->order_code}",
                    'ref_salespayments_id' => $orderPaymentIds[$method] ?? null,
                    'customer_id' => $order->customer_id,
                    'creator' => $user->id,
                    'slug' => Str::orderedUuid() . uniqid(),
                    'status' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
        
        // 4.2: Record advance adjustment transaction
        if ($advanceAdjustment > 0 && $customerPaymentEvent) {
            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $order->order_code,
                'transaction_date' => $order->sale_date ?? now(),
                'transaction_type' => 'advance_adjustment',
                'event_type' => 'customer_payment',
                'debit_account_id' => $customerPaymentEvent->debit_account_id,
                'credit_account_id' => $customerPaymentEvent->credit_account_id,
                'debit_amt' => $advanceAdjustment,
                'credit_amt' => $advanceAdjustment,
                'note' => "Advance adjustment for order {$order->order_code}",
                'customer_id' => $order->customer_id,
                'creator' => $user->id,
                'slug' => Str::orderedUuid() . uniqid(),
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
        
        // 4.3: Record sales revenue transaction (Primary)
        if ($salesEvent) {
            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $order->order_code,
                'transaction_date' => $order->sale_date ?? now(),
                'transaction_type' => $order->due_amount > 0 ? 'sales_credit' : 'sales_cash',
                'event_type' => $order->due_amount > 0 ? 'sales_credit' : 'sales',
                'debit_account_id' => $salesEvent->debit_account_id, // AR or Cash
                'credit_account_id' => $salesEvent->credit_account_id, // Sales Revenue
                'debit_amt' => $order->total,
                'credit_amt' => $order->total,
                'note' => "Sales transaction for order {$order->order_code}",
                'customer_id' => $order->customer_id,
                'creator' => $user->id,
                'slug' => Str::orderedUuid() . uniqid(),
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            // 4.4: Record COGS transaction (Secondary)
            if ($salesEvent->secondary_debit_account_id && $salesEvent->secondary_credit_account_id) {
                // Calculate COGS from order products
                $cogs = 0;
                foreach ($order->order_products as $product) {
                    $cogs += $product->product_price * $product->qty;
                }
                
                AcTransaction::create([
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $order->order_code,
                    'transaction_date' => $order->sale_date ?? now(),
                    'transaction_type' => 'cogs',
                    'event_type' => $order->due_amount > 0 ? 'sales_credit' : 'sales',
                    'debit_account_id' => $salesEvent->secondary_debit_account_id, // COGS
                    'credit_account_id' => $salesEvent->secondary_credit_account_id, // Inventory
                    'debit_amt' => $cogs,
                    'credit_amt' => $cogs,
                    'note' => "COGS for order {$order->order_code}",
                    'customer_id' => $order->customer_id,
                    'creator' => $user->id,
                    'slug' => Str::orderedUuid() . uniqid(),
                    'status' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
        
        return ['success' => true, 'message' => 'Sales accounting recorded successfully'];
    }
}

if (!function_exists('record_sales_accounting_update')) {
    function record_sales_accounting_update($order, $options = [])
    {
        // TODO: Implement update logic (reverse old entries and create new ones)
        return ['success' => true, 'message' => 'Update action not yet implemented'];
    }
}

if (!function_exists('record_sales_accounting_delete')) {
    function record_sales_accounting_delete($order)
    {
        // TODO: Implement delete logic (create reversing entries)
        return ['success' => true, 'message' => 'Delete action not yet implemented'];
    }
}

if (!function_exists('record_sales_accounting_return')) {
    /**
     * Record accounting entries for sales returns
     * 
     * @param object $order ProductOrder object
     * @param string $action 'return_partial' or 'return_full'
     * @param array $options ['return' => ProductOrderReturn object, 'return_amount' => amount]
     * @return array Status and message
     */
    function record_sales_accounting_return($order, $action, $options = [])
    {
        try {
            $user = auth()->user();
            $return = $options['return'] ?? null;
            $returnAmount = $options['return_amount'] ?? 0;

            if (!$return) {
                return ['success' => false, 'message' => 'Return object not provided'];
            }

            // Step 1: Record customer payment as advance/credit
            $paymentType = $return->refund_method === 'advance_payment' ? 'advance' : 'credit';
            
            DbCustomerPayment::create([
                'customer_id' => $order->customer_id,
                'payment_date' => $return->return_date ?? now(),
                'payment_type' => $paymentType,
                'payment' => $returnAmount,
                'payment_note' => "Product return refund for order {$order->order_code}, Return Code: {$return->return_code}",
                'creator' => $user->id,
                'slug' => Str::orderedUuid() . uniqid(),
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            // Step 2: Get accounting event mapping for sales returns
            $returnEvent = AcEventMapping::where('event_name', 'sales_return')
                ->where('status', 'active')
                ->first();

            if (!$returnEvent) {
                logger()->warning('Sales return event mapping not found in ac_event_mappings table');
                return ['success' => true, 'message' => 'Customer payment recorded, but accounting event mapping not found'];
            }

            // Step 3: Record revenue reversal transaction
            // Debit: Sales Revenue (reverse the income)
            // Credit: Accounts Receivable or Cash (depending on refund method)
            
            $refundAccountId = getPaymentAccountId($return->refund_method);
            
            if ($returnEvent->primary_debit_account_id && $returnEvent->primary_credit_account_id) {
                AcTransaction::create([
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $return->return_code,
                    'transaction_date' => $return->return_date ?? now(),
                    'transaction_type' => 'sales_return',
                    'event_type' => 'sales_return',
                    'debit_account_id' => $returnEvent->primary_debit_account_id, // Sales Revenue
                    'credit_account_id' => $refundAccountId ?? $returnEvent->primary_credit_account_id, // Cash/Bank/AR
                    'debit_amt' => $returnAmount,
                    'credit_amt' => $returnAmount,
                    'note' => "Sales return for order {$order->order_code}",
                    'customer_id' => $order->customer_id,
                    'ref_salespaymentsreturn_id' => $return->id,
                    'creator' => $user->id,
                    'slug' => Str::orderedUuid() . uniqid(),
                    'status' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            // Step 4: Record COGS reversal (if secondary accounts exist)
            // Debit: Inventory (add back to inventory)
            // Credit: COGS (reverse the expense)
            if ($returnEvent->secondary_debit_account_id && $returnEvent->secondary_credit_account_id) {
                // Calculate COGS for returned products
                $cogs = 0;
                foreach ($return->return_products as $product) {
                    $cogs += $product->product_price * $product->qty;
                }

                if ($cogs > 0) {
                    AcTransaction::create([
                        'store_id' => $user->store_id ?? null,
                        'payment_code' => $return->return_code,
                        'transaction_date' => $return->return_date ?? now(),
                        'transaction_type' => 'cogs_reversal',
                        'event_type' => 'sales_return',
                        'debit_account_id' => $returnEvent->secondary_debit_account_id, // Inventory
                        'credit_account_id' => $returnEvent->secondary_credit_account_id, // COGS
                        'debit_amt' => $cogs,
                        'credit_amt' => $cogs,
                        'note' => "COGS reversal for return {$return->return_code}",
                        'customer_id' => $order->customer_id,
                        'ref_salespaymentsreturn_id' => $return->id,
                        'creator' => $user->id,
                        'slug' => Str::orderedUuid() . uniqid(),
                        'status' => 'active',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }

            return ['success' => true, 'message' => 'Sales return accounting recorded successfully'];
        } catch (\Exception $e) {
            logger()->error('Sales Return Accounting Error: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
                'return_id' => $options['return']->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'message' => 'Error recording return accounting: ' . $e->getMessage()];
        }
    }
}

if (!function_exists('getPaymentAccountId')) {
    /**
     * Map payment method to chart of accounts ID
     * 
     * @param string $paymentMethod
     * @return int|null
     */
    function getPaymentAccountId($paymentMethod)
    {
        // Normalize payment method
        $method = strtolower($paymentMethod);
        
        // Cash payments go to Cash on Hand account
        if ($method === 'cash') {
            $account = AcAccount::where('account_selection_name', 'cash_on_hand')
                ->where('status', 'active')
                ->first();
            return $account ? $account->id : null;
        }
        
        // Digital payments (bkash, rocket, nogod, bank, gateway, cheque) go to Bank Account
        if (in_array($method, ['bkash', 'rocket', 'nogod', 'bank', 'gateway', 'cheque'])) {
            $account = AcAccount::where('account_selection_name', 'bank_account')
                ->where('status', 'active')
                ->first();
            return $account ? $account->id : null;
        }
        
        // Credit goes to Accounts Receivable
        if ($method === 'credit') {
            $account = AcAccount::where('account_selection_name', 'accounts_receivable')
                ->where('status', 'active')
                ->first();
            return $account ? $account->id : null;
        }
        
        // Default to Cash on Hand if unknown
        $account = AcAccount::where('account_selection_name', 'cash_on_hand')
            ->where('status', 'active')
            ->first();
        return $account ? $account->id : null;
    }
}

if (!function_exists('numberToWords')) {
    /**
     * Convert a number to words
     * 
     * @param float|int $number
     * @return string
     */
    function numberToWords($number)
    {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'Negative ';
        $decimal     = ' Point ';
        $dictionary  = array(
            0                   => 'Zero',
            1                   => 'One',
            2                   => 'Two',
            3                   => 'Three',
            4                   => 'Four',
            5                   => 'Five',
            6                   => 'Six',
            7                   => 'Seven',
            8                   => 'Eight',
            9                   => 'Nine',
            10                  => 'Ten',
            11                  => 'Eleven',
            12                  => 'Twelve',
            13                  => 'Thirteen',
            14                  => 'Fourteen',
            15                  => 'Fifteen',
            16                  => 'Sixteen',
            17                  => 'Seventeen',
            18                  => 'Eighteen',
            19                  => 'Nineteen',
            20                  => 'Twenty',
            30                  => 'Thirty',
            40                  => 'Forty',
            50                  => 'Fifty',
            60                  => 'Sixty',
            70                  => 'Seventy',
            80                  => 'Eighty',
            90                  => 'Ninety',
            100                 => 'Hundred',
            1000                => 'Thousand',
            100000              => 'Lakh',
            10000000            => 'Crore'
        );

        if (!is_numeric($number)) {
            return '';
        }

        if ($number < 0) {
            return $negative . numberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[(int)$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . numberToWords($remainder);
                }
                break;
            case $number < 100000:
                $thousands   = $number / 1000;
                $remainder = $number % 1000;
                $string = numberToWords((int)$thousands) . ' ' . $dictionary[1000];
                if ($remainder) {
                    $string .= $separator . numberToWords($remainder);
                }
                break;
            case $number < 10000000:
                $lakhs   = $number / 100000;
                $remainder = $number % 100000;
                $string = numberToWords((int)$lakhs) . ' ' . $dictionary[100000];
                if ($remainder) {
                    $string .= $separator . numberToWords($remainder);
                }
                break;
            default:
                $crores   = $number / 10000000;
                $remainder = $number % 10000000;
                $string = numberToWords((int)$crores) . ' ' . $dictionary[10000000];
                if ($remainder) {
                    $string .= $separator . numberToWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $digit) {
                $words[] = $dictionary[$digit];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}

/**
 * Recalculate product stock from variants
 */
if (!function_exists('recalculate_product_stock')) {
    function recalculate_product_stock($productId)
    {
        // Get total stock from all variant combinations
        $totalStock = DB::table('product_variant_combinations')
            ->where('product_id', $productId)
            ->sum('stock');

        // Update product stock
        DB::table('products')
            ->where('id', $productId)
            ->update([
                'stock' => $totalStock,
                'updated_at' => now()
            ]);

        return $totalStock;
    }
}

/**
 * Update variant combinations stock from product_stocks table
 * This function calculates stock from product_stocks and updates product_variant_combinations.stock
 */
if (!function_exists('update_variant_combinations_stock_from_product_stocks')) {
    function update_variant_combinations_stock_from_product_stocks($productId = null)
    {
        // If productId is provided, update only that product's variants
        // Otherwise, update all products with variants
        $query = \App\Models\ProductVariantCombination::query();
        if ($productId) {
            $query->where('product_id', $productId);
        } else {
            // Get all products that have variants
            $query->whereIn('product_id', function($subQuery) {
                $subQuery->select('id')
                    ->from('products')
                    ->where('has_variant', 1);
            });
        }
        
        $variants = $query->get();
        $updatedCount = 0;
        
        foreach ($variants as $variant) {
            // Calculate total stock for this variant combination from product_stocks
            $variantStockQuery = DB::table('product_stocks')
                ->where('product_id', $variant->product_id)
                ->where('has_variant', 1)
                ->where('status', 'active');
            
            // Check if variant_combination_id column exists
            if (Schema::hasColumn('product_stocks', 'variant_combination_id')) {
                // Match by variant_combination_id OR by combination_key (regardless of variant_combination_id value)
                $variantStockQuery->where(function ($query) use ($variant) {
                    $query->where('variant_combination_id', $variant->id)
                        ->orWhere('variant_combination_key', $variant->combination_key);
                });
            } else {
                $variantStockQuery->where('variant_combination_key', $variant->combination_key);
            }
            
            $variantStock = $variantStockQuery->sum('qty') ?? 0;
            
            // Update variant combination stock directly using DB
            DB::table('product_variant_combinations')
                ->where('id', $variant->id)
                ->update(['stock' => $variantStock]);
            $updatedCount++;
        }
        
        // If productId was provided, also update the product's total stock
        if ($productId) {
            recalculate_product_stock($productId);
        }
        
        return [
            'updated_count' => $updatedCount,
            'product_id' => $productId
        ];
    }
}


if (!function_exists('record_purchase_create_accounting')) {
    function record_purchase_create_accounting($purchase)
    {
        try {
            // Check if transaction already exists for this purchase
            $existingTransaction = AcTransaction::where('ref_purchase_id', $purchase->id)
                ->where('transaction_type', 'PURCHASE_CREATE')
                ->first();

            if ($existingTransaction) {
                Log::info('Purchase accounting transaction already exists', [
                    'purchase_id' => $purchase->id,
                    'purchase_code' => $purchase->code ?? null,
                    'existing_transaction_id' => $existingTransaction->id
                ]);
                return;
            }

            $user = auth()->user();
            $purchase_event = AcEventMapping::where('event_name', 'purchase')->first();

            if (!$purchase_event) {
                Log::error('Purchase event mapping not found', [
                    'purchase_id' => $purchase->id,
                    'purchase_code' => $purchase->code ?? null
                ]);
                return;
            }

            // Generate payment code for this group of transactions (PC = Purchase Create)
            $paymentCode = generate_payment_code('PC');
            $transactionDate = $purchase->purchase_date ?? now();
            $baseNote = "Purchase {$purchase->code} ref {$purchase->reference}";
            $transactions = [];

            // Create dual entry: Debit and Credit in separate rows
            if ($purchase_event->debit_account_id && $purchase_event->credit_account_id) {
                // Row 1: Debit Entry Only
                // Debit: Inventory/Purchase Account
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_date' => $transactionDate,
                    'transaction_type' => 'PURCHASE_CREATE',
                    'debit_account_id' => $purchase_event->debit_account_id,
                    'credit_account_id' => null,
                    'debit_amt' => $purchase->total,
                    'credit_amt' => null,
                    'note' => $baseNote . ' - Stock added',
                    'ref_purchase_id' => $purchase->id,
                    'supplier_id' => $purchase->product_supplier_id,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Row 2: Credit Entry Only
                // Credit: Accounts Payable/Supplier Account
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_date' => $transactionDate,
                    'transaction_type' => 'PURCHASE_CREATE',
                    'debit_account_id' => null,
                    'credit_account_id' => $purchase_event->credit_account_id,
                    'debit_amt' => null,
                    'credit_amt' => $purchase->total,
                    'note' => $baseNote . ' - Supplier payment due',
                    'ref_purchase_id' => $purchase->id,
                    'supplier_id' => $purchase->product_supplier_id,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Create all transactions in the group with the same payment_code
            if (!empty($transactions)) {
                AcTransaction::insert($transactions);
                
                Log::info('Purchase create accounting transactions created', [
                    'purchase_id' => $purchase->id,
                    'purchase_code' => $purchase->code ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_count' => count($transactions)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Purchase Create Accounting Error', [
                'message' => $e->getMessage(),
                'purchase_id' => $purchase->id ?? null,
                'purchase_code' => $purchase->code ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

if (!function_exists('generate_payment_code')) {
    /**
     * Generate incremental payment code for accounting transactions
     * Format: PREFIX-YYMMDD#### (e.g., PR-2512310001, SP-2512310001)
     * 
     * @param string $prefix Prefix for the payment code (default: 'PR' for Purchase Return)
     * @return string
     */
    function generate_payment_code($prefix = 'PR')
    {
        $datePrefix = date('ymd'); // YYMMDD format
        $fullPrefix = $prefix . '-' . $datePrefix;

        // Find last payment code with same prefix
        $lastPaymentCode = AcTransaction::where('payment_code', 'LIKE', $fullPrefix . '%')
            ->whereNotNull('payment_code')
            ->orderBy('payment_code', 'desc')
            ->value('payment_code');

        if (!$lastPaymentCode) {
            // First payment code of the day with this prefix
            return $fullPrefix . '0001';
        }

        // Extract last 4 digits (sequence number)
        $sequence = substr($lastPaymentCode, -4); // Get last 4 characters
        $nextSequence = (int) $sequence + 1;

        // Ensure it doesn't exceed 9999
        if ($nextSequence > 9999) {
            $nextSequence = 1;
        }

        return $fullPrefix . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('record_purchase_return_create_accounting')) {
    function record_purchase_return_create_accounting($purchaseReturn)
    {
        try {
            // Check if transaction already exists for this purchase return
            $existingTransaction = AcTransaction::where('ref_purchase_return_id', $purchaseReturn->id)
                ->where('transaction_type', 'PURCHASE_RETURN_CREATE')
                ->first();

            if ($existingTransaction) {
                Log::info('Purchase return accounting transaction already exists', [
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_return_code' => $purchaseReturn->code ?? null,
                    'existing_transaction_id' => $existingTransaction->id
                ]);
                return;
            }

            $user = auth()->user();
            $purchase_return_event = AcEventMapping::where('event_name', 'purchase_return')->first();

            if (!$purchase_return_event) {
                Log::error('Purchase return event mapping not found', [
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_return_code' => $purchaseReturn->code ?? null
                ]);
                return;
            }

            // Generate payment code for this group of transactions (PR = Purchase Return)
            $paymentCode = generate_payment_code('PR');
            $transactionDate = $purchaseReturn->return_date ?? now();
            $baseNote = "Purchase Return {$purchaseReturn->code} ref {$purchaseReturn->reference}";
            $transactions = [];

            // Transaction 1: Primary - Reverse Purchase (Debit what was credited, Credit what was debited)
            if ($purchase_return_event->debit_account_id && $purchase_return_event->credit_account_id) {
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_date' => $transactionDate,
                    'transaction_type' => 'PURCHASE_RETURN_CREATE',
                    'debit_account_id' => $purchase_return_event->credit_account_id, // Reversed: Credit account becomes debit
                    'credit_account_id' => $purchase_return_event->debit_account_id, // Reversed: Debit account becomes credit
                    'debit_amt' => $purchaseReturn->total,
                    'credit_amt' => $purchaseReturn->total,
                    'note' => $baseNote . ' - Purchase Reversal',
                    'ref_purchase_return_id' => $purchaseReturn->id,
                    'supplier_id' => $purchaseReturn->product_supplier_id,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Transaction 2: Secondary - Reverse COGS (if secondary accounts exist)
            // Debit: Inventory (add back to inventory)
            // Credit: COGS (reverse the expense)
            if ($purchase_return_event->secondary_debit_account_id && $purchase_return_event->secondary_credit_account_id) {
                // Calculate COGS for returned products
                $cogs = 0;
                if (isset($purchaseReturn->return_products) && $purchaseReturn->return_products) {
                    foreach ($purchaseReturn->return_products as $product) {
                        $cogs += ($product->product_price ?? 0) * ($product->qty ?? 0);
                    }
                } else {
                    // Fallback: use total if return_products not available
                    $cogs = $purchaseReturn->total;
                }

                if ($cogs > 0) {
                    $transactions[] = [
                        'store_id' => $user->store_id ?? null,
                        'payment_code' => $paymentCode,
                        'transaction_date' => $transactionDate,
                        'transaction_type' => 'PURCHASE_RETURN_COGS',
                        'debit_account_id' => $purchase_return_event->secondary_debit_account_id, // Inventory
                        'credit_account_id' => $purchase_return_event->secondary_credit_account_id, // COGS
                        'debit_amt' => $cogs,
                        'credit_amt' => $cogs,
                        'note' => $baseNote . ' - COGS Reversal',
                        'ref_purchase_return_id' => $purchaseReturn->id,
                        'supplier_id' => $purchaseReturn->product_supplier_id,
                        'created_by' => substr($user->name, 0, 50),
                        'creator' => $user->id,
                        'slug' => uniqid() . time(),
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            // Create all transactions in the group with the same payment_code
            if (!empty($transactions)) {
                AcTransaction::insert($transactions);
                
                Log::info('Purchase return accounting transactions created', [
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_return_code' => $purchaseReturn->code ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_count' => count($transactions)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Purchase Return Create Accounting Error', [
                'message' => $e->getMessage(),
                'purchase_return_id' => $purchaseReturn->id ?? null,
                'purchase_return_code' => $purchaseReturn->code ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

if (!function_exists('record_supplier_payment_accounting')) {
    function record_supplier_payment_accounting($supplier, $paymentAmount, $paymentDate, $note = '', $fromAccountId = null, $supplierPayment = null)
    {
        try {
            $user = auth()->user();
            $supplier_payment_event = AcEventMapping::where('event_name', 'supplier_payment')->first();

            if (!$supplier_payment_event) {
                Log::error('Supplier payment event mapping not found', [
                    'supplier_id' => $supplier->id ?? null,
                    'supplier_name' => $supplier->name ?? null
                ]);
                return;
            }

            // Generate payment code for this group of transactions (SP = Supplier Payment)
            $paymentCode = generate_payment_code('SP');
            $baseNote = $note ?: "Supplier Payment to {$supplier->name}";
            $transactions = [];

            // Transaction 1: Debit Entry - Supplier Payable (Accounts Payable)
            // Transaction 2: Credit Entry - From Account (Cash/Bank/etc - the account from which payment is made)
            $debitAccountId = $supplier_payment_event->debit_account_id; // Supplier Payable
            $creditAccountId = $fromAccountId ?? $supplier_payment_event->credit_account_id; // From Account

            if ($debitAccountId && $creditAccountId && $paymentAmount > 0) {
                // Entry 1: Debit Entry
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_date' => $paymentDate,
                    'transaction_type' => 'SUPPLIER_PAYMENT',
                    'debit_account_id' => $debitAccountId,
                    'credit_account_id' => null,
                    'debit_amt' => $paymentAmount,
                    'credit_amt' => null,
                    'note' => $baseNote . ' - Debit Entry',
                    'supplier_id' => $supplier->id,
                    'ref_supplier_payment_id' => $supplierPayment?->id ?? null,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Entry 2: Credit Entry
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_date' => $paymentDate,
                    'transaction_type' => 'SUPPLIER_PAYMENT',
                    'debit_account_id' => null,
                    'credit_account_id' => $creditAccountId,
                    'debit_amt' => null,
                    'credit_amt' => $paymentAmount,
                    'note' => $baseNote . ' - Credit Entry',
                    'supplier_id' => $supplier->id,
                    'ref_supplier_payment_id' => $supplierPayment?->id ?? null,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Transaction 2: Secondary - Additional accounting entries (if secondary accounts exist)
            // This can be extended for additional accounting requirements
            if ($supplier_payment_event->secondary_debit_account_id && $supplier_payment_event->secondary_credit_account_id) {
                // Add secondary transaction if needed
                // Example: Bank charges, fees, etc.
                // Uncomment and modify as per your accounting requirements
                /*
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_date' => $paymentDate,
                    'transaction_type' => 'SUPPLIER_PAYMENT_FEE',
                    'debit_account_id' => $supplier_payment_event->secondary_debit_account_id,
                    'credit_account_id' => $supplier_payment_event->secondary_credit_account_id,
                    'debit_amt' => $feeAmount,
                    'credit_amt' => $feeAmount,
                    'note' => $baseNote . ' - Bank Charges/Fees',
                    'supplier_id' => $supplier->id,
                    'ref_supplier_payment_id' => $supplierPayment?->id ?? null,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                */
            }

            // Create all transactions in the group with the same payment_code
            if (!empty($transactions)) {
                AcTransaction::insert($transactions);
                
                Log::info('Supplier payment accounting transactions created', [
                    'supplier_id' => $supplier->id ?? null,
                    'supplier_name' => $supplier->name ?? null,
                    'payment_amount' => $paymentAmount,
                    'payment_code' => $paymentCode,
                    'transaction_count' => count($transactions)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Supplier Payment Accounting Error', [
                'message' => $e->getMessage(),
                'supplier_id' => $supplier->id ?? null,
                'supplier_name' => $supplier->name ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

if (!function_exists('record_customer_advance_payment_accounting')) {
    function record_customer_advance_payment_accounting($customer, $paymentAmount, $paymentDate, $note = '', $paymentTypeId = null, $fromAccountId = null, $customerPayment = null)
    {
        try {
            $user = auth()->user();
            $customer_advance_payment_event = AcEventMapping::where('event_name', 'customer_advance_payment')->first();
            $paymentType = DbPaymentType::where('id', $paymentTypeId)->first();


            if (!$customer_advance_payment_event) {
                Log::error('Customer advance payment event mapping not found', [
                    'customer_id' => $customer->id ?? null,
                    'customer_name' => $customer->name ?? null
                ]);
                return;
            }

            // Generate payment code for this group of transactions (CA = Customer Advance)
            $paymentCode = generate_payment_code('CA');
            $baseNote = "Customer Advance Payment from {$customer->name} - {$note}";
            $transactions = [];

            // Transaction 1: Debit Entry - From Account (Cash/Bank/etc - the account from which payment is received)
            // Transaction 2: Credit Entry - Customer Advance Account (Liability - money owed to customer)
            $debitAccountId = $paymentType->debit_account_id; // From Account (Cash/Bank)
            $creditAccountId = $customer_advance_payment_event->credit_account_id; // Customer Advance Account

            if ($debitAccountId && $creditAccountId && $paymentAmount > 0) {
                // Entry 1: Debit Entry
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_date' => $paymentDate,
                    'transaction_type' => 'CUSTOMER_ADVANCE_PAYMENT',
                    'debit_account_id' => $paymentType->debit_account_id,
                    'credit_account_id' => null,
                    'debit_amt' => $paymentAmount,
                    'credit_amt' => null,
                    'note' => $baseNote . ' - Debit Entry',
                    'customer_id' => $customer->id,
                    'ref_customer_payment_id' => $customerPayment?->id ?? null,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Entry 2: Credit Entry
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_date' => $paymentDate,
                    'transaction_type' => 'CUSTOMER_ADVANCE_PAYMENT',
                    'debit_account_id' => null,
                    'credit_account_id' => $creditAccountId,
                    'debit_amt' => null,
                    'credit_amt' => $paymentAmount,
                    'note' => $baseNote . ' - Credit Entry',
                    'customer_id' => $customer->id,
                    'ref_customer_payment_id' => $customerPayment?->id ?? null,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Transaction 2: Secondary - Additional accounting entries (if secondary accounts exist)
            // This can be extended for additional accounting requirements
            if ($customer_advance_payment_event->secondary_debit_account_id && $customer_advance_payment_event->secondary_credit_account_id) {
                // Add secondary transaction if needed
                // Example: Bank charges, fees, etc.
                // Uncomment and modify as per your accounting requirements
                /*
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_date' => $paymentDate,
                    'transaction_type' => 'CUSTOMER_ADVANCE_PAYMENT_FEE',
                    'debit_account_id' => $customer_advance_payment_event->secondary_debit_account_id,
                    'credit_account_id' => $customer_advance_payment_event->secondary_credit_account_id,
                    'debit_amt' => $feeAmount,
                    'credit_amt' => $feeAmount,
                    'note' => $baseNote . ' - Bank Charges/Fees',
                    'customer_id' => $customer->id,
                    'ref_customer_payment_id' => $customerPayment?->id ?? null,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                */
            }

            // Create all transactions in the group with the same payment_code
            if (!empty($transactions)) {
                AcTransaction::insert($transactions);
                
                Log::info('Customer advance payment accounting transactions created', [
                    'customer_id' => $customer->id ?? null,
                    'customer_name' => $customer->name ?? null,
                    'payment_amount' => $paymentAmount,
                    'payment_code' => $paymentCode,
                    'transaction_count' => count($transactions)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Customer Advance Payment Accounting Error', [
                'message' => $e->getMessage(),
                'customer_id' => $customer->id ?? null,
                'customer_name' => $customer->name ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

if (!function_exists('record_customer_due_payment_accounting')) {
    function record_customer_due_payment_accounting(
        $customer, $paymentAmount, $paymentDate, $note = '', $paymentTypeId = null, 
        $fromAccountId = null, $customerPayment = null, $order = null
    )
    {
        try {
            $user = auth()->user();
            $customer_due_payment_event = AcEventMapping::where('event_name', 'customer_due_payment')->first();
            $paymentType = DbPaymentType::where('id', $paymentTypeId)->first();

            if (!$customer_due_payment_event) {
                Log::error('Customer advance payment event mapping not found', [
                    'customer_id' => $customer->id ?? null,
                    'customer_name' => $customer->name ?? null
                ]);
                return;
            }

            // Generate payment code for this group of transactions (CA = Customer Advance)
            $paymentCode = generate_payment_code('CA');
            $baseNote = "Customer Due Payment from {$customer->name} - {$note}";
            $transactions = [];

            // Transaction 1: Debit Entry - From Account (Cash/Bank/etc - the account from which payment is received)
            // Transaction 2: Credit Entry - Customer Advance Account (Liability - money owed to customer)
            $debitAccountId = $paymentType->debit_account_id; // Into Account (Cash/Bank)
            $creditAccountId = $customer_due_payment_event->credit_account_id; // account receivable credit account

            if ($debitAccountId && $creditAccountId && $paymentAmount > 0) {
                // Entry 1: Debit Entry
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_date' => $paymentDate,
                    'transaction_type' => 'CUSTOMER_DUE_PAYMENT',
                    'debit_account_id' => $paymentType->debit_account_id,
                    'credit_account_id' => null,
                    'debit_amt' => $paymentAmount,
                    'credit_amt' => null,
                    'note' => $baseNote . ' - Debit Entry',
                    'customer_id' => $customer->id,
                    'ref_customer_payment_id' => $customerPayment?->id ?? null,
                    'ref_sales_id' => $order?->id ?? null,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Entry 2: Credit Entry
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,
                    'payment_code' => $paymentCode,
                    'transaction_date' => $paymentDate,
                    'transaction_type' => 'CUSTOMER_DUE_PAYMENT',
                    'debit_account_id' => null,
                    'credit_account_id' => $creditAccountId,
                    'debit_amt' => null,
                    'credit_amt' => $paymentAmount,
                    'note' => $baseNote . ' - Credit Entry',
                    'customer_id' => $customer->id,
                    'ref_customer_payment_id' => $customerPayment?->id ?? null,
                    'ref_sales_id' => $order?->id ?? null,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Transaction 2: Secondary - Additional accounting entries (if secondary accounts exist)
            // This can be extended for additional accounting requirements
                if ($customer_due_payment_event->secondary_debit_account_id && $customer_due_payment_event->secondary_credit_account_id) {
                // Add secondary transaction if needed
                // Example: Bank charges, fees, etc.
                // Uncomment and modify as per your accounting requirements
                /*
                $transactions[] = [
                    'store_id' => $user->store_id ?? null,  
                    'payment_code' => $paymentCode,
                    'transaction_date' => $paymentDate,
                    'transaction_type' => 'CUSTOMER_DUE_PAYMENT_FEE',
                    'debit_account_id' => $customer_due_payment_event->secondary_debit_account_id,
                    'credit_account_id' => $customer_due_payment_event->secondary_credit_account_id,
                    'debit_amt' => $feeAmount,
                    'credit_amt' => $feeAmount,
                    'note' => $baseNote . ' - Bank Charges/Fees',
                    'customer_id' => $customer->id,
                    'ref_customer_payment_id' => $customerPayment?->id ?? null,
                    'created_by' => substr($user->name, 0, 50),
                    'creator' => $user->id,
                    'slug' => uniqid() . time(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                */
            }

            // Create all transactions in the group with the same payment_code
            if (!empty($transactions)) {
                AcTransaction::insert($transactions);
                
                Log::info('Customer due payment accounting transactions created', [
                    'customer_id' => $customer->id ?? null,
                    'customer_name' => $customer->name ?? null,
                    'payment_amount' => $paymentAmount,
                    'payment_code' => $paymentCode,
                    'transaction_count' => count($transactions)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Customer Due Payment Accounting Error', [
                'message' => $e->getMessage(),
                'customer_id' => $customer->id ?? null,
                'customer_name' => $customer->name ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

if (!function_exists('calc_customer_balance')) {
    function calc_customer_balance($customerId)
    {
        try {
            $customer = Customer::find($customerId);
            $totalPaid = DbCustomerPayment::where('customer_id', $customer->id)->sum('payment');
            $totalDue = ProductOrder::where('customer_id', $customer->id)->sum('due_amount');

            if ($totalDue > $totalPaid) {
                $balance = $totalDue - $totalPaid;
            } else {
                $balance = $totalPaid - $totalDue;
            }
            $customer->balance = $balance;
            $customer->available_advance = $balance;
            
            $customer->save();
            return $balance;
        } catch (\Exception $e) {
            Log::error('Customer Balance Calculation Error', [
                'message' => $e->getMessage(),
                'customer_id' => $customer->id ?? null,
                'customer_name' => $customer->name ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}