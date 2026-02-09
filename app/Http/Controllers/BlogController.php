<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function blogCategories(Request $request){
        if ($request->ajax()) {
            $data = BlogCategory::orderBy('serial', 'asc')->get();
            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 1){
                            return 'Active';
                        } else {
                            return 'Inactive';
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
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Edit" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';

                        if($data->featured == 0){
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" title="Featured" data-original-title="Featured" class="btn-sm btn-success rounded featureBtn"><i class="feather-chevrons-up"></i></a>';
                        } else {
                            $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" title="Featured" data-original-title="Featured" class="btn-sm btn-danger rounded featureBtn"><i class="feather-chevrons-down"></i></a>';
                        }

                        return $btn;
                    })
                    ->rawColumns(['action', 'featured'])
                    ->make(true);
        }
        return view('backend.blog.category');
    }

    public function saveBlogCategory(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->name)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        BlogCategory::insert([
            'name' => $request->name,
            'slug' => $slug.time(),
            'status' => 1,
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Category has been Added', 'Success');
        return back();
    }

    public function deleteBlogCategory($slug){
        $data = BlogCategory::where('slug', $slug)->first();

        $used = Blog::where('category_id', $data->id)->count();
        if($used > 0){
            return response()->json(['success' => 'Category cannot be deleted', 'data' => 0]);
        } else {
            $data->delete();
            return response()->json(['success' => 'Category deleted successfully.', 'data' => 1]);
        }
    }

    public function featureBlogCategory($slug){
        $data = BlogCategory::where('slug', $slug)->first();
        if($data->featured == 0){
            $data->featured = 1;
            $data->save();
        } else {
            $data->featured = 0;
            $data->save();
        }
        return response()->json(['success' => 'Status Changed successfully.']);
    }

    public function getBlogCategoryInfo($slug){
        $data = BlogCategory::where('slug', $slug)->first();
        return response()->json($data);
    }

    public function updateBlogCategoryInfo(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_status' => 'required',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->name)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        BlogCategory::where('slug', $request->category_slug)->update([
            'name' => $request->name,
            'slug' => $slug.time(),
            'status' => $request->category_status,
            'updated_at' => Carbon::now()
        ]);

        return response()->json(['success' => 'Data Updated successfully.']);
    }

    public function rearrangeBlogCategory(){
        $categories = BlogCategory::orderBy('serial', 'asc')->get();
        return view('backend.blog.rearrange', compact('categories'));
    }

    public function saveRearrangeCategory(Request $request){
        $sl = 1;
        foreach($request->slug as $slug){
            BlogCategory::where('slug', $slug)->update([
                'serial' => $sl
            ]);
            $sl++;
        }
        Toastr::success('Category has been Rerranged', 'Success');
        return redirect('/blog/categories');
    }

    public function addNewBlog(){
        return view('backend.blog.create');
    }

    public function saveNewBlog(Request $request){

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => 'required',
            'image' => 'required',
        ]);

        $image = null;
        if ($request->hasFile('image')){
            $get_image = $request->file('image');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('blogImages/');
            $get_image->move($location, $image_name);
            $image = "blogImages/" . $image_name;
        }

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        Blog::insert([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'image' => $image,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'tags' => $request->tags,
            'slug' => $slug.time(),
            'status' => 1,
            'created_at' => Carbon::now()
        ]);

        Toastr::success('New Blog Has Published', 'Success');
        return back();
    }

    public function viewAllBlogs(Request $request){
        if ($request->ajax()) {

            $data = DB::table('blogs')
                        ->leftJoin('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                        ->select('blogs.*', 'blog_categories.name as blog_category_name')
                        ->orderBy('blogs.id', 'desc')
                        ->get();

            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 1){
                            return 'Active';
                        } else {
                            return 'Inactive';
                        }
                    })
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d", strtotime($data->created_at));
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('edit/blog').'/'.$data->slug.'" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('backend.blog.view');
    }

    public function deleteBlog($slug){

        $data = Blog::where('slug', $slug)->first();
        if($data->image){
            if(file_exists(public_path($data->image))){
                unlink(public_path($data->image));
            }
        }
        $data->delete();
        return response()->json(['success' => 'Blog Deleted successfully.']);
    }

    public function editBlog($slug){
        $data = Blog::where('slug', $slug)->first();
        return view('backend.blog.edit', compact('data'));
    }

    public function updateBlog(Request $request){

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => 'required',
            'status' => 'required',
        ]);

        $blog = Blog::where('id', $request->blog_id)->first();

        $image = $blog->image;
        if ($request->hasFile('image')){

            if($blog->image != '' && file_exists(public_path($blog->image))){
                unlink(public_path($blog->image));
            }

            $get_image = $request->file('image');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('blogImages/');
            $get_image->move($location, $image_name);
            $image = "blogImages/" . $image_name;
        }

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);


        $blog->category_id = $request->category_id;
        $blog->title = $request->title;
        $blog->image = $image;
        $blog->short_description = $request->short_description;
        $blog->description = $request->description;
        $blog->tags = $request->tags;
        if($blog->title != $request->title){
            $blog->slug = $slug.time();
        }
        $blog->status = $request->status;
        $blog->updated_at = Carbon::now();
        $blog->save();

        Toastr::success('Blog Has been Updated', 'Success');
        $data = $blog;
        return view('backend.blog.edit', compact('data'));
    }
}
