<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Account\Models\DbPaymentType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymenttypeController extends Controller
{
    public function addNewPaymentType()
    {
        // $payment_types = DbPaymentType::where('status', 'active')->get();
        // $users = User::where('status', 1)->get();
        // return view('backend.paymenttype.create', compact('customer_categories', 'customer_source_types', 'users'));
        return view('backend.paymenttype.create');
    }

    public function saveNewPaymentType(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'payment_type' => ['required', 'string', 'max:100'],
        ], [
            'payment_type.required' => 'Payment type is required.',
            'payment_type.max' => 'Payment type must not exceed 100 characters.',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->payment_type)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        // $customer_category = CustomerCategory::where('id', request()->customer_category_id)->first();
        // $customer_source_type = CustomerSourceType::where('id', request()->customer_source_type_id)->first();
        // dd(5);

        DbPaymentType::insert([
            'store_id' => 1,
            'payment_type' => request()->payment_type ?? '',

            'creator' => auth()->user()->id,
            'slug' => $slug . time(),
            'status' => 'active',
            'created_at' => Carbon::now('Asia/Dhaka')
        ]);

        Toastr::success('Added successfully!', 'Success');
        return back();
    }

    // public function viewAllPaymentType(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $data = DbPaymentType::with('user')
    //             ->where('status', 'active')
    //             ->orderBy('id', 'DESC')
    //             ->get();

    //         $col_data = Datatables::of($data)
    //             // ->editColumn('status', function ($data) {
    //             //     return $data->status == "active" ? 'Active' : 'Inactive';
    //             // })
    //             // ->editColumn('created_at', function ($data) {
    //             //     return date("Y-m-d", strtotime($data->created_at));
    //             // })
    //             // ->addIndexColumn()
    //             ->addColumn('payment_type', function ($data) {
    //                 return $data->payment_type ? $data->payment_type : 'N/A';
    //             });
    //         // ->addColumn('customer_source_type', function ($data) {
    //         //     return $data->customerSourceType ? $data->customerSourceType->title : 'N/A';
    //         // })
    //         // ->addColumn('reference_by', function ($data) {
    //         //     return $data->referenceBy ? $data->referenceBy->name : 'N/A';
    //         // })

    //             $col_data->addColumn('user', function ($data) {
    //                 return $data->user ? $data->user->name : 'N/A';
    //             });

    //             // return $col_data;
    //             $col_data->addColumn('action', function ($data) {
    //                 $btn = '<a href="' . url('edit/payment-type') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
    //                 $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
    //                 return $btn;
    //             })
    //             ;

    //         return $col_data->rawColumns(['action'])
    //         ->make(true);
    //     }
    //     return view('backend.paymenttype.view');
    // }



    public function viewAllPaymentType(Request $request)
    {
        if ($request->ajax()) {
            $data = DbPaymentType::with('user');
                // ->orderBy('id', 'desc');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user', function ($data) {
                    return $data->user ? $data->user->name : 'N/A';
                })
                ->addColumn('total_amount', function ($data) {
                    $amount = $data->total_amount ?? 0;
                    $formatted = number_format($amount, 2);
                    $color = $amount >= 0 ? 'text-success' : 'text-danger';
                    return '<span class="' . $color . ' font-weight-bold">৳ ' . $formatted . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . url('edit/payment-type') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['total_amount', 'action'])
                ->make(true);
        }
        return view('backend.paymenttype.view');
    }


    public function editPaymentType($slug)
    {
        $data = DbPaymentType::where('slug', $slug)->first();
        return view('backend.paymenttype.edit', compact('data'));
    }

    public function updatePaymentType(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'payment_type' => ['required', 'string', 'max:100'],
        ], [
            'payment_type.required' => 'Payment type is required.',
            'payment_type.max' => 'Payment type must not exceed 100 characters.',
        ]);

        // Check if the selected product_warehouse_room_id exists for the selected product_warehouse_id        
        $data = DbPaymentType::where('id', request()->paymenttype_id)->first();

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($data->payment_type)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        $data->store_id = request()->store_id ?? $data->store_id;
        $data->payment_type = request()->payment_type ?? $data->payment_type;
        $data->status = request()->status ?? $data->status;

        if ($data->payment_type != $request->payment_type) {
            $data->slug = $slug . time();
        }

        $data->creator = auth()->user()->id;
        $data->status = request()->status ?? $data->status;
        $data->updated_at = Carbon::now();
        $data->save();

        Toastr::success('Successfully Updated', 'Success!');
        return redirect()->route('ViewAllPaymentType');
    }


    public function deletePaymentType($slug)
    {
        $data = DbPaymentType::where('slug', $slug)->first();

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();

        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}
