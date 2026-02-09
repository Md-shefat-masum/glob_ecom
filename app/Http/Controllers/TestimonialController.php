<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class TestimonialController extends Controller
{
    public function viewTestimonials(Request $request){
        if ($request->ajax()) {

            $data = Testimonial::orderBy('id', 'desc')->get();

            return Datatables::of($data)
                    ->editColumn('rating', function($data) {
                        $rating = "";
                        for($i=1;$i<=$data->rating;$i++){
                            $rating .= '<i class="feather-star" style="color: goldenrod;"></i>';
                        }
                        return $rating;
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('edit/testimonial').'/'.$data->slug.'" class="mb-1 btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Status" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'rating'])
                    ->make(true);
        }
        return view('backend.testimonial.view');
    }

    public function addTestimonial(){
        return view('backend.testimonial.add');
    }

    public function saveTestimonial(Request $request){
        $request->validate([
            'name' => 'required|max:255',
            'rating' => 'required',
            'description' => 'required'
        ]);


        $image = null;
        if ($request->hasFile('image')){
            $get_image = $request->file('image');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('testimonial/');
            // Image::make($get_image)->save($location . $image_name, 80);
            $get_image->move($location, $image_name);
            $image = "testimonial/" . $image_name;
        }

        Testimonial::insert([
            'description' => $request->description,
            'rating' => $request->rating,
            'customer_name' => $request->name,
            'designation' => $request->designation,
            'customer_image' => $image,
            'slug' => str::random(5) . time(),
            'created_at' => Carbon::now(),
        ]);

        Toastr::success('Testimonial Saved', 'Success');
        return back();
    }

    public function deleteTestimonial($slug){
        $data = Testimonial::where('slug', $slug)->first();
        if($data->customer_image){
            if(file_exists(public_path($data->customer_image))){
                unlink(public_path($data->customer_image));
            }
        }
        $data->delete();
        return response()->json(['success' => 'Data deleted successfully.']);
    }

    public function editTestimonial($slug){
        $data = Testimonial::where('slug', $slug)->first();
        return view('backend.testimonial.edit', compact('data'));
    }

    public function updateTestimonial(Request $request){
        $request->validate([
            'name' => 'required|max:255',
            'rating' => 'required',
            'description' => 'required'
        ]);

        $data = Testimonial::where('slug', $request->slug)->first();

        $image = $data->customer_image;
        if ($request->hasFile('image')){

            if($data->image != '' && file_exists(public_path($data->image))){
                unlink(public_path($data->image));
            }

            $get_image = $request->file('image');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('testimonial/');
            // Image::make($get_image)->save($location . $image_name, 80);
            $get_image->move($location, $image_name);
            $image = "testimonial/" . $image_name;
        }

        $data->customer_image = $image;
        $data->description = $request->description;
        $data->rating = $request->rating;
        $data->customer_name = $request->name;
        $data->designation = $request->designation;
        $data->updated_at = Carbon::now();
        $data->save();

        Toastr::success('Testimonial Updated', 'Success');
        return redirect('/view/testimonials');
    }
}
