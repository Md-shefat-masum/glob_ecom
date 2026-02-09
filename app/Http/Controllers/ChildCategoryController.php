<?php

namespace App\Http\Controllers;

use App\Models\ChildCategory;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DataTables;

class ChildCategoryController extends Controller
{
    public function addNewChildcategory(){
        return view('backend.childcategory.create');
    }

    public function subcategoryCategoryWise(Request $request){
        $data = Subcategory::where("category_id", $request->category_id)->where('status', 1)->select('name', 'id')->get();
        return response()->json($data);
    }

    public function saveNewChildcategory(Request $request){

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => 'required',
            'subcategory_id' => 'required',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->name)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        ChildCategory::insert([
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'name' => $request->name,
            'slug' => $slug."-".time()."-".str::random(5),
            'status' => 1,
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Child Category has been Added', 'Success');
        return back();
    }

    public function viewAllChildcategory(Request $request){
        if ($request->ajax()) {

            $data = DB::table('child_categories')
                        ->join('categories', 'child_categories.category_id', '=' , 'categories.id')
                        ->join('subcategories', 'child_categories.subcategory_id', '=' , 'subcategories.id')
                        ->select('child_categories.*', 'categories.name as category_name', 'subcategories.name as subcategory_name')
                        ->orderBy('child_categories.id', 'desc')
                        ->get();

            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 1){
                            return 'Active';
                        } else {
                            return 'Inactive';
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('edit/childcategory').'/'.$data->slug.'" class="mb-1 btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('backend.childcategory.view');
    }

    public function deleteChildcategory($slug){
        $data = ChildCategory::where('slug', $slug)->first();
        $used = Product::where('childcategory_id', $data->id)->count();
        if($used > 0){
            return response()->json(['success' => 'Cannot be deleted', 'data' => 0]);
        } else {
            ChildCategory::where('slug', $slug)->delete();
            return response()->json(['success' => 'Deleted successfully.', 'data' => 1]);
        }
    }

    public function editChildcategory($slug){
        $childcategory = ChildCategory::where('slug', $slug)->first();
        $subcategories = Subcategory::where('category_id', $childcategory->category_id)->select('name', 'id')->orderBy('name', 'asc')->get();
        return view('backend.childcategory.update', compact('childcategory', 'subcategories'));
    }

    public function updateChildcategory(Request $request){

        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'status' => 'required',
        ]);

        $duplicateChildCategoryExists = ChildCategory::where('name', $request->name)->where('id', '!=', $request->id)->first();
        if($duplicateChildCategoryExists){
            Toastr::warning('Duplicate Child Category Exists', 'Success');
            return back();
        }

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->name)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        ChildCategory::where('slug', $request->slug)->update([
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'name' => $request->name,
            'slug' => $slug."-".time()."-".str::random(5),
            'status' => $request->status,
            'updated_at' => Carbon::now()
        ]);

        Toastr::success('Child Category has been Added', 'Success');
        return redirect('/view/all/childcategory');
    }
}
