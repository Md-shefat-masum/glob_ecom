<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Yajra\DataTables\DataTables;
class ProductSizeController extends Controller
{
    public function addNewProductSize()
    {
        return view('backend.product_size.create');
    }

    public function saveNewProductSize(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

       
        $data = ProductSize::create([
            'name' => request()->name,
            'created_at' => Carbon::now()
        ]);
   
        Toastr::success('Added successfully!', 'Success');
        return back();
    }

    public function viewAllProductSize(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductSize::orderBy('id', 'desc')
                ->get();

            return Datatables::of($data)
                ->addColumn('name', function ($data) {
                    return $data->name ? $data->name : 'N/A';
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('edit/product-size') . '/' . $data->id . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.product_size.view');
    }


    public function editProductSize($slug)
    {
        $data = ProductSize::where('id', $slug)->first();
        return view('backend.product_size.edit', compact('data'));
    }

    public function updateProductSize(Request $request)
    {
        $data = ProductSize::findOrFail($request->id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data->update([
            'name' => request()->name ?? $data->name,
            'created_at' => Carbon::now()
        ]);
   
        
        Toastr::success('Updated Successfully', 'Success!');
        return view('backend.product_size.edit', compact('data'));
    }


    public function deleteProductSize($slug)
    {
        $data = ProductSize::where('id', $slug)->first();
        $data->delete();

        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}
