<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoom;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoomCartoon;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductWarehouseController extends Controller
{
    public function addNewProductWarehouse()
    {
        return view('backend.product_warehouse.create');
    }

    public function saveNewProductWarehouse(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            // 'code' => ['required', 'string', 'max:255', 'unique:product_warehouses,code'],
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        $lastWarehouse = ProductWarehouse::orderBy('id', 'desc')->first();
        $code = $lastWarehouse ? $lastWarehouse->code + 1 : 1000;


        $image = null;
        if ($request->hasFile('image')) {

            $get_image = $request->file('image');
            $image_name = Str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('productWarehouseImages/'); // Folder path within the public directory

            // Check if the image is an SVG or other types
            if ($get_image->getClientOriginalExtension() == 'svg') {
                $get_image->move($location, $image_name);
            } else {
                // Create and save the image using Intervention Image
                $image = Image::make($get_image)->encode('jpg', 60); // You can change encoding format and quality
                $image->save($location . $image_name);
            }

            // Save the image path in the database (relative to the public folder)
            $image = "productWarehouseImages/" . $image_name;
        }
        // dd(5);

        ProductWarehouse::insert([
            'title' => request()->title,
            'code' => $code,
            'address' => request()->address,
            'description' => request()->description,
            'image' => $image,
            'creator' => auth()->user()->id,
            'slug' => $slug . time(),
            'status' => 'active',
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Product Warehouse has been added successfully!', 'Success');
        return back();
        // return redirect()->back()->with('success', 'Product Warehouse has been added successfully!');
        // return redirect()->back()->with('error', 'An error occurred!');
    }

    public function viewAllProductWarehouse(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::table('product_warehouses')
                ->orderBy('product_warehouses.id', 'desc')
                ->get();

            return Datatables::of($data)
                ->editColumn('status', function ($data) {
                    if ($data->status == "active") {
                        return 'Active';
                    } else {
                        return 'Inactive';
                    }
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d", strtotime($data->created_at));
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('edit/product-warehouse') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.product_warehouse.view');
    }


    public function editProductWarehouse($slug)
    {
        $data = ProductWarehouse::where('slug', $slug)->first();
        return view('backend.product_warehouse.edit', compact('data'));
    }

    public function updateProductWarehouse(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            // 'code' => ['required', 'string', 'max:255', 'unique:product_warehouses,code'],
        ]);

        $data = ProductWarehouse::where('id', request()->product_warehouse_id)->first();

        $image = $data->image;
        if ($request->hasFile('image')) {

            if ($data->image != '' && file_exists(public_path($data->image))) {
                unlink(public_path($data->image));
            }

            $get_image = $request->file('image');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('productWarehouseImages/');
            $get_image->move($location, $image_name);
            $image = "productWarehouseImages/" . $image_name;
        }

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);


        $data->title = request()->title ?? $data->title;
        $data->address = request()->address ?? $data->address;
        $data->description = request()->description ?? $data->description;
        $data->image = $image;
        if ($data->title != $request->title) {
            $data->slug = $slug . time();
        }
        $data->creator = auth()->user()->id;
        $data->status = request()->status ?? $data->status;
        $data->updated_at = Carbon::now();
        $data->save();

        Toastr::success('Product Warehouse Has been Updated', 'Success!');
        return view('backend.product_warehouse.edit', compact('data'));
    }


    public function deleteProductWarehouse($slug)
    {
        $data = ProductWarehouse::where('slug', $slug)->first();

        if ($data->productWarehouseRoom()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete this warehouse because it has associated rooms.'
            ], 400); // Sending error message with HTTP status 400
        }

        if ($data->image) {
            if (file_exists(public_path($data->image)) && $data->is_demo == 0) {
                unlink(public_path($data->image));
            }
        }

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();
        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }


    public function getWarehouseRooms(Request $request) {
        $rooms = ProductWarehouseRoom::where('product_warehouse_id', request()->warehouse_id)->get();
        return response()->json($rooms);
    }

    public function getWarehouseRoomCartoons(Request $request) {
        $cartoons = ProductWarehouseRoomCartoon::where('product_warehouse_room_id', request()->warehouse_room_id)
                                            ->where('product_warehouse_id', request()->warehouse_id)
                                             ->get();
        return response()->json($cartoons);
    }


    public function apiGetetWarehouseRooms(Request $request, $warehouseId) {
        $rooms = ProductWarehouseRoom::where('product_warehouse_id', $warehouseId)->get();
        return response()->json($rooms);
    }

    public function apiGetetWarehouseRoomCartoons(Request $request, $warehouseId, $roomId) {
        $cartoons = ProductWarehouseRoomCartoon::where('product_warehouse_room_id', $roomId)
            ->where('product_warehouse_id', $warehouseId)
            ->get();
        return response()->json($cartoons);
    }

}
