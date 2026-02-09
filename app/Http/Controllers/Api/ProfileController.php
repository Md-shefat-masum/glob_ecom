<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCardResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserCard;
use Carbon\Carbon;
use Image;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function userProfileUpdateWeb(Request $request){

        $userImage = User::where('id', auth()->user()->id)->first()->image;
        if ($request->hasFile('image')){

            if($userImage && file_exists(public_path($userImage))){
                unlink(public_path($userImage));
            }

            $get_image = $request->file('image');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('userProfileImages/');
            Image::make($get_image)->save($location . $image_name, 50);
            $userImage = "userProfileImages/" . $image_name;
        }

        $user_id = auth()->user()->id;
        $name = $request->name;
        $phone = $request->phone;
        $email = $request->email;

        if($email != '' && User::where('id', auth()->user()->id)->first()->email != $email){
            $email_check = User::where('email', $email)->first();
            if($email_check){
                return response()->json([
                    'success'=> false,
                    'message'=> 'Email already used ! Please use another Email'
                ]);
            }
        }
        if($phone != '' && User::where('id', auth()->user()->id)->first()->phone != $phone){
            $phone_check = User::where('phone', $phone)->first();
            if($phone_check){
                return response()->json([
                    'success'=> false,
                    'message'=> 'Mobile No already used ! Please use another Mobile No'
                ]);
            }
        }

        if(($email == '' || $email == NULL) && ($phone == '' || $phone == NULL)){
            return response()->json([
                'success'=> false,
                'message'=> 'Both Email & Phone Cannot be Null'
            ]);
        } else {
            User::where('id', $user_id)->update([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'image' => $userImage,
                'updated_at' => Carbon::now()
            ]);

            UserAddress::insert([
                'user_id' => $user_id,
                'address_type' => "Home",
                'name' => $name,
                // 'address' => $request->address,
                // 'country' => $request->country,
                // 'city' => $request->city,
                // 'state' => $request->state,
                // 'post_code' => $request->post_code,
                'phone' => $phone,
                'slug' => str::random(5) . time(),
                'created_at' => Carbon::now()
            ]);

            return response()->json([
                'success'=> true,
                'message'=> 'Profile Updated Successfully'
            ]);
        }

    }

    public function userChangePasswordWeb(Request $request){

        $user_id = auth()->user()->id;
        $userInfo = User::where('id', $user_id)->first();
        $current_password = $request->current_password;
        $new_password = $request->new_password;

        if($current_password != '' && $new_password != ''){
            if(Hash::check($current_password, $userInfo->password)){
                User::where('id', $user_id)->update([
                    'password' => Hash::make($new_password),
                    'updated_at' => Carbon::now()
                ]);

                return response()->json([
                    'success'=> true,
                    'message'=> 'Password Changed Successfully'
                ]);

            } else {
                return response()->json([
                    'success'=> false,
                    'message'=> 'Your Current Password is Incorrect'
                ]);
            }
        } else {
            return response()->json([
                'success'=> false,
                'message'=> 'Password Cannot be Null'
            ]);
        }

    }

    public function addNewCard(Request $request){
        UserCard::insert([
            'user_id' => auth()->user()->id,
            'type' => $request->type,
            'card_name' => $request->card_name,
            'card_no' => $request->card_no,
            'expiry_date' => $request->expiry_date,
            'code' => $request->code,
            'default' => $request->default,
            'slug' => str::random(5) . time(),
            'created_at' => Carbon::now()
        ]);

        return response()->json([
            'success'=> true,
            'message'=> 'New Card Added Successfully'
        ]);
    }

    public function getMyCards(){
        $cards = UserCard::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'date' => UserCardResource::collection($cards)
        ], 200);
    }

    public function deleteMyCard(Request $request){
        UserCard::where('user_id', auth()->user()->id)->where('id', $request->card_id)->delete();
        return response()->json([
            'success' => true,
            'message' => "Card Removed Successfully"
        ], 200);
    }

    public function updateMyCard(Request $request){
        UserCard::where('user_id', auth()->user()->id)->where('id', $request->card_id)->update([
            'type' => $request->type,
            'card_name' => $request->card_name,
            'card_no' => $request->card_no,
            'expiry_date' => $request->expiry_date,
            'code' => $request->code,
            'default' => $request->default,
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success'=> true,
            'message'=> 'Card Updated Successfully'
        ]);
    }


    public function addNewAddress(Request $request){

        UserAddress::where('user_id', auth()->user()->id)->where('address_type', $request->address_type)->delete();

        UserAddress::insert([
            'user_id' => auth()->user()->id,
            'address_type' => $request->address_type,
            'name' => $request->name,
            'address' => $request->address,
            'country' => $request->country,
            'city' => $request->city,
            'state' => $request->state,
            'post_code' => $request->post_code,
            'phone' => $request->phone,
            'slug' => str::random(5) . time(),
            'created_at' => Carbon::now()
        ]);

        return response()->json([
            'success'=> true,
            'message'=> 'New Aaddress Added Successfully'
        ]);
    }

    public function getAllAddress(){
        $address = UserAddress::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'date' => $address
        ], 200);
    }

    public function updateMyAddress(Request $request){
        UserAddress::where('user_id', auth()->user()->id)->where('id', $request->address_id)->update([
            'address_type' => $request->address_type,
            'name' => $request->name,
            'address' => $request->address,
            'country' => $request->country,
            'city' => $request->city,
            'state' => $request->state,
            'post_code' => $request->post_code,
            'phone' => $request->phone,
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success'=> true,
            'message'=> 'Aaddress Updated Successfully'
        ]);
    }

    public function deleteMyAddress(Request $request){
        UserAddress::where('user_id', auth()->user()->id)->where('id', $request->address_id)->delete();
        return response()->json([
            'success' => true,
            'message' => "Aaddress Removed Successfully"
        ], 200);
    }

    public function sendAccountDeleteRequest(){
        User::where('id', auth()->user()->id)->update([
            'delete_request_submitted' => 1,
            'delete_request_submitted_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Account Delete Request Submitted"
        ], 200);
    }

    public function uploadProfilePhoto(Request $request){
        if ($request->hasFile('image')){
            $get_attachment = $request->file('image');
            $attachment_name = $get_attachment->getClientOriginalName();
            $location = public_path('userProfileImages/');
            $get_attachment->move($location, $attachment_name);
        }
        return response()->json([
            'success' => true,
            'message' => 'File Uploaded Successfully',
        ], 200);
    }
}
