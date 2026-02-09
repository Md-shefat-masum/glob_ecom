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

class CustomerContactHistoryController extends Controller
{

    public function addNewCustomerContactHistory()
    {
        $customers = Customer::where('status', 'active')->get();
        $users = User::where('status', 1)->get();
        return view('backend.customer_contact_history.create', compact('customers', 'users'));
    }

    public function saveNewCustomerContactHistory(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'customer_id' => ['required'],
            'employee_id' => ['required'],

        ], [
            'customer_id.required' => 'customer is required.',
            'employee_id.required' => 'employee is required.',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->date)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        // $customer_category = CustomerCategory::where('id', request()->customer_category_id)->first();
        // $customer_source_type = CustomerSourceType::where('id', request()->customer_source_type_id)->first();
        // dd(5);

        CustomerContactHistory::insert([
            'customer_id' => request()->customer_id ?? '',
            'employee_id' => auth()->user()->id ?? '',
            'date' => Carbon::now('Asia/Dhaka'),
            'product_id' => request()->product_id ?? null,
            'product_name' => request()->product_name ?? null,
            'subject' => request()->subject ?? null,
            'note' => request()->note,
            'contact_history_status' => request()->contact_history_status,
            'priority' => request()->priority,

            'creator' => auth()->user()->id,
            'slug' => $slug . time(),
            'status' => 'active',
            'created_at' => Carbon::now()
        ]);

        if (request()->next_date && request()->next_date != '') {
            CustomerNextContactDate::insert([
                'customer_id' => request()->customer_id ?? '',
                'next_date' => request()->next_date,
                'contact_status' => 'pending',

                'creator' => auth()->user()->id,
                'slug' => $slug . time(),
                'status' => 'active',
                'created_at' => Carbon::now()
            ]);
        }

        Toastr::success('Added successfully!', 'Success');
        return back();
    }

    public function viewAllCustomerContactHistory(Request $request)
    {
        $user = auth()->user();

        if ($request->ajax()) {
            if ($user->user_type == 1) {
                $data = CustomerContactHistory::with(['customer', 'employee'])
                    ->orderBy('id', 'DESC')
                    ->get();
            }
            else {
                $data = CustomerContactHistory::with(['customer', 'employee'])
                    ->where('employee_id', $user->id)
                    ->orderBy('id', 'DESC')
                    ->get();
            }
            // dd($data->toArray());
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
                ->addColumn('employee', function ($data) {
                    return $data->employee ? $data->employee->name : 'N/A';
                })
                ->editColumn('contact_history_status', function ($data) {
                    switch ($data->contact_history_status) {
                        case 'planned':
                            return 'Planned';
                        case 'held':
                            return 'Held';
                        case 'not_held':
                            return 'Not Held';
                        default:
                            return 'Unknown';
                    }
                })
                ->editColumn('priority', function ($data) {
                    switch ($data->priority) {
                        case 'low':
                            return 'Low';
                        case 'normal':
                            return 'Normal';
                        case 'medium':
                            return 'Medium';
                        case 'high':
                            return 'High';
                        case 'urgent':
                            return 'Urgent';
                        case 'immediate':
                            return 'Immediate';
                        default:
                            return 'Unknown';
                    }
                })
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . url('edit/customer-contact-history') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.customer_contact_history.view');
    }


    public function editCustomerContactHistory($slug)
    {
        $data = CustomerContactHistory::where('slug', $slug)->first();
        $customers = Customer::where('status', 'active')->get();
        $users = User::where('status', 1)->get();
        $next_contact_data = CustomerNextContactDate::where('customer_id', $data->customer_id)
            ->where('next_date', '>=', now())->first();
        // dd($next_contact_data);

        return view('backend.customer_contact_history.edit', compact('data', 'customers', 'users', 'next_contact_data'));
    }

    public function updateCustomerContactHistory(Request $request)
    {
        $request->validate([
            'customer_id' => ['required'],
            'employee_id' => ['required'],

        ], [
            'customer_id.required' => 'customer is required.',
            'employee_id.required' => 'employee is required.',
        ]);

        // Check if the selected product_warehouse_room_id exists for the selected product_warehouse_id        
        $data = CustomerContactHistory::where('id', request()->customer_contact_history_id)->first();

        $data->customer_id = request()->customer_id ?? $data->customer_id;
        $data->employee_id = request()->employee_id ?? $data->employee_id;
        $data->date = request()->next_date ?? $data->next_date;
        $data->product_id = request()->product_id ?? $data->product_id;
        $data->product_name = request()->product_name ?? $data->product_name;
        $data->subject = request()->subject ?? $data->subject;
        $data->note = request()->note ?? $data->note;

        $data->contact_history_status = request()->contact_history_status ?? $data->contact_history_status;
        $data->priority = request()->priority ?? $data->priority;


        $data->creator = auth()->user()->id;
        $data->status = request()->status ?? $data->status;
        $data->updated_at = Carbon::now();
        $data->save();



        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->date)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        $next_contact_data = CustomerNextContactDate::where('customer_id', request()->customer_id)
            ->where('next_date', request()->next_date)
            ->exists();

        if (!$next_contact_data) {
            CustomerNextContactDate::insert([
                'customer_id' => request()->customer_id ?? '',
                'next_date' => request()->next_date,
                'contact_status' => 'pending',

                'creator' => auth()->user()->id,
                'slug' => $slug . time(),
                'status' => 'active',
                'created_at' => Carbon::now()
            ]);
        }


        Toastr::success('Successfully Updated', 'Success!');
        return redirect()->route('ViewAllCustomerContactHistories');
    }


    public function deleteCustomerContactHistory($slug)
    {
        $data = CustomerContactHistory::where('slug', $slug)->first();

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();
        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}
