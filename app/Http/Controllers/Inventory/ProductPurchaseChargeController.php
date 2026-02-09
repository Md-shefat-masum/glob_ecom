<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Inventory\Models\ProductSupplier;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoom;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoomCartoon;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrder;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrderProduct;
use App\Http\Controllers\Inventory\Models\ProductPurchaseQuotation;
use App\Http\Controllers\Inventory\Models\ProductPurchaseQuotationProduct;
use App\Http\Controllers\Inventory\Models\ProductStock;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOtherCharge;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductPurchaseChargeController extends Controller
{
    public function addNewPurchaseProductCharge()
    {
        // $products = Product::where('status', 'active')->get();
        // $suppliers = ProductSupplier::where('status', 'active')->get();
        // $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        // $productWarehouseRooms = ProductWarehouseRoom::where('status', 'active')->get();
        // $productWarehouseRoomCartoons = ProductWarehouseRoomCartoon::where('status', 'active')->get();
        return view('backend.product_charge.create');
    }

    public function saveNewPurchaseProductCharge(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        // dd(5);

        ProductPurchaseOtherCharge::insert([
            'title' => request()->title,
            'type' => strtolower(request()->type),
            'creator' => auth()->user()->id,
            'slug' => $slug . time(),
            'status' => 'active',
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Other has been added successfully!', 'Success');
        return redirect()->route('ViewAllPurchaseProductCharge');
    }

    public function viewAllPurchaseProductCharge(Request $request)
    {
        if ($request->ajax()) {

            $data = ProductPurchaseOtherCharge::where('status', 'active')
                                                ->orderBy('id', 'desc') 
                                                ->get();

                // dd($data);
            return Datatables::of($data)
                // ->editColumn('creator', function ($data) {
                //     return $data->creator ? $data->creator->name : 'N/A'; // Access creator name
                // })
                // ->editColumn('is_ordered', function ($data) {
                //     return $data->is_ordered == 1 
                //         ? '<span class="text-success">Ordered</span>' 
                //         : '<span class="text-danger">Pending</span>';
                // })
                // ->editColumn('status', function ($data) {
                //     return $data->status == "active" ? 'Active' : 'Inactive';
                // })
                // ->editColumn('created_at', function ($data) {
                //     return date("Y-m-d", strtotime($data->created_at));
                // })
                ->addIndexColumn()
                // ->addColumn('action', function ($data) {
                //     $btn = '<a href="' . url('edit/purchase-product/quotation') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                //     $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                //     return $btn;
                // })
                ->addColumn('action', function ($data) {
                    $btn = '<div class="dropdown">';
                    $btn .= '<button class="btn-sm btn-primary dropdown-toggle rounded" type="button" id="actionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    $btn .= 'Action';
                    $btn .= '</button>';
                    $btn .= '<div class="dropdown-menu" aria-labelledby="actionDropdown">';
                    $btn .= '<a class="dropdown-item" href="' . url('edit/purchase-product/charge') . '/' . $data->slug . '"><i class="fas fa-edit"></i> Edit</a>';                    
                    $btn .= '<a class="dropdown-item deleteBtn" href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" ><i class="fas fa-trash-alt"></i> Delete</a>';
                    $btn .= '</div>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.product_charge.view');
    }


    public function editPurchaseProductCharge($slug)
    {
        // dd($slug);
        $data = ProductPurchaseOtherCharge::where('slug', $slug)->first();
        // $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        // $productWarehouseRooms = ProductWarehouseRoom::where('product_warehouse_id', $data->product_warehouse_id)->where('status', 'active')->get();
        // $productWarehouseRoomCartoon = ProductWarehouseRoomCartoon::where('product_warehouse_id', $data->product_warehouse_id)->where('product_warehouse_room_id', $data->product_warehouse_room_id)->where('status', 'active')->get();
        // $suppliers = ProductSupplier::where('status', 'active')->get();
        return view('backend.product_charge.edit', compact('data'));
    }



    public function updatePurchaseProductCharge(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
        ]);
        
        $other_charge = ProductPurchaseOtherCharge::where('id', request()->other_charge_id)->first();

        $user = auth()->user();

        $other_charge->title = request()->title;
        $other_charge->type = request()->type;
        $other_charge->creator = $user->id;
        $other_charge->status = 'active';        
        $other_charge->updated_at = Carbon::now();
        $other_charge->save();

        Toastr::success('Updation Has been Successfull', 'Success!');        
        return redirect()->route('ViewAllPurchaseProductCharge');
    }



    public function deletePurchaseProductCharge($slug)
    {
        $data = ProductPurchaseOtherCharge::where('slug', $slug)->first();

        $data->delete();        

        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }

}
