<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoom;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductWarehouseRoomController extends Controller
{
    public function addNewProductWarehouseRoom()
    {
        $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        return view('backend.product_warehouse_room.create', compact('productWarehouses'));
    }

    public function saveNewProductWarehouseRoom(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'product_warehouse_id' => ['required'],
            // 'code' => ['required', 'string', 'max:255', 'unique:product_warehouse_rooms,code'],
        ], [
            'title.required' => 'title is required.',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);


        $productWarehouse = ProductWarehouse::where('id', request()->product_warehouse_id)->first();

        $lastRoom = ProductWarehouseRoom::where('product_warehouse_id', request()->product_warehouse_id)
            ->orderBy('id', 'desc')
            ->first();
        $baseCode = $productWarehouse->code . '1000';
        $roomCode = $lastRoom ? $lastRoom->code + 1 : $baseCode;

        // dd(request()->all());
        $image = null;
        if ($request->hasFile('image')) {

            $get_image = $request->file('image');
            $image_name = Str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('productWarehouseRoomImages/'); // Folder path within the public directory

            // Check if the image is an SVG or other types
            if ($get_image->getClientOriginalExtension() == 'svg') {
                $get_image->move($location, $image_name);
            } else {
                // Create and save the image using Intervention Image
                $image = Image::make($get_image)->encode('jpg', 60); // You can change encoding format and quality
                $image->save($location . $image_name);
            }

            // Save the image path in the database (relative to the public folder)
            $image = "productWarehouseRoomImages/" . $image_name;
        }
        // dd(5);

        ProductWarehouseRoom::insert([
            'title' => request()->title,
            'product_warehouse_id' => request()->product_warehouse_id,
            'code' => $roomCode,
            'description' => request()->description,
            'image' => $image,
            'creator' => auth()->user()->id,
            'slug' => $slug . time(),
            'status' => 'active',
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Product Warehouse Room has been added successfully!', 'Success');
        return back();
        // return redirect()->back()->with('success', 'Product Warehouse has been added successfully!');
        // return redirect()->back()->with('error', 'An error occurred!');
    }

    public function viewAllProductWarehouseRoom(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('product_warehouse_rooms')
                ->join('product_warehouses', 'product_warehouse_rooms.product_warehouse_id', '=', 'product_warehouses.id')
                // ->where('product_warehouse_rooms.status', 'active') // Only select 'active' status
                ->select('product_warehouse_rooms.*', 'product_warehouses.title as warehouse_title', 'product_warehouse_rooms.title as room_title')
                ->orderBy('product_warehouse_rooms.id', 'desc')
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
                    $btn = '<a href="' . url('edit/product-warehouse-room') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.product_warehouse_room.view');
    }



    public function editProductWarehouseRoom($slug)
    {
        $data = ProductWarehouseRoom::where('slug', $slug)->first();
        $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        return view('backend.product_warehouse_room.edit', compact('data', 'productWarehouses'));
    }

    public function updateProductWarehouseRoom(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'product_warehouse_id' => ['required'],
            // 'code' => ['required', 'string', 'max:255', 'unique:product_warehouse_rooms,code'],
        ]);

        $data = ProductWarehouseRoom::where('id', request()->product_warehouse_room_id)->first();

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


        $data->title = request()->title ?? $data->title;
        $data->product_warehouse_id = request()->product_warehouse_id ?? $data->product_warehouse_id;
        // $data->code = request()->code ?? $data->code;
        $data->description = request()->description ?? $data->description;
        $data->image = $image;
        if ($data->title != $request->title) {
            $data->slug = $slug . time();
        }

        $data->creator = auth()->user()->id;
        $data->status = request()->status ?? $data->status;
        $data->updated_at = Carbon::now();
        $data->save();

        Toastr::success('Product Warehouse Room Has been Updated', 'Success!');
        // return view('backend.product_warehouse_room.edit', compact('data'));
        return redirect()->route('EditProductWarehouseRoom', $data->slug);
    }


    public function deleteProductWarehouseRoom($slug)
    {
        $data = ProductWarehouseRoom::where('slug', $slug)->first();


        // Check if the room has any associated cartoons
        if ($data->productWarehouseRoomCartoon()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete this product warehouse room because it has associated cartoons.'
            ], 400); // Return 400 error for validation failure
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


    public function getProductWarehouseRooms(Request $request)
    {
        // dd(request()->all());
        $warehouseId = request()->product_warehouse_id;
        $rooms = ProductWarehouseRoom::where('product_warehouse_id', $warehouseId)->get();
        return response()->json($rooms);
    }
}
