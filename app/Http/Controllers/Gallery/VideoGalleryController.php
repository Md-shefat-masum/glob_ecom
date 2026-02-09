<?php

namespace App\Http\Controllers\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gallery\Models\VideoGallery;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class VideoGalleryController extends Controller
{
    public function addNewVideoGallery()
    {
        return view('backend.video.create');
    }

    public function saveNewVideoGallery(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'title' => ['required'],
            'source' => ['required'],
            // 'code' => ['required', 'string', 'max:255', 'unique:outlets,code'],
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);



        // $images = [];

        // if ($request->hasFile('images')) {
        //     foreach ($request->file('images') as $get_image) {
        //         $image_name = Str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
        //         $location = public_path('outletImages/'); // Folder path within the public directory

        //         if ($get_image->getClientOriginalExtension() == 'svg') {
        //             $get_image->move($location, $image_name);
        //         } else {
        //             $image = Image::make($get_image)->encode('jpg', 60); // Encode to jpg (60% quality)
        //             $image->save($location . $image_name);
        //         }

        //         $images[] = "outletImages/" . $image_name;
        //     }
        // }

        // Insert data into Outlet table
        VideoGallery::create([
            'title' => $request->title,
            'source' => $request->source,
            'creator' => auth()->user()->id,
            'slug' => Str::slug($request->title) . time(),
            'status' => 'active',
            'created_at' => Carbon::now(),
        ]);

        Toastr::success('Added successfully!', 'Success');
        return back();
        // return redirect()->back()->with('success', 'Product Warehouse has been added successfully!');
        // return redirect()->back()->with('error', 'An error occurred!');
    }

    public function viewAllVideoGallery(Request $request)
    {

        if ($request->ajax()) {
            $data = VideoGallery::orderBy('id', 'desc')  // Order by ID in descending order
                ->get();

            // dd($data);
            return Datatables::of($data)
                ->editColumn('status', function ($data) {
                    if ($data->status == "active") {
                        return 'Active';
                    } else {
                        return 'Inactive';
                    }
                })
                // ->editColumn('created_at', function ($data) {
                //     return date("Y-m-d", strtotime($data->created_at));
                // })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('edit/video-gallery') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['source', 'action'])
                ->make(true);
        }
        return view('backend.video.view');
    }

    public function editVideoGallery($slug) {
        $data = VideoGallery::where('slug', $slug)->first();
        return view('backend.video.edit', compact('data'));
    }
    
    public function updateVideoGallery(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => ['required'],
            'source' => ['required'],
            // 'code' => ['required', 'string', 'max:255', 'unique:outlets,code'],
        ]);

        $data = VideoGallery::where('id', request()->video_gallery_id)->first();

     
        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);


        // Update the outlet data
        $data->title = $request->title ?? $data->title;
        $data->source = $request->source ?? $data->source;       
        if ($data->title != $request->title) {
            $data->slug = Str::slug($request->title) . time();
        }

        $data->status = $request->status ?? $data->status;
        $data->updated_at = Carbon::now();
        $data->save();

        Toastr::success('Updated Successfully', 'Success!');
        return view('backend.video.edit', compact('data'));
    }

    
    public function deleteVideoGallery($slug)
    {
        $data = VideoGallery::where('slug', $slug)->first();

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();
        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }

   

}
