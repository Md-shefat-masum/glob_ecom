<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoom;
use App\Http\Controllers\Inventory\Models\ProductSupplier;
use App\Http\Controllers\Inventory\Models\ProductSupplierContact;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductSupplierController extends Controller
{
    public function addNewProductSupplier()
    {
        $productSupplierContacts = ProductSupplierContact::where('status', 'active')->get();
        $supplier_type = DB::table('supplier_source_types')->where('status', 'active')->get();
        return view('backend.product_supplier.create', compact('productSupplierContacts', 'supplier_type'));
    }

    public function saveNewProductSupplier(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255', 'unique:product_supplier_contacts,contact_number'],
        ], [
            'name.required' => 'name is required.',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);


        $image = null;
        if ($request->hasFile('image')) {

            $get_image = $request->file('image');
            $image_name = Str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('productSupplierImages/'); // Folder path within the public directory

            // Check if the image is an SVG or other types
            if ($get_image->getClientOriginalExtension() == 'svg') {
                $get_image->move($location, $image_name);
            } else {
                // Create and save the image using Intervention Image
                $image = Image::make($get_image)->encode('jpg', 60); // You can change encoding format and quality
                $image->save($location . $image_name);
            }

            // Save the image path in the database (relative to the public folder)
            $image = "productSupplierImages/" . $image_name;
        }
        // dd(5);

        $product_supplier_id = ProductSupplier::insertGetId([
                                            'name' => request()->name,
                                            'supplier_type' => request()->supplier_type,
                                            'address' => request()->address,            
                                            'image' => $image,
                                            'creator' => auth()->user()->id,
                                            'slug' => $slug . uniqid() . time(),
                                            'status' => 'active',
                                            'created_at' => Carbon::now()
                                        ]);

        ProductSupplierContact::insert([
            'product_supplier_id' => $product_supplier_id,
            'contact_number' => request()->contact_number,
            'creator' => auth()->user()->id,
            'slug' => $slug . $product_supplier_id . time(),
            'status' => 'active',
            'created_at' => Carbon::now()
        ]);
        // 'product_warehouse_id' => request()->product_warehouse_id,

        Toastr::success('Product supplier has been added successfully!', 'Success');
        return back();
        // return redirect()->back()->with('success', 'Product Warehouse has been added successfully!');
        // return redirect()->back()->with('error', 'An error occurred!');
    }

    public function viewAllProductSupplier(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('product_suppliers')
                            ->leftJoin('product_supplier_contacts', 'product_suppliers.id', '=', 'product_supplier_contacts.product_supplier_id')
                            ->select(
                                'product_suppliers.*',
                                'product_supplier_contacts.contact_number'
                            )
                            ->orderBy('product_suppliers.id', 'desc')
                            ->get();
            // dd($data);

            return Datatables::of($data)
                ->editColumn('status', function ($data) {
                    return $data->status == "active" ? 'Active' : 'Inactive';
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d", strtotime($data->created_at));
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . url('edit/product-supplier') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.product_supplier.view');
    }



    public function editProductSupplier($slug)
    {
        $data = ProductSupplier::where('slug', $slug)->first();
        $product_supplier_contacts = ProductSupplierContact::where('status', 'active')->get();
        $supplier_type = DB::table('supplier_source_types')->where('status', 'active')->get();
        return view('backend.product_supplier.edit', compact('data', 'product_supplier_contacts','supplier_type'));
    }

    public function updateProductSupplier(Request $request)
    {
        // dd($request->all());
        $data = ProductSupplier::where('id', request()->product_supplier_id)->first();
        $product_supplier_contact = ProductSupplierContact::where('product_supplier_id', $data->id)->first();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_supplier_contacts', 'contact_number')->ignore($product_supplier_contact->id)
            ],
        ], [
            'name.required' => 'Name is required.',
            'contact_number.unique' => 'The contact number has already been taken.',
        ]);

        

        $image = $data->image;
        if ($request->hasFile('image')) {

            if ($data->image != '' && file_exists(public_path($data->image))) {
                unlink(public_path($data->image));
            }

            $get_image = $request->file('image');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('productWarehouseRoomImages/');
            $get_image->move($location, $image_name);
            $image = "productWarehouseRoomImages/" . $image_name;
        }

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);


        $data->name = request()->name ?? $data->name;
        $data->supplier_type = request()->supplier_type ?? $data->supplier_type;
        $data->address = request()->address ?? $data->address;
        $data->image = $image;
        if ($data->name != $request->name) {
            $data->slug = $slug . time();
        }
        $data->creator = auth()->user()->id;
        $data->status = request()->status ?? $data->status;
        $data->updated_at = Carbon::now();
        $data->save();

        
        $product_supplier_contact->contact_number = request()->contact_number ?? $product_supplier_contact->contact_number;
        $product_supplier_contact->save();


        // $data->product_warehouse_id = request()->product_warehouse_id ?? $data->product_warehouse_id;

        Toastr::success('Product Warehouse Has been Updated', 'Success!');
        // return view('backend.product_warehouse_room.edit', compact('data'));
        return redirect()->route('EditProductSupplier', $data->slug);
    }


    public function deleteProductSupplier($slug)
    {
        $data = ProductSupplier::where('slug', $slug)->first();

        if ($data->image) {
            if (file_exists(public_path($data->image)) && $data->is_demo == 0) {
                unlink(public_path($data->image));
            }
        }

        $product_supplier_contact = ProductSupplierContact::where('product_supplier_id', $data->id)->first();
        $product_supplier_contact->delete();

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();
        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}
