<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use App\Models\ProductSizeValue;
use Yajra\DataTables\DataTables;
use Brian2694\Toastr\Facades\Toastr;

class ProductSizeValueController extends Controller
{
    public function addNewProductSizeValue()
    {
        return view('backend.product_size_value.create');
    }

    public function saveNewProductSizeValue(Request $request)
    {

        $request->validate([
            'size' => ['required', 'string', 'max:255'],
            'name*' => ['required', 'array'],
            'name*.*' => ['required', 'string', 'max:255'],
            // 'value*' => ['required', 'array'],
            // 'value*.*' => ['required', 'string', 'max:255'],

        ]);

        foreach ($request->name as $name) {
            ProductSizeValue::create([
                'product_size_id' => $request->size,
                'name' => $name,
                // 'value' => $request->value[array_search($name, $request->name)],
                'created_at' => Carbon::now(),
            ]);
        }

        Toastr::success('Added successfully!', 'Success');
        return back();
    }

    public function viewAllProductSizeValue(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductSizeValue::with('productSize')->orderBy('id', 'desc')
                ->get();

            return Datatables::of($data)
                ->addColumn('product_size_id', function ($data) {
                    return $data->productSize->name ? $data->productSize->name  : 'N/A';
                })
                ->addColumn('name', function ($data) {
                    return $data->name ? $data->name : 'N/A';
                })
                ->addColumn('value', function ($data) {
                    return $data->value ? $data->value : 'N/A';
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('edit/product-size-value') . '/' . $data->id . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.product_size_value.view');
    }


    public function editProductSizeValue($slug)
    {
        $productSizeValue = ProductSizeValue::where('id', $slug)->first();

        $productSizeValues = ProductSizeValue::where('product_size_id', $productSizeValue->product_size_id)->get();

        $productSize = ProductSize::where('id',$productSizeValues[0]->product_size_id)->first();

        return view('backend.product_size_value.edit', compact('productSize', 'productSizeValues'));
    }

    public function updateProductSizeValue(Request $request)
    {
        $request->validate([
            'size' => ['required', 'string', 'max:255'],
            'name' => ['required', 'array'],
            'name.*' => ['required', 'string', 'max:255'],
            // 'value' => ['required', 'array'],
            // 'value.*' => ['required', 'string', 'max:255'],
        ]);

        // Delete all existing ProductSizeValue records for the given product_size_id
        ProductSizeValue::where('product_size_id', $request->id)->delete();

        // If name and value arrays are provided, create new records
        if ($request->filled('name') && $request->filled('value')) {
            $newNames = $request->name;
            $newValues = $request->value;

            foreach ($newNames as $index => $name) {
                // if (isset($newValues[$index])) { // Ensure value exists for the name
                    ProductSizeValue::create([
                        'product_size_id' => $request->size,
                        'name' => $name,
                        // 'value' => $newValues[$index],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                // }
            }
        }


        Toastr::success('Updated Successfully', 'Success!');
        $lastInsertedId = ProductSizeValue::latest('id')->first()->id;
        return redirect('edit/product-size-value/' . $lastInsertedId);
    }


    public function deleteProductSizeValue($slug)
    {
        $data = ProductSizeValue::where('id', $slug)->first();
        $data->delete();

        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}
