<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Account\Models\AccountIncomeCategory;
use App\Http\Controllers\Account\Models\AcAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountIncomeCategoryController extends Controller
{
    public function addNewIncomeCategory()
    {
        return view('backend.incomecategory.create');
    }

    public function saveNewIncomeCategory(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:100'],
            'debit_id' => ['required', 'exists:ac_accounts,id'],
            'credit_id' => ['required', 'exists:ac_accounts,id'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name must not exceed 100 characters.',
            'code.required' => 'Category code is required.',
            'code.max' => 'Category code must not exceed 100 characters.',
            'debit_id.required' => 'To Account (Debit) is required.',
            'debit_id.exists' => 'Selected debit account does not exist.',
            'credit_id.required' => 'From Account (Credit) is required.',
            'credit_id.exists' => 'Selected credit account does not exist.',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->name));
        $slug = preg_replace('!\s+!', '-', $clean);

        AccountIncomeCategory::create([
            'store_id' => auth()->user()->store_id ?? 1,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description ?? '',
            'debit_id' => $request->debit_id,
            'credit_id' => $request->credit_id,
            'created_by' => auth()->user()->name ?? null,
            'created_date' => Carbon::now('Asia/Dhaka')->format('Y-m-d'),
            'creator' => auth()->user()->id,
            'slug' => $slug . time(),
            'status' => 'active',
            'created_at' => Carbon::now('Asia/Dhaka')
        ]);

        Toastr::success('Income category added successfully!', 'Success');
        return redirect()->route('ViewAllIncomeCategory');
    }

    public function viewAllIncomeCategory(Request $request)
    {
        if ($request->ajax()) {
            $data = AccountIncomeCategory::with('user', 'debitAccount', 'creditAccount')
                        ->orderBy('id', 'DESC')
                        ->get();

            return Datatables::of($data)
                ->addIndexColumn()                       
                ->addColumn('user', function ($data) {
                    return $data->user ? $data->user->name : 'N/A';
                })
                ->addColumn('from_account', function ($data) {
                    return $data->creditAccount ? $data->creditAccount->account_name : 'N/A';
                })
                ->addColumn('to_account', function ($data) {
                    return $data->debitAccount ? $data->debitAccount->account_name : 'N/A';
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d h:i", strtotime($data->created_at));
                })        
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . route('EditIncomeCategory', $data->slug) . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.incomecategory.view');
    }

    public function editIncomeCategory($slug)
    {
        $data = AccountIncomeCategory::where('slug', $slug)->first();
        return view('backend.incomecategory.edit', compact('data'));
    }

    public function updateIncomeCategory(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:100'],
            'debit_id' => ['required', 'exists:ac_accounts,id'],
            'credit_id' => ['required', 'exists:ac_accounts,id'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name must not exceed 100 characters.',
            'code.required' => 'Category code is required.',
            'code.max' => 'Category code must not exceed 100 characters.',
            'debit_id.required' => 'To Account (Debit) is required.',
            'debit_id.exists' => 'Selected debit account does not exist.',
            'credit_id.required' => 'From Account (Credit) is required.',
            'credit_id.exists' => 'Selected credit account does not exist.',
        ]);

        $data = AccountIncomeCategory::where('id', $request->income_category_id)->first();

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($data->name));
        $slug = preg_replace('!\s+!', '-', $clean);

        $data->store_id = $request->store_id ?? $data->store_id;
        $data->name = $request->name ?? $data->name;
        $data->code = $request->code ?? $data->code;
        $data->description = $request->description ?? $data->description;
        $data->debit_id = $request->debit_id ?? $data->debit_id;
        $data->credit_id = $request->credit_id ?? $data->credit_id;

        if ($data->name != $request->name) {
            $data->slug = $slug . time();
        }

        $data->creator = auth()->user()->id;
        $data->status = $request->status ?? $data->status;
        $data->updated_at = Carbon::now('Asia/Dhaka');
        $data->save();

        Toastr::success('Successfully Updated', 'Success!');
        return redirect()->route('ViewAllIncomeCategory');
    }

    public function deleteIncomeCategory($slug)
    {
        $data = AccountIncomeCategory::where('slug', $slug)->first();

        $data->delete();
        
        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }
}

