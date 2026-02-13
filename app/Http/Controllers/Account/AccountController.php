<?php

namespace App\Http\Controllers\Account;

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

class AccountController extends Controller
{
    public function addNewAcAccount()
    {
        $accounts = AcAccount::where('status', 'active')->get();
        // $users = User::where('status', 1)->get();
        // return view('backend.account.create', compact('customer_categories', 'customer_source_types', 'users'));
        return view('backend.account.create', compact('accounts'));
    }

    public function saveNewAcAccount(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'account_name' => ['required', 'string', 'max:100'],
            'account_code' => ['required', 'string', 'max:100'],
        ], [
            'account_name.required' => 'account name is required.',
            'account_name.max' => 'account name must not exceed 100 characters.',
            'account_code.required' => 'account code is required.',
            'account_code.max' => 'account code must not exceed 100 characters.',
        ]);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower(request()->account_name)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        $lastCountId = AcAccount::max('count_id') ?? 0;
        $countId = $lastCountId + 1;
        
        $parentAccount = AcAccount::where('id', request()->parent_id)->first();
        $accountType = '';
        $normalBalance = '';
        if ($parentAccount) {
            $accountType = $parentAccount->account_type;
            $normalBalance = $parentAccount->normal_balance;
        } else {
            $accountType = '';
            $normalBalance = '';
        }

        AcAccount::insert([
            'store_id' => 1,
            'count_id' => $countId,
            'account_type' => $accountType,
            'normal_balance' => $normalBalance,
            'parent_id' => request()->parent_id ?? '',
            'account_name' => request()->account_name ?? '',
            'account_code' => request()->account_code ?? '',
            'sort_code' => request()->account_code ?? '',
            'balance' => 0.0000,
            'note' => request()->note ?? '',

            'creator' => auth()->user()->id,
            'slug' => $slug . time(),
            'status' => 'active',
            'created_at' => Carbon::now('Asia/Dhaka')
        ]);

        Toastr::success('Added successfully!', 'Success');
        return back();

    }


    public function viewAllAcAccount(Request $request)
    {
        if ($request->ajax()) {
            $data = AcAccount::with('user')
                ->orderBy('id', 'DESC')
                ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user', function ($data) {
                    return $data->user ? $data->user->name : 'N/A';
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d h:i", strtotime($data->created_at));
                })
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . url('edit/ac-account') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.account.view');
    }


    public function editAcAccount($slug)
    {
        $data = AcAccount::where('status', 'active')->where('slug', $slug)->first();
        $accounts = AcAccount::where('status', 'active')->get();
        return view('backend.account.edit', compact('data', 'accounts'));
    }

    public function updateAcAccount(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'account_name' => ['required', 'string', 'max:100'],
            'account_code' => ['required', 'string', 'max:100'],
        ], [
            'account_name.required' => 'account name is required.',
            'account_name.max' => 'account name must not exceed 100 characters.',
            'account_code.required' => 'account code is required.',
            'account_code.max' => 'account code must not exceed 100 characters.',
        ]);

        // Check if the selected product_warehouse_room_id exists for the selected product_warehouse_id        
        $data = AcAccount::where('id', request()->account_id)->first();

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($data->account_name)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        $data->store_id = request()->store_id ?? $data->store_id;
        $data->parent_id = request()->parent_id ?? $data->parent_id;
        $data->account_name = request()->account_name ?? $data->account_name;
        $data->account_code = request()->account_code ?? $data->account_code;
        $data->sort_code = request()->account_code ?? $data->sort_code;
        $data->balance = 0.0000;
        $data->note = request()->note ?? $data->note;


        if ($data->account_name != $request->account_name) {
            $data->slug = $slug . time();
        }

        $data->creator = auth()->user()->id;
        $data->status = request()->status ?? $data->status;
        $data->updated_at = Carbon::now('Asia/Dhaka');
        $data->save();

        Toastr::success('Successfully Updated', 'Success!');
        return redirect()->route('ViewAllAcAccount');
    }


    public function deleteAcAccount($slug)
    {
        $data = AcAccount::where('slug', $slug)->first();

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();

        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }


    private function buildTree($accounts, $parentId = null)
    {
        $tree = [];

        foreach ($accounts as $account) {
            if ($account->parent_id == $parentId) {
                $children = $this->buildTree($accounts, $account->id);
                $node = [
                    'id' => $account->id,
                    'text' => $account->account_name,
                ];

                if (!empty($children)) {
                    $node['inc'] = $children;
                }

                $tree[] = $node;
            }
        }

        return $tree;
    }


    public function getJsonAcAccount()
    {
        $accounts = AcAccount::where('status', 'active')
                                ->where('account_name', '!=', 'Expense')
                                ->get();
        
        // Build tree structure
        $nestedData = $this->buildTree($accounts);
        
        // If tree is empty, try building with parent_id = 0 as root
        if (empty($nestedData)) {
            $nestedData = $this->buildTree($accounts, 0);
        }
        
        // If still empty, return flat structure as fallback
        if (empty($nestedData) && $accounts->count() > 0) {
            $nestedData = $accounts->map(function($account) {
                return [
                    'id' => $account->id,
                    'text' => $account->account_name,
                ];
            })->toArray();
        }
        
        return response()->json($nestedData);
    }

    public function getJsonAcAccountExpense()
    {
        // Get all expense account heads
        $accounts = AcAccount::where('status', 'active')
            ->where('account_type', 'expense')
            ->get();
        
        // Build tree structure
        $nestedData = $this->buildTree($accounts);
        
        // If tree is empty, try building with parent_id = 0 as root
        if (empty($nestedData)) {
            $nestedData = $this->buildTree($accounts, 0);
        }
        
        // If still empty, return flat structure as fallback
        if (empty($nestedData) && $accounts->count() > 0) {
            $nestedData = $accounts->map(function($account) {
                return [
                    'id' => $account->id,
                    'text' => $account->account_name,
                ];
            })->toArray();
        }
        
        return response()->json($nestedData);
    }

    /**
     * Get credit accounts from payment types
     * Returns account heads related to payment types' credit_account_id
     */
    public function getJsonAcAccountFromPaymentTypes()
    {
        // Get all unique credit_account_id from db_paymenttypes
        $paymentTypes = \App\Http\Controllers\Account\Models\DbPaymentType::where('status', 'active')
            ->whereNotNull('debit_account_id')
            ->distinct()
            ->pluck('debit_account_id')
            ->toArray();
        
        // Get account heads for these credit account IDs
        $accounts = AcAccount::where('status', 'active')
            ->whereIn('id', $paymentTypes)
            ->get();
        
        // Also get parent accounts if they exist
        $parentIds = $accounts->pluck('parent_id')->filter()->unique()->toArray();
        if (!empty($parentIds)) {
            $parentAccounts = AcAccount::where('status', 'active')
                ->whereIn('id', $parentIds)
                ->get();
            $accounts = $accounts->merge($parentAccounts)->unique('id');
        }
        
        // Build tree structure
        $nestedData = $this->buildTree($accounts);
        
        // If tree is empty, try building with parent_id = 0 as root
        if (empty($nestedData)) {
            $nestedData = $this->buildTree($accounts, 0);
        }
        
        // If still empty, return flat structure as fallback
        if (empty($nestedData) && $accounts->count() > 0) {
            $nestedData = $accounts->map(function($account) {
                return [
                    'id' => $account->id,
                    'text' => $account->account_name,
                ];
            })->toArray();
        }
        
        return response()->json($nestedData);
    }

    /**
     * Get revenue accounts (for income categories)
     * Returns account heads where account_type is revenue
     */
    public function getJsonAcAccountRevenue()
    {
        // Get all revenue account heads
        $accounts = AcAccount::where('status', 'active')
            ->where('account_type', 'revenue')
            ->get();
        
        // Build tree structure
        $nestedData = $this->buildTree($accounts);
        
        // If tree is empty, try building with parent_id = 0 as root
        if (empty($nestedData)) {
            $nestedData = $this->buildTree($accounts, 0);
        }
        
        // If still empty, return flat structure as fallback
        if (empty($nestedData) && $accounts->count() > 0) {
            $nestedData = $accounts->map(function($account) {
                return [
                    'id' => $account->id,
                    'text' => $account->account_name,
                ];
            })->toArray();
        }
        
        return response()->json($nestedData);
    }

}
