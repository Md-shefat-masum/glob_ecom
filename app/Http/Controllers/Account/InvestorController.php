<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Models\AcAccount;
use App\Http\Controllers\Account\Models\AcMoneyDeposit;
use App\Http\Controllers\Account\Models\AcMoneyWithdraw;
use App\Http\Controllers\Account\Models\AcInvestorRule;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class InvestorController extends Controller
{
    /**
     * Show create investor form
     */
    public function create()
    {
        return view('backend.investor.create');
    }

    /**
     * Store new investor
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'address' => ['nullable', 'string', 'max:500'],
            'start_date' => ['required', 'date'],
            'profit_ratio' => ['required', 'numeric', 'min:0', 'max:100'],
            'password' => ['required', 'string', 'min:6'],
            'confirm_password' => ['required', 'same:password'],
        ], [
            'name.required' => 'Name is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email already exists.',
            'phone.required' => 'Phone is required.',
            'phone.unique' => 'Phone already exists.',
            'start_date.required' => 'Invest start date is required.',
            'start_date.date' => 'Invest start date must be a valid date.',
            'profit_ratio.required' => 'Profit ratio is required.',
            'profit_ratio.numeric' => 'Profit ratio must be a number.',
            'profit_ratio.min' => 'Profit ratio must be between 0 and 100.',
            'profit_ratio.max' => 'Profit ratio must be between 0 and 100.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'confirm_password.required' => 'Confirm password is required.',
            'confirm_password.same' => 'Passwords do not match.',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();

            // Create user
            $investor = User::create([
                'name' => $request->name,
                'email' => $request->email ? mb_strtolower($request->email) : null,
                'phone' => preg_replace('/\s+/', '', $request->phone),
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'user_type' => 5, // Fixed for investor
                'store_id' => $user->store_id ?? null,
                'image' => $request->image ?? null,
                'status' => 1,
            ]);

            // Create investor account head
            $investorAccountName = 'investor_' . $investor->id;
            $investorNote = $investor->name;
            if ($investor->email) {
                $investorNote .= ', ' . $investor->email;
            }
            if ($investor->phone) {
                $investorNote .= ', ' . $investor->phone;
            }

            // Get all children accounts of Equity account (ID 15)
            $equityAccount = AcAccount::find(15);
            if (!$equityAccount) {
                throw new \Exception('Equity account (ID 15) not found!');
            }

            $childrenAccounts = AcAccount::where('parent_id', 15)
                ->where('status', 'active')
                ->get();

            // Find max numeric short_code value
            $maxShortCodeValue = 3000; // Start from equity account code
            foreach ($childrenAccounts as $child) {
                if ($child->short_code) {
                    // Extract numeric value from short_code
                    $numericValue = preg_replace('/[^0-9]/', '', $child->short_code);
                    if ($numericValue && is_numeric($numericValue)) {
                        $numericValue = (int)$numericValue;
                        if ($numericValue > $maxShortCodeValue) {
                            $maxShortCodeValue = $numericValue;
                        }
                    }
                }
            }

            // Increment short_code
            $newShortCode = $maxShortCodeValue + 1;

            // Generate account_code
            $accountCode = 'AC-' . $newShortCode;

            $investorAccount = AcAccount::create([
                'store_id' => $user->store_id ?? null,
                'account_type' => 'equity',
                'parent_id' => 15,
                'normal_balance' => 'credit',
                'account_name' => $investorAccountName,
                'account_selection_name' => $investorAccountName,
                'note' => $investorNote,
                'balance' => 0.0000,
                'status' => 'active',
                'creator' => $user->id,
                'slug' => Str::slug($investorAccountName) . '-' . time(),
                'short_code' => $newShortCode,
                'account_code' => $accountCode,
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka')
            ]);

            // Create investor rule
            AcInvestorRule::create([
                'store_id' => $user->store_id ?? null,
                'investor_id' => $investor->id,
                'start_date' => $request->start_date,
                'profit_ratio' => $request->profit_ratio,
                'creator' => $user->id,
                'slug' => Str::slug('investor-rule-' . $investor->id) . '-' . time(),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka')
            ]);

            DB::commit();
            
            // Check if request is AJAX before setting Toastr (which uses session)
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Investor created successfully!'
                ]);
            }
            
            Toastr::success('Investor created successfully!', 'Success');
            return redirect()->route('ViewAllInvestor');

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Error: ' . $e->getMessage(), 'Error');
            Log::error('Investor Create Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'user_id' => $user->id ?? null,
            ]);
            return back()->withInput();
        }
    }

    /**
     * Show all investors list
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $investors = User::where('user_type', '5')
                ->select('id', 'name', 'phone', 'email', 'image', 'created_at')
                ->with('investorRule');

            return DataTables::of($investors)
                ->addIndexColumn()
                ->addColumn('image', function ($data) {
                    if ($data->image) {
                        $imageUrl = str_replace(env('FILE_URL') . '/', '', $data->image);
                        $fullUrl = env('FILE_URL') . '/' . $imageUrl;
                        return '<img src="' . $fullUrl . '" alt="' . htmlspecialchars($data->name) . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">';
                    }
                    return '<div style="width: 50px; height: 50px; border-radius: 50%; background: #e0e0e0; display: flex; align-items: center; justify-content: center; color: #999;"><i class="fas fa-user"></i></div>';
                })
                ->addColumn('start_date', function ($data) {
                    if ($data->investorRule && $data->investorRule->start_date) {
                        return \Carbon\Carbon::parse($data->investorRule->start_date)->format('d M Y');
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('profit_ratio', function ($data) {
                    if ($data->investorRule && $data->investorRule->profit_ratio !== null) {
                        return '<span style="font-weight: 600;">' . number_format($data->investorRule->profit_ratio, 2) . '%</span>';
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('deposits', function ($data) {
                    $totalDeposits = AcMoneyDeposit::where('investor_id', $data->id)
                        ->sum('amount') ?? 0;
                    return '৳ ' . number_format($totalDeposits, 2);
                })
                ->addColumn('withdraws', function ($data) {
                    $totalWithdraws = AcMoneyWithdraw::where('investor_id', $data->id)
                        ->sum('amount') ?? 0;
                    return '৳ ' . number_format($totalWithdraws, 2);
                })
                ->addColumn('balance', function ($data) {
                    $totalDeposits = AcMoneyDeposit::where('investor_id', $data->id)
                        ->sum('amount') ?? 0;
                    $totalWithdraws = AcMoneyWithdraw::where('investor_id', $data->id)
                        ->sum('amount') ?? 0;
                    $balance = $totalDeposits - $totalWithdraws;
                    $color = $balance >= 0 ? 'green' : 'red';
                    return '<span style="color: ' . $color . '; font-weight: 600;">৳ ' . number_format($balance, 2) . '</span>';
                })
                ->addColumn('action', function ($data) {
                    $viewBtn = '<a href="' . route('ViewInvestorDetails', $data->id) . '" class="btn btn-info btn-sm" title="View Details"><i class="fas fa-eye"></i></a>';
                    $editBtn = '<a href="' . route('EditInvestor', $data->id) . '" class="btn btn-primary btn-sm" title="Edit"><i class="fas fa-edit"></i></a>';
                    return '<div class="btn-group">' . $viewBtn . ' ' . $editBtn . '</div>';
                })
                ->rawColumns(['image', 'start_date', 'profit_ratio', 'balance', 'action'])
                ->make(true);
        }

        // Calculate analytics
        $totalInvestors = User::where('user_type', '5')->count();
        $totalDeposits = AcMoneyDeposit::sum('amount') ?? 0;
        $totalWithdraws = AcMoneyWithdraw::sum('amount') ?? 0;
        $netBalance = $totalDeposits - $totalWithdraws;
        
        // Calculate average profit ratio (weighted by deposits)
        $investorsWithRules = User::where('user_type', '5')
            ->with('investorRule')
            ->get();
        
        $totalWeightedRatio = 0;
        $totalDepositForRatio = 0;
        $averageProfitRatio = 0;
        
        foreach ($investorsWithRules as $investor) {
            if ($investor->investorRule && $investor->investorRule->profit_ratio !== null) {
                $investorDeposits = AcMoneyDeposit::where('investor_id', $investor->id)->sum('amount') ?? 0;
                if ($investorDeposits > 0) {
                    $totalWeightedRatio += ($investor->investorRule->profit_ratio * $investorDeposits);
                    $totalDepositForRatio += $investorDeposits;
                }
            }
        }
        
        if ($totalDepositForRatio > 0) {
            $averageProfitRatio = $totalWeightedRatio / $totalDepositForRatio;
        }

        return view('backend.investor.index', compact(
            'totalInvestors',
            'totalDeposits',
            'totalWithdraws',
            'netBalance',
            'averageProfitRatio'
        ));
    }

    /**
     * Show investor details
     */
    public function show($id)
    {
        $investor = User::where('user_type', '5')->findOrFail($id);
        
        $deposits = AcMoneyDeposit::where('investor_id', $id)
            ->with(['paymentType', 'creator_info'])
            ->orderBy('id', 'desc')
            ->get();

        $withdraws = AcMoneyWithdraw::where('investor_id', $id)
            ->with(['paymentType', 'creator_info'])
            ->orderBy('id', 'desc')
            ->get();

        $totalDeposits = $deposits->sum('amount');
        $totalWithdraws = $withdraws->sum('amount');
        $balance = $totalDeposits - $totalWithdraws;

        return view('backend.investor.details', compact('investor', 'deposits', 'withdraws', 'totalDeposits', 'totalWithdraws', 'balance'));
    }

    /**
     * Show edit investor form
     */
    public function edit($id)
    {
        $investor = User::where('user_type', '5')->findOrFail($id);
        return view('backend.investor.edit', compact('investor'));
    }

    /**
     * Update investor
     */
    public function update(Request $request, $id)
    {
        $investor = User::where('user_type', '5')->findOrFail($id);

        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone,' . $id],
            'address' => ['nullable', 'string', 'max:500'],
            'start_date' => ['required', 'date'],
            'profit_ratio' => ['required', 'numeric', 'min:0', 'max:100'],
        ];

        $validationMessages = [
            'name.required' => 'Name is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email already exists.',
            'phone.required' => 'Phone is required.',
            'phone.unique' => 'Phone already exists.',
            'start_date.required' => 'Invest start date is required.',
            'start_date.date' => 'Invest start date must be a valid date.',
            'profit_ratio.required' => 'Profit ratio is required.',
            'profit_ratio.numeric' => 'Profit ratio must be a number.',
            'profit_ratio.min' => 'Profit ratio must be between 0 and 100.',
            'profit_ratio.max' => 'Profit ratio must be between 0 and 100.',
        ];

        // Only validate password if it's provided
        if ($request->filled('password')) {
            $validationRules['password'] = ['required', 'string', 'min:6'];
            $validationRules['confirm_password'] = ['required', 'same:password'];
            $validationMessages['password.required'] = 'Password is required.';
            $validationMessages['password.min'] = 'Password must be at least 6 characters.';
            $validationMessages['confirm_password.required'] = 'Confirm password is required.';
            $validationMessages['confirm_password.same'] = 'Passwords do not match.';
        }

        $request->validate($validationRules, $validationMessages);

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email ? mb_strtolower($request->email) : null,
                'phone' => preg_replace('/\s+/', '', $request->phone),
                'address' => $request->address,
            ];

            if ($request->image) {
                $updateData['image'] = $request->image;
            }

            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }

            $investor->update($updateData);

            // Update account note if account exists
            $investorAccountName = 'investor_' . $investor->id;
            $investorAccount = AcAccount::where('account_name', $investorAccountName)->first();
            
            if ($investorAccount) {
                $investorNote = $investor->name;
                if ($investor->email) {
                    $investorNote .= ', ' . $investor->email;
                }
                if ($investor->phone) {
                    $investorNote .= ', ' . $investor->phone;
                }
                
                $investorAccount->update([
                    'note' => $investorNote,
                    'updated_at' => Carbon::now('Asia/Dhaka')
                ]);
            }

            // Update or create investor rule
            $investorRule = AcInvestorRule::where('investor_id', $investor->id)->first();
            if ($investorRule) {
                $investorRule->update([
                    'start_date' => $request->start_date,
                    'profit_ratio' => $request->profit_ratio,
                    'updated_at' => Carbon::now('Asia/Dhaka')
                ]);
            } else {
                AcInvestorRule::create([
                    'store_id' => auth()->user()->store_id ?? null,
                    'investor_id' => $investor->id,
                    'start_date' => $request->start_date,
                    'profit_ratio' => $request->profit_ratio,
                    'creator' => auth()->user()->id,
                    'slug' => Str::slug('investor-rule-' . $investor->id) . '-' . time(),
                    'status' => 'active',
                    'created_at' => Carbon::now('Asia/Dhaka'),
                    'updated_at' => Carbon::now('Asia/Dhaka')
                ]);
            }

            // Check if request is AJAX before setting Toastr (which uses session)
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Investor updated successfully!'
                ]);
            }
            
            Toastr::success('Investor updated successfully!', 'Success');
            return redirect()->route('ViewAllInvestor');

        } catch (\Exception $e) {
            Toastr::error('Error: ' . $e->getMessage(), 'Error');
            Log::error('Investor Update Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'investor_id' => $id,
            ]);
            return back()->withInput();
        }
    }
}

