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
use App\Models\User;

class CustomerController extends Controller
{
    public function addNewCustomer()
    {
        $customer_categories = CustomerCategory::where('status', 'active')->get();
        $customer_source_types = CustomerSourceType::where('status', 'active')->get();
        $users = User::where('status', 1)->get();
        return view('backend.customer.create', compact('customer_categories', 'customer_source_types', 'users'));
    }

    public function customers($customer_id = null)
    {

        $query = Customer::query();

        if ($customer_id) {
            $query->where(function($q) use ($customer_id) {
                $q->where('id', $customer_id)
                ->orWhere('slug', $customer_id);
            });
            $data = $query->first();
        } else {
            if (request()->q) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . request()->q . '%');
                    $q->orWhere('phone', 'like', '%' . request()->q . '%');
                });
            }
            $data = $query->limit(10)->get();
        }
        return response()->json($data);
    }

    public function customer_store()
    {
        request()->validate([
            'name' => ['required'],
            'phone' => ['required'],

        ], [
            'name.required' => 'name is required.',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->name)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        $data = Customer::create([
            "name" => request()->name,
            "phone" => request()->phone,
            "address" => request()->address,
            'slug' => $slug . time(),
        ]);

        return response()->json($data);
    }

    public function saveNewCustomer(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'name' => ['required'],
            'phone' => ['required'],

        ], [
            'name.required' => 'name is required.',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->name)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        // $customer_category = CustomerCategory::where('id', request()->customer_category_id)->first();
        // $customer_source_type = CustomerSourceType::where('id', request()->customer_source_type_id)->first();
        // dd(5);

        Customer::insert([
            'customer_category_id' => request()->customer_category_id ?? '',
            'customer_source_type_id' => request()->customer_source_type_id ?? '',
            'reference_by' => request()->reference_id ?? '',
            'name' => request()->name,
            'phone' => request()->phone,
            'email' => request()->email,
            'address' => request()->address,

            'creator' => auth()->user()->id,
            'slug' => $slug . time(),
            'status' => 'active',
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Added successfully!', 'Success');
        return back();
    }

    public function viewAllCustomer(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::where('status', 'active')
                ->with(['customerCategory', 'customerSourceType', 'referenceBy'])
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
                ->addColumn('customer_category', function ($data) {
                    return $data->customerCategory ? $data->customerCategory->title : 'N/A';
                })
                ->addColumn('customer_source_type', function ($data) {
                    return $data->customerSourceType ? $data->customerSourceType->title : 'N/A';
                })
                ->addColumn('reference_by', function ($data) {
                    return $data->referenceBy ? $data->referenceBy->name : 'N/A';
                })
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . url('edit/customers') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.customer.view');
    }


    public function editCustomer($slug)
    {
        $data = Customer::where('slug', $slug)->first();
        $customer_categories = CustomerCategory::where('status', 'active')->get();
        $customer_source_types = CustomerSourceType::where('status', 'active')->get();
        $users = User::where('status', 1)->get();
        return view('backend.customer.edit', compact('data', 'customer_categories', 'customer_source_types', 'users'));
    }

    public function updateCustomer(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => ['required'],
            'phone' => ['required'],

        ], [
            'name.required' => 'name is required.',
        ]);

        // Check if the selected product_warehouse_room_id exists for the selected product_warehouse_id        
        $data = Customer::where('id', request()->customer_id)->first();

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->name)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        $data->customer_category_id = request()->customer_category_id ?? $data->customer_category_id;
        $data->customer_source_type_id = request()->customer_source_type_id ?? $data->customer_source_type_id;
        $data->reference_by = request()->reference_id ?? $data->reference_by;
        $data->name = request()->name ?? $data->name;
        $data->phone = request()->phone ?? $data->phone;
        $data->email = request()->email ?? $data->email;
        $data->address = request()->address ?? $data->address;

        if ($data->name != $request->name) {
            $data->slug = $slug . time();
        }

        $data->creator = auth()->user()->id;
        $data->status = request()->status ?? $data->status;
        $data->updated_at = Carbon::now();
        $data->save();

        Toastr::success('Successfully Updated', 'Success!');
        return redirect()->route('ViewAllCustomer');
    }


    public function deleteCustomer($slug)
    {
        $data = Customer::where('slug', $slug)->first();

        // $data->delete();
        $data->status = 'inactive';
        $data->save();
        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}
