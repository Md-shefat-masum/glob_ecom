<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailConfigure;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\SocialLoginResource;
use App\Models\User;
use App\Models\SmsGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserVerificationEmail;
use App\Mail\ForgetPasswordEmail;
use App\Models\SocialLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;

class AuthenticationController extends Controller
{
    const AUTHORIZATION_TOKEN = 'GenericCommerceV1-SBW7583837NUDD82';

    public function userRegistration(Request $request){
        if ($request->header('Authorization') == AuthenticationController::AUTHORIZATION_TOKEN) {

            if(!$request->name || !$request->username || !$request->password || !$request->address){
                return response()->json([
                    'success' => false,
                    'message' => 'All the Fields are required'
                ]);
            }

            $name = $request->name;
            $username = $request->username;
            $password = $request->password;
            $address = $request->address;
            $data = array();

            // check its a email or phone
            if (filter_var($username, FILTER_VALIDATE_EMAIL)) { // email

                User::where('email', $username)->where('email_verified_at', null)->delete();
                $checkExistingEmail = User::where('email', $username)->where('email_verified_at', '!=', null)->first();
                if($username != '' && $checkExistingEmail){
                    return response()->json([
                        'success'=> false,
                        'message'=> 'You are already registered! Try login',
                        'data' => $data
                    ]);
                } else {

                    $randomCode = rand(100000, 999999);
                    User::insert([
                        'name' => $name,
                        'email' => $username,
                        'password' => Hash::make($password),
                        'email_verified_at' => null,
                        'verification_code' => $randomCode,
                        'address' => $address,
                        'user_type' => 3,
                        'status' => 0,
                        'delete_request_submitted' => 0,
                        'delete_request_submitted_at' => NULL,
                        'created_at' => Carbon::now(),
                    ]);

                    $data['name'] = $name;
                    $data['email'] = $username;
                    $data['phone'] = null;
                    $data['email_verified_at'] = null;
                    // $data['user_type'] = 3;
                    $data['status'] = 0;
                    $data['image'] = null;
                    $data['address'] = $address;
                    $data['balance'] = 0;

                    try {

                        $emailConfig = EmailConfigure::where('status', 1)->orderBy('id', 'desc')->first();
                        if($emailConfig){

                            $decryption = "";
                            if($emailConfig){
                                $ciphering = "AES-128-CTR";
                                $options = 0;
                                $decryption_iv = '1234567891011121';
                                $decryption_key = "GenericCommerceV1";
                                $decryption = openssl_decrypt ($emailConfig->password, $ciphering, $decryption_key, $options, $decryption_iv);
                            }

                            config([
                                'mail.mailers.smtp.host' => $emailConfig ? $emailConfig->host : '',
                                'mail.mailers.smtp.port' => $emailConfig ? $emailConfig->port : '',
                                'mail.mailers.smtp.username' => $emailConfig ? $emailConfig->email : '',
                                'mail.mailers.smtp.password' => $decryption != "" ? $decryption : '',
                                'mail.mailers.smtp.encryption' => $emailConfig ? ($emailConfig->encryption == 1 ? 'tls' : ($emailConfig->encryption == 2 ? 'ssl' : '')) : '',
                            ]);

                            $mailData = array();
                            $mailData['code'] = $randomCode;
                            Mail::to(trim($username))->send(new UserVerificationEmail($mailData));

                            return response()->json([
                                'success' => true,
                                'message' => "Verification Email Sent",
                                'data' => $data
                            ], 200);

                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => "No Mail Server is Active Yet",
                                'data' => $data
                            ], 200);
                        }

                    } catch(\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => "Something Went Wrong while Sending Email",
                            'data' => $data
                        ], 200);
                    }
                }

            } else { // phone

                User::where('phone', $username)->where('email_verified_at', null)->delete();
                $checkExistingPhone = User::where('phone', $username)->where('email_verified_at', '!=', null)->first();
                if($username != '' && $checkExistingPhone){
                    return response()->json([
                        'success' => false,
                        'message' => 'Phone already used ! Please use another Mobile No',
                        'data' => $data
                    ]);
                } else {

                    $randomCode = rand(100000, 999999);
                    User::insert([
                        'name' => $name,
                        'email' => null,
                        'phone' => $username,
                        'password' => Hash::make($password),
                        'email_verified_at' => null,
                        'verification_code' => $randomCode,
                        'address' => $address,
                        'user_type' => 3,
                        'status' => 0,
                        'delete_request_submitted' => 0,
                        'delete_request_submitted_at' => NULL,
                        'created_at' => Carbon::now(),
                    ]);

                    $data['name'] = $name;
                    $data['email'] = null;
                    $data['phone'] = $username;
                    $data['email_verified_at'] = null;
                    // $data['user_type'] = 3;
                    $data['status'] = 0;
                    $data['image'] = null;
                    $data['address'] = $address;
                    $data['balance'] = 0;

                    $smsGateway = SmsGateway::where('status', 1)->first();
                    if($smsGateway && $smsGateway->provider_name == 'Reve'){
                        $response = Http::get($smsGateway->api_endpoint, [
                            'apikey' => $smsGateway->api_key,
                            'secretkey' => $smsGateway->secret_key,
                            "callerID" => $smsGateway->sender_id,
                            "toUser" => $username,
                            "messageContent" => "Verification Code is : ". $randomCode
                        ]);

                        if($response->status() == 200){
                            return response()->json([
                                'success' => true,
                                'message' => "Verification SMS Sent Successfully", //$response
                                'data' => $data
                            ], 200);

                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => "Failed to Send SMS",
                                'data' => $data
                            ], 200);
                        }

                    } elseif($smsGateway && $smsGateway->provider_name == 'ElitBuzz'){

                        $response = Http::get($smsGateway->api_endpoint, [
                            'api_key' => $smsGateway->api_key,
                            "type" => "text",
                            "contacts" => $username, //“88017xxxxxxxx,88018xxxxxxxx”
                            "senderid" => $smsGateway->sender_id,
                            "msg" => "Verification Code is : ". $randomCode
                        ]);

                        if($response->status() == 200){
                            return response()->json([
                                'success' => true,
                                'message' => "SMS Sent Successfully",
                                'data' => $data
                            ], 200);
                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => "Failed to Send SMS",
                                'data' => $data
                            ], 200);
                        }

                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => "No SMS Gateway is Active Yet",
                            'data' => $data
                        ], 200);
                    }
                }

            }

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function userVerification(Request $request){
        if ($request->header('Authorization') == AuthenticationController::AUTHORIZATION_TOKEN) {

            if(!$request->code || !$request->username || !$request->password){
                return response()->json([
                    'success' => false,
                    'message' => 'All the Fields are required'
                ]);
            }

            if(Auth::attempt(['email' => $request->username, 'password' => $request->password, 'verification_code' => $request->code]) || Auth::attempt(['phone' => $request->username, 'password' => $request->password, 'verification_code' => $request->code])){

                $user = Auth::user();

                User::where('id', $user->id)->update([
                    'email_verified_at' => Carbon::now(),
                    'status' => 1,
                    'updated_at' => Carbon::now(),
                ]);

                $data['token'] = $user->createToken('GenericCommerceV1')->plainTextToken;
                $data['name'] = $user->name;
                $data['email'] = $user->email;
                $data['phone'] = $user->phone;
                $data['email_verified_at'] = date("Y-m-d H:i:s", strtotime($user->email_verified_at));
                // $data['user_type'] = 3;
                $data['status'] = 1;
                $data['image'] = $user->image;
                $data['address'] = $user->address;
                $data['balance'] = $user->balance;

                return response()->json([
                    'success'=> true,
                    'message'=> 'Successfully Verified And Logged In',
                    'data' => $data
                ]);

            }
            else{
                $data = array();
                return response()->json([
                    'success'=> false,
                    'message'=> 'Wrong Verification Code',
                    'data' => $data
                ]);
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function userLogin(Request $request){

        if ($request->header('Authorization') == AuthenticationController::AUTHORIZATION_TOKEN) {

            if(!$request->username || !$request->password){
                return response()->json([
                    'success' => false,
                    'message' => 'All the Fields are required'
                ]);
            }

            if(Auth::attempt(['email' => $request->username, 'password' => $request->password]) || Auth::attempt(['phone' => $request->username, 'password' => $request->password])){

                $user = Auth::user();
                User::where('id', $user->id)->update([
                    'delete_request_submitted' => 0,
                    'delete_request_submitted_at' => NULL,
                ]);

                $data['token'] = $user->createToken('GenericCommerceV1')->plainTextToken;
                $data['name'] = $user->name;
                $data['email'] = $user->email;
                $data['phone'] = $user->phone;
                $data['email_verified_at'] = date("Y-m-d H:i:s", strtotime($user->email_verified_at));
                // $data['user_type'] = 3;
                $data['status'] = 1;
                $data['image'] = $user->image;
                $data['address'] = $user->address;
                $data['balance'] = $user->balance;

                return response()->json([
                    'success'=> true,
                    'message'=> 'Successfully Logged In',
                    'data' => $data
                ]);

            }
            else{
                return response()->json([
                    'success' => false,
                    'message' => 'Wrong Login Credentials',
                    'data' => array(),
                ]);
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function socialLoginCredentials(Request $request){
        if ($request->header('Authorization') == AuthenticationController::AUTHORIZATION_TOKEN) {

            $data = SocialLogin::where('id', 1)->first();

            return response()->json([
                'success' => false,
                'message' => 'Wrong Login Credentials',
                'data' => new SocialLoginResource($data),
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function socialLogin(Request $request){
        if ($request->header('Authorization') == AuthenticationController::AUTHORIZATION_TOKEN) {

            $provider = $request->provider_name;
            $token = $request->access_token;



            $providerUser = Socialite::driver($provider)->userFromToken($token);
            dd($providerUser);

            // $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $token;
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_POST, 0);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $response = curl_exec($ch);
            // $err = curl_error($ch);  //if you need
            // curl_close($ch);
            // $providerUser = json_decode($response);
            // print_r($providerUser);
            // exit();



            $user = User::where('provider_name', $provider)->where('provider_id', $providerUser->id)->first();
            if($user == null){
                $user = User::create([
                    'provider_name' => $provider,
                    'provider_id' => $providerUser->id,
                ]);
            }

            $data['token'] = $user->createToken('GenericCommerceV1')->plainTextToken;
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['phone'] = $user->phone;
            $data['email_verified_at'] = date("Y-m-d H:i:s", strtotime($user->email_verified_at));
            // $data['user_type'] = 3;
            $data['status'] = 1;
            $data['image'] = $user->image;
            $data['address'] = $user->address;
            $data['balance'] = $user->balance;

            return response()->json([
                'success'=> true,
                'message'=> 'Successfully Logged In',
                'data' => $data
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function forgetPassword(Request $request){
        if ($request->header('Authorization') == AuthenticationController::AUTHORIZATION_TOKEN) {

            if(!$request->username){
                return response()->json([
                    'success' => false,
                    'message' => 'Please Write Your Username'
                ]);
            }

            $username = $request->username;

            // check its a email or phone
            if (filter_var($username, FILTER_VALIDATE_EMAIL)) { // email

                $userInfo = User::where('email', $username)->first();
                if(!$userInfo){
                    return response()->json([
                        'success' => false,
                        'message' => 'Email does not Exist',
                        'data' => null
                    ]);
                } else {

                    $randomCode = rand(100000, 999999);
                    $userInfo->verification_code = $randomCode;
                    $userInfo->save();

                    try {

                        $emailConfig = EmailConfigure::where('status', 1)->orderBy('id', 'desc')->first();
                        if($emailConfig){

                            $decryption = "";
                            if($emailConfig){
                                $ciphering = "AES-128-CTR";
                                $options = 0;
                                $decryption_iv = '1234567891011121';
                                $decryption_key = "GenericCommerceV1";
                                $decryption = openssl_decrypt ($emailConfig->password, $ciphering, $decryption_key, $options, $decryption_iv);
                            }

                            config([
                                'mail.mailers.smtp.host' => $emailConfig ? $emailConfig->host : '',
                                'mail.mailers.smtp.port' => $emailConfig ? $emailConfig->port : '',
                                'mail.mailers.smtp.username' => $emailConfig ? $emailConfig->email : '',
                                'mail.mailers.smtp.password' => $decryption != "" ? $decryption : '',
                                'mail.mailers.smtp.encryption' => $emailConfig ? ($emailConfig->encryption == 1 ? 'tls' : ($emailConfig->encryption == 2 ? 'ssl' : '')) : '',
                            ]);

                            $mailData = array();
                            $mailData['code'] = $randomCode;
                            Mail::to(trim($username))->send(new ForgetPasswordEmail($mailData));

                            return response()->json([
                                'success' => true,
                                'message' => "Password Reset Email Sent",
                                'data' => null
                            ], 200);

                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => "No Mail Server is Active Yet",
                                'data' => null
                            ], 200);
                        }

                    } catch(\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => "Something Went Wrong while Sending Email",
                            'data' => null
                        ], 200);
                    }
                }

            } else {

                $userInfo = User::where('phone', $username)->first();
                if(!$userInfo){
                    return response()->json([
                        'success' => false,
                        'message' => 'Phone No. Not Found',
                        'data' => null
                    ]);
                } else {

                    $randomCode = rand(100000, 999999);
                    $userInfo->verification_code = $randomCode;
                    $userInfo->save();

                    $smsGateway = SmsGateway::where('status', 1)->first();
                    if($smsGateway && $smsGateway->provider_name == 'Reve'){
                        $response = Http::get($smsGateway->api_endpoint, [
                            'apikey' => $smsGateway->api_key,
                            'secretkey' => $smsGateway->secret_key,
                            "callerID" => $smsGateway->sender_id,
                            "toUser" => $username,
                            "messageContent" => "Verification Code is : ". $randomCode
                        ]);

                        if($response->status() == 200){
                            return response()->json([
                                'success' => true,
                                'message' => "Password Reset SMS Sent Successfully",
                                'data' => null
                            ], 200);

                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => "Failed to Send SMS",
                                'data' => null
                            ], 200);
                        }

                    } elseif($smsGateway && $smsGateway->provider_name == 'ElitBuzz'){

                        $response = Http::get($smsGateway->api_endpoint, [
                            'api_key' => $smsGateway->api_key,
                            "type" => "text",
                            "contacts" => $username, //“88017xxxxxxxx,88018xxxxxxxx”
                            "senderid" => $smsGateway->sender_id,
                            "msg" => "Verification Code is : ". $randomCode
                        ]);

                        if($response->status() == 200){
                            return response()->json([
                                'success' => true,
                                'message' => "Password Reset SMS Sent Successfully",
                                'data' => null
                            ], 200);
                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => "Failed to Send SMS",
                                'data' => null
                            ], 200);
                        }

                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => "No SMS Gateway is Active Yet",
                            'data' => null
                        ], 200);
                    }
                }
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function verifyResetCode(Request $request){
        if ($request->header('Authorization') == AuthenticationController::AUTHORIZATION_TOKEN) {

            if(!$request->username && !$request->code){
                return response()->json([
                    'success' => false,
                    'message' => 'Please Provide Username with Verification Code',
                    'data' => null
                ]);
            }

            $username = $request->username;
            $code = $request->code;
            $data = array();

            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {

                $userInfo = User::where('email', $username)->where('verification_code', $code)->first();
                if(!$userInfo){
                    return response()->json([
                        'success' => false,
                        'message' => 'Wrong Password Reset Code',
                        'data' => null
                    ]);
                } else {

                    $data['name'] = $userInfo->name;
                    $data['email'] = $userInfo->email;
                    $data['phone'] = $userInfo->phone;
                    $data['email_verified_at'] = date("Y-m-d H:i:s", strtotime($userInfo->email_verified_at));
                    $data['status'] = $userInfo->status;
                    $data['image'] = $userInfo->image;
                    $data['address'] = $userInfo->address;
                    $data['balance'] = $userInfo->balance;

                    return response()->json([
                        'success' => true,
                        'message' => "Code is Verified. Now Change Your Password",
                        'data' => $data
                    ], 200);

                }

            } else {

                $userInfo = User::where('phone', $username)->where('verification_code', $code)->first();
                if(!$userInfo){
                    return response()->json([
                        'success' => false,
                        'message' => 'Wrong Password Reset Code',
                        'data' => null
                    ]);
                } else {

                    $data['name'] = $userInfo->name;
                    $data['email'] = $userInfo->email;
                    $data['phone'] = $userInfo->phone;
                    $data['email_verified_at'] = date("Y-m-d H:i:s", strtotime($userInfo->email_verified_at));
                    $data['status'] = $userInfo->status;
                    $data['image'] = $userInfo->image;
                    $data['address'] = $userInfo->address;
                    $data['balance'] = $userInfo->balance;

                    return response()->json([
                        'success' => true,
                        'message' => "Code is Verified. Now Change Your Password",
                        'data' => $data
                    ], 200);

                }
            }

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function changePassword(Request $request){
        if ($request->header('Authorization') == AuthenticationController::AUTHORIZATION_TOKEN) {

            if(!$request->username && !$request->code){
                return response()->json([
                    'success' => false,
                    'message' => 'Please Provide Username with Verification Code',
                    'data' => null
                ]);
            }

            $username = $request->username;
            $password = $request->password;

            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {

                $userInfo = User::where('email', $username)->first();
                $userInfo->password = Hash::make($password);
                $userInfo->save();

                $data['name'] = $userInfo->name;
                $data['email'] = $userInfo->email;
                $data['phone'] = $userInfo->phone;
                $data['email_verified_at'] = date("Y-m-d H:i:s", strtotime($userInfo->email_verified_at));
                $data['status'] = $userInfo->status;
                $data['image'] = $userInfo->image;
                $data['address'] = $userInfo->address;
                $data['balance'] = $userInfo->balance;

                return response()->json([
                    'success' => true,
                    'message' => "Password Changed Successfully",
                    'data' => $data
                ], 200);

            } else {

                $userInfo = User::where('phone', $username)->first();
                $userInfo->password = Hash::make($password);
                $userInfo->save();

                $data['name'] = $userInfo->name;
                $data['email'] = $userInfo->email;
                $data['phone'] = $userInfo->phone;
                $data['email_verified_at'] = date("Y-m-d H:i:s", strtotime($userInfo->email_verified_at));
                $data['status'] = $userInfo->status;
                $data['image'] = $userInfo->image;
                $data['address'] = $userInfo->address;
                $data['balance'] = $userInfo->balance;

                return response()->json([
                    'success' => true,
                    'message' => "Password Changed Successfully",
                    'data' => $data
                ], 200);

            }

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

}
