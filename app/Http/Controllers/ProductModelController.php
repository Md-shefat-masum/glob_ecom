<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class ProductModelController extends Controller
{
    public function viewAllModels(Request $request){
        if ($request->ajax()) {

            $data = DB::table('product_models')
                        ->leftJoin('brands', 'product_models.brand_id' , '=', 'brands.id')
                        ->select('product_models.*', 'brands.name as brand_name')
                        ->orderBy('id', 'desc')
                        ->get();

            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 1){
                            return '<span class="btn btn-sm btn-success rounded" style="padding: 0.1rem .5rem;">Active</span>';
                        } else {
                            return '<span class="btn btn-sm btn-warning rounded" style="padding: 0.1rem .5rem;">Inactive</span>';
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('edit/model').'/'.$data->slug.'" class="mb-1 btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" title="Featured" data-original-title="Featured" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';

                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('backend.product_model.view');
    }

    public function addNewModel(){
        return view('backend.product_model.create');
    }

    public function saveNewModel(Request $request){

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand_id' => 'required',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->name)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        ProductModel::insert([
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'code' => $request->code,
            'status' => 1,
            'slug' => $slug .'-'. time(),
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Model Inserted', 'Success');
        return back();

    }

    public function deleteModel($id){
        ProductModel::where('id', $id)->delete();
        return response()->json(['success' => 'ProductModel deleted successfully.']);
    }

    public function editModel($slug){
        $data = ProductModel::where('slug', $slug)->first();
        return view('backend.product_model.update', compact('data'));
    }

    public function updateModel(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'brand_id' => 'required',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->name)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        ProductModel::where('id', $request->id)->update([
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
            'slug' => $slug.'-'.time(),
            'updated_at' => Carbon::now()
        ]);

        Toastr::success('Model Updated', 'Success');
        return redirect('/view/all/models');
    }

    public function brandWiseModel(Request $request){
        $data = ProductModel::where("brand_id", $request->brand_id)->select('name', 'id')->get();
        return response()->json($data);
    }
}
