<?php

namespace App\Http\Controllers\Outlet;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Outlet\Models\Outlet;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class OutletController extends Controller
{
    public function addNewOutlet()
    {
        return view('backend.outlet.create');
    }

    public function saveNewOutlet(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'address' => ['required'],
            // 'code' => ['required', 'string', 'max:255', 'unique:outlets,code'],
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);



        $images = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $get_image) {
                $image_name = Str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
                $location = public_path('outletImages/'); // Folder path within the public directory

                if ($get_image->getClientOriginalExtension() == 'svg') {
                    $get_image->move($location, $image_name);
                } else {
                    $image = Image::make($get_image)->encode('jpg', 60); // Encode to jpg (60% quality)
                    $image->save($location . $image_name);
                }

                $images[] = "outletImages/" . $image_name;
            }
        }

        // Insert data into Outlet table
        Outlet::create([
            'title' => $request->title,
            'address' => $request->address,
            'image' => json_encode($images, JSON_UNESCAPED_SLASHES), // Prevents escaping slashes
            'opening' => $request->opening,
            'contact_number_1' => $request->contact_number_1,
            'contact_number_2' => $request->contact_number_2,
            'contact_number_3' => $request->contact_number_3,
            'map' => $request->map,
            'description' => $request->description,
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

    public function viewAllOutlet(Request $request)
    {

        if ($request->ajax()) {
            $data = Outlet::orderBy('id', 'desc')  // Order by ID in descending order
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
                ->editColumn('image', function ($row) {
                    $images = json_decode($row->image, true);
                    $firstImage = $images[0] ?? 'default.jpg';  // Fallback if no image exists
                    return asset($firstImage);
                })
                // ->editColumn('created_at', function ($data) {
                //     return date("Y-m-d", strtotime($data->created_at));
                // })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('edit/outlet') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.outlet.view');
    }


    public function editOutlet($slug)
    {
        $data = Outlet::where('slug', $slug)->first();
        return view('backend.outlet.edit', compact('data'));
    }

    public function updateOutlet(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'address' => ['required'],
            // 'code' => ['required', 'string', 'max:255', 'unique:outlets,code'],
        ]);

        $data = Outlet::where('id', request()->outlet_id)->first();

        $images = []; // Initialize the $images array as empty

        if ($request->hasFile('images')) {
            // First, remove existing images
            if (!empty($data->image)) {
                $existing_images = json_decode($data->image, true);

                // Delete old images if any
                if (!empty($existing_images)) {
                    foreach ($existing_images as $old_image) {
                        if (file_exists(public_path($old_image))) {
                            unlink(public_path($old_image)); // Delete the old image
                        }
                    }
                }
            }

            // Add new images
            foreach ($request->file('images') as $get_image) {
                $image_name = Str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
                $location = public_path('outletImages/'); // Folder path within the public directory

                if ($get_image->getClientOriginalExtension() == 'svg') {
                    $get_image->move($location, $image_name);
                } else {
                    $image = Image::make($get_image)->encode('jpg', 60); // Encode to jpg (60% quality)
                    $image->save($location . $image_name);
                }

                $images[] = "outletImages/" . $image_name; // Add the new image to the images array
            }

           
        }

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->title)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);


        // Update the outlet data
        $data->title = $request->title ?? $data->title;
        $data->address = $request->address ?? $data->address;
        $data->opening = $request->opening ?? $data->opening;
        $data->contact_number_1 = $request->contact_number_1 ?? $data->contact_number_1;
        $data->contact_number_2 = $request->contact_number_2 ?? $data->contact_number_2;
        $data->contact_number_3 = $request->contact_number_3 ?? $data->contact_number_3;
        $data->map = $request->map ?? $data->map;
        $data->description = $request->description ?? $data->description;
        $data->image = !empty($images) ? json_encode($images, JSON_UNESCAPED_SLASHES) : $data->image; // Update the image field with new images or keep existing

        if ($data->title != $request->title) {
            $data->slug = Str::slug($request->title) . time();
        }

        $data->status = $request->status ?? $data->status;
        $data->updated_at = Carbon::now();
        $data->save();

        Toastr::success('Updated Successfully', 'Success!');
        return view('backend.outlet.edit', compact('data'));
    }


    public function deleteOutlet($slug)
    {
        $data = Outlet::where('slug', $slug)->first();

        // if ($data->productWarehouseRoom()->count() > 0) {
        //     return response()->json([
        //         'error' => 'Cannot delete this warehouse because it has associated rooms.'
        //     ], 400); // Sending error message with HTTP status 400
        // }

        // if ($data->image) {
        //     if (file_exists(public_path($data->image)) && $data->is_demo == 0) {
        //         unlink(public_path($data->image));
        //     }
        // }

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();
        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}
