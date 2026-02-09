<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Outlet\Models\CustomerSourceType;
use App\Http\Controllers\Customer\Models\CustomerCategory;
use App\Http\Controllers\Customer\Models\Customer;
use App\Http\Controllers\Customer\Models\CustomerContactHistory;
use App\Http\Controllers\Customer\Models\CustomerNextContactDate;
use App\Models\User;

class CustomerNextContactDateController extends Controller
{

    public function addNewCustomerNextContactDate()
    {
        $customers = Customer::where('status', 'active')->get();
        $users = User::where('status', 1)->get();
        return view('backend.customer_next_contact_date.create', compact('customers', 'users'));
    }

    public function saveNewCustomerNextContactDate(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'customer_id' => ['required'],
            'next_date' => ['required'],

        ], [
            'customer_id.required' => 'customer is required.',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->date)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        // $customer_category = CustomerCategory::where('id', request()->customer_category_id)->first();
        // $customer_source_type = CustomerSourceType::where('id', request()->customer_source_type_id)->first();
        // dd(5);

        CustomerNextContactDate::insert([
            'customer_id' => request()->customer_id ?? '',
            'employee_id' => request()->employee_id ?? '',
            'next_date' => request()->next_date,
            'contact_status' => request()->contact_status ?? 'pending',

            'creator' => auth()->user()->id,
            'slug' => $slug . time(),
            'status' => 'active',
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Added successfully!', 'Success');
        return back();

    }

    public function viewAllCustomerNextContactDate(Request $request)
    {
        if ($request->ajax()) {
            $data = CustomerNextContactDate::with(['customer'])
                                        ->orderBy('id', 'DESC')
                                        ->get();

            return Datatables::of($data)
                // ->editColumn('status', function ($data) {
                //     return $data->status == "active" ? 'Active' : 'Inactive';
                // })
                // ->editColumn('created_at', function ($data) {
                //     return date("Y-m-d", strtotime($data->created_at));
                // })
                ->addIndexColumn()
                ->addColumn('customer', function ($data) {
                    return $data->customer ? $data->customer->name : 'N/A';
                })
                ->editColumn('contact_status', function ($data) {
                    switch ($data->contact_status) {
                        case 'pending':
                            return 'Pending';
                        case 'missed':
                            return 'Missed';
                        case 'done':
                            return 'Done';
                        default:
                            return 'Unknown'; 
                    }
                })
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . url('edit/customer-next-contact-date') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.customer_next_contact_date.view');
    }


    public function editCustomerNextContactDate($slug)
    {
        $data = CustomerNextContactDate::where('slug', $slug)->first();
        $customers = Customer::where('status', 'active')->get();
        $users = User::where('status', 1)->get();
        return view('backend.customer_next_contact_date.edit', compact('data', 'customers', 'users'));
    }

    public function updateCustomerNextContactDate(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'customer_id' => ['required'],

        ], [
            'customer_id.required' => 'customer is required.',
        ]);



        // Check if the selected product_warehouse_room_id exists for the selected product_warehouse_id        
        $data = CustomerNextContactDate::where('id', request()->customer_next_contact_date_id)->first();


        $data->customer_id = request()->customer_id ?? $data->customer_id;        
        $data->employee_id = request()->employee_id ?? $data->employee_id;        
        $data->next_date = request()->next_date ?? $data->next_date;
        $data->contact_status = request()->contact_status ?? $data->contact_status;


        $data->creator = auth()->user()->id;
        $data->status = request()->status ?? $data->status;
        $data->updated_at = Carbon::now();
        $data->save();

        Toastr::success('Successfully Updated', 'Success!');
        return redirect()->route('ViewAllCustomerContactHistories');
    }


    public function deleteCustomerNextContactDate($slug)
    {
        // dd($slug);
        $data = CustomerNextContactDate::where('slug', $slug)->first();
        
        // $data->status = 'inactive';
        $data->delete();
        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}
