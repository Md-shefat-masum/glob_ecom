<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Resources\OrderResource;
use App\Models\OrderPayment;
use App\Models\PaymentGateway;
use App\Models\User;
use Carbon\Carbon;
use DGvai\SSLCommerz\SSLCommerz;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $gatewayInfo = PaymentGateway::where('id', 1)->first();
        config([
            'sslcommerz.store.id' => $gatewayInfo ? $gatewayInfo->api_key : '',
            'sslcommerz.store.password' => $gatewayInfo ? $gatewayInfo->secret_key : '',
            'sslcommerz.sandbox' => ($gatewayInfo && $gatewayInfo->live == 1) ? false : true,
        ]);
    }

    public function orderPayment(Request $request)
    {
        $orderId = $request->order_id;
        $orderInfo = Order::where('id', $orderId)->first();
        if($orderInfo){
            $userInfo = User::where('id', $orderInfo->user_id)->first();
            $userEmailContact = ($userInfo->email != '' && $userInfo->email != NULL) ? $userInfo->email : $userInfo->phone;

            $sslc = new SSLCommerz();
            $sslc->amount($orderInfo->total)
                ->trxid($orderInfo->trx_id)
                ->product('Products of GenericCommerceV1')
                ->customer($userInfo->name, $userEmailContact);
            return $sslc->make_payment();
        } else {
            return response()->json([
                'success' => false,
                'message' => "No Order Found",
            ], 200);
        }
    }

    public function success(Request $request)
    {
        $validate = SSLCommerz::validate_payment($request);
        if($validate)
        {
            // dd($request->all());
            if(strpos($request->card_type, "BKash") !== false){
                $payment_method = 2;
            } else if(strpos($request->card_type, "Nagad") !== false){
                $payment_method = 3;
            } else {
                $payment_method = 4;
            }

            $bankID = $request->bank_tran_id; //KEEP THIS bank_tran_id FOR REFUNDING ISSUE
            $trxID = $request->tran_id;

            $orderInfo = Order::where('trx_id', $trxID)->first();
            $orderInfo->bank_tran_id = $bankID;
            $orderInfo->payment_method = $payment_method;
            $orderInfo->payment_status = 1; //success
            $orderInfo->save();

            OrderPayment::insert([
                'order_id' => $orderInfo->id,
                'payment_through' => "SSL COMMERZ",
                'tran_id' => $request->tran_id,
                'val_id' => $request->val_id,
                'amount' => $request->amount,
                'card_type' => $request->card_type,
                'store_amount' => $request->store_amount,
                'card_no' => $request->card_no,
                'status' => $request->status,
                'tran_date' => $request->tran_date,
                'currency' => $request->currency,
                'card_issuer' => $request->card_issuer,
                'card_brand' => $request->card_brand,
                'card_sub_brand' => $request->card_sub_brand,
                'card_issuer_country' => $request->card_issuer_country,
                'created_at' => Carbon::now()
            ]);

            // return response()->json([
            //     'success' => true,
            //     'message' => "Order Payment is Successfull",
            //     'data' => new OrderResource(Order::where('id', $orderInfo->id)->first())
            // ], 200);
            return redirect()->to('https://GenericCommerceV1.com/checkout/orderconfirmations');

        }
    }

    public function failure(Request $request)
    {
        //  do the database works
        //  also same goes for cancel()
        //  for IPN() you can leave it untouched or can follow
        //  official documentation about IPN from SSLCommerz Panel

        $bankID = $request->bank_tran_id; //KEEP THIS bank_tran_id FOR REFUNDING ISSUE
        $trxID = $request->tran_id;

        $orderInfo = Order::where('trx_id', $trxID)->first();
        $orderInfo->bank_tran_id = $bankID;
        $orderInfo->payment_method = 0;
        $orderInfo->payment_status = 2; //Failed
        $orderInfo->save();

        OrderPayment::insert([
            'order_id' => $orderInfo->id,
            'payment_through' => "SSL COMMERZ",
            'tran_id' => $request->tran_id,
            'val_id' => $request->val_id,
            'amount' => $request->amount,
            'card_type' => $request->card_type,
            'store_amount' => $request->store_amount,
            'card_no' => $request->card_no,
            'status' => $request->status,
            'tran_date' => $request->tran_date,
            'currency' => $request->currency,
            'card_issuer' => $request->card_issuer,
            'card_brand' => $request->card_brand,
            'card_sub_brand' => $request->card_sub_brand,
            'card_issuer_country' => $request->card_issuer_country,
            'created_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Order Payment is Failed",
            'data' => new OrderResource(Order::where('id', $orderInfo->id)->first())
        ], 200);


    }

    public function cancel(Request $request)
    {

        //  do the database works
        //  for IPN() you can leave it untouched or can follow
        //  official documentation about IPN from SSLCommerz Panel

        $bankID = $request->bank_tran_id; //KEEP THIS bank_tran_id FOR REFUNDING ISSUE
        $trxID = $request->tran_id;

        $orderInfo = Order::where('trx_id', $trxID)->first();
        $orderInfo->bank_tran_id = $bankID;
        $orderInfo->payment_method = 0;
        $orderInfo->payment_status = 2; //Failed
        $orderInfo->save();

        OrderPayment::insert([
            'order_id' => $orderInfo->id,
            'payment_through' => "SSL COMMERZ",
            'tran_id' => $request->tran_id,
            'val_id' => $request->val_id,
            'amount' => $request->amount,
            'card_type' => $request->card_type,
            'store_amount' => $request->store_amount,
            'card_no' => $request->card_no,
            'status' => $request->status,
            'tran_date' => $request->tran_date,
            'currency' => $request->currency,
            'card_issuer' => $request->card_issuer,
            'card_brand' => $request->card_brand,
            'card_sub_brand' => $request->card_sub_brand,
            'card_issuer_country' => $request->card_issuer_country,
            'created_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Order Payment is Cancelled",
            'data' => new OrderResource(Order::where('id', $orderInfo->id)->first())
        ], 200);

    }

    public function refund($bankID)
    {
        /**
         * SSLCommerz::refund($bank_trans_id, $amount [,$reason])
         */

        $refund = SSLCommerz::refund($bankID, $refund_amount=100);

        if($refund->status)
        {
            /**
             * States:
             * success : Refund request is initiated successfully
             * failed : Refund request is failed to initiate
             * processing : The refund has been initiated already
            */

            $state  = $refund->refund_state;

            /**
             * RefID will be used for post-refund status checking
            */

            $refID  = $refund->ref_id;

            /**
             *  To get all the outputs
            */

            dd($refund->output);
        }
        else
        {
            return $refund->message;
        }
    }

    public function check_refund_status($refID)
    {
        $refund = SSLCommerz::query_refund($refID);

        if($refund->status)
        {
            /**
             * States:
             * refunded : Refund request has been proceeded successfully
             * processing : Refund request is under processing
             * cancelled : Refund request has been proceeded successfully
            */

            $state  = $refund->refund_state;

            /**
             * RefID will be used for post-refund status checking
            */

            $refID  = $refund->ref_id;

            /**
             *  To get all the outputs
            */

            dd($refund->output);
        }
        else
        {
            return $refund->message;
        }
    }

    public function get_transaction_status($trxID)
    {
        $query = SSLCommerz::query_transaction($trxID);

        if($query->status)
        {
            dd($query->output);
        }
        else
        {
            $query->message;
        }
    }
}
