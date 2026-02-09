<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Account\Models\AcAccount;
use App\Http\Controllers\Account\Models\AcTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class AdjustmentController extends Controller
{
    /**
     * Show adjustment list page
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = AcTransaction::with([
                    'debitAccount:id,account_name',
                    'creditAccount:id,account_name',
                    'user:id,name'
                ])
                ->where('transaction_type', 'ACCOUNT_ADJUSTMENT')
                ->where('status', 'active')
                ->orderBy('id', 'DESC');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function ($data) {
                    return $data->transaction_date ? date('Y-m-d', strtotime($data->transaction_date)) : 'N/A';
                })
                ->addColumn('debit_account', function ($data) {
                    if (isset($data->debitAccount) && is_object($data->debitAccount)) {
                        return '<span style="color:green; font-weight: 600;">' . $data->debitAccount->account_name . '</span>';
                    }
                    return '<span style="color:red;">N/A</span>';
                })
                ->addColumn('credit_account', function ($data) {
                    if (isset($data->creditAccount) && is_object($data->creditAccount)) {
                        return '<span style="color:blue; font-weight: 600;">' . $data->creditAccount->account_name . '</span>';
                    }
                    return '<span style="color:red;">N/A</span>';
                })
                ->addColumn('amount', function ($data) {
                    $amount = $data->debit_amt ?? $data->credit_amt ?? 0;
                    $formatted = number_format($amount, 2);
                    return '<span style="color:green; font-weight: 600;">৳ ' . $formatted . '</span>';
                })
                ->addColumn('note', function ($data) {
                    $note = $data->note ?? 'N/A';
                    $noteLength = strlen($note);
                    if ($noteLength > 50) {
                        return '<span title="' . htmlspecialchars($note) . '">' . substr($note, 0, 50) . '...</span>';
                    }
                    return $note;
                })
                ->addColumn('creator_name', function ($data) {
                    if (isset($data->user) && is_object($data->user) && isset($data->user->name)) {
                        return ucfirst($data->user->name);
                    }
                    return 'N/A';
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d h:i a", strtotime($data->created_at));
                })
                ->rawColumns(['debit_account', 'credit_account', 'amount', 'note'])
                ->make(true);
        }
        return view('backend.transaction_adjustment.list');
    }

    /**
     * Show create adjustment form
     */
    public function create()
    {
        return view('backend.transaction_adjustment.create');
    }

    /**
     * Store new adjustment
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'debit_account' => ['required'],
            'credit_account' => ['required'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'note' => ['required', 'string', 'min:10'],
        ], [
            'date.required' => 'Date is required.',
            'date.date' => 'Date must be a valid date.',
            'debit_account.required' => 'Debit account is required.',
            'credit_account.required' => 'Credit account is required.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be greater than 0.',
            'note.required' => 'Note is required.',
            'note.min' => 'Note must be at least 10 characters.',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();

            // Generate payment code (ADJ = Adjustment)
            $paymentCode = generate_payment_code('ADJ');
            
            // Generate slug
            $slug = 'adj-' . time() . '-' . uniqid();

            // Create double entry transactions
            $transactions = [];

            // Row 1: Debit Entry Only
            $transactions[] = [
                'store_id' => $user->store_id ?? null,
                'payment_code' => $paymentCode,
                'transaction_date' => $request->date,
                'transaction_type' => 'ACCOUNT_ADJUSTMENT',
                'debit_account_id' => $request->debit_account,
                'credit_account_id' => null,
                'debit_amt' => $request->amount,
                'credit_amt' => null,
                'note' => $request->note,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => $slug . '-debit',
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka')
            ];

            // Row 2: Credit Entry Only
            $transactions[] = [
                'store_id' => $user->store_id ?? null,
                'payment_code' => $paymentCode,
                'transaction_date' => $request->date,
                'transaction_type' => 'ACCOUNT_ADJUSTMENT',
                'debit_account_id' => null,
                'credit_account_id' => $request->credit_account,
                'debit_amt' => null,
                'credit_amt' => $request->amount,
                'note' => $request->note,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => $slug . '-credit',
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka')
            ];

            // Insert transactions
            AcTransaction::insert($transactions);

            DB::commit();

            Toastr::success('Adjustment recorded successfully!', 'Success');
            return redirect()->route('ViewAllAdjustment');

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Error: ' . $e->getMessage(), 'Error');
            return back()->withInput();
        }
    }
}

