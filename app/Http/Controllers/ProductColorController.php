<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Color;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Yajra\DataTables\DataTables;
class ProductColorController extends Controller
{
    public function addNewProductColor()
    {
        return view('backend.product_color.create');
    }

    public function saveNewProductColor(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255']
        ]);

       
        $data = Color::create([
            'name' => request()->name,
            'code' => request()->code,
            'created_at' => Carbon::now()
        ]);
   
        Toastr::success('Added successfully!', 'Success');
        return back();
    }

    public function viewAllProductColor(Request $request)
    {
        if ($request->ajax()) {
            $data = Color::orderBy('id', 'desc')
                ->get();

            return Datatables::of($data)
                ->addColumn('name', function ($data) {
                    return $data->name ? $data->name : 'N/A';
                })
                ->addColumn('code', function ($data) {
                    return $data->code ? $data->code : 'N/A';
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('edit/product-color') . '/' . $data->id . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.product_color.view');
    }


    public function editProductColor($slug)
    {
        $data = Color::where('id', $slug)->first();
        return view('backend.product_color.edit', compact('data'));
    }

    public function updateProductColor(Request $request)
    {
        $data = color::findOrFail($request->id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255']
        ]);

        $data->update([
            'name' => request()->name ?? $data->name,
            'code' => request()->code ?? $data->code,
            'created_at' => Carbon::now()
        ]);
   
        
        Toastr::success('Updated Successfully', 'Success!');
        return view('backend.product_color.edit', compact('data'));
    }


    public function deleteProductColor($slug)
    {
        $data = Color::where('id', $slug)->first();
        $data->delete();

        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}
