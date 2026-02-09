<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class DeliveryChargeController extends Controller
{
    public function viewAllDeliveryCharges(Request $request){
        if ($request->ajax()) {

            $data = DB::table('districts')
                    ->join('divisions','districts.division_id', '=', 'divisions.id')
                    ->select('districts.*', 'divisions.name as division_name')
                    ->orderBy('id', 'asc')
                    ->get();

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('delivery_charge', function($data) {
                        return "<span style='color: green; font-weight: 600;'>BDT ".$data->delivery_charge."/=</span>";
                    })
                    ->addColumn('action', function($data){
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" data-original-title="Delete" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'delivery_charge'])
                    ->make(true);
        }
        return view('backend.delivery_charges');
    }

    public function getDeliveryCharge($id){
        $data = DB::table('districts')->where('id', $id)->first();
        return response()->json($data);
    }

    public function updateDeliveryCharge(Request $request){
        DB::table('districts')->where('id', $request->delivery_charge_id)->update([
            'delivery_charge' => $request->delivery_charge,
        ]);
        return response()->json(['success'=>'Updated successfully.']);
    }

    public function viewUpazilaThana(Request $request){
        if ($request->ajax()) {

            $data = DB::table('upazilas')
                    ->join('districts', 'upazilas.district_id', 'districts.id')
                    ->select('upazilas.*', 'districts.name as district_name')
                    ->orderBy('districts.id', 'asc')
                    ->get();

            return Datatables::of($data)
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" data-original-title="Edit" class="btn-sm mb-1 d-inline-block btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" data-original-title="Delete" class="btn-sm mb-1 d-inline-block btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.upazila_thana');
    }

    public function getUpazilaInfo($id){

        $data = DB::table('upazilas')
                    ->join('districts', 'upazilas.district_id', 'districts.id')
                    ->select('upazilas.*', 'districts.name as district_name')
                    ->where('upazilas.id', $id)
                    ->first();

        return response()->json($data);
    }

    public function updateUpazilaInfo(Request $request){
        DB::table('upazilas')->where('id', $request->upazila_id)->update([
            'name' => $request->name,
            'bn_name' => $request->bn_name,
            'url' => $request->url,
        ]);
        return response()->json(['success'=>'Updated successfully.']);
    }

    public function saveNewUpazila(Request $request){
        DB::table('upazilas')->insert([
            'district_id' => $request->district_id,
            'name' => $request->name,
            'bn_name' => $request->bn_name,
            'url' => $request->url,
        ]);
        return response()->json(['success'=>'Saved Successfully.']);
    }

    public function deleteUpazila($id){
        DB::table('upazilas')->where('id', $id)->delete();
        return response()->json(['success'=>'Removed Successfully.']);
    }
}
