<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\Models\Customer;
use App\Http\Controllers\Account\Models\DbCustomerPayment;
use App\Models\Product;
use App\Models\ManualProductReturn;
use App\Models\ManualProductReturnItem;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManualProductReturnController extends Controller
{
    /**
     * Show create form
     */
    public function create()
    {
        $customers = Customer::where('status', 'active')->get();
        $products = Product::where('status', 'active')->select('id', 'name', 'price')->get();
        $return_code = $this->generateReturnCode();
        
        return view('backend.manual_product_return.create', compact('customers', 'products', 'return_code'));
    }

    /**
     * Store manual return
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'return_date' => 'required|date',
            'return_code' => 'required|unique:manual_product_returns,return_code',
            'return_items' => 'required|array|min:1',
            'return_items.*.product_name' => 'required|string',
            'return_items.*.qty' => 'required|integer|min:1',
            'return_items.*.unit_price' => 'required|numeric|min:0',
        ], [
            'return_items.required' => 'No products selected for return.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($request->customer_id);
            $user = auth()->user();
            $random_no = random_int(100, 999) . random_int(1000, 9999);
            $slug = Str::orderedUuid() . uniqid() . $random_no;

            // Calculate totals
            $subtotal = 0;
            foreach ($request->return_items as $item) {
                $subtotal += $item['qty'] * $item['unit_price'];
            }

            // Create return record
            $return = new ManualProductReturn();
            $return->return_code = $request->return_code;
            $return->customer_id = $customer->id;
            $return->return_date = $request->return_date;
            $return->return_reason = $request->return_reason;
            $return->subtotal = $subtotal;
            $return->total = $subtotal;
            $return->refund_method = 'wallet';
            $return->refund_status = 'completed';
            $return->return_status = 'approved';
            $return->note = $request->note;
            $return->creator = $user->id;
            $return->status = 'active';
            $return->created_at = Carbon::now();
            $return->save();

            // Create return items
            foreach ($request->return_items as $item) {
                $item_slug = Str::orderedUuid() . $random_no . $return->id . uniqid();
                
                ManualProductReturnItem::create([
                    'manual_product_return_id' => $return->id,
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['qty'] * $item['unit_price'],
                    'slug' => $item_slug,
                    'creator' => $user->id,
                ]);

                // Update product stock if product exists in system
                if (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->stock += $item['qty'];
                        $product->save();

                        // Insert stock log
                        insert_stock_log([
                            'warehouse_id' => null,
                            'product_id' => $item['product_id'],
                            'product_name' => $product->name,
                            'product_return_id' => $return->id,
                            'quantity' => $item['qty'],
                            'type' => 'return',
                        ]);
                    }
                }
            }

            $return->slug = $return->id . $slug;
            $return->save();

            // Add refund to customer wallet
            $customer->available_advance += $subtotal;
            $customer->save();

            // Record customer payment as advance
            DbCustomerPayment::create([
                'customer_id' => $customer->id,
                'order_id' => null,
                'payment_date' => $request->return_date,
                'payment_type' => 'advance',
                'payment' => $subtotal,
                'payment_mode' => 'wallet',
                'payment_note' => "Manual product return refund - Return Code: {$return->return_code}",
                'creator' => $user->id,
                'slug' => Str::orderedUuid() . uniqid(),
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            // Record accounting entry
            $this->recordManualReturnAccounting($return, $customer, $subtotal);

            DB::commit();

            Toastr::success('Manual return has been recorded successfully!', 'Success');
            return response()->json([
                'success' => true,
                'message' => 'Manual return has been recorded successfully!',
                'redirect' => route('ViewAllManualProductReturns')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View all manual returns
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ManualProductReturn::with(['customer', 'return_items', 'creator'])
                ->orderBy('id', 'desc')
                ->get();

            return Datatables::of($data)
                ->editColumn('customer', function ($data) {
                    return $data->customer ? $data->customer->name : 'N/A';
                })
                ->editColumn('return_date', function ($data) {
                    return date("Y-m-d", strtotime($data->return_date));
                })
                ->editColumn('return_status', function ($data) {
                    $badges = [
                        'approved' => '<span class="badge badge-success">Approved</span>',
                        'pending' => '<span class="badge badge-warning">Pending</span>',
                        'rejected' => '<span class="badge badge-danger">Rejected</span>',
                    ];
                    return $badges[$data->return_status] ?? '<span class="badge badge-light">N/A</span>';
                })
                ->editColumn('total', function ($data) {
                    return '৳' . number_format($data->total, 2);
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '<div class="dropdown">';
                    $btn .= '<button class="btn-sm btn-primary dropdown-toggle rounded" type="button" data-toggle="dropdown">';
                    $btn .= '<i class="fas fa-cog"></i> Actions';
                    $btn .= '</button>';
                    $btn .= '<div class="dropdown-menu">';
                    
                    // View
                    $btn .= '<a class="dropdown-item" href="' . route('ShowManualProductReturn', $data->slug) . '" target="_blank"><i class="fas fa-eye text-info"></i> View</a>';
                    
                    // Edit
                    $btn .= '<a class="dropdown-item" href="' . route('EditManualProductReturn', $data->slug) . '"><i class="fas fa-edit text-warning"></i> Edit</a>';
                    
                    // Delete
                    $btn .= '<div class="dropdown-divider"></div>';
                    $btn .= '<a class="dropdown-item deleteBtn" href="javascript:void(0)" data-id="' . $data->slug . '"><i class="fas fa-trash-alt text-danger"></i> Delete</a>';
                    
                    $btn .= '</div></div>';
                    return $btn;
                })
                ->rawColumns(['return_status', 'action'])
                ->make(true);
        }
        return view('backend.manual_product_return.index');
    }

    /**
     * Show return details
     */
    public function show($slug)
    {
        $return = ManualProductReturn::with(['return_items', 'customer', 'creator'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('backend.manual_product_return.show', compact('return'));
    }

    /**
     * Show edit form
     */
    public function edit($slug)
    {
        $return = ManualProductReturn::with('return_items')->where('slug', $slug)->firstOrFail();
        $customers = Customer::where('status', 'active')->get();
        $products = Product::where('status', 'active')->select('id', 'name', 'price')->get();
        
        return view('backend.manual_product_return.edit', compact('return', 'customers', 'products'));
    }

    /**
     * Update return
     */
    public function update(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'manual_product_return_id' => 'required|exists:manual_product_returns,id',
            'customer_id' => 'required|exists:customers,id',
            'return_date' => 'required|date',
            'return_items' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $return = ManualProductReturn::findOrFail($request->manual_product_return_id);
            $oldTotal = $return->total;
            $customer = Customer::findOrFail($request->customer_id);
            $user = auth()->user();

            // Reverse previous wallet credit
            $customer->available_advance -= $oldTotal;

            // Reverse previous stock changes
            foreach ($return->return_items as $oldItem) {
                if ($oldItem->product_id) {
                    $product = Product::find($oldItem->product_id);
                    if ($product) {
                        $product->stock -= $oldItem->qty;
                        $product->save();
                    }
                }
            }

            // Delete old items
            ManualProductReturnItem::where('manual_product_return_id', $return->id)->delete();

            // Calculate new totals
            $subtotal = 0;
            foreach ($request->return_items as $item) {
                $subtotal += $item['qty'] * $item['unit_price'];
            }

            // Update return record
            $return->customer_id = $customer->id;
            $return->return_date = $request->return_date;
            $return->return_reason = $request->return_reason;
            $return->subtotal = $subtotal;
            $return->total = $subtotal;
            $return->note = $request->note;
            $return->updated_at = Carbon::now();
            $return->save();

            $random_no = random_int(100, 999) . random_int(1000, 9999);

            // Create new return items
            foreach ($request->return_items as $item) {
                $item_slug = Str::orderedUuid() . $random_no . $return->id . uniqid();
                
                ManualProductReturnItem::create([
                    'manual_product_return_id' => $return->id,
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['qty'] * $item['unit_price'],
                    'slug' => $item_slug,
                    'creator' => $user->id,
                ]);

                // Update product stock
                if (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->stock += $item['qty'];
                        $product->save();

                        insert_stock_log([
                            'warehouse_id' => null,
                            'product_id' => $item['product_id'],
                            'product_name' => $product->name,
                            'product_return_id' => $return->id,
                            'quantity' => $item['qty'],
                            'type' => 'return',
                        ]);
                    }
                }
            }

            // Add new wallet credit
            $customer->available_advance += $subtotal;
            $customer->save();

            DB::commit();

            Toastr::success('Manual return has been updated successfully!', 'Success');
            return response()->json([
                'success' => true,
                'message' => 'Manual return has been updated successfully!',
                'redirect' => route('ViewAllManualProductReturns')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete return (soft delete)
     */
    public function destroy($slug)
    {
        try {
            DB::beginTransaction();

            $return = ManualProductReturn::with('return_items')->where('slug', $slug)->firstOrFail();
            $customer = Customer::find($return->customer_id);

            // Reverse wallet credit
            if ($customer) {
                $customer->available_advance -= $return->total;
                $customer->save();
            }

            // Reverse stock changes
            foreach ($return->return_items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->stock -= $item->qty;
                        $product->save();
                    }
                }
            }

            // Soft delete
            $return->status = 'inactive';
            $return->save();

            DB::commit();

            return response()->json([
                'success' => 'Deleted successfully!',
                'data' => 1
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting return: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Generate unique return code
     */
    private function generateReturnCode()
    {
        $year = Carbon::now()->format('y');
        $month = Carbon::now()->format('m');
        $prefix = 'MR' . $year . $month;

        $latestReturn = ManualProductReturn::where('return_code', 'like', $prefix . '%')
            ->orderBy('return_code', 'desc')
            ->first();

        if ($latestReturn) {
            $lastNumber = intval(substr($latestReturn->return_code, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Helper: Record accounting entry
     */
    private function recordManualReturnAccounting($return, $customer, $amount)
    {
        try {
            // This would create accounting entries
            // Debit: Sales Return Account
            // Credit: Customer Wallet/Advance Account
            
            // Implementation depends on your accounting system structure
            // You can use the existing record_sales_accounting_return function
            // or create a dedicated manual return accounting function
            
            logger()->info('Manual return accounting recorded', [
                'return_id' => $return->id,
                'return_code' => $return->return_code,
                'customer_id' => $customer->id,
                'amount' => $amount
            ]);
            
            return true;
        } catch (\Exception $e) {
            logger()->error('Manual return accounting error: ' . $e->getMessage());
            return false;
        }
    }
}

