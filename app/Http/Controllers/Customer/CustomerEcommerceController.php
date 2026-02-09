<?php

namespace App\Http\Controllers\Customer;

use DataTables;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Models\EmailConfigure;
use App\Mail\UserVerificationEmail;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CustomerEcommerceController extends Controller
{
    public function addNewCustomerEcommerce()
    {
        return view('backend.customer_ecommerce.create');
    }

    public function saveNewCustomerEcommerce(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            // 'code' => ['required', 'string', 'max:255', 'unique:customer_sources,code'],
        ]);

        $image = null;
        if ($request->hasFile('image')) {
            $get_image = $request->file('image');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('userProfileImages/');
            $get_image->move($location, $image_name);
            $image = "userProfileImages/" . $image_name;
        }

        $data = User::create([
            'name' => request()->name,
            'phone' => request()->phone,
            'email' => request()->email,
            'address' => request()->address,
            'password' => \Illuminate\Support\Facades\Hash::make(request()->password),
            'image' => $image ?? null,
            'user_type' => 3,
            'verification_code' => Str::random(6),

            'status' => 1,
            'created_at' => Carbon::now()
        ]);
   
        $emailConfig = EmailConfigure::where('status', 1)->orderBy('id', 'desc')->first();

        if (!$emailConfig) {
            return response()->json(['error' => 'No active email configuration found.']);
        }

        $userEmail = trim(request()->email);

        if (!$userEmail) {
            return response()->json(['error' => 'No email provided.']);
        }
        // Set mail config dynamically
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => trim($emailConfig->host), // e.g., smtp.gmail.com
            'mail.mailers.smtp.port' => $emailConfig->port,
            'mail.mailers.smtp.username' => $emailConfig->email,
            'mail.mailers.smtp.password' => $emailConfig->password,
            'mail.mailers.smtp.encryption' => $emailConfig->encryption == 1 ? 'tls' : ($emailConfig->encryption == 2 ? 'ssl' : null),
            'mail.from.address' => $emailConfig->email,
            'mail.from.name' => $emailConfig->mail_from_name,
        ]);

        try {
            Mail::to($userEmail)->send(new UserVerificationEmail($data));
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), '❌ Mail error');
        }

        Toastr::success('Added successfully!', 'Success');
        return back();
    }

    public function viewAllCustomerEcommerce(Request $request)
    {
        if ($request->ajax()) {
            $data = User::where('user_type', 3)
                ->orderBy('id', 'desc')  // Order by ID in descending order
                ->get();

            return Datatables::of($data)
                ->editColumn('status', function ($data) {
                    if ($data->status == 1) {
                        return 'Active';
                    } else {
                        return 'Inactive';
                    }
                })
                ->addColumn('active_status', function ($data) {
                    $activity = UserActivity::where('user_id', $data->id)->first();

                    if ($activity && $activity->last_seen) {
                        $diff = Carbon::now()->diffInMinutes($activity->last_seen);
                        if ($diff < 1) {
                            return '<span class="badge" style="background: linear-gradient(90deg, #00c853 0%, #43e97b 100%); color: #fff; font-weight: 600; border-radius: 12px; padding: 6px 14px; font-size: 14px;"><i class="fas fa-circle" style="color:#fff; margin-right:6px;"></i>Active now</span>';
                        } elseif ($diff < 2) {
                            return '<span class="badge" style="background: linear-gradient(90deg, #ff9800 0%, #ffc107 100%); color: #fff; font-weight: 600; border-radius: 12px; padding: 6px 14px; font-size: 14px;">
                                <i class="fas fa-clock" style="color:#fff; margin-right:6px;"></i>
                                Last seen ' . $diff . ' min ago
                            </span>';
                        } else {
                            UserActivity::where('user_id', $data->id)->delete();
                            return '<span class="badge" style="background: linear-gradient(90deg, #434343 0%, #262626 100%); color: #fff; font-weight: 600; border-radius: 12px; padding: 6px 14px; font-size: 14px;"><i class="fas fa-circle" style="color:#888; margin-right:6px;"></i>Offline</span>';
                        }
                    } else {
                        return '<span class="badge" style="background: linear-gradient(90deg, #434343 0%, #262626 100%); color: #fff; font-weight: 600; border-radius: 12px; padding: 6px 14px; font-size: 14px;"><i class="fas fa-circle" style="color:#888; margin-right:6px;"></i>Offline</span>';
                    }
                })
                ->addColumn('name', function ($data) {
                    return $data->name ? $data->name : 'N/A';
                })
                ->editColumn('image', function ($data) {
                    if ($data->image && file_exists(public_path($data->image))) {
                        return $data->image;
                    }
                })
                ->addColumn('phone', function ($data) {
                    return $data->phone ? $data->phone : 'N/A';
                })
                ->addColumn('email', function ($data) {
                    return $data->email ? $data->email : 'N/A';
                })
                ->addColumn('address', function ($data) {
                    return $data->address ? $data->address : 'N/A';
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('edit/customer-ecommerce') . '/' . $data->id . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'active_status'])
                ->make(true);
        }
        return view('backend.customer_ecommerce.view');
    }


    public function editCustomerEcommerce($slug)
    {
        $data = User::where('id', $slug)->first();
        return view('backend.customer_ecommerce.edit', compact('data'));
    }

    public function updateCustomerEcommerce(Request $request)
    {
        $user = User::findOrFail($request->id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users,email,' . $user->id . ',id'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ]);

        $image = $user->image;

        if ($request->hasFile('image')) {
            // delete old image if exists
            if ($user->image && file_exists(public_path($user->image))) {
                unlink(public_path($user->image));
            }

            $get_image = $request->file('image');
            $image_name = Str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('userProfileImages/');
            $get_image->move($location, $image_name);
            $image = "userProfileImages/" . $image_name;
        }

        $user->update([
            'name' => $request->name ?? $user->name,
            'phone' => $request->phone ?? $user->phone,
            'email' => $request->email ?? $user->email,
            'address' => $request->address ?? $user->address,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
            'image' => $image ?? $user->image,
            'user_type' => 3,
            'status' => 1,
            'updated_at' => Carbon::now()
        ]);

        $data = $user;
   
        // $emailConfig = EmailConfigure::where('status', 1)->orderBy('id', 'desc')->first();

        // if (!$emailConfig) {
        //     return response()->json(['error' => 'No active email configuration found.']);
        // }

        // $userEmail = trim(request()->email);

        // if (!$userEmail) {
        //     return response()->json(['error' => 'No email provided.']);
        // }
        // // Set mail config dynamically
        // config([
        //     'mail.default' => 'smtp',
        //     'mail.mailers.smtp.transport' => 'smtp',
        //     'mail.mailers.smtp.host' => trim($emailConfig->host), // e.g., smtp.gmail.com
        //     'mail.mailers.smtp.port' => $emailConfig->port,
        //     'mail.mailers.smtp.username' => $emailConfig->email,
        //     'mail.mailers.smtp.password' => $emailConfig->password,
        //     'mail.mailers.smtp.encryption' => $emailConfig->encryption == 1 ? 'tls' : ($emailConfig->encryption == 2 ? 'ssl' : null),
        //     'mail.from.address' => $emailConfig->email,
        //     'mail.from.name' => $emailConfig->mail_from_name,
        // ]);

        // // logger('Mail config:', config('mail.mailers.smtp'));

        // try {
        //     Mail::to($userEmail)->send(new UserVerificationEmail($data));
        //     return response()->json(['success' => '✅ Email sent!']);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => '❌ Mail error: ' . $e->getMessage()]);
        // }

        Toastr::success('Updated Successfully', 'Success!');
        return view('backend.customer_ecommerce.edit', compact('data'));
    }


    public function deleteCustomerEcommerce($slug)
    {
        $data = user::where('id', $slug)->first();

        if ($data->image && file_exists(public_path($data->image))) {
            unlink(public_path($data->image));
        }

        $data->delete();

        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}
