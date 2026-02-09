<?php

namespace App\Http\Controllers;

use App\Models\StorageType;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use DataTables;
use Carbon\Carbon;

class StorageController extends Controller
{
    public function viewAllStorages(Request $request){
        if ($request->ajax()) {

            $data = StorageType::orderBy('serial', 'asc')->get();

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
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" title="Featured" data-original-title="Featured" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                        // $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" title="Featured" data-original-title="Featured" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }
        return view('backend.config.storage');
    }

    public function addNewStorage(Request $request){
        $request->validate([
            'ram' => ['required', 'string', 'max:255'],
            'rom' => ['required', 'string', 'max:255'],
        ]);

        StorageType::insert([
            'ram' => $request->ram,
            'rom' => $request->rom,
            'status' => 1,
            'slug' => str::random(5) . time(),
            'created_at' => Carbon::now()
        ]);

        return response()->json(['success'=>'Created successfully.']);
    }

    public function deleteStorage($id){
        StorageType::where('id', $id)->delete();
        return response()->json(['success' => 'StorageType deleted successfully.']);
    }

    public function getStorageInfo($id){
        $data = StorageType::where('id', $id)->first();
        return response()->json($data);
    }

    public function updateStorage(Request $request){
        $request->validate([
            'ram' => ['required', 'string', 'max:255'],
            'rom' => ['required', 'string', 'max:255'],
            'status' => 'required',
        ]);

        StorageType::where('id', $request->storage_type_id)->update([
            'ram' => $request->ram,
            'rom' => $request->rom,
            'status' => $request->status,
            'updated_at' => Carbon::now()
        ]);

        return response()->json(['success' => 'Updated Successfully.']);
    }

    public function rearrangeStorage(){
        $storages = StorageType::where('status', 1)->orderBy('serial', 'asc')->get();
        return view('backend.config.rearrangeStorage', compact('storages'));
    }

    public function saveRearrangeStorage(Request $request){
        $sl = 1;
        foreach($request->slug as $slug){
            StorageType::where('slug', $slug)->update([
                'serial' => $sl
            ]);
            $sl++;
        }
        Toastr::success('Storages has been Rerranged', 'Success');
        return redirect('/view/all/storages');
    }
}
