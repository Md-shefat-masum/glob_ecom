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


class CustomerCategoryController extends Controller
{
    public function addNewCustomerCategory()
    {
        return view('backend.customer_category.create');
    }

    public function saveNewCustomerCategory(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            // 'code' => ['required', 'string', 'max:255', 'unique:customer_sources,code'],
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);


        CustomerCategory::insert([

            'title' => request()->title,
            'description' => request()->description,
                        
            'creator' => auth()->user()->id,
            'slug' => $slug . time(),
            'status' => 'active',
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Added successfully!', 'Success');
        return back();
    }

    public function viewAllCustomerCategory(Request $request)
    {   
        
        if ($request->ajax()) {
            $data = CustomerCategory::orderBy('id', 'desc')  // Order by ID in descending order
                ->get();


            return Datatables::of($data)
                ->editColumn('status', function ($data) {
                    if ($data->status == "active") {
                        return 'Active';
                    } else {
                        return 'Inactive';
                    }
                })
                // ->editColumn('created_at', function ($data) {
                //     return date("Y-m-d", strtotime($data->created_at));
                // })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('edit/customer-category') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.customer_category.view');
    }


    public function editCustomerCategory($slug)
    {
        $data = CustomerCategory::where('slug', $slug)->first();
        return view('backend.customer_category.edit', compact('data'));
    }

    public function updateCustomerCategory(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $data = CustomerCategory::where('id', request()->customer_source_id)->first();

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);


        $data->title = request()->title ?? $data->title;        
        $data->description = request()->description ?? $data->description;
        
        if ($data->title != $request->title) {
            $data->slug = $slug . time();
        }

        $data->creator = auth()->user()->id;
        $data->status = request()->status ?? $data->status;
        $data->updated_at = Carbon::now();
        $data->save();

        Toastr::success('Updated Successfully', 'Success!');
        return view('backend.customer_category.edit', compact('data'));
    }


    public function deleteCustomerCategory($slug)
    {
        $data = CustomerCategory::where('slug', $slug)->first();

        // if ($data->productWarehouseRoom()->count() > 0) {
        //     return response()->json([
        //         'error' => 'Cannot delete this warehouse because it has associated rooms.'
        //     ], 400); // Sending error message with HTTP status 400
        // }

        // if ($data->image) {
        //     if (file_exists(public_path($data->image)) && $data->is_demo == 0) {
        //         unlink(public_path($data->image));
        //     }
        // }

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();
        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}
