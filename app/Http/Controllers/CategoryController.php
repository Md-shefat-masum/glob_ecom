<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Sohibd\Laravelslug\Generate;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Image;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    public function addNewCategory(){
        return view('backend.category.create');
    }

    public function saveNewCategory(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories'],
        ]);

        $icon = request('icon');
        $categoryBanner = request('banner_image');

        Category::insert([
            'name' => $request->name,
            'featured' => $request->featured ? $request->featured : 0,
            'show_on_navbar' => $request->show_on_navbar ? $request->show_on_navbar : 1,
            'icon' => $icon,
            'banner_image' => $categoryBanner,
            'page_title' => $request->page_title,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'slug' => Generate::Slug($request->name),
            'status' => 1,
            'serial' => Category::min('serial') - 1,
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Category has been Added', 'Success');
        return back();

    }

    public function viewAllCategory(Request $request){
        if ($request->ajax()) {
            $data = Category::orderBy('serial', 'asc')->get();
            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 1){
                            return '<span style="color:green; font-weight: 600">Active</span>';
                        } else {
                            return '<span style="color:#DF3554; font-weight: 600">Inactive</span>';
                        }
                    })
                    ->editColumn('icon', function($data) {
                        if($data->icon && file_exists(public_path($data->icon))){
                            return $data->icon;
                        }
                    })
                    ->editColumn('banner_image', function($data) {
                        if($data->banner_image && file_exists(public_path($data->banner_image))){
                            return $data->banner_image;
                        }
                    })
                    ->editColumn('featured', function($data) {
                        if($data->featured == 0){
                            return '<span class="badge badge-pill p-2 badge-danger" style="font-size: 11px; border-radius: 4px;">Not Featured</span>';
                        } else {
                            return '<span class="badge badge-pill p-2 badge-success" style="font-size: 11px; border-radius: 4px;">Featured</span>';
                        }
                    })
                    ->editColumn('show_on_navbar', function($data) {
                        if($data->show_on_navbar == 1){
                            return '<span class="badge badge-pill p-2 badge-success" style="font-size: 11px; border-radius: 4px;">Yes</span>';
                        } else {
                            return '<span class="badge badge-pill p-2 badge-danger" style="font-size: 11px; border-radius: 4px;">No</span>';
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('edit/category').'/'.$data->slug.'" class="mb-1 btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';

                        // if($data->featured == 0){
                        //     $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" title="Featured" data-original-title="Featured" class="btn-sm btn-success rounded featureBtn"><i class="feather-chevrons-up"></i></a>';
                        // } else {
                        //     $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" title="Featured" data-original-title="Featured" class="btn-sm btn-danger rounded featureBtn"><i class="feather-chevrons-down"></i></a>';
                        // }

                        return $btn;
                    })
                    ->rawColumns(['action', 'icon', 'featured', 'show_on_navbar', 'status'])
                    ->make(true);
        }
        return view('backend.category.view');
    }
    
    public function deleteCategory($slug){
        $data = Category::where('slug', $slug)->first();
        if($data->icon){
            if(file_exists(public_path($data->icon))){
                unlink(public_path($data->icon));
            }
        }
        if($data->banner_image){
            if(file_exists(public_path($data->banner_image))){
                unlink(public_path($data->banner_image));
            }
        }
        $data->delete();
        return response()->json(['success' => 'Category deleted successfully.']);
    }

    public function featureCategory($slug){
        $data = Category::where('slug', $slug)->first();
        if($data->featured == 0){
            $data->featured = 1;
            $data->save();
        } else {
            $data->featured = 0;
            $data->save();
        }
        return response()->json(['success' => 'Status Changed successfully.']);
    }

    public function editCategory($slug){
        $category = Category::where('slug', $slug)->first();
        return view('backend.category.update', compact('category'));
    }

    public function updateCategory(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => 'required',
        ]);

        $duplicateCategoryExists = Category::where('id', '!=', $request->id)->where('name', $request->name)->first();
        $duplicateCategorySlugExists = Category::where('id', '!=', $request->id)->where('slug', $request->slug)->first();
        if($duplicateCategoryExists || $duplicateCategorySlugExists){
            Toastr::warning('Duplicate Category Or Slug Exists', 'Duplicate');
            return back();
        }

        $data = Category::where('id', $request->id)->first();

        $icon = $data->icon;
        if ($request->icon){
            $icon = request('icon');
        }

        $banner_image = $data->banner_image;
        if ($request->banner_image){
            $banner_image = request('banner_image');
        }

        Category::where('id', $request->id)->update([
            'name' => $request->name,
            'icon' => $icon,
            'banner_image' => $banner_image,
            'slug' => Generate::Slug($request->slug),
            'status' => $request->status,
            'featured' => $request->featured ? $request->featured : 0,
            'show_on_navbar' => $request->show_on_navbar,
            'page_title' => $request->page_title,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'updated_at' => Carbon::now()
        ]);

        Toastr::success('Category has been Updated', 'Success');
        return redirect('/view/all/category');
    }

    public function rearrangeCategory(){
        $categories = Category::orderBy('serial', 'asc')->get();
        return view('backend.category.rearrange', compact('categories'));
    }

    public function saveRearrangeCategoryOrder(Request $request){
        $sl = 1;
        foreach($request->slug as $slug){
            Category::where('slug', $slug)->update([
                'serial' => $sl
            ]);
            $sl++;
        }
        Toastr::success('Category has been Rerranged', 'Success');
        return redirect('/view/all/category');
    }
}
