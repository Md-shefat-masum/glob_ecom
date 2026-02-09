<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use DataTables;

class PromoCodeController extends Controller
{
    public function addPromoCode(){
        return view('backend.promo_code.create');
    }

    public function savePromoCode(Request $request){

        $request->validate([
            'title' => 'required|max:255',
            'type' => 'required',
            'value' => 'required',
            'code' => 'required',
            'effective_date' => 'required',
            'expire_date' => 'required',
        ]);

        if($request->type == 2 && $request->value > 100){
            Toastr::error('Percentage Cannot be Greater than 100', 'Success');
            return back();
        }

        if(strtotime(str_replace("/","-",$request->effective_date)) > strtotime(str_replace("/","-",$request->expire_date))){
            Toastr::error('Effective Date Cannot be greater than Expiry Date', 'Success');
            return back();
        }

        $icon = request('icon');

        PromoCode::insert([
            'icon' => $icon,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'value' => $request->value,
            'minimum_order_amount' => $request->minimum_order_amount,
            'code' => $request->code,
            'effective_date' => date("Y-m-d", strtotime(str_replace("/","-",$request->effective_date))),
            'expire_date' => date("Y-m-d", strtotime(str_replace("/","-",$request->expire_date))),
            'slug' => str::random(5) . time(),
            'status' => 1,
            'created_at' => Carbon::now()
        ]);

        Toastr::success('Promo Code Generated', 'Success');
        return back();
    }

    public function viewAllPromoCodes(Request $request){
        if ($request->ajax()) {

            $data = PromoCode::orderBy('id', 'desc')->get();

            return Datatables::of($data)
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d h:i:s a", strtotime($data->created_at));
                    })
                    ->editColumn('type', function($data) {
                        if($data->type == 1){
                            return "Amount (৳)";
                        } else {
                            return "Percentage (%)";
                        }
                    })
                    ->editColumn('status', function($data) {
                        if($data->status == 1){
                            return '<span class="alert alert-success p-0 pl-2 pr-2">Active</span>';
                        } else {
                            return '<span class="alert alert-danger p-0 pl-2 pr-2">Inactive</span>';
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        $btn = ' <a href="'.url('edit/promo/code').'/'.$data->slug.'" class="mb-1 btn-sm btn-warning rounded"><i class="fas fa-edit"></i></a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->slug.'" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status'])
                    ->make(true);
        }

        $data = PromoCode::orderBy('id', 'desc')->get();
        $today = strtotime(date("Y-m-d"));
        foreach($data as $item){
            if(strtotime($item->expire_date) < $today){
                $item->status = 0;
                $item->save();
            }
        }

        return view('backend.promo_code.view');
    }

    public function editPromoCode($slug){
        $data = PromoCode::where('slug', $slug)->first();
        return view('backend.promo_code.update', compact('data'));
    }

    public function updatePromoCode(Request $request){

        // dd($request->all());
        $request->validate([
            'title' => 'required|max:255',
            'type' => 'required',
            'value' => 'required',
            'code' => 'required',
            'effective_date' => 'required',
            'expire_date' => 'required',
            'status' => 'required'
        ]);

        if($request->type == 2 && $request->value > 100){
            Toastr::error('Percentage Cannot be Greater than 100', 'Success');
            return back();
        }

        if(strtotime(str_replace("/","-",$request->effective_date)) > strtotime(str_replace("/","-",$request->expire_date))){
            Toastr::error('Effective Date Cannot be greater than Expiry Date', 'Success');
            return back();
        }

        $expireDateTimestamp = strtotime(str_replace("/", "-", $request->expire_date));
        if ($expireDateTimestamp && $expireDateTimestamp < now()->timestamp) {
            Toastr::error('The date is expired. You can’t change the status.', 'Failed');
            return back();
        }

        $data = PromoCode::where('slug', $request->slug)->first();

        $icon = $data->icon;
        if(request('icon')){
            $icon = request('icon');
        }

        PromoCode::where('slug', $request->slug)->update([
            'icon' => $icon,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'value' => $request->value,
            'minimum_order_amount' => $request->minimum_order_amount,
            'code' => $request->code,
            'effective_date' => date("Y-m-d", strtotime(str_replace("/","-",$request->effective_date))),
            'expire_date' => date("Y-m-d", strtotime(str_replace("/","-",$request->expire_date))),
            'status' => $request->status,
        ]);

        Toastr::success('Promo Code Updated', 'Success');
        return redirect('/view/all/promo/codes');
    }

    public function removePromoCode($slug){

        $data = PromoCode::where('slug', $slug)->first();
        if($data->icon){
            if(file_exists(public_path($data->icon))){
                unlink(public_path($data->icon));
            }
        }
        PromoCode::where('slug', $slug)->delete();
        return response()->json(['success' => 'Category deleted successfully.']);

    }
}
