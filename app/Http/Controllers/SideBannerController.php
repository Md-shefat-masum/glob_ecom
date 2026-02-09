<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\SideBanner;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Sohibd\Laravelslug\Generate;
use Yajra\DataTables\DataTables;
use Brian2694\Toastr\Facades\Toastr;

class SideBannerController extends Controller
{
    public function addNewSideBanner(){
        return view('backend.side_banner.create');
    }

    public function saveNewSideBanner(Request $request){
        $request->validate([
            'banner_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'banner_link' => 'nullable|url',
            'title' => 'nullable|string|max:255',
            'button_title' => 'nullable|string|max:255',
            'button_url' => 'nullable|url',
        ]);

        $image = null;
        if ($request->hasFile('banner_img')){
            $get_image = $request->file('banner_img');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('banner_img/');
            $get_image->move($location, $image_name);
            $image = "banner_img/" . $image_name;
        }


        $slug = Generate::Slug($request->banner_link);
        $sameSlugCount = SideBanner::where('slug', $slug)->count();
        if($sameSlugCount > 0){
            $slug .= "-".$sameSlugCount+1;
        }

        SideBanner::insert([
            'banner_img' => $image,
            'banner_link' => request()->banner_link,
            'title' => $request->title,
            'button_title' => $request->button_title,
            'button_url' => $request->button_url,

            'creator' => auth()->user()->id,
            'slug' => $slug,
            'status' => 'active',
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Side Banner has been Created', 'Success');
        return back();
    }

    public function viewAllSideBanner(Request $request){
        if ($request->ajax()) {

            $data = SideBanner::orderBy('id', 'desc')->get();
            return Datatables::of($data)
                    ->editColumn('status', function($data) {
                        if($data->status == 'active'){
                            return 'Active';
                        } else {
                            return 'Inactive';
                        }
                    })
                    ->editColumn('banner_img', function($data) {
                        return $data->banner_img ? $data->banner_img : 'No Image';
                    })
                    ->editColumn('banner_link', function($data) {
                        return '<a href="'.$data->banner_link.'" target="_blank">'.$data->banner_link.'</a>';
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('edit/side-banner').'/'.$data->slug.'" class="mb-1 btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'banner_img', 'banner_link'])
                    ->make(true);
        }
        return view('backend.side_banner.view');
    }

    public function deleteSideBanner($slug){
        $data = SideBanner::where('slug', $slug)->first();
        if($data->image){
            if(file_exists(public_path($data->image))){
                unlink(public_path($data->image));
            }
        }
        $data->delete();
        return response()->json(['success' => 'Data deleted successfully.']);
    }

    public function editSideBanner($slug){
        $data = SideBanner::where('slug', $slug)->first();
        return view('backend.side_banner.update', compact('data'));
    }

    public function updateSideBanner(Request $request){
        $data = SideBanner::where('id', $request->custom_id)->first();

        $request->validate([
            'banner_img' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'banner_link' => 'required|url',
            'status' => 'nullable|in:active,inactive',
            'title' => 'nullable|string|max:255',
            'button_title' => 'nullable|string|max:255',
            'button_url' => 'nullable|url',
        ]);
        $image = $data->banner_img;
        if ($request->hasFile('banner_img')){

            if($image != '' && file_exists(public_path($image))){
                unlink(public_path($image));
            }

            $get_image = $request->file('banner_img');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('banner_img/');
            // Image::make($get_image)->save($location . $image_name, 80);
            $get_image->move($location, $image_name);
            $image = "banner_img/" . $image_name;
        }

        $slug = Generate::Slug($request->banner_link);
        $sameSlugCount = SideBanner::where('slug', $slug)->where('id', '!=', $request->custom_id)->count();
        if($sameSlugCount > 0){
            $slug .= "-".$sameSlugCount+1;
        }

        SideBanner::where('id', $request->custom_id)->update([
            'banner_img' => $image,
            'banner_link' => $request->banner_link,
            'title' => $request->title,
            'button_title' => $request->button_title,
            'button_url' => $request->button_url,
            
            'creator' => auth()->user()->id,
            'slug' => $slug,
            'status' => $request->status,
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Side Banner has been Updated', 'Success');
        return redirect('view/all/side-banner');

    }
}
