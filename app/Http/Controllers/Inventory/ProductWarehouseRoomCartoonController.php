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

class ProductWarehouseRoomCartoonController extends Controller
{
    public function addNewProductWarehouseRoomCartoon()
    {
        $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        $productWarehouseRooms = ProductWarehouseRoom::where('status', 'active')->get();
        return view('backend.product_warehouse_room_cartoon.create', compact('productWarehouses', 'productWarehouseRooms'));
    }

    public function saveNewProductWarehouseRoomCartoon(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'product_warehouse_id' => ['required'],
            'product_warehouse_room_id' => ['required'],
            // 'code' => ['required', 'string', 'max:255', 'unique:product_warehouse_rooms,code'],
        ], [
            'title.required' => 'title is required.',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);


        $productWarehouseRoom = ProductWarehouseRoom::where('id', request()->product_warehouse_room_id)->first();

        $lastCartoon = ProductWarehouseRoomCartoon::where('product_warehouse_id', request()->product_warehouse_id)
            ->where('product_warehouse_room_id', request()->product_warehouse_room_id)
            ->orderBy('id', 'desc')
            ->first();
        $baseCode = $productWarehouseRoom->code . '1000';
        $cartoonCode = $lastCartoon ? $lastCartoon->code + 1 : $baseCode;


        // dd(5);

        ProductWarehouseRoomCartoon::insert([
            'product_warehouse_id' => request()->product_warehouse_id,
            'product_warehouse_room_id' => request()->product_warehouse_room_id,
            'title' => request()->title,
            'description' => request()->description ?? '',
            'code' => $cartoonCode,
            'creator' => auth()->user()->id,
            'slug' => $slug . time(),
            'status' => 'active',
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Product Warehouse Room Cartoon has been added successfully!', 'Success');
        return back();
        // return redirect()->back()->with('success', 'Product Warehouse has been added successfully!');
        // return redirect()->back()->with('error', 'An error occurred!');
    }

    public function viewAllProductWarehouseRoomCartoon(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('product_warehouse_rooms')
                ->join('product_warehouses', 'product_warehouse_rooms.product_warehouse_id', '=', 'product_warehouses.id')
                ->leftJoin('product_warehouse_room_cartoons', 'product_warehouse_rooms.id', '=', 'product_warehouse_room_cartoons.product_warehouse_room_id')
                ->select(
                    'product_warehouse_room_cartoons.slug', // Use cartoon slug here
                    'product_warehouses.title as warehouse_title',
                    'product_warehouse_rooms.title as room_title',
                    'product_warehouse_room_cartoons.title as cartoon_title',
                    'product_warehouse_room_cartoons.code as cartoon_code',
                    'product_warehouse_room_cartoons.description as description',
                    'product_warehouse_room_cartoons.status as status',
                    'product_warehouse_room_cartoons.created_at'
                )
                ->whereNotNull('product_warehouse_room_cartoons.title') // Exclude records with null cartoon_title
                ->where('product_warehouse_room_cartoons.title', '!=', '') // Exclude empty cartoon_title
                // ->where('product_warehouse_room_cartoons.status', 'active') // Filter for active cartoons
                ->orderBy('product_warehouse_room_cartoons.id', 'desc') // Order by cartoon id
                ->get();

            return Datatables::of($data)
                ->editColumn('status', function ($data) {
                    return $data->status == "active" ? 'Active' : 'Inactive';
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d", strtotime($data->created_at));
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . url('edit/product-warehouse-room-cartoon') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.product_warehouse_room_cartoon.view');
    }




    public function editProductWarehouseRoomCartoon($slug)
    {
        $data = ProductWarehouseRoomCartoon::where('slug', $slug)->first();
        $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        $productWarehouseRooms = ProductWarehouseRoom::where('product_warehouse_id', $data->product_warehouse_id)->where('status', 'active')->get();
        return view('backend.product_warehouse_room_cartoon.edit', compact('data', 'productWarehouses', 'productWarehouseRooms'));
    }

    public function updateProductWarehouseRoomCartoon(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'product_warehouse_id' => ['required'],
            'product_warehouse_room_id' => ['required'],
            // 'code' => ['required', 'string', 'max:255', 'unique:product_warehouse_rooms,code'],
        ]);

        // Check if the selected product_warehouse_room_id exists for the selected product_warehouse_id
        $roomExists = DB::table('product_warehouse_rooms')
            ->where('product_warehouse_id', request()->product_warehouse_id)
            ->where('id', $request->product_warehouse_room_id)
            ->exists();

        // If the room does not exist for the selected warehouse, return an error message
        if (!$roomExists) {
            Toastr::error('The selected room does not belong to the specified warehouse.', 'Error!');
            return redirect()->back();
        }

        $data = ProductWarehouseRoomCartoon::where('id', request()->cartoon_id)->first();



        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);


        $data->title = request()->title ?? $data->title;
        $data->product_warehouse_id = request()->product_warehouse_id ?? $data->product_warehouse_id;
        $data->product_warehouse_room_id = request()->product_warehouse_room_id ?? $data->product_warehouse_room_id;
        $data->description = request()->description ?? $data->description;
        if ($data->title != $request->title) {
            $data->slug = $slug . time();
        }

        $data->creator = auth()->user()->id;
        $data->status = request()->status ?? $data->status;
        $data->updated_at = Carbon::now();
        $data->save();

        Toastr::success('Product Warehouse Room Cartoon Has been Updated', 'Success!');
        return redirect()->route('EditProductWarehouseRoomCartoon', $data->slug);
    }


    public function deleteProductWarehouseRoomCartoon($slug)
    {
        $data = ProductWarehouseRoomCartoon::where('slug', $slug)->first();

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();
        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }


    public function getProductWarehouseRoomCartoons(Request $request) {
        $roomId = request()->product_warehouse_room_id;

        if ($roomId) {
            $cartoons = ProductWarehouseRoomCartoon::where('product_warehouse_room_id', $roomId)
                ->where('status', 'active')
                ->get();

            return response()->json($cartoons);
        }

        return response()->json([], 400); // Return an empty array if no room is provided
    }
}
