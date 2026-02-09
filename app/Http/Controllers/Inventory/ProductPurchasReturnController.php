<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventory\Models\ProductSupplier;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoom;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoomCartoon;
use App\Http\Controllers\Inventory\Models\ProductPurchaseQuotation;
use App\Http\Controllers\Inventory\Models\ProductPurchaseQuotationProduct;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOtherCharge;
use App\Http\Controllers\Inventory\Models\ProductStock;
use App\Models\Product;
use App\Models\ProductPurchaseOrderProductUnit;
use App\Models\ProductPurchaseReturn;
use App\Models\ProductPurchaseReturnProduct;
use App\Models\ProductStockLog;
use App\Models\ProductVariantCombination;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class ProductPurchasReturnController extends Controller
{
    public function addNewPurchaseReturnOrder()
    {
        $products = Product::where('status', 'active')->get();
        $suppliers = ProductSupplier::where('status', 'active')->get();
        $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        $productWarehouseRooms = ProductWarehouseRoom::where('status', 'active')->get();
        $productWarehouseRoomCartoons = ProductWarehouseRoomCartoon::where('status', 'active')->get();
        $other_charges_types = ProductPurchaseOtherCharge::where('status', 'active')->get();
        return view('backend.purchase_product_return.create', compact('products', 'suppliers', 'productWarehouses', 'productWarehouseRooms', 'productWarehouseRoomCartoons', 'other_charges_types'));
    }

    public function calc_other_charges($other_charges, $subtotal)
    {

        $percent_total = 0;
        $fixed_total = 0;

        foreach ($other_charges as $charge) {
            if ($charge['type'] === 'percent') {
                $percent_total += ($subtotal * $charge['amount']) / 100;
            } else {
                $fixed_total += $charge['amount'];
            }
        }

        $total = $percent_total + $fixed_total;
        return $total;
    }

    public function saveNewPurchaseReturnOrder(Request $request)
    {
        // dd(request()->all());
        // $request->validate([
        //     'title' => ['required', 'string', 'max:255'],
        //     'product_warehouse_id' => ['required'],
        //     'product_warehouse_room_id' => ['required'],
        // ], [
        //     'title.required' => 'title is required.',
        // ]);

        try {
            DB::beginTransaction();

            $other_charge_total = $this->calc_other_charges(request()->other_charges, request()->subtotal);

            $random_no = random_int(100, 999) . random_int(1000, 9999);
            $slug = Str::orderedUuid() . uniqid() . $random_no;

            $user = auth()->user();
            $order = new ProductPurchaseReturn();
            $order->product_warehouse_id = request()->purchase_product_warehouse_id;
            $order->product_warehouse_room_id = null;
            $order->product_warehouse_room_cartoon_id = null;
            $order->product_supplier_id = request()->supplier_id;
            // $order->product_purchase_quotation_id = request()->product_purchase_quotation_id;
            $order->date = request()->purchase_date;
            $order->purchase_code = request()->purchase_code;

            $order->other_charge_type = json_encode(request()->other_charges);
            $order->other_charge_amount = $other_charge_total;
            $order->discount_type = request()->discount_to_all_type;
            $order->discount_amount = request()->discount_on_all;
            $order->calculated_discount_amount = request()->discount_to_all_amt;
            $order->round_off = (float) (request()->total_round_off_amt ?? 0);
            $order->subtotal = request()->subtotal_amt;
            $order->total = request()->grand_total_amt;
            $order->note = request()->purchase_note;
            $order->order_status = request()->order_status ?? 'pending';
            $order->reference = request()->reference ?? null;
            $order->creator = $user->id;
            $order->status = 'active';
            $order->created_at = Carbon::now();
            $order->save();

            $products = $request->input('product', []);

            foreach ($products as $productItem) {
                $productId = $productItem['id'] ?? $productItem['product_id'] ?? null;
                if (!$productId) {
                    continue;
                }

                $variantCombinationId = $productItem['variant_combination_id'] ?? null;

                $unit_price = (float)($productItem['prices'] ?? $productItem['price'] ?? 0);
                $discount_percent = (float)($productItem['discounts'] ?? $productItem['discount'] ?? 0);
                $tax_percent = (float)($productItem['taxes'] ?? $productItem['tax'] ?? 0);
                $quantity = (float)($productItem['quantities'] ?? $productItem['quantity'] ?? 0);

                $warehouseRoomId = $productItem['warehouse_room_id'] ?? null;
                $warehouseRoomId = ($warehouseRoomId === '' || $warehouseRoomId === 'null') ? null : $warehouseRoomId;

                $warehouseCartoonId = $productItem['warehouse_cartoon_id'] ?? null;
                $warehouseCartoonId = ($warehouseCartoonId === '' || $warehouseCartoonId === 'null') ? null : $warehouseCartoonId;

                $discounted_price = $unit_price * (1 - ($discount_percent / 100));
                $final_price_per_unit = $discounted_price * (1 + ($tax_percent / 100));

                $product_slug = Str::orderedUuid() . $random_no . $order->id . uniqid();

                $product = Product::find($productId);
                $productName = $productItem['display_name'] ?? $productItem['name'] ?? ($product->name ?? null);

                // Process stock_codes - convert associative array to simple indexed array
                $codes = [];
                if (isset($productItem['stock_codes']) && is_array($productItem['stock_codes'])) {
                    // array_values() converts associative array with numeric keys to simple indexed array
                    $stockCodesArray = array_values($productItem['stock_codes']);
                    foreach ($stockCodesArray as $stock_code) {
                        $trimmedCode = trim($stock_code);
                        if (!empty($trimmedCode)) {
                            $codes[] = $trimmedCode;
                        }
                    }
                }

                $createPayload = [
                    'product_warehouse_id' => request()->purchase_product_warehouse_id,
                    'product_warehouse_room_id' => $warehouseRoomId,
                    'product_warehouse_room_cartoon_id' => $warehouseCartoonId,
                    'product_supplier_id' => request()->supplier_id,
                    'product_purchase_return_id' => $order->id,
                    'stock_codes' => $codes,
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'qty' => $quantity,
                    'product_price' => $unit_price,
                    'discount_type' => 'in_percentage',
                    'discount_amount' => $discount_percent,
                    'tax' => $tax_percent,
                    'purchase_price' => $final_price_per_unit,
                    'slug' => $product_slug,
                ];

                if (Schema::hasColumn('product_purchase_return_products', 'variant_combination_id')) {
                    $createPayload['variant_combination_id'] = $variantCombinationId;
                }

                if (Schema::hasColumn('product_purchase_return_products', 'previous_stock')) {
                    $createPayload['previous_stock'] = $productItem['previous_stock'] ?? null;
                }

                ProductPurchaseReturnProduct::create($createPayload);

                if (request()->order_status == 'returned') {
                    foreach ($codes as $stock_code) {
                        $productUnit = ProductPurchaseOrderProductUnit::where('code', $stock_code)->first();
                        if ($productUnit) {
                            $productUnit->unit_status = 'returned';
                            $productUnit->return_id = $order->id;
                            $productUnit->save();
                        }
                    }
                }
            }

            // Generate code in format PR10001, PR10002, etc.
            $last = ProductPurchaseReturn::whereNotNull('code')
                ->orderBy('id', 'desc')
                ->first();

            if ($last && $last->code) {
                // Extract number from last code (e.g., "PR10001" -> 10001)
                $lastNumber = (int) preg_replace('/[^0-9]/', '', $last->code);
                $newNumber = $lastNumber + 1;
                $new_code = "PR" . $newNumber;
            } else {
                // Start from PR10001 if no previous code exists
                $new_code = "PR10001";
            }

            $order->code = $new_code;
            $order->reference = request()->reference;
            $order->slug = $order->id . $slug;
            $order->save();

            if (request()->order_status == 'returned') {
                record_purchase_return_create_accounting($order);
            }

            DB::commit();

            Toastr::success('Product return has been added successfully!', 'Success');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Something went wrong! ' . $e->getMessage(), 'Error');
            return back()->withInput();
        }
    }

    public function viewAllPurchaseReturnOrder(Request $request)
    {
        if ($request->ajax()) {

            $data = ProductPurchaseReturn::with('creator', 'order_products')
                // ->where('status', 'active')
                ->orderBy('id', 'desc') // Order by the ID
                ->get();

            // dd($data);
            return Datatables::of($data)
                // ->editColumn('creator', function ($data) {
                //     return $data->creator ? $data->creator->name : 'N/A'; // Access creator name
                // })
                ->editColumn('status', function ($data) {
                    return $data->status == "active" ? 'Active' : 'Inactive';
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d", strtotime($data->created_at));
                })
                ->addIndexColumn()
                // ->addColumn('action', function ($data) {
                //     $btn = '<a href="' . url('edit/purchase-return/quotation') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                //     $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                //     return $btn;
                // })
                ->addColumn('action', function ($data) {
                    if ($data->order_status == 'returned') {
                        return '<div class="text-success">Return</div>';
                    } else {
                        $btn = '<div class="dropdown">';
                        $btn .= '<button class="btn-sm btn-primary dropdown-toggle rounded" type="button" id="actionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                        $btn .= 'Action';
                        $btn .= '</button>';
                        $btn .= '<div class="dropdown-menu" aria-labelledby="actionDropdown">';
                        $btn .= '<a class="dropdown-item" href="' . url('edit/purchase-return/order') . '/' . $data->slug . '"><i class="fas fa-edit"></i> Edit</a>';
                        $btn .= '<a class="dropdown-item" href="' . url('edit/purchase-return/order/confirm') . '/' . $data->slug . '"><i class="fas fa-edit"></i> Confirm Return</a>';
                        $btn .= '<a class="dropdown-item deleteBtn" href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete"><i class="fas fa-trash-alt"></i> Delete</a>';
                        $btn .= '</div>';
                        $btn .= '</div>';
                        return $btn;
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.purchase_product_return.view');
    }

    public function editPurchaseReturnOrder($slug)
    {
        $data = ProductPurchaseReturn::where('slug', $slug)->first();
        $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        $productWarehouseRooms = ProductWarehouseRoom::where('product_warehouse_id', $data->product_warehouse_id)->where('status', 'active')->get();
        $productWarehouseRoomCartoon = ProductWarehouseRoomCartoon::where('product_warehouse_id', $data->product_warehouse_id)->where('product_warehouse_room_id', $data->product_warehouse_room_id)->where('status', 'active')->get();
        $suppliers = ProductSupplier::where('status', 'active')->get();

        $other_charges_types = ProductPurchaseOtherCharge::where('status', 'active')->get();

        return view('backend.purchase_product_return.edit', compact('data', 'productWarehouses', 'productWarehouseRooms', 'productWarehouseRoomCartoon', 'suppliers', 'other_charges_types'));
    }

    public function apiEditPurchaseReturn($slug)
    {
        // dd($slug);
        $data = ProductPurchaseReturn::with(['order_products' => function ($query) {
            $query->with(['product', 'variantCombination']);
        }])->where('slug', $slug)->first();
        $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        $productWarehouseRooms = ProductWarehouseRoom::where('product_warehouse_id', $data->product_warehouse_id)->where('status', 'active')->get();
        $productWarehouseRoomCartoon = ProductWarehouseRoomCartoon::where('product_warehouse_id', $data->product_warehouse_id)->where('product_warehouse_room_id', $data->product_warehouse_room_id)->where('status', 'active')->get();
        $suppliers = ProductSupplier::where('status', 'active')->get();

        return response()->json([
            'data' => $data,
        ]);

        // return view('backend.purchase_product_quotation.edit', compact('data', 'productWarehouses', 'productWarehouseRooms', 'productWarehouseRoomCartoon', 'suppliers'));
    }

    public function updatePurchaseReturnOrder(Request $request)
    {
        // dd(request()->all());
        $other_charge_total = $this->calc_other_charges(request()->other_charges, request()->subtotal);

        $order = ProductPurchaseReturn::where('id', request()->purchase_product_return_id)->first();

        if (!$order) {
            Toastr::error('Purchase return order not found!', 'Error');
            return back();
        }

        $previousOrderStatus = $order->order_status;
        $newOrderStatus = request()->order_status ?? 'pending';

        $user = auth()->user();
        $order->product_warehouse_id = request()->purchase_product_warehouse_id;
        $order->product_warehouse_room_id = null;
        $order->product_warehouse_room_cartoon_id = null;
        $order->product_supplier_id = request()->supplier_id;
        // $order->product_purchase_quotation_id = request()->product_purchase_quotation_id;
        $order->date = request()->purchase_date;
        $order->purchase_code = request()->purchase_code;
        $order->reference = request()->reference ?? null;
        $order->order_status = $newOrderStatus;

        // $order->other_charge_type = request()->other_charges_type;
        // $order->other_charge_percentage = request()->other_charges_input_amount;
        // $order->other_charge_amount = request()->other_charges_amt;

        $order->other_charge_type = json_encode(request()->other_charges);
        // $order->other_charge_percentage = request()->other_charges_input_amount;
        $order->other_charge_amount = $other_charge_total;

        $order->discount_type = request()->discount_to_all_type;
        $order->discount_amount = request()->discount_on_all;
        $order->calculated_discount_amount = request()->discount_to_all_amt;
        $order->round_off = (float) (request()->total_round_off_amt ?? 0);
        $order->subtotal = request()->subtotal;
        $order->total = request()->grand_total_amt;
        $order->note = request()->purchase_note;
        // $order->is_ordered = 'pending';
        $order->creator = $user->id;
        $order->status = 'active';
        $order->save();

        $variantColumnExists = Schema::hasColumn('product_purchase_return_products', 'variant_combination_id');
        $previousStockColumnExists = Schema::hasColumn('product_purchase_return_products', 'previous_stock');

        $processedProductIds = [];

        $products = $request->input('product', []);

        foreach ($products as $productItem) {
            $product_id = $productItem['id'] ?? $productItem['product_id'] ?? null;
            if (!$product_id) {
                continue;
            }

            $variantCombinationId = $productItem['variant_combination_id'] ?? null;
            $unit_price = (float)($productItem['prices'] ?? $productItem['price'] ?? 0);
            $discount_percent = (float)($productItem['discounts'] ?? $productItem['discount'] ?? 0);
            $tax_percent = (float)($productItem['taxes'] ?? $productItem['tax'] ?? 0);
            $quantity = (float)($productItem['quantities'] ?? $productItem['quantity'] ?? 0);

            $warehouseRoomId = $productItem['warehouse_room_id'] ?? null;
            $warehouseRoomId = ($warehouseRoomId === '' || $warehouseRoomId === 'null') ? null : $warehouseRoomId;

            $warehouseCartoonId = $productItem['warehouse_cartoon_id'] ?? null;
            $warehouseCartoonId = ($warehouseCartoonId === '' || $warehouseCartoonId === 'null') ? null : $warehouseCartoonId;

            $discounted_price = $unit_price * (1 - ($discount_percent / 100));
            $final_price_per_unit = $discounted_price * (1 + ($tax_percent / 100));

            $product_slug = Str::orderedUuid() . $order->id . uniqid();

            $productName = $productItem['display_name'] ?? $productItem['name'] ?? null;

            // Process stock_codes - convert associative array to simple indexed array
            $codes = [];
            if (isset($productItem['stock_codes']) && is_array($productItem['stock_codes'])) {
                // array_values() converts associative array with numeric keys to simple indexed array
                $stockCodesArray = array_values($productItem['stock_codes']);
                foreach ($stockCodesArray as $stock_code) {
                    $trimmedCode = trim($stock_code);
                    if (!empty($trimmedCode)) {
                        $codes[] = $trimmedCode;
                    }
                }
            }

            $query = ProductPurchaseReturnProduct::where('product_purchase_return_id', $order->id)
                ->where('product_id', $product_id);

            if ($variantColumnExists) {
                if ($variantCombinationId) {
                    $query->where('variant_combination_id', $variantCombinationId);
                } else {
                    $query->whereNull('variant_combination_id');
                }
            }

            $existingProduct = $query->first();

            $payload = [
                'product_warehouse_id' => $request->purchase_product_warehouse_id,
                'product_warehouse_room_id' => $warehouseRoomId,
                'product_warehouse_room_cartoon_id' => $warehouseCartoonId,
                'product_supplier_id' => $request->supplier_id,
                'product_name' => $productName,
                'stock_codes' => $codes,
                'qty' => $quantity,
                'product_price' => $unit_price,
                'discount_type' => 'in_percentage',
                'discount_amount' => $discount_percent,
                'tax' => $tax_percent,
                'purchase_price' => $final_price_per_unit,
                'slug' => $product_slug,
                'updated_at' => now(),
            ];

            if ($previousStockColumnExists) {
                $payload['previous_stock'] = $productItem['previous_stock'] ?? null;
            }

            if ($variantColumnExists) {
                $payload['variant_combination_id'] = $variantCombinationId;
            }

            if ($existingProduct) {
                $existingProduct->update($payload);
                $processedProductIds[] = $existingProduct->id;
            } else {
                $payload['product_purchase_return_id'] = $order->id;
                $payload['product_id'] = $product_id;
                $payload['created_at'] = now();
                $newProduct = ProductPurchaseReturnProduct::create($payload);
                $processedProductIds[] = $newProduct->id;
            }
        }

        // Handle ProductPurchaseOrderProductUnit status updates after all products are processed
        if ($previousOrderStatus == 'returned' && $newOrderStatus != 'returned') {
            // If status changed from 'returned' to something else, revert all unit statuses
            $existingReturnProducts = ProductPurchaseReturnProduct::where('product_purchase_return_id', $order->id)->get();
            foreach ($existingReturnProducts as $returnProduct) {
                $oldCodes = is_array($returnProduct->stock_codes) ? $returnProduct->stock_codes : [];
                foreach ($oldCodes as $stock_code) {
                    $productUnit = ProductPurchaseOrderProductUnit::where('code', $stock_code)
                        ->where('return_id', $order->id)
                        ->first();
                    if ($productUnit) {
                        $productUnit->unit_status = 'instock';
                        $productUnit->return_id = null;
                        $productUnit->save();
                    }
                }
            }
        } elseif ($newOrderStatus == 'returned') {
            // If status is 'returned', revert old codes first (if status was already 'returned')
            if ($previousOrderStatus == 'returned') {
                $oldReturnProducts = ProductPurchaseReturnProduct::where('product_purchase_return_id', $order->id)
                    ->whereNotIn('id', $processedProductIds)
                    ->get();
                foreach ($oldReturnProducts as $returnProduct) {
                    $oldCodes = is_array($returnProduct->stock_codes) ? $returnProduct->stock_codes : [];
                    foreach ($oldCodes as $stock_code) {
                        $productUnit = ProductPurchaseOrderProductUnit::where('code', $stock_code)
                            ->where('return_id', $order->id)
                            ->first();
                        if ($productUnit) {
                            $productUnit->unit_status = 'instock';
                            $productUnit->return_id = null;
                            $productUnit->save();
                        }
                    }
                }
            }
            
            // Now mark all current codes as returned
            $currentReturnProducts = ProductPurchaseReturnProduct::where('product_purchase_return_id', $order->id)
                ->whereIn('id', $processedProductIds)
                ->get();
            foreach ($currentReturnProducts as $returnProduct) {
                $codes = is_array($returnProduct->stock_codes) ? $returnProduct->stock_codes : [];
                foreach ($codes as $stock_code) {
                    $productUnit = ProductPurchaseOrderProductUnit::where('code', $stock_code)->first();
                    if ($productUnit) {
                        $productUnit->unit_status = 'returned';
                        $productUnit->return_id = $order->id;
                        $productUnit->save();
                    }
                }
            }
        }

        if (!empty($processedProductIds)) {
            ProductPurchaseReturnProduct::where('product_purchase_return_id', $order->id)
                ->whereNotIn('id', $processedProductIds)
                ->delete();
        } else {
            ProductPurchaseReturnProduct::where('product_purchase_return_id', $order->id)->delete();
        }

        // Handle accounting when status changes to 'returned'
        if ($newOrderStatus == 'returned' && $previousOrderStatus != 'returned') {
            record_purchase_return_create_accounting($order);
        }

        Toastr::success('Updation Has been Successful', 'Success!');
        return redirect()->route('ViewAllPurchaseReturnOrder');
    }

    public function editPurchaseReturnOrderConfirm($slug)
    {
        $data = ProductPurchaseReturn::with('order_products')->where('slug', $slug)->first();

        if (!$data) {
            Toastr::error('Purchase return order not found!', 'Error');
            return back();
        }

        if ($data->order_status == 'returned') {
            Toastr::error('Order has been returned already!', 'Error');
            return back();
        }

        try {
            DB::beginTransaction();

            $data->order_status = 'returned';
            $data->save();

            $random_no = random_int(100, 999) . random_int(1000, 9999);
            $slug = Str::orderedUuid() . uniqid() . $random_no;

            $logHasColumns = [
                'has_variant' => Schema::hasColumn('product_stock_logs', 'has_variant'),
                'variant_combination_key' => Schema::hasColumn('product_stock_logs', 'variant_combination_key'),
                'variant_sku' => Schema::hasColumn('product_stock_logs', 'variant_sku'),
                'variant_data' => Schema::hasColumn('product_stock_logs', 'variant_data'),
                'variant_combination_id' => Schema::hasColumn('product_stock_logs', 'variant_combination_id'),
            ];

            foreach ($data->order_products as $product) {
                $productModel = Product::where('id', $product->product_id)->first();
                $hasVariant = !empty($product->variant_combination_id);
                $variantCombination = null;

                // Update ProductPurchaseOrderProductUnit status for stock codes
                $stockCodes = is_array($product->stock_codes) ? $product->stock_codes : [];
                foreach ($stockCodes as $stock_code) {
                    $productUnit = ProductPurchaseOrderProductUnit::where('code', $stock_code)->first();
                    if ($productUnit) {
                        $productUnit->unit_status = 'returned';
                        $productUnit->return_id = $data->id;
                        $productUnit->save();
                    }
                }

                if ($hasVariant) {
                    $variantCombination = ProductVariantCombination::find($product->variant_combination_id);
                    if ($variantCombination) {
                        $newVariantStock = ($variantCombination->stock ?? 0) - $product->qty;
                        $variantCombination->stock = $newVariantStock > 0 ? $newVariantStock : 0;
                        if ($product->product_warehouse_id) {
                            $variantCombination->product_warehouse_id = $product->product_warehouse_id;
                        }
                        if ($product->product_warehouse_room_id) {
                            $variantCombination->product_warehouse_room_id = $product->product_warehouse_room_id;
                        }
                        if ($product->product_warehouse_room_cartoon_id) {
                            $variantCombination->product_warehouse_room_cartoon_id = $product->product_warehouse_room_cartoon_id;
                        }
                        $variantCombination->save();
                    }
                }

                if ($productModel) {
                    if ($hasVariant) {
                        $productModel->stock = $productModel->variantCombinations()->sum('stock');
                    } else {
                        $currentStock = $productModel->stock ?? 0;
                        $newStock = $currentStock - $product->qty;
                        $productModel->stock = $newStock > 0 ? $newStock : 0;
                    }
                    $productModel->save();
                } else {
                    Log::warning('Product not found for stock update.', ['product_id' => $product->product_id]);
                }

                $logData = [
                    'warehouse_id'              => $product->product_warehouse_id,
                    'product_id'                => $product->product_id,
                    'product_name'              => $productModel->name ?? null,
                    'product_return_id'         => $data->id,
                    'quantity'                  => $product->qty,
                    'type'                      => 'purchase_return',
                ];

                if ($logHasColumns['has_variant']) {
                    $logData['has_variant'] = $hasVariant;
                }
                if ($logHasColumns['variant_combination_key']) {
                    $logData['variant_combination_key'] = $variantCombination->combination_key ?? null;
                }
                if ($logHasColumns['variant_sku']) {
                    $logData['variant_sku'] = $variantCombination->sku ?? null;
                }
                if ($logHasColumns['variant_data']) {
                    $logData['variant_data'] = $variantCombination?->variant_values ?? null;
                }
                if ($logHasColumns['variant_combination_id']) {
                    $logData['variant_combination_id'] = $product->variant_combination_id ?? null;
                }

                insert_stock_log($logData);
            }

            // Hit accounting for purchase return
            record_purchase_return_create_accounting($data);

            DB::commit();

            Toastr::success('Purchase Return has been confirmed successfully!', 'Success!');
            return redirect()->route('ViewAllPurchaseReturnOrder');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase Return Confirmation Error', [
                'message' => $e->getMessage(),
                'purchase_return_id' => $data->id ?? null,
                'purchase_return_code' => $data->code ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            Toastr::error('Error confirming purchase return: ' . $e->getMessage(), 'Error');
            return back();
        }
    }

    public function deletePurchaseReturnOrder($slug)
    {
        $data = ProductPurchaseReturn::where('slug', $slug)->first();

        ProductStockLog::where('product_return_id', $data->id)->delete();

        $data->delete();

        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }


    public function searchProduct(Request $request)
    {
        // Check if the search query is an exact match
        $query = request()->query('query');
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'price', 'slug')
            ->limit(10)  // Limit to 200 products
            ->get();  // Use `get()` to return all matched products in a single request

        return response()->json($products);
    }
}
