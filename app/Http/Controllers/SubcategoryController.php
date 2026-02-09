<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Sohibd\Laravelslug\Generate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Image;
use Yajra\DataTables\DataTables;

class SubcategoryController extends Controller
{
    public function addNewSubcategory(){
        return view('backend.subcategory.create');
    }

    public function saveNewSubcategory(Request $request){

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => 'required',
        ]);

        Subcategory::insert([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'icon' => $request->icon,
            'image' => $request->image,
            'slug' => Generate::Slug($request->name),
            'status' => 1,
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Subcategory has been Added', 'Success');
        return back();
    }

    public function viewAllSubcategory(Request $request){
        if ($request->ajax()) {

            $data = DB::table('subcategories')
                ->leftJoin('categories', 'subcategories.category_id', '=', 'categories.id')
                ->select('subcategories.*', 'categories.name as category_name')
                ->orderBy('subcategories.id', 'desc')
                ->get();

            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 1){
                            return '<span style="color:green; font-weight: 600">Active</span>';
                        } else {
                            return '<span style="color:#DF3554; font-weight: 600">Inactive</span>';
                        }
                    })
                    ->editColumn('featured', function($data) {
                        if($data->featured == 0){
                            return '<button class="btn btn-sm btn-danger rounded">Not Featured</button>';
                        } else {
                            return '<button class="btn btn-sm btn-success rounded">Featured</button>';
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){

                        $btn = ' <a href="'.url('edit/subcategory').'/'.$data->slug.'" class="mb-1 btn-sm btn-warning rounded d-inline-block"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn"><i class="fas fa-trash-alt"></i></a>';

                        if($data->featured == 0){
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" title="Featured" data-original-title="Featured" class="btn-sm btn-success rounded d-inline-block featureBtn"><i class="feather-chevrons-up"></i></a>';
                        } else {
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" title="Featured" data-original-title="Featured" class="btn-sm btn-danger rounded d-inline-block featureBtn"><i class="feather-chevrons-down"></i></a>';
                        }

                        return $btn;
                    })
                    ->rawColumns(['action', 'icon', 'featured', 'status'])
                    ->make(true);
        }
        return view('backend.subcategory.view');
    }

    public function deleteSubcategory($slug){
        $data = Subcategory::where('slug', $slug)->first();
        if($data->icon){
            if(file_exists(public_path($data->icon))){
                unlink(public_path($data->icon));
            }
        }
        $data->delete();
        return response()->json(['success' => 'Subcategory deleted successfully.']);
    }

    public function editSubcategory($slug){
        $subcategory = Subcategory::where('slug', $slug)->first();
        return view('backend.subcategory.update', compact('subcategory'));
    }

    public function updateSubcategory(Request $request){

        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required',
            'status' => 'required',
        ]);

        $duplicateSubCategoryExists = Subcategory::where('id', '!=', $request->id)->where('category_id', $request->category_id)->where('name', $request->name)->first();
        $duplicateSubCategorySlugExists = Subcategory::where('id', '!=', $request->id)->where('category_id', $request->category_id)->where('slug', $request->slug)->first();
        if($duplicateSubCategoryExists || $duplicateSubCategorySlugExists){
            Toastr::warning('Duplicate SubCategory Exists', 'Success');
            return back();
        }

        $data = Subcategory::where('id', $request->id)->first();

       
        $icon = null;
        $image = null;
        if($request->icon){
            $icon = $request->icon;
        }
        if($request->image){
            $image = $request->image;
        }
        Subcategory::where('id', $request->id)->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'icon' => $icon,
            'image' => $image,
            'slug' => Generate::Slug($request->slug),
            'status' => $request->status,
            'updated_at' => Carbon::now()
        ]);

        Toastr::success('Subcategory has been Added', 'Success');
        return redirect('/view/all/subcategory');

    }

    public function featureSubcategory($id){
        $data = Subcategory::where('id', $id)->first();
        if($data->featured == 0){
            $data->featured = 1;
            $data->save();
        } else {
            $data->featured = 0;
            $data->save();
        }
        return response()->json(['success' => 'Satatus Changed successfully.']);
    }
}
