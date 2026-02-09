<?php

namespace App\Http\Controllers;

use App\Models\PromotionalBanner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class PromotionalBannerController extends Controller
{
    public function viewAllPromotionalBanners(Request $request)
    {
        if ($request->ajax()) {
            $data = PromotionalBanner::orderBy('serial', 'asc')->get();

            return Datatables::of($data)
                ->editColumn('status', function($data) {
                    if($data->status == 1){
                        return '<span class="btn btn-sm btn-success rounded" style="padding: 0.1rem .5rem;">Active</span>';
                    } else {
                        return '<span class="btn btn-sm btn-warning rounded" style="padding: 0.1rem .5rem;">Inactive</span>';
                    }
                })
                ->editColumn('product_image', function($data) {
                    if($data->product_image) {
                        $baseUrl = env('FILE_URL', env('APP_URL', url('/')));
                        $baseUrl = rtrim($baseUrl, '/');
                        $imagePath = ltrim($data->product_image, '/');
                        $imageUrl = $baseUrl . '/' . $imagePath;
                        return '<img src="' . $imageUrl . '" width="60" height="60" style="object-fit: cover; border-radius: 4px;"/>';
                    }
                    return '';
                })
                ->addIndexColumn()
                ->addColumn('action', function($data){
                    $btn = ' <a href="'.url('edit/promotional/banner').'/'.$data->slug.'" class="mb-1 btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'product_image', 'status'])
                ->make(true);
        }
        return view('backend.banners.promotional.index');
    }

    public function addNewPromotionalBanner()
    {
        return view('backend.banners.promotional.create');
    }

    public function saveNewPromotionalBanner(Request $request)
    {
        $request->validate([
            'icon' => 'nullable|string',
            'product_image' => 'nullable|string',
            'background_image' => 'nullable|string',
        ]);

        // Date formatting
        $started_at = null;
        if ($request->started_at) {
            $started_at = str_replace("/","-",$request->started_at);
            $started_at = date("Y-m-d H:i:s", strtotime($started_at));
        }

        $end_at = null;
        if ($request->end_at) {
            $end_at = str_replace("/","-",$request->end_at);
            $end_at = date("Y-m-d H:i:s", strtotime($end_at));
        }

        // Calculate serial number
        $minSerial = PromotionalBanner::min('serial');
        $serial = $minSerial ? $minSerial - 1 : 1;

        PromotionalBanner::insert([
            'icon' => $request->icon ?? null,
            'product_image' => $request->product_image ?? null,
            'background_image' => $request->background_image ?? null,
            'heading' => $request->heading ?? null,
            'heading_color' => $request->heading_color ?? null,
            'title' => $request->title ?? null,
            'title_color' => $request->title_color ?? null,
            'description' => $request->description ?? null,
            'description_color' => $request->description_color ?? null,
            'url' => $request->url ?? null,
            'btn_text' => $request->btn_text ?? null,
            'btn_text_color' => $request->btn_text_color ?? null,
            'btn_bg_color' => $request->btn_bg_color ?? null,
            'background_color' => $request->background_color ?? null,
            'video_url' => $request->video_url ?? null,
            'started_at' => $started_at,
            'end_at' => $end_at,
            'time_bg_color' => $request->time_bg_color ?? null,
            'time_font_color' => $request->time_font_color ?? null,
            'serial' => $serial,
            'status' => 1,
            'slug' => Str::random(5) . time(),
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Promotional Banner has been Added', 'Success');
        return redirect()->route('ViewAllPromotionalBanners');
    }

    public function editPromotionalBanner($slug)
    {
        $data = PromotionalBanner::where('slug', $slug)->first();
        if (!$data) {
            Toastr::error('Promotional Banner not found', 'Error');
            return redirect()->route('ViewAllPromotionalBanners');
        }
        return view('backend.banners.promotional.update', compact('data'));
    }

    public function updatePromotionalBanner(Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $data = PromotionalBanner::where('slug', $request->slug)->first();

        if (!$data) {
            Toastr::error('Promotional Banner not found', 'Error');
            return back();
        }

        // Date formatting
        $started_at = null;
        if ($request->started_at) {
            $started_at = str_replace("/","-",$request->started_at);
            $started_at = date("Y-m-d H:i:s", strtotime($started_at));
        }

        $end_at = null;
        if ($request->end_at) {
            $end_at = str_replace("/","-",$request->end_at);
            $end_at = date("Y-m-d H:i:s", strtotime($end_at));
        }

        // Update images if provided
        if ($request->filled('icon')) {
            $data->icon = $request->icon;
        }
        if ($request->filled('product_image')) {
            $data->product_image = $request->product_image;
        }
        if ($request->filled('background_image')) {
            $data->background_image = $request->background_image;
        }

        $data->heading = $request->heading ?? null;
        $data->heading_color = $request->heading_color ?? null;
        $data->title = $request->title ?? null;
        $data->title_color = $request->title_color ?? null;
        $data->description = $request->description ?? null;
        $data->description_color = $request->description_color ?? null;
        $data->url = $request->url ?? null;
        $data->btn_text = $request->btn_text ?? null;
        $data->btn_text_color = $request->btn_text_color ?? null;
        $data->btn_bg_color = $request->btn_bg_color ?? null;
        $data->background_color = $request->background_color ?? null;
        $data->video_url = $request->video_url ?? null;
        $data->started_at = $started_at;
        $data->end_at = $end_at;
        $data->time_bg_color = $request->time_bg_color ?? null;
        $data->time_font_color = $request->time_font_color ?? null;
        $data->status = $request->status;
        $data->updated_at = Carbon::now();
        $data->save();

        Toastr::success('Data has been Updated', 'Success');
        return redirect()->route('ViewAllPromotionalBanners');
    }

    public function deletePromotionalBanner($slug)
    {
        $data = PromotionalBanner::where('slug', $slug)->first();
        if ($data) {
            $data->delete();
            return response()->json(['success' => 'Data deleted successfully.']);
        }
        return response()->json(['error' => 'Data not found.'], 404);
    }

    public function rearrangePromotionalBanners()
    {
        $data = PromotionalBanner::orderBy('serial', 'asc')->get();
        return view('backend.banners.promotional.rearrange', compact('data'));
    }

    public function updateRearrangedPromotionalBanners(Request $request)
    {
        $sl = 1;
        foreach($request->slug as $slug){
            PromotionalBanner::where('slug', $slug)->update([
                'serial' => $sl
            ]);
            $sl++;
        }
        Toastr::success('Promotional Banners have been Rearranged', 'Success');
        return redirect()->route('ViewAllPromotionalBanners');
    }
}

