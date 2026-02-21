<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventory\Models\ProductSupplier;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoom;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoomCartoon;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrder;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrderProduct;
use App\Http\Controllers\Inventory\Models\ProductPurchaseQuotation;
use App\Http\Controllers\Inventory\Models\ProductPurchaseQuotationProduct;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOtherCharge;
use App\Http\Controllers\Inventory\Models\ProductStock;
use App\Models\Product;
use App\Models\ProductPurchaseOrderProductUnit;
use App\Models\ProductVariantCombination;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ProductPurchaseOrderController extends Controller
{
    public function addNewPurchaseProductOrder()
    {
        $products = Product::where('status', 'active')->get();
        $suppliers = ProductSupplier::where('status', 'active')->get();
        $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        $productWarehouseRooms = ProductWarehouseRoom::where('status', 'active')->get();
        $productWarehouseRoomCartoons = ProductWarehouseRoomCartoon::where('status', 'active')->get();
        $other_charges_types = ProductPurchaseOtherCharge::where('status', 'active')->get();
        return view('backend.purchase_product_order.create', compact('products', 'suppliers', 'productWarehouses', 'productWarehouseRooms', 'productWarehouseRoomCartoons', 'other_charges_types'));
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

    public function viewAllPurchaseProductOrder(Request $request)
    {
        if ($request->ajax()) {

            $data = ProductPurchaseOrder::with('creator', 'order_products')
                ->orderBy('id', 'desc'); // Order by the ID

            // dd($data);
            return Datatables::of($data)
                ->editColumn('status', function ($data) {
                    return $data->status == "active" ? 'Active' : 'Inactive';
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d", strtotime($data->created_at));
                })
                ->editColumn('total', function ($data) {
                    $total = $data->total;
                    $total_products = $data->order_products->count();
                    return "<div>
                        <div>Total Products: <span class='badge badge-info'>$total_products</span></div>
                        <div>Total: <span class='badge badge-info'>$total</span></div>
                    </div>";
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    if ($data->order_status != 'received') {
                        $btn = '<div class="dropdown">';
                        $btn .= '<button class="btn-sm btn-primary dropdown-toggle rounded" type="button" id="actionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                        $btn .= 'Action';
                        $btn .= '</button>';
                        $btn .= '<div class="dropdown-menu" aria-labelledby="actionDropdown">';
                        $btn .= '<a class="dropdown-item" href="' . url('edit/purchase-product/order') . '/' . $data->slug . '"><i class="fas fa-edit"></i> Edit</a>';
                        $btn .= '<a class="dropdown-item" href="' . url('edit/purchase-product/order/confirm') . '/' . $data->slug . '"><i class="fas fa-edit"></i> Confirm Order</a>';
                        $btn .= '<a class="dropdown-item deleteBtn" href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete"><i class="fas fa-trash-alt"></i> Delete</a>';
                        $btn .= '</div>';
                        $btn .= '</div>';
                        return $btn;
                    } else {
                        return '<div>
                            <div class="text-success">
                                Order Confirmed
                            </div>
                            <div>
                                <a class="btn btn-sm btn-primary" href="' . url('print-purchase-barcode') . '/' . $data->id . '">
                                    <i class="fas fa-barcode"></i>
                                    Generate Barcode
                                </a>
                                <a class="dropdown-item deleteBtn" href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete"><i class="fas fa-trash-alt"></i> Delete</a>
                            </div>
                        </div>';
                    }
                })
                ->rawColumns(['action', 'total'])
                ->make(true);
            
        }
        return view('backend.purchase_product_order.view');
    }

    public function saveNewPurchaseProductOrder(Request $request)
    {
        // dd(request()->all());
        // $request->validate([
        //     'title' => ['required', 'string', 'max:255'],
        //     'product_warehouse_id' => ['required'],
        //     'product_warehouse_room_id' => ['required'],
        // ], [
        //     'title.required' => 'title is required.',
        // ]);

        $validationRules = [
            'purchase_product_warehouse_id' => 'required',
            'supplier_id' => 'required',
            'purchase_date' => 'required|date',
        ];

        $validationMessages = [
            'purchase_product_warehouse_id.required' => 'Product warehouse is required.',
            'supplier_id.required' => 'Supplier is required.',
            'purchase_date.required' => 'Purchase date is required.',
            'purchase_date.date' => 'Purchase date must be a valid date.',
            'other_charges.required' => 'Other charges are required.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $validationMessages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $other_charge_total = $this->calc_other_charges(request()->other_charges, request()->subtotal);

            $random_no = random_int(100, 999) . random_int(1000, 9999);
            $slug = Str::orderedUuid() . uniqid() . $random_no;

            $user = auth()->user();
            $order = new ProductPurchaseOrder();
            $order->product_warehouse_id = request()->purchase_product_warehouse_id;
            $order->product_warehouse_room_id = null;
            $order->product_warehouse_room_cartoon_id = null;
            $order->product_supplier_id = request()->supplier_id;
            $order->date = request()->purchase_date;

            $order->other_charge_type = json_encode(request()->other_charges);
            $order->other_charge_amount = $other_charge_total;
            $order->discount_type = request()->discount_to_all_type;
            $order->discount_amount = request()->discount_on_all;
            $order->calculated_discount_amount = request()->discount_to_all_amt;
            $order->round_off = (float) (request()->total_round_off_amt ?? 0);
            $order->subtotal = request()->subtotal_amt;
            $order->total = request()->grand_total_amt;
            $order->note = request()->purchase_note;
            $order->order_status = 'pending';
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

                $createPayload = [
                    'product_warehouse_id' => request()->purchase_product_warehouse_id,
                    'product_warehouse_room_id' => $warehouseRoomId,
                    'product_warehouse_room_cartoon_id' => $warehouseCartoonId,
                    'product_supplier_id' => request()->supplier_id,
                    'product_purchase_order_id' => $order->id,
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

                if (Schema::hasColumn('product_purchase_order_products', 'variant_combination_id')) {
                    $createPayload['variant_combination_id'] = $variantCombinationId;
                }

                if (Schema::hasColumn('product_purchase_order_products', 'previous_stock')) {
                    $createPayload['previous_stock'] = $productItem['previous_stock'] ?? null;
                }

                ProductPurchaseOrderProduct::create($createPayload);
            }

            // Generate code in format PO10007, PO10008, etc.
            $last = ProductPurchaseOrder::whereNotNull('code')
                ->orderBy('id', 'desc')
                ->first();
            
            if ($last && $last->code) {
                // Extract number from last code (e.g., "PO10007" -> 10007)
                $lastNumber = (int) preg_replace('/[^0-9]/', '', $last->code);
                $newNumber = $lastNumber + 1;
                $new_code = "PO" . $newNumber;
            } else {
                // Start from PO10001 if no previous code exists
                $new_code = "PO10001";
            }

            $order->code = $new_code;
            $order->reference = request()->reference ?? null;
            $order->slug = $order->id . $slug;
            $order->save();

            DB::commit();

            if (request()->order_status == 'received') {
                return redirect(url('edit/purchase-product/order/confirm') . '/' . $order->slug);
            }

            Toastr::success('Purchase Order has been added successfully!', 'Success');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Something went wrong! ' . $e->getMessage(), 'Error');
            return back()->withInput();
        }
    }

    public function editPurchaseProductOrder($slug)
    {
        $data = ProductPurchaseOrder::where('slug', $slug)->first();
        $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        $productWarehouseRooms = ProductWarehouseRoom::where('product_warehouse_id', $data->product_warehouse_id)->where('status', 'active')->get();
        $productWarehouseRoomCartoon = ProductWarehouseRoomCartoon::where('product_warehouse_id', $data->product_warehouse_id)->where('product_warehouse_room_id', $data->product_warehouse_room_id)->where('status', 'active')->get();
        $suppliers = ProductSupplier::where('status', 'active')->get();

        $other_charges_types = ProductPurchaseOtherCharge::where('status', 'active')->get();

        return view('backend.purchase_product_order.edit', compact('data', 'productWarehouses', 'productWarehouseRooms', 'productWarehouseRoomCartoon', 'suppliers', 'other_charges_types'));
    }

    public function apiEditPurchaseProduct($slug)
    {
        // dd($slug);
        $data = ProductPurchaseOrder::with(['order_products' => function ($query) {
            $query->with(['product', 'variantCombination']);
        }])->where('slug', $slug)->first();
        // $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        // $productWarehouseRooms = ProductWarehouseRoom::where('product_warehouse_id', $data->product_warehouse_id)->where('status', 'active')->get();
        // $productWarehouseRoomCartoon = ProductWarehouseRoomCartoon::where('product_warehouse_id', $data->product_warehouse_id)->where('product_warehouse_room_id', $data->product_warehouse_room_id)->where('status', 'active')->get();
        // $suppliers = ProductSupplier::where('status', 'active')->get();

        return response()->json([
            'data' => $data,
        ]);

        // return view('backend.purchase_product_quotation.edit', compact('data', 'productWarehouses', 'productWarehouseRooms', 'productWarehouseRoomCartoon', 'suppliers'));
    }

    public function updatePurchaseProductOrder(Request $request)
    {
        // dd(request()->all());
        $other_charge_total = $this->calc_other_charges(request()->other_charges, request()->subtotal);

        $order = ProductPurchaseOrder::where('id', request()->purchase_product_order_id)->first();

        $user = auth()->user();
        $order->product_warehouse_id = request()->purchase_product_warehouse_id;
        $order->product_warehouse_room_id = null;
        $order->product_warehouse_room_cartoon_id = null;
        $order->product_supplier_id = request()->supplier_id;
        $order->product_purchase_quotation_id = $order->product_purchase_quotation_id ?? '';
        $order->date = request()->purchase_date;

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
        $order->created_at = Carbon::now();
        $order->save();

        $variantColumnExists = Schema::hasColumn('product_purchase_order_products', 'variant_combination_id');
        $previousStockColumnExists = Schema::hasColumn('product_purchase_order_products', 'previous_stock');

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

            $query = ProductPurchaseOrderProduct::where('product_purchase_order_id', $order->id)
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
                $payload['product_purchase_order_id'] = $order->id;
                $payload['product_id'] = $product_id;
                $payload['created_at'] = now();
                $newProduct = ProductPurchaseOrderProduct::create($payload);
                $processedProductIds[] = $newProduct->id;
            }
        }

        if (!empty($processedProductIds)) {
            ProductPurchaseOrderProduct::where('product_purchase_order_id', $order->id)
                ->whereNotIn('id', $processedProductIds)
                ->delete();
        } else {
            ProductPurchaseOrderProduct::where('product_purchase_order_id', $order->id)->delete();
        }

        Toastr::success('Updation Has been Successful', 'Success!');
        return redirect()->route('ViewAllPurchaseProductOrder');
    }

    public function editPurchaseProductOrderConfirm($slug)
    {
        $data = ProductPurchaseOrder::with('order_products')->where('slug', $slug)->first();

        if ($data->order_status == 'received') {
            Toastr::error('Order has been received already!', 'Error');
            return back();
        }
        
        $data->order_status = 'received';
        $data->save();
    
        // hit ac_transations for purchase create
        record_purchase_create_accounting($data);

        $random_no = random_int(100, 999) . random_int(1000, 9999);
        $slug = Str::orderedUuid() . uniqid() . $random_no;

        // Insert records into ProductStock for each product in the order
        $stockHasColumns = [
            'has_variant' => Schema::hasColumn('product_stocks', 'has_variant'),
            'variant_combination_key' => Schema::hasColumn('product_stocks', 'variant_combination_key'),
            'variant_sku' => Schema::hasColumn('product_stocks', 'variant_sku'),
            'variant_barcode' => Schema::hasColumn('product_stocks', 'variant_barcode'),
            'variant_data' => Schema::hasColumn('product_stocks', 'variant_data'),
            'variant_price' => Schema::hasColumn('product_stocks', 'variant_price'),
            'variant_discount_price' => Schema::hasColumn('product_stocks', 'variant_discount_price'),
        ];

        $logHasColumns = [
            'has_variant' => Schema::hasColumn('product_stock_logs', 'has_variant'),
            'variant_combination_key' => Schema::hasColumn('product_stock_logs', 'variant_combination_key'),
            'variant_sku' => Schema::hasColumn('product_stock_logs', 'variant_sku'),
            'variant_data' => Schema::hasColumn('product_stock_logs', 'variant_data'),
            'variant_combination_id' => Schema::hasColumn('product_stock_logs', 'variant_combination_id'),
        ];

        foreach ($data->order_products as $product) {
            $product_stock = new ProductStock();
            $product_stock->product_warehouse_id = $product->product_warehouse_id;
            $product_stock->product_warehouse_room_id = $product->product_warehouse_room_id;
            $product_stock->product_warehouse_room_cartoon_id = $product->product_warehouse_room_cartoon_id;
            $product_stock->product_supplier_id = $product->product_supplier_id;
            $product_stock->product_purchase_order_id = $product->product_purchase_order_id;
            $product_stock->product_id = $product->product_id;
            if (Schema::hasColumn('product_stocks', 'variant_combination_id')) {
                $product_stock->variant_combination_id = $product->variant_combination_id ?? null;
            }

            $variantCombination = null;
            $hasVariant = !empty($product->variant_combination_id);
            if ($hasVariant) {
                $variantCombination = ProductVariantCombination::find($product->variant_combination_id);
            }

            if ($stockHasColumns['has_variant']) {
                $product_stock->has_variant = $hasVariant;
            }
            if ($stockHasColumns['variant_combination_key']) {
                $product_stock->variant_combination_key = $variantCombination->combination_key ?? null;
            }
            if ($stockHasColumns['variant_sku']) {
                $product_stock->variant_sku = $variantCombination->sku ?? null;
            }
            if ($stockHasColumns['variant_barcode']) {
                $product_stock->variant_barcode = $variantCombination->barcode ?? null;
            }
            if ($stockHasColumns['variant_data']) {
                $variantData = $variantCombination?->variant_values;
                $product_stock->variant_data = $variantData ? ($variantData) : null;
            }
            if ($stockHasColumns['variant_price']) {
                $product_stock->variant_price = $variantCombination->price ?? null;
            }
            if ($stockHasColumns['variant_discount_price']) {
                $product_stock->variant_discount_price = $variantCombination->discount_price ?? null;
            }

            $product_stock->date = $data->date;
            $product_stock->qty = $product->qty;
            $product_stock->purchase_price = $product->purchase_price;
            $product_stock->status = 'active';
            $product_stock->slug = $slug;
            $product_stock->save();

            $productModel = Product::where('id', $product->product_id)->first();

            if ($hasVariant) {
                if ($variantCombination) {
                    $variantCombination->stock = ($variantCombination->stock ?? 0) + $product_stock->qty;
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

                if ($productModel) {
                    $productModel->stock = $productModel->variantCombinations()->sum('stock');
                    $productModel->save();
                }
            } else {
                if ($productModel) {
                    $productModel->stock = ($productModel->stock ?? 0) + $product_stock->qty;
                    $productModel->update();
                } else {
                    logger()->warning('Product not found for stock update.', ['product_id' => $product->product_id]);
                }
            }

            $logData = [
                'warehouse_id'              => $product->product_warehouse_id,
                'product_id'                => $product->product_id,
                'product_name'              => $productModel->name ?? null,
                'product_purchase_id'       => $data->id,
                'quantity'                  => $product->qty,
                'type'                      => 'purchase',     // purchase / sales / return / initial / transfer
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

            // Get initial count of existing units for this product
            $existingUnitsCount = ProductPurchaseOrderProductUnit::where('product_purchase_order_product_id', $product->id)->count();
            
            // Create units based on purchased quantity
            $qty = (int)($product->qty ?? 1);
            for ($i = 1; $i <= $qty; $i++) {
                // Generate unique code: product_id + (existing count + current iteration)
                // Carbon::now()->format('ymd') . 
                $unitCode = $product->product_id. ($existingUnitsCount++);
                $productPurchaseOrderProductUnit = new ProductPurchaseOrderProductUnit();
                $productPurchaseOrderProductUnit->product_warehouse_id = $product->product_warehouse_id;
                $productPurchaseOrderProductUnit->product_purchase_order_id = $data->id;
                $productPurchaseOrderProductUnit->product_purchase_order_product_id = $product->id;
                $productPurchaseOrderProductUnit->product_id = $product->product_id;
                $productPurchaseOrderProductUnit->variant_combination_id = $product->variant_combination_id ?? null;
                $productPurchaseOrderProductUnit->code = $unitCode;
                $productPurchaseOrderProductUnit->price = $product->purchase_price ?? 0;
                $productPurchaseOrderProductUnit->unit_status = 'instock';
                $productPurchaseOrderProductUnit->creator = auth()->user()->id;
                $productPurchaseOrderProductUnit->slug = $product->id . ($existingUnitsCount++);
                $productPurchaseOrderProductUnit->created_at = Carbon::now();
                $productPurchaseOrderProductUnit->save();
            }
        }

        Toastr::success('Purchase Order has been received successfully!', 'Success!');
        return redirect()->route('ViewAllPurchaseProductOrder');
    }

    public function deletePurchaseProductOrder($slug)
    {
        $data = ProductPurchaseOrder::where('slug', $slug)->first();
        $product_unis = ProductPurchaseOrderProductUnit::where('product_purchase_order_id', $data->id)->get();
        foreach ($product_unis as $product_unit) {
            $variantCombination = ProductVariantCombination::where('id', $product_unit->variant_combination_id)->first();
            if ($variantCombination && $variantCombination->stock > 0) {
                $variantCombination->stock = ($variantCombination->stock ?? 0) - 1;
                $variantCombination->save();
            }
            $product_stock = ProductStock::where('variant_combination_id', $product_unit->variant_combination_id)->first();
            if ($product_stock && $product_stock->qty > 0) {
                $product_stock->qty = ($product_stock->qty ?? 0) - 1;
                $product_stock->save();
            }
        }

        ProductPurchaseOrderProductUnit::where('product_purchase_order_id', $data->id)->delete();
        ProductPurchaseOrderProduct::where('product_purchase_order_id', $data->id)->delete();

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();
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

    public function printPurchaseBarcode($purchase_id)
    {
        $purchaseOrder = ProductPurchaseOrder::find($purchase_id);
        
        if (!$purchaseOrder) {
            Toastr::error('Purchase order not found!', 'Error');
            return redirect()->route('ViewAllPurchaseProductOrder');
        }

        return view('backend.purchase_product_order.purchase_product_barcode_print', compact('purchase_id'));
    }

    public function apiGetPurchaseBarcodeUnits($purchase_id)
    {
        $units = ProductPurchaseOrderProductUnit::with([
            'product',
            'productPurchaseOrderProduct.product',
            'variantCombination'
        ])
        ->where('product_purchase_order_id', $purchase_id)
        ->get();

        $formattedUnits = $units->map(function ($unit) {
            $productName = $unit->productPurchaseOrderProduct->product_name ?? $unit->product->name ?? 'N/A';
            $productImage = $unit->product->image ?? $unit->productPurchaseOrderProduct->product->image ?? null;
            $variantTitle = null;
            
            if ($unit->variant_combination_id && $unit->variantCombination) {
                $variantValues = $unit->variantCombination->variant_values ?? [];
                if (is_array($variantValues) && !empty($variantValues)) {
                    $variantTitle = collect($variantValues)->map(function($value, $key) {
                        return ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
                    })->implode(' | ');
                } else {
                    $variantTitle = $unit->variantCombination->combination_key ?? null;
                }
            }

            $salesPrice = $unit->product->price ?? $unit->productPurchaseOrderProduct->purchase_price ?? $unit->price ?? 0;
            $sku = '';
            if ($unit->product && $unit->product->sku) {
                $sku = $unit->product->sku;
            } elseif ($unit->productPurchaseOrderProduct && $unit->productPurchaseOrderProduct->product && $unit->productPurchaseOrderProduct->product->sku) {
                $sku = $unit->productPurchaseOrderProduct->product->sku;
            }

            return [
                'id' => $unit->id,
                'code' => $unit->code,
                'product_name' => $productName,
                'product_image' => env('FILE_URL').'/'.$productImage,
                'variant_title' => $variantTitle,
                'unit_status' => $unit->unit_status,
                'sales_price' => (float)$salesPrice,
                'sku' => $sku,
                'barcode_value' => $unit->code, // Using code as barcode value
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedUnits
        ]);
    }

    public function apiUpdateBarcodeUnitCode(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:product_purchase_order_product_units,id',
            'code' => 'required|string|max:10|unique:product_purchase_order_product_units,code,' . $request->unit_id,
        ]);

        $unit = ProductPurchaseOrderProductUnit::find($request->unit_id);
        
        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => 'Unit not found'
            ], 404);
        }

        $unit->code = $request->code;
        $unit->save();

        return response()->json([
            'success' => true,
            'message' => 'Code updated successfully',
            'data' => [
                'id' => $unit->id,
                'code' => $unit->code
            ]
        ]);
    }
}
