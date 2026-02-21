<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Models\AcAccount;
use App\Http\Controllers\Account\Models\AcTransaction;
use App\Http\Controllers\Account\Models\DbPaymentType;
use App\Http\Controllers\Courier\PathaoController;
use App\Http\Controllers\Courier\SteadfastController;
use App\Http\Controllers\Customer\Models\Customer;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoom;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoomCartoon;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrderProduct;
use App\Models\AcEventMapping;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductOrderHold;
use App\Models\ProductOrderHoldItem;
use App\Models\ProductOrderProduct;
use App\Models\ProductPurchaseOrderProductUnit;
use App\Http\Controllers\Inventory\Models\ProductStock;
use App\Http\Controllers\Outlet\Models\CustomerSourceType;
use App\Http\Controllers\Outlet\Models\Outlet;
use App\Models\ProductVariantCombination;
use App\Models\BillingAddress;
use App\Models\ProductOrderCourierMethod;
use App\Models\ProductOrderDeliveryMethod;
use App\Models\ShippingInfo;
use App\Models\User;
use App\Models\UserSalesTarget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\ProductOrderProductUnit;
use App\Models\ProductStockLog;
use Illuminate\Support\Facades\Log;

class DesktopPosController extends Controller
{
    /**
     * Main POS page inside dashboard layout.
     */
    public function index()
    {
        $warehouses = ProductWarehouse::where('status', 'active')
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('backend.pos.desktop', compact('warehouses'));
    }

    /**
     * Target stats (sales target analytics) for the authenticated user.
     * Returns totals for current month by default; optional from_date, to_date.
     */
    public function targetStats(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'data' => null]);
        }

        $from = $request->get('from_date');
        $to = $request->get('to_date');
        $query = UserSalesTarget::where('user_id', $userId);

        if ($from) {
            $query->where('date', '>=', $from);
        }
        if ($to) {
            $query->where('date', '<=', $to);
        }
        if (!$from && !$to) {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $query->whereBetween('date', [$start, $end]);
        }

        $totalTargets = (clone $query)->sum('target');
        $totalCompleted = (clone $query)->sum('completed');
        $totalRemains = (clone $query)->sum('remains');
        $achievePercent = $totalTargets > 0 ? round(($totalCompleted / $totalTargets) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_targets' => (float) $totalTargets,
                'sales' => (float) $totalCompleted,
                'remains' => (float) $totalRemains,
                'achieve_percent' => $achievePercent,
            ],
        ]);
    }

    /**
     * Mobile-optimized POS layout.
     */
    public function mobile()
    {
        $warehouses = ProductWarehouse::where('status', 'active')
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('backend.pos.mobile', compact('warehouses'));
    }

    /**
     * Category data for filters.
     */
    public function categories()
    {
        $categories = DB::table('categories')
            ->select('id', 'name')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $subcategories = DB::table('subcategories')
            ->select('id', 'name', 'category_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        // Note: the table name in the schema is `child_categories`
        $childcategories = DB::table('child_categories')
            ->select('id', 'name', 'subcategory_id')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'subcategories' => $subcategories,
                'childcategories' => $childcategories,
            ],
        ]);
    }

    /**
     * Get all categories in nested form: category[subcategory[childcategory[]]]
     */
    public function nestedCategories()
    {
        // Get all active categories
        $categories = DB::table('categories')
            ->select('id', 'name', 'slug', 'icon')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        // Get all active subcategories
        $subcategories = DB::table('subcategories')
            ->select('id', 'name', 'slug', 'category_id', 'icon')
            ->where('status', 1)
            ->orderBy('name')
            ->get()
            ->groupBy('category_id');

        // Get all active child categories
        $childcategories = DB::table('child_categories')
            ->select('id', 'name', 'slug', 'category_id', 'subcategory_id', 'icon')
            ->where('status', 1)
            ->orderBy('name')
            ->get()
            ->groupBy('subcategory_id');

        // Build nested structure
        $nestedCategories = $categories->map(function ($category) use ($subcategories, $childcategories) {
            $categorySubcategories = $subcategories->get($category->id, collect())->map(function ($subcategory) use ($childcategories) {
                $subcategoryChildcategories = $childcategories->get($subcategory->id, collect())->map(function ($childcategory) {
                    return [
                        'id' => $childcategory->id,
                        'name' => $childcategory->name,
                        'slug' => $childcategory->slug,
                        'icon' => $childcategory->icon,
                    ];
                })->values();

                return [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'slug' => $subcategory->slug,
                    'icon' => $subcategory->icon,
                    'childcategories' => $subcategoryChildcategories,
                ];
            })->values();

            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'subcategories' => $categorySubcategories,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $nestedCategories,
        ]);
    }

    /**
     * Product search endpoint used by the live search bar.
     */
    public function searchProducts(Request $request)
    {
        return $this->productsByCategory($request);

        $q = trim($request->get('q', ''));
        $productId = $request->get('product_id');
        $warehouseId = $request->get('warehouse_id');

        if ($productId) {
            $product = Product::with('variantCombinations')->where('status', 1)->findOrFail($productId);
            $variants = $product->variantCombinations()->active()->get()->map(function (ProductVariantCombination $variant) use ($product, $warehouseId) {
                $this->ensureVariantBarcode($variant);

                return [
                    'id' => $variant->id,
                    'product_id' => $product->id,
                    'title' => $variant->name,
                    'barcode' => $variant->barcode,
                    'unit_price' => $variant->getEffectiveDiscountPrice() ?? $variant->getFinalPrice(),
                    'stock' => $this->getVariantStockForWarehouse($variant, $warehouseId),
                    'image_url' => $this->productImageUrl($variant->image ?: $product->image),
                    'has_variants' => true,
                    'variant_id' => $variant->id,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $variants,
            ]);
        }

        $query = Product::query()
            ->where('status', 1)
            ->where('is_package', 0);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->get('subcategory_id'));
        }
        if ($request->filled('childcategory_id')) {
            $query->where('childcategory_id', $request->get('childcategory_id'));
        }

        $products = $query->orderBy('name')
            ->limit(30)
            ->get()
            ->map(function (Product $product) use ($warehouseId) {
                $this->ensureProductBarcode($product);

                $stock_items = $this->getProductStockItemsForWarehouse($product, $warehouseId);
                $stock = $stock_items->sum('qty');

                return [
                    'id' => $product->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'title' => $product->name,
                    'sku' => $product->sku ?? $product->code,
                    'barcode' => $product->barcode,
                    'has_variants' => (bool) $product->has_variant,
                    'unit_price' => $product->discount_price && $product->discount_price > 0
                        ? $product->discount_price
                        : $product->price,
                    'stock' => $stock,
                    'stock_items' => $stock_items,
                    'image_url' => $this->productImageUrl($product->image),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Products listing by category filters + pagination.
     */
    public function productsByCategory(Request $request)
    {
        $page = max(1, (int) $request->get('page', 1));
        $perPage = max(1, min(48, (int) $request->get('per_page', 5)));
        $warehouseId = $request->get('warehouse_id');
        $q = trim($request->get('q', ''));

        $query = Product::query()
            ->where('status', 1)
            ->where('is_package', 0);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%');
                $sub->orWhere('code', 'like', '%' . $q . '%');
                $sub->orWhere('barcode', 'like', '%' . $q . '%');
                $sub->orWhere('sku', 'like', '%' . $q . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->get('subcategory_id'));
        }
        if ($request->filled('childcategory_id')) {
            $query->where('childcategory_id', $request->get('childcategory_id'));
        }

        // When warehouse is set: join warehouse stock and order by stock_qty desc (in-stock first)
        if ($warehouseId !== null && $warehouseId > 0) {
            $stockSub = DB::table('product_stocks')
                ->selectRaw('product_id, COALESCE(SUM(qty), 0) as stock_qty')
                ->where('product_warehouse_id', $warehouseId)
                ->groupBy('product_id');
            $query->leftJoinSub($stockSub, 'warehouse_stock', 'products.id', '=', 'warehouse_stock.product_id')
                ->orderByRaw('COALESCE(warehouse_stock.stock_qty, 0) DESC')
                ->orderBy('products.name');
        } else {
            $query->orderBy('name');
        }

        $total = $query->count();
        $items = $query->select('products.*')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function (Product $product) use ($warehouseId) {
                $this->ensureProductBarcode($product);

                $stock_items = $this->getProductStockItemsForWarehouse($product, $warehouseId);
                // $stock = $stock_items->sum('qty');

                $variant_values = [];
                $variant_stocks = [];

                if ($product->has_variant && $stock_items->count() > 0) {
                    $variant_keys = [];
                    $keys = [];
                    $values = [];

                    // First loop: collect stock data and variant data separately
                    foreach ($stock_items as $stock_item) {
                        // Store stock by variant combination key
                        if ($stock_item->variant_combination_key) {
                            $variant_stocks[$stock_item->variant_combination_key] = $stock_item->qty ?? 0;
                        }

                        // Collect variant_data for processing variant values
                        $variant_data = $stock_item->variant_data;
                        if ($variant_data) {
                            $variant_keys[] = $variant_data;
                            foreach ($variant_data as $key => $value) {
                                $keys[] = $key;
                                $values[] = $value;
                            }
                        }
                    }
                    $unique_keys = array_unique($keys);

                    // Second loop: build variant values structure
                    foreach ($unique_keys as $key) {
                        $key_values = [];
                        foreach ($variant_keys as $variant) {
                            if (isset($variant[$key])) {
                                $key_values[] = $variant[$key];
                            }
                        }
                        $variant_values[] = [$key => array_values(array_unique($key_values))];
                    }
                }

                $final_price = + ($product->discount_price && $product->discount_price > 0
                    ? $product->discount_price
                    : $product->price);

                return [
                    'id' => $product->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'title' => $product->name,
                    'sku' => $product->sku ?? $product->code,
                    'barcode' => $product->barcode,
                    'has_variants' => (bool) $product->has_variant,

                    // 'unit_price' => $final_price,
                    'unit_price' => +$product->price,
                    'main_price' => +$product->price,
                    'final_price' => $final_price,
                    'discount_price' => +$product->discount_price,
                    'discount_parcent' => +$product->discount_parcent,
                    'discount' => [
                        'percent' => +$product->discount_parcent,
                        'fixed' => +$product->price - +$product->discount_price,
                        'value' => $final_price,
                    ],

                    'stock' => $this->getProductStockForWarehouse($product, $warehouseId),
                    'variant_values' => $variant_values,
                    'variant_stocks' => $variant_stocks,
                    'image_url' => $this->productImageUrl($product->image),
                    'weight' => $product->weight ?? $product->shipping_info['weight'] ?? 0.5, // default 0.5kg
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'has_more' => ($page * $perPage) < $total,
                'page' => $page,
            ],
        ]);
    }

    /**
     * Find product by barcode/code from product_purchase_order_product_units.
     * Returns product info with unit info attached.
     */
    public function productsByBarcode(Request $request)
    {
        $code = trim($request->get('code', ''));
        $warehouseId = $request->get('warehouse_id');

        if ($code === '') {
            return response()->json([
                'success' => false,
                'message' => 'Barcode/code is required.',
            ], 400);
        }

        $unit = ProductPurchaseOrderProductUnit::where('code', $code)
            ->where('product_warehouse_id', $warehouseId)
            ->where('unit_status', 'instock')
            ->with(['product', 'variantCombination'])
            ->whereHas('product', function ($query) {
                $query->where('status', 1);
            })
            ->first();

        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => 'product unavailable for this barcode.',
            ], 404);
        }

        $product = $unit->product;
        $variant = $unit->variantCombination;

        $warehouseName = null;
        $roomName = null;
        $cartoonName = null;
        $final_price = 0;

        if ($unit->product_purchase_order_product_id) {
            $orderProduct = ProductPurchaseOrderProduct::find($unit->product_purchase_order_product_id);
            if ($orderProduct) {
                if ($orderProduct->product_warehouse_id) {
                    $wh = ProductWarehouse::find($orderProduct->product_warehouse_id);
                    $warehouseName = $wh ? $wh->title : null;
                }
                if ($orderProduct->product_warehouse_room_id) {
                    $room = ProductWarehouseRoom::find($orderProduct->product_warehouse_room_id);
                    $roomName = $room ? $room->title : null;
                }
                if ($orderProduct->product_warehouse_room_cartoon_id) {
                    $cartoon = ProductWarehouseRoomCartoon::find($orderProduct->product_warehouse_room_cartoon_id);
                    $cartoonName = $cartoon ? $cartoon->title : null;
                }
            }
        }

        $purchasePrice = (float) ($unit->price ?? 0);
        $final_price = (float) ($product->discount_price && $product->discount_price > 0
            ? $product->discount_price
            : $product->price);

        $productData = [
            'id' => $product->id,
            'name' => $product->name,
            'barcode' => $product->barcode,
            'image_url' => $this->productImageUrl($product->image),
            'unit' => [
                'id' => $unit->id,
                'code' => $unit->code,
                'purchase_price' => $purchasePrice,
                'price' => $product->price,
                'unit_status' => $unit->unit_status,
                'product_purchase_order_product_id' => $unit->product_purchase_order_product_id,
                'variant_combination_id' => $unit->variant_combination_id,
                'warehouse_name' => $warehouseName,
                'room_name' => $roomName,
                'cartoon_name' => $cartoonName,
            ],

            'unit_code' => $unit->code,
        ];

        if ($variant) {
            $productData['variant_id'] = $variant->id;
            $productData['variant_name'] = $variant->name;
            $productData['variant_barcode'] = $variant->barcode ?? null;
            $productData['image_url'] = $this->productImageUrl($variant->image ?: $product->image);

            $productData['purchase_price'] = $purchasePrice;
            $productData['unit_price'] = $product->price;

            // $productData['unit_price'] = (float) ($product->discount_price && $product->discount_price > 0
            //     ? $product->discount_price
            //     : ($variant->getEffectiveDiscountPrice() ?? $variant->getFinalPrice()));

            // $productData['unit_price'] = $unit->price !== null && $unit->price > 0
            //     ? (float) $unit->price
            //     : ($variant->getEffectiveDiscountPrice() ?? $variant->getFinalPrice());

            $productData['stock'] = $this->getVariantStockForWarehouse($variant, $warehouseId);
            $productData['max_qty'] = $productData['stock'];
        } else {
            $productData['variant_id'] = null;

            $productData['purchase_price'] = $purchasePrice;

            // $productData['unit_price'] = (float) ($product->discount_price && $product->discount_price > 0
            //     ? $product->discount_price
            //     : $product->price);

            $productData['unit_price'] = $product->price;

            $productData['stock'] = $this->getProductStockForWarehouse($product, $warehouseId);
            $productData['max_qty'] = $productData['stock'];
        }

        $productData = [
            ...$productData,

            // 'unit_price' => $final_price,
            'unit_price' => +$product->price,
            'main_price' => +$product->price,
            'final_price' => $final_price,
            'discount_price' => +$product->discount_price,
            'discount_parcent' => +$product->discount_parcent,
            'discount' => [
                'percent' => +$product->discount_parcent,
                'fixed' => +$product->price - +$product->discount_price,
                'value' => $final_price,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $productData,
        ]);
    }

    /**
     * Barcode lookup with priority: variant barcode > product barcode.
     */
    public function barcodeLookup(Request $request)
    {
        $code = trim($request->get('code', ''));
        $warehouseId = $request->get('warehouse_id');

        if ($code === '') {
            return response()->json([
                'success' => false,
                'message' => 'Barcode is required.',
            ], 400);
        }

        // 1. Variant barcode
        $variant = ProductVariantCombination::where('barcode', $code)->first();
        if ($variant) {
            $product = $variant->product;
            $this->ensureVariantBarcode($variant);

            return response()->json([
                'success' => true,
                'data' => [
                    'single' => [
                        'id' => $variant->id,
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                        'title' => $variant->name,
                        'barcode' => $variant->barcode,
                        'unit_price' => $variant->getEffectiveDiscountPrice() ?? $variant->getFinalPrice(),
                        'stock' => $this->getVariantStockForWarehouse($variant, $warehouseId),
                        'image_url' => $this->productImageUrl($variant->image ?: $product->image),
                    ],
                ],
            ]);
        }

        // 2. Product barcode
        $product = Product::where('barcode', $code)->first();
        if ($product) {
            $this->ensureProductBarcode($product);

            if ($product->has_variant) {
                $variants = $product->variantCombinations()->active()->get()->map(function (ProductVariantCombination $v) use ($product, $warehouseId) {
                    $this->ensureVariantBarcode($v);

                    return [
                        'id' => $v->id,
                        'product_id' => $product->id,
                        'variant_id' => $v->id,
                        'title' => $v->name,
                        'barcode' => $v->barcode,
                        'unit_price' => $v->getEffectiveDiscountPrice() ?? $v->getFinalPrice(),
                        'stock' => $this->getVariantStockForWarehouse($v, $warehouseId),
                        'image_url' => $this->productImageUrl($v->image ?: $product->image),
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => [
                        'items' => $variants,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'single' => [
                        'id' => $product->id,
                        'product_id' => $product->id,
                        'variant_id' => null,
                        'title' => $product->name,
                        'barcode' => $product->barcode,
                        'unit_price' => $product->discount_price && $product->discount_price > 0
                            ? $product->discount_price
                            : $product->price,
                        'stock' => $this->getProductStockForWarehouse($product, $warehouseId),
                        'image_url' => $this->productImageUrl($product->image),
                    ],
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No product found for barcode.',
        ], 404);
    }

    /**
     * Optional server-side add-to-cart validation (stock check).
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variant_combinations,id',
            'qty' => 'required|numeric|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $variant = null;
        $warehouseId = $request->get('warehouse_id');
        $availableStock = $this->getProductStockForWarehouse($product, $warehouseId);

        if (!empty($validated['variant_id'])) {
            $variant = ProductVariantCombination::findOrFail($validated['variant_id']);
            $availableStock = $this->getVariantStockForWarehouse($variant, $warehouseId);
        }

        if ($availableStock < $validated['qty']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ok' => true,
            ],
        ]);
    }

    /**
     * Save order as hold.
     */
    public function holdOrder(Request $request)
    {
        $payload = $request->validate([
            'cart' => 'required|array|min:1',
            'totals' => 'required|array',
            'customer.id' => 'nullable|integer',
            'order_note' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $warehouseId = $request->get('warehouse_id');

        $hold = ProductOrderHold::create([
            'user_id' => $user ? $user->id : null,
            'customer_id' => data_get($payload, 'customer.id'),
            'product_warehouse_id' => $warehouseId,
            'subtotal' => data_get($payload, 'totals.subtotal', 0),
            'discount_amount' => data_get($payload, 'totals.discount.amount', 0),
            'coupon_amount' => data_get($payload, 'totals.coupon.amount', 0),
            'extra_charge' => data_get($payload, 'totals.extra_charge', 0),
            'delivery_charge' => data_get($payload, 'totals.delivery_charge', 0),
            'round_off' => data_get($payload, 'totals.round_off', 0),
            'grand_total' => data_get($payload, 'totals.grand_total', 0),
            'meta' => [
                'cart' => $payload['cart'],
                'totals' => $payload['totals'],
                'customer' => $request->get('customer'),
                'note' => $payload['order_note'] ?? null,
            ],
        ]);

        foreach ($payload['cart'] as $item) {
            ProductOrderHoldItem::create([
                'hold_id' => $hold->id,
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'title' => $item['title'] ?? null,
                'qty' => $item['qty'] ?? 0,
                'unit_price' => $item['unit_price'] ?? 0,
                'discount_amount' => data_get($item, 'discount.amount', 0),
                'final_price' => $item['final_price'] ?? 0,
                'meta' => $item,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $hold->id,
            ],
            'message' => 'Order held successfully.',
        ]);
    }

    /**
     * Retrieve a hold order to resume.
     */
    public function getHold($id)
    {
        $hold = ProductOrderHold::with('items')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'cart' => $hold->meta['cart'] ?? [],
                'totals' => $hold->meta['totals'] ?? [],
                'customer' => $hold->meta['customer'] ?? null,
                'note' => $hold->meta['note'] ?? null,
                'hold' => $hold,
            ],
        ]);
    }

    /**
     * List holds for a warehouse (or all) for Hold List modal.
     */
    public function listHolds(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');

        $query = ProductOrderHold::with('items')
            ->orderBy('id', 'desc');

        if ($warehouseId) {
            $query->where('product_warehouse_id', $warehouseId);
        }

        $holds = $query->limit(100)->get()->map(function (ProductOrderHold $hold) {
            return [
                'id' => $hold->id,
                'warehouse_id' => $hold->product_warehouse_id,
                'customer_id' => $hold->customer_id,
                'subtotal' => $hold->subtotal,
                'grand_total' => $hold->grand_total,
                'created_at' => optional($hold->created_at)->toDateTimeString(),
                'items_count' => $hold->items->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $holds,
        ]);
    }

    /**
     * Search customer by phone, name or email.
     */
    public function searchCustomer(Request $request)
    {
        $q = trim($request->get('q', ''));
        $phone = trim($request->get('phone', ''));
        $first = $request->get('first', false);
        $customerId = $request->get('customer_id', null);

        $customer_query = Customer::query()
            ->select('customers.*')
            ->selectRaw('(SELECT COUNT(*) FROM product_orders WHERE product_orders.customer_id = customers.id) as order_count');

        // If customer_id is provided, use it directly
        if ($customerId) {
            $customer_query->where('id', $customerId);
        } elseif ($q !== '') {
            // Otherwise use search query
            $customer_query->where(function ($sub) use ($q) {
                $sub->where('phone', 'like', '%' . $q . '%')
                    ->orWhere('name', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%')
                    ->orWhere('id', $q);
            });
        }

        $customer_query->where('status', 'active')
            ->orderBy('id', 'desc');

        if ($phone !== '') {
            $first = true;
            $customers = $customer_query->where('phone', $phone)->first();
        } else {
            $customers = $first ? $customer_query->first() : $customer_query->paginate(10);
        }

        if (!$first) {
            foreach ($customers as $customer) {
                $customer->due_amount = ProductOrder::where('customer_id', $customer->id)->sum('due_amount');
                $customer->advance = $customer->available_advance ?? 0;

                // Load billing addresses from billing_addresses table
                $customer->billing_address = BillingAddress::where('customer_id', $customer->id)
                    ->where('order_id', 0)
                    ->get()
                    ->map(function ($addr) {
                        return [
                            'full_name' => $addr->full_name,
                            'phone' => $addr->phone,
                            'address' => $addr->address,
                            'division_id' => $addr->division_id,
                            'district_id' => $addr->district_id,
                        ];
                    })
                    ->toArray();

                // Load shipping addresses from shipping_addresses table
                $customer->shipping_address = ShippingInfo::where('customer_id', $customer->id)
                    ->where('order_id', 0)
                    ->get()
                    ->map(function ($addr) {
                        return [
                            'full_name' => $addr->full_name,
                            'phone' => $addr->phone,
                            'address' => $addr->address,
                            'division_id' => $addr->division_id,
                            'district_id' => $addr->district_id,
                        ];
                    })
                    ->toArray();
            }
            $customers->appends(request()->all());
        } else if ($customers) {
            $customers->due_amount = ProductOrder::where('customer_id', $customers->id)->sum('due_amount');
            $customers->advance = $customers->available_advance ?? 0;

            // Load billing addresses from billing_addresses table
            $customers->billing_address = BillingAddress::where('customer_id', $customers->id)
                ->where('order_id', 0)
                ->get()
                ->map(function ($addr) {
                    return [
                        'full_name' => $addr->full_name,
                        'phone' => $addr->phone,
                        'address' => $addr->address,
                        'division_id' => $addr->division_id,
                        'district_id' => $addr->district_id,
                    ];
                })
                ->toArray();

            // Load shipping addresses from shipping_addresses table
            $customers->shipping_address = ShippingInfo::where('customer_id', $customers->id)
                ->where('order_id', 0)
                ->get()
                ->map(function ($addr) {
                    return [
                        'full_name' => $addr->full_name,
                        'phone' => $addr->phone,
                        'address' => $addr->address,
                        'division_id' => $addr->division_id,
                        'district_id' => $addr->district_id,
                    ];
                })
                ->toArray();
        }

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    /**
     * Create or update a customer from POS.
     */
    public function createCustomer(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|integer|exists:customers,id',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:60',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'save_as_user' => 'nullable|boolean',
            'password' => 'nullable|string|min:6|required_if:save_as_user,1',
            'billing_address' => 'nullable|array',
            'billing_address.*.full_name' => 'nullable|string|max:255',
            'billing_address.*.phone' => 'nullable|string|max:60',
            'billing_address.*.address' => 'nullable|string',
            'shipping_address' => 'nullable|array',
            'shipping_address.*.full_name' => 'nullable|string|max:255',
            'shipping_address.*.phone' => 'nullable|string|max:60',
            'shipping_address.*.address' => 'nullable|string',
            'customer_source_type_id' => 'nullable|integer|exists:customer_source_types,id',
        ]);

        if (!empty($data['id'])) {
            $customer = Customer::find($data['id']) ?? new Customer();
        } elseif (!empty($data['mobile'])) {
            $customer = Customer::where('phone', $data['mobile'])->first() ?? new Customer();
        } else {
            $customer = new Customer();
        }

        $customer->name = $data['name'];
        $customer->phone = $data['mobile'];
        $customer->email = $data['email'] ?? null;
        $customer->address = $data['address'] ?? null;
        $customer->customer_source_type_id = $data['customer_source_type_id'] ?? null;

        if (!$customer->exists) {
            $customer->creator = Auth::id();
            $customer->slug = Str::orderedUuid();
            $customer->status = 'active';
        }
        $customer->save();

        // Handle billing addresses - save to billing_addresses table
        if (!empty($data['billing_address']) && is_array($data['billing_address'])) {
            $billingAddresses = array_filter($data['billing_address'], function ($addr) {
                return !empty($addr['full_name']) || !empty($addr['address']);
            });

            if (!empty($billingAddresses)) {
                // Delete existing billing addresses for this customer if updating
                if ($customer->exists) {
                    BillingAddress::where('customer_id', $customer->id)
                        ->where('order_id', 0)
                        ->delete();
                }

                // Insert new billing addresses
                foreach ($billingAddresses as $addr) {
                    BillingAddress::create([
                        'customer_id' => $customer->id,
                        'order_id' => 0, // 0 for customer addresses (not tied to an order)
                        'full_name' => $addr['full_name'] ?? null,
                        'phone' => $addr['phone'] ?? null,
                        'address' => $addr['address'] ?? null,
                        'division_id' => $addr['division_id'] ?? null,
                        'district_id' => $addr['district_id'] ?? null,
                        'city' => null,
                        'country' => null,
                        'thana' => null,
                        'post_code' => null,
                    ]);
                }
            }
        }

        // Handle shipping addresses - save to shipping_addresses table
        if (!empty($data['shipping_address']) && is_array($data['shipping_address'])) {
            $shippingAddresses = array_filter($data['shipping_address'], function ($addr) {
                return !empty($addr['full_name']) || !empty($addr['address']);
            });

            if (!empty($shippingAddresses)) {
                // Delete existing shipping addresses for this customer if updating
                if ($customer->exists) {
                    ShippingInfo::where('customer_id', $customer->id)
                        ->where('order_id', 0)
                        ->delete();
                }

                // Insert new shipping addresses
                foreach ($shippingAddresses as $addr) {
                    ShippingInfo::create([
                        'customer_id' => $customer->id,
                        'order_id' => 0, // 0 for customer addresses (not tied to an order)
                        'full_name' => $addr['full_name'] ?? null,
                        'phone' => $addr['phone'] ?? null,
                        'address' => $addr['address'] ?? null,
                        'division_id' => $addr['division_id'] ?? null,
                        'district_id' => $addr['district_id'] ?? null,
                        'email' => null,
                        'gender' => null,
                        'city' => null,
                        'country' => null,
                        'thana' => null,
                        'post_code' => null,
                    ]);
                }
            }
        }

        $user = null;
        // Create User account if save_as_user is true
        if (!empty($data['save_as_user']) && ($data['save_as_user'] == 1 || $data['save_as_user'] === true)) {
            if (!empty($data['password'])) {
                // Check if user already exists with this email or phone
                $existingUser = User::where(function ($query) use ($data) {
                    if (!empty($data['email'])) {
                        $query->where('email', $data['email']);
                    }
                    $query->orWhere('phone', $data['mobile']);
                })->first();

                if (!$existingUser) {
                    // Create new user
                    $user = User::create([
                        'store_id' => Auth::user()->store_id ?? null,
                        'name' => $data['name'],
                        'phone' => $data['mobile'],
                        'email' => $data['email'] ?? null,
                        'password' => Hash::make($data['password']),
                        'address' => $data['address'] ?? null,
                        'balance' => 0,
                        'user_type' => 3, // Customer type
                        'status' => 1, // Active
                    ]);

                    $customer->user_id = $user->id;
                    $customer->save();
                }
            }
        }

        $dueAmount = ProductOrder::where('customer_id', $customer->id)->sum('due_amount');

        // Load billing and shipping addresses from separate tables
        $billingAddresses = BillingAddress::where('customer_id', $customer->id)
            ->where('order_id', 0)
            ->get()
            ->map(function ($addr) {
                return [
                    'full_name' => $addr->full_name,
                    'phone' => $addr->phone,
                    'address' => $addr->address,
                ];
            })
            ->toArray();

        $shippingAddresses = ShippingInfo::where('customer_id', $customer->id)
            ->where('order_id', 0)
            ->get()
            ->map(function ($addr) {
                return [
                    'full_name' => $addr->full_name,
                    'phone' => $addr->phone,
                    'address' => $addr->address,
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address' => $customer->address,
                'billing_address' => $billingAddresses,
                'shipping_address' => $shippingAddresses,
                'advance' => $customer->available_advance ?? 0,
                'due' => $dueAmount,
            ],
            'message' => 'Customer saved successfully.',
        ]);
    }

    public function deleteCustomer(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:customers,id',
        ]);

        $customer = Customer::findOrFail($data['id']);

        // Check if customer has any orders
        $orderCount = ProductOrder::where('customer_id', $customer->id)->count();

        if ($orderCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete customer. Customer has ' . $orderCount . ' order(s). Please remove orders first.',
            ], 422);
        }

        // Delete related billing addresses
        BillingAddress::where('customer_id', $customer->id)
            ->where('order_id', 0)
            ->delete();

        // Delete related shipping addresses
        ShippingInfo::where('customer_id', $customer->id)
            ->where('order_id', 0)
            ->delete();

        // Delete the customer
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.',
        ]);
    }

    /**
     * Apply coupon using existing promo_codes logic.
     */
    public function applyCoupon(Request $request)
    {
        $code = trim($request->get('code', ''));
        $subtotal = (float) $request->get('subtotal', 0);

        if ($code === '') {
            return response()->json([
                'success' => false,
                'message' => 'Coupon code is required.',
            ], 400);
        }

        $coupon = DB::table('promo_codes')->where('code', $code)->where('status', 1)->first();
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found.',
            ], 404);
        }

        $today = date('Y-m-d');
        if ($coupon->effective_date && $coupon->effective_date > $today) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not applicable.',
            ], 422);
        }
        if ($coupon->expire_date && $coupon->expire_date < $today) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is expired.',
            ], 422);
        }

        if ($coupon->minimum_order_amount && $coupon->minimum_order_amount > $subtotal) {
            return response()->json([
                'success' => false,
                'message' => 'Order amount is below coupon minimum.',
            ], 422);
        }

        $discount = 0.0;
        $type = $coupon->type === 2 ? 'percent' : 'fixed';

        if ($type === 'percent') {
            $discount = ($subtotal * $coupon->value) / 100;
        } else {
            $discount = $coupon->value;
        }

        if ($discount > $subtotal) {
            return response()->json([
                'success' => false,
                'message' => 'Discount cannot exceed subtotal.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'type' => $type,
                'value' => (float) $coupon->value,
                'amount' => $discount,
            ],
            'message' => 'Coupon applied.',
        ]);
    }

    /**
     * Optional totals calculation endpoint (authoritative).
     */
    public function calculateTotals(Request $request)
    {
        $cart = $request->get('cart', []);
        $extra = (float) $request->get('extra_charge', 0);
        $delivery = (float) $request->get('delivery_charge', 0);
        $roundOff = (float) $request->get('round_off', 0);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += (float) ($item['final_price'] ?? 0);
        }

        $discountAmount = (float) data_get($request->get('discount', []), 'amount', 0);
        $couponAmount = (float) data_get($request->get('coupon', []), 'amount', 0);

        $grand = $subtotal - $discountAmount - $couponAmount + $extra + $delivery - $roundOff;

        return response()->json([
            'success' => true,
            'data' => [
                'subtotal' => $subtotal,
                'discount' => ['amount' => $discountAmount],
                'coupon' => ['amount' => $couponAmount],
                'extra_charge' => $extra,
                'delivery_charge' => $delivery,
                'round_off' => $roundOff,
                'grand_total' => $grand,
            ],
        ]);
    }

    /**
     * Create final ProductOrder from POS payload.
     */
    public function createOrder(Request $request)
    {
        // return response()->json($request->all(), 500);
        $payload = $request->validate([
            'cart' => 'required|array|min:1',
            'totals' => 'required|array',
            'customer.id' => 'nullable|integer|exists:customers,id',
            'customer.name' => 'nullable|string|max:255',
            // 'payments' => 'required|array|min:0',
            'use_advance' => 'boolean',
            'advance_amount' => 'nullable|numeric|min:0',
            'order_note' => 'nullable|string|max:500',
            'order_source' => 'nullable|string|max:50',
        ]);

        $cart = $payload['cart'];
        $totals = $payload['totals'];
        $paymentLines = $request->get('payments', []) ?? [];
        $useAdvance = (bool) ($payload['use_advance'] ?? false);
        $warehouseId = $request->get('warehouse_id');
        $deliveryInfo = $request->get('delivery_info', []) ?? [];
        $orderStatus = $request->get('order_status', 'quotation');

        $customerAddress = request()->get('customer')['address'] ?? null;
        $customerPhone = request()->get('customer')['phone'] ?? null;
        $customerName = request()->get('customer')['name'] ?? null;

        $grandTotal = (float) data_get($totals, 'grand_total', 0);
        $paymentTotal = 0;
        foreach ($paymentLines as $line) {
            if (!empty($line['amount'])) {
                $paymentTotal += (float) $line['amount'];
            }
        }

        $customerId = data_get($payload, 'customer.id');
        $advanceUsed = 0;
        $customer = null;

        $is_confirmed = in_array($orderStatus, ['invoiced', 'delivered']);

        if ($customerId && $customerId > 1) {
            $customer = Customer::findOrFail($customerId);
            if ($customerAddress) {
                $customer->address = $customerAddress;
                $customer->save();
            }
            if ($useAdvance) {
                // Use provided advance_amount if available, otherwise calculate automatically
                $providedAdvanceAmount = data_get($payload, 'advance_amount', 0);
                if ($providedAdvanceAmount > 0) {
                    // Validate that provided amount doesn't exceed available advance
                    $advanceUsed = min($providedAdvanceAmount, $customer->available_advance, $grandTotal - $paymentTotal);
                } else {
                    // Auto-calculate if not provided
                    if ($customer->available_advance > 0) {
                        $advanceUsed = min($customer->available_advance, $grandTotal - $paymentTotal);
                    }
                }
            }
        }

        $totalPaid = $paymentTotal + $advanceUsed;

        // Check if walking customer (id == 1) cannot have due amount
        if ($customerId == 1 && abs($totalPaid - $grandTotal) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Walking customer orders must be fully paid. Due amounts are not allowed for walk-in customers.',
            ], 422);
        }

        // POS orders must be fully paid; no due allowed
        // if (abs($totalPaid - $grandTotal) > 0.01) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'POS orders must be fully paid. Total payment (including advance) must equal grand total.',
        //     ], 422);
        // }

        DB::beginTransaction();
        try {
            $order = new ProductOrder();
            $order->store_id = auth()->user()->store_id;
            $order->order_code = $this->generateOrderCode();
            $order->product_warehouse_id = $warehouseId;
            $order->customer_id = $customerId;
            $order->customer_phone = $customerPhone;
            $order->customer_name = $customerName;
            $order->sale_date = Carbon::now();
            $order->subtotal = data_get($totals, 'subtotal', 0);
            $order->other_charges = [
                'extra_charge' => data_get($totals, 'extra_charge', 0),
                'delivery_charge' => data_get($totals, 'delivery_charge', 0),
            ];
            $order->other_charge_amount = ((float) data_get($totals, 'extra_charge', 0)) + ((float) data_get($totals, 'delivery_charge', 0));
            $order->discount_type = data_get($totals, 'discount.type', null);
            $order->discount_amount = data_get($totals, 'discount.value', 0);
            $order->calculated_discount_amount = data_get($totals, 'discount.amount', 0);
            $order->round_off_from_total = data_get($totals, 'round_off', 0);
            $order->decimal_round_off = data_get($totals, 'round_off', 0);
            $order->total = $grandTotal;
            $order->paid_amount = $totalPaid;
            $order->due_amount = $grandTotal - $totalPaid;
            $order->address = $customerAddress;
            $order->payments = array_merge(
                $this->paymentsArrayFromLines($paymentLines),
                [
                    'advance_used' => $advanceUsed,
                    'total_paid' => $totalPaid,
                    'total_due' => 0,
                ]
            );
            $order->note = $deliveryInfo['order_note'] ?? null;
            $order->order_source = $payload['order_source'] ?? 'pos';
            $order->order_status = $orderStatus;
            $order->delivery_info = $deliveryInfo;
            $order->creator = Auth::id();
            $order->status = 'active';
            $order->created_at = Carbon::now();
            $order->save();

            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);
                $variantId = $item['variant_id'] ?? null;
                $unitCode = $item['unit_code'] ?? null;

                $productOrderProduct = ProductOrderProduct::create([
                    'product_warehouse_id' => $warehouseId,
                    'product_warehouse_room_id' => null,
                    'product_warehouse_room_cartoon_id' => null,
                    'product_supplier_id' => null,
                    'product_order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => $variantId,
                    'unit_price_id' => null,
                    'product_name' => $product->name,
                    'qty' => $item['qty'],
                    'sale_price' => $item['unit_price'],
                    'discount_type' => data_get($item, 'discount.type', 'in_percentage'),
                    'discount_amount' => data_get($item, 'discount.value', 0),
                    'tax' => 0,
                    'total_price' => $item['final_price'],
                    'product_price' => $product->discount_price ?: $product->price,
                    'slug' => Str::orderedUuid(),
                ]);

                $unit_info = null;
                if ($unitCode) {
                    if ($is_confirmed) {
                        $unit_info = ProductPurchaseOrderProductUnit::where('code', $unitCode)->first();
                        if ($unit_info) {
                            $unit_info->update([
                                'sale_id' => $order->id,
                                'unit_status' => 'sold',
                                'updated_at' => Carbon::now(),

                                'product_order_product_id' => $productOrderProduct->id,
                            ]);
                        }
                    }
                } else {
                    if ($is_confirmed) {
                        $unit_info = ProductPurchaseOrderProductUnit::where('product_id', $product->id)
                            ->where('unit_status', 'instock')
                            ->orderBy('id')
                            ->first();
                        if ($unit_info) {
                            $unit_info->update([
                                'sale_id' => $order->id,
                                'unit_status' => 'sold',
                                'product_order_product_id' => $productOrderProduct->id,
                            ]);
                        }
                    }
                }

                // only confirmed orders can decrement stock
                if ($is_confirmed) {
                    // update product order product with purchase order product info
                    $purchase_order_product = $unit_info->productPurchaseOrderProduct;
                    if ($purchase_order_product) {
                        $productOrderProduct->update([
                            'product_warehouse_room_id' => $purchase_order_product->product_warehouse_room_id ?? null,
                            'product_warehouse_room_cartoon_id' => $purchase_order_product->product_warehouse_room_cartoon_id ?? null,
                            'product_supplier_id' => $purchase_order_product->product_supplier_id ?? null,
                        ]);
                    }

                    // decrement stock
                    if ($variantId) {
                        $variant = ProductVariantCombination::find($variantId);
                        if ($variant) {
                            $variant->decrement('stock', $item['qty']);
                        }
                    }
                    $product->decrement('stock', $item['qty']);

                    if ($variantId) {
                        ProductStock::where('product_id', $product->id)
                            ->where('product_warehouse_id', $warehouseId)
                            ->where('status', 'active')
                            ->where('variant_combination_id', $variantId)
                            ->where('qty', '>=', $item['qty'])
                            ->decrement('qty', $item['qty']);

                        ProductStockLog::create([
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouseId,
                            'product_name' => $product->name,
                            'product_sales_id' => $order->id,
                            'variant_combination_id' => $variantId,
                            'quantity' => $item['qty'],
                            'type' => 'sales',
                            'has_variant' => 1,
                            'creator' => Auth::id(),
                            'slug' => uniqid() . time(),
                            'status' => 'active',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    } else {
                        ProductStock::where('product_id', $product->id)
                            ->where('product_warehouse_id', $warehouseId)
                            ->where('status', 'active')
                            ->where('qty', '>=', $item['qty'])
                            ->decrement('qty', $item['qty']);

                        ProductStockLog::create([
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouseId,
                            'product_name' => $product->name,
                            'product_sales_id' => $order->id,
                            'quantity' => $item['qty'],
                            'type' => 'sales',
                            'has_variant' => 0,
                            'creator' => Auth::id(),
                            'slug' => uniqid() . time(),
                            'status' => 'active',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }
            }

            if ($customer && $advanceUsed > 0 && $is_confirmed) {
                calc_customer_balance($customer->id);
            }

            $random_no = random_int(100, 999) . random_int(1000, 9999);
            $order->slug = $order->id . Str::orderedUuid() . uniqid() . $random_no;
            $order->save();

            if ($is_confirmed) {
                // Record accounting transactions before commit
                $this->recordPosOrderAccounting($order, $paymentLines, $advanceUsed, $cart, $totals);

                /** auth user target update */
                $this->updateUserTarget($order);
            }

            if ($deliveryInfo['courier_method']) {
                if (strtolower($deliveryInfo['courier_method_title']) == 'pathao') {
                    $pathao = new PathaoController();
                    $pathao->createOrder($order, $deliveryInfo['courier_method']);
                }
                if (strtolower($deliveryInfo['courier_method_title']) == 'steadfast') {
                    $steadfast = new SteadfastController();
                    $steadfast->createOrder($order, $deliveryInfo['courier_method']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'order_slug' => $order->slug,
                    'order_code' => $order->order_code,
                ],
                'message' => 'Order created successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // throw $e;
            return response()->json([
                'success' => false,
                'message' => 'Order creation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return printable HTML for preview popup.
     */
    public function preview(Request $request)
    {
        $company = [
            'name' => 'BME Trading Company',
            'address' => '123 Business District, Dhaka-1000, Bangladesh',
            'phone' => '+880 1700-000000',
            'email' => 'info@bmetrading.com',
            'website' => 'www.bmetrading.com',
            'logo' => '/logo.png',
        ];

        $orderSlug = $request->get('order_slug');
        if ($orderSlug) {
            $order = ProductOrder::with(['order_products.variant', 'order_products.unitPrice', 'customer', 'warehouse'])
                ->where('slug', $orderSlug)
                ->firstOrFail();

            $qrData = route('order.invoice', $order->slug);
        } else {
            $payload = $request->validate([
                'cart' => 'required|array|min:1',
                'totals' => 'required|array',
                'customer' => 'nullable|array',
                'order_note' => 'nullable|string|max:500',
            ]);
            $warehouseId = $request->get('warehouse_id');
            $order = $this->makePreviewOrder($payload, $warehouseId);
            $qrData = 'POS PREVIEW';
        }

        $html = view('invoice.product-order', compact('order', 'company', 'qrData'))->render();

        return response()->json([
            'success' => true,
            'data' => [
                'html' => $html,
            ],
        ]);
    }

    protected function makePreviewOrder(array $payload, $warehouseId = null): ProductOrder
    {
        $order = new ProductOrder();
        $totals = $payload['totals'] ?? [];

        $order->order_code = 'PREVIEW-' . now()->format('His');
        $order->sale_date = Carbon::now();
        $order->subtotal = (float) data_get($totals, 'subtotal', 0);
        $order->other_charge_amount = (float) data_get($totals, 'extra_charge', 0) + (float) data_get($totals, 'delivery_charge', 0);
        $order->discount_type = data_get($totals, 'discount.type', 'percent');
        $order->discount_amount = (float) data_get($totals, 'discount.value', 0);
        $order->calculated_discount_amount = (float) data_get($totals, 'discount.amount', 0) + (float) data_get($totals, 'coupon.amount', 0);
        $order->round_off_from_total = (float) data_get($totals, 'round_off', 0);
        $order->decimal_round_off = (float) data_get($totals, 'round_off', 0);
        $order->total = (float) data_get($totals, 'grand_total', 0);
        $order->paid_amount = $order->total;
        $order->due_amount = 0;
        $order->order_status = 'preview';
        $order->note = $payload['order_note'] ?? null;
        $order->order_source = 'pos';
        $order->payments = [];

        $customerData = $payload['customer'] ?? [];
        $customer = new Customer();
        $customer->name = $customerData['name'] ?? 'Walk-in customer';
        $customer->phone = $customerData['mobile'] ?? null;
        $customer->email = $customerData['email'] ?? null;
        $customer->address = $customerData['address'] ?? null;
        $order->setRelation('customer', $customer);

        if ($warehouseId) {
            $warehouse = ProductWarehouse::find($warehouseId);
        }
        if (empty($warehouse)) {
            $warehouse = new ProductWarehouse();
            $warehouse->name = 'Selected Warehouse';
        }
        $order->setRelation('warehouse', $warehouse);

        $items = collect($payload['cart'])->map(function ($item) {
            $product = new ProductOrderProduct();
            $product->product_name = $item['title'] ?? 'Item';
            $product->sale_price = (float) ($item['unit_price'] ?? 0);
            $product->qty = (float) ($item['qty'] ?? 0);
            $product->discount_amount = (float) data_get($item, 'discount.percent', 0);
            $product->tax = 0;
            $product->total_price = (float) ($item['final_price'] ?? 0);
            return $product;
        });
        $order->setRelation('order_products', $items);

        return $order;
    }

    /**
     * Print endpoint for POS that returns HTML containing POS or A4 invoice.
     */
    public function print($slug)
    {
        $order = ProductOrder::with(['order_products.variant', 'order_products.unitPrice', 'customer', 'warehouse'])
            ->where('slug', $slug)
            ->firstOrFail();

        $company = [
            'name' => 'BME Trading Company',
            'address' => '123 Business District, Dhaka-1000, Bangladesh',
            'phone' => '+880 1700-000000',
            'email' => 'info@bmetrading.com',
            'website' => 'www.bmetrading.com',
            'logo' => '/logo.png',
        ];

        $qrData = route('order.invoice', $order->slug);

        $html = view('invoice.product-order-pos', compact('order', 'company', 'qrData'))->render();

        return response()->json([
            'success' => true,
            'data' => [
                'html' => $html,
            ],
        ]);
    }

    /**
     * Generate a new POS order code (YYMM + incremental number).
     */
    protected function generateOrderCode(): string
    {
        $year = Carbon::now()->format('y');
        $month = Carbon::now()->format('m');
        $prefix = $year . $month;

        $latestOrder = ProductOrder::query()
            ->where('order_code', 'like', $prefix . '%')
            ->orderBy('order_code', 'desc')
            ->first();

        if ($latestOrder) {
            $lastNumber = (int) substr($latestOrder->order_code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Map payment methods array from POS into keyed amounts.
     */
    protected function paymentsArrayFromLines(array $lines): array
    {
        $result = [
            'cash' => 0,
            'bkash' => 0,
            'nogod' => 0,
            'rocket' => 0,
            'bank' => 0,
        ];

        foreach ($lines as $line) {
            $method = $line['method'] ?? null;
            $amount = (float) ($line['amount'] ?? 0);
            if (!$method || $amount <= 0) {
                continue;
            }

            if (!array_key_exists($method, $result)) {
                $result[$method] = 0;
            }
            $result[$method] += $amount;
        }

        return $result;
    }

    /**
     * Ensure product has a barcode and non-zero stock for local testing.
     */
    protected function ensureProductBarcode(Product $product): void
    {
        if (!$product->barcode) {
            $product->barcode = 'P' . str_pad((string) $product->id, 11, '0', STR_PAD_LEFT);
        }

        // For local/dev, ensure some stock to allow testing
        if (app()->environment('local') && (!$product->stock || $product->stock < 1)) {
            $product->stock = 20;
        }

        if ($product->isDirty()) {
            $product->save();
        }
    }

    /**
     * Ensure variant combination has a barcode and stock for testing.
     */
    protected function ensureVariantBarcode(ProductVariantCombination $variant): void
    {
        if (!$variant->barcode) {
            $variant->barcode = 'V' . str_pad((string) $variant->id, 11, '0', STR_PAD_LEFT);
        }

        if (app()->environment('local') && ($variant->stock === null || $variant->stock < 1)) {
            $variant->stock = 15;
        }

        if ($variant->isDirty()) {
            $variant->save();
        }
    }

    /**
     * Build public URL to product image or fallback.
     */
    protected function productImageUrl(?string $path): string
    {
        return env('FILE_URL') . '/' . $path;
    }

    /**
     * Get product stock for a specific warehouse from product_stocks table.
     * This is the final calculation track after all events from a warehouse.
     * 
     * @param Product $product Product model
     * @param int|null $warehouseId Warehouse ID (optional, null means all warehouses)
     * @return float Stock quantity
     */
    protected function getProductStockForWarehouse(Product $product, $warehouseId): float
    {
        $query = DB::table('product_stocks')
            ->where('product_id', $product->id);
        // ->where('status', 'active');
        // ->where('has_variant', false);

        // Filter by warehouse if provided
        if ($warehouseId !== null && $warehouseId > 0) {
            $query->where('product_warehouse_id', $warehouseId);
        }

        $qty = $query->sum('qty');

        return (float) ($qty ?? 0);
    }
    protected function getProductStockItemsForWarehouse(Product $product, $warehouseId)
    {
        $query = ProductStock::query()
            ->where('product_id', $product->id);

        // Filter by warehouse if provided
        if ($warehouseId !== null && $warehouseId > 0) {
            $query->where('product_warehouse_id', $warehouseId);
        }

        $items = $query->get();

        return $items;
    }

    /**
     * Get variant stock for a specific warehouse from product_stocks table.
     * This is the final calculation track after all events from a warehouse.
     * 
     * @param ProductVariantCombination $variant Variant combination model
     * @param int|null $warehouseId Warehouse ID (optional, null means all warehouses)
     * @return float Stock quantity
     */
    protected function getVariantStockForWarehouse(ProductVariantCombination $variant, $warehouseId): float
    {
        $query = DB::table('product_stocks')
            ->where('product_id', $variant->product_id)
            ->where('status', 'active')
            ->where('has_variant', true);

        // Filter by variant using variant_combination_id or combination_key or barcode
        if (!empty($variant->id)) {
            $query->where(function ($q) use ($variant) {
                $q->where('variant_combination_id', $variant->id);

                if (!empty($variant->combination_key)) {
                    $q->orWhere('variant_combination_key', $variant->combination_key);
                }

                if (!empty($variant->barcode)) {
                    $q->orWhere('variant_barcode', $variant->barcode);
                }
            });
        }

        // Filter by warehouse if provided
        if ($warehouseId !== null && $warehouseId > 0) {
            $query->where('product_warehouse_id', $warehouseId);
        }

        $qty = $query->sum('qty');

        return (float) ($qty ?? 0);
    }

    /**
     * Get payment methods from DbPaymentType model
     */
    public function getPaymentMethods()
    {
        try {
            $paymentTypes = DbPaymentType::where('status', 'active')
                ->orderBy('payment_type')
                ->get(['id', 'payment_type']);

            $methods = $paymentTypes->map(function ($type) {
                // Get account for this payment type
                $account = AcAccount::where('paymenttypes_id', $type->id)
                    ->where('status', 'active')
                    ->first();

                return [
                    'id' => strtolower(str_replace(' ', '_', $type->payment_type)),
                    'payment_type_id' => $type->id,
                    'title' => $type->payment_type,
                    'account_id' => $account ? $account->id : null,
                    'account_name' => $account ? $account->account_name : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $methods,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching payment methods: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record accounting transactions for POS order
     * Based on record_sales_accounting_create helper function
     */
    protected function recordPosOrderAccounting($order, $paymentLines, $advanceUsed, $cart, $totals)
    {
        $user = Auth::user();

        // Get event mappings
        $customerPaymentEvent = AcEventMapping::getByEventName('customer_advance_payment');
        $salesEvent = AcEventMapping::getByEventName('sales'); // POS orders are always cash sales credit->sales_revenue debit->payment_accounts

        if (!$salesEvent) {
            Log::warning('Sales event mapping not found for POS order accounting');
            return;
        }

        $payment_code = generate_payment_code('POS');
        $totalPaymentAmount = 0;

        $coupon_amount = data_get($totals, 'coupon.amount', 0);
        $discount_amount = data_get($totals, 'discount.amount', 0);
        $round_off_amount = data_get($totals, 'round_off', 0);

        $sales_revenue_amount = data_get($totals, 'subtotal', 0);
        $extra_charge_amount = data_get($totals, 'extra_charge', 0);
        $delivery_charge_amount = data_get($totals, 'delivery_charge', 0);

        /** save asset debit */
        foreach ($paymentLines as $payment_type) {
            if ($payment_type['amount'] > 0) {
                $payment_info = DbPaymentType::find($payment_type['payment_type_id']);
                $totalPaymentAmount += $payment_type['amount'];
                if ($payment_info) {
                    $pt = strtolower($payment_info->payment_type ?? '');
                    $paymentNote = str_contains($pt, 'cash')
                        ? "POS Sale - Cash received from customer for order {$order->order_code}"
                        : (str_contains($pt, 'bank')
                            ? "POS Sale - Bank payment received from customer for order {$order->order_code}"
                            : "POS Sale - Mobile wallet payment received from customer for order {$order->order_code}");
                    AcTransaction::create([
                        'store_id' => $user->store_id ?? null,
                        'payment_code' => $payment_code,
                        'transaction_date' => Carbon::today()->format('Y-m-d'),
                        'transaction_type' => 'Asset',
                        'debit_account_id' => $payment_info->debit_account_id,
                        'credit_account_id' => null,
                        'debit_amt' => $payment_type['amount'],
                        'credit_amt' => null,
                        'note' => $paymentNote,
                        'customer_id' => $order->customer_id,
                        'ref_sales_id' => $order->id,
                        'created_by' => substr($user->name, 0, 50),
                        'creator' => $user->id,
                        'slug' => uniqid() . time(),
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        /** advance advance debit */
        if ($advanceUsed > 0) {
            $totalPaymentAmount += $advanceUsed;

            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $payment_code,
                'transaction_date' => Carbon::today()->format('Y-m-d'),
                'transaction_type' => 'POS_SALE',
                'debit_account_id' => $customerPaymentEvent->credit_account_id,
                'credit_account_id' => null,
                'debit_amt' => $advanceUsed,
                'credit_amt' => null,
                'note' => "POS Sale - Applied customer advance to reduce payment for order {$order->order_code}",
                'customer_id' => $order->customer_id,
                'ref_sales_id' => $order->id,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        /** customer due debit */
        $totalDue = $order->total - $totalPaymentAmount;
        if ($totalDue > 0) {
            $dueMapping = AcEventMapping::getByEventName('customer_due_order');

            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $payment_code,
                'transaction_date' => Carbon::today()->format('Y-m-d'),
                'transaction_type' => 'POS_SALE',
                'debit_account_id' => $dueMapping->debit_account_id,
                'credit_account_id' => null,
                'debit_amt' => $totalDue,
                'credit_amt' => null,
                'note' => "POS Sale - Customer due recorded for unpaid portion of order {$order->order_code}",
                'customer_id' => $order->customer_id,
                'ref_sales_id' => $order->id,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        /** promotional discount debits */
        if ($coupon_amount > 0) {
            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $payment_code,
                'transaction_date' => Carbon::today()->format('Y-m-d'),
                'transaction_type' => 'POS_SALE',
                'debit_account_id' => 30, // Discount Account
                'credit_account_id' => null,
                'debit_amt' => $coupon_amount,
                'credit_amt' => null,
                'note' => "POS Sale - Coupon applied for promotional discount for order {$order->order_code}",
                'customer_id' => $order->customer_id,
                'ref_sales_id' => $order->id,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        /** discount debits */
        if ($discount_amount > 0) {
            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $payment_code,
                'transaction_date' => Carbon::today()->format('Y-m-d'),
                'transaction_type' => 'POS_SALE',
                'debit_account_id' => 40, // Discount Account
                'credit_account_id' => null,
                'debit_amt' => $discount_amount,
                'credit_amt' => null,
                'note' => "POS Sale - Shop discount allowed for order {$order->order_code}",
                'customer_id' => $order->customer_id,
                'ref_sales_id' => $order->id,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        /** round off debits */
        if ($round_off_amount > 0) {
            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $payment_code,
                'transaction_date' => Carbon::today()->format('Y-m-d'),
                'transaction_type' => 'POS_SALE',
                'debit_account_id' => 41, // Round Off Account
                'credit_account_id' => null,
                'debit_amt' => $round_off_amount,
                'credit_amt' => null,
                'note' => "POS Sale - Round off adjustment for order {$order->order_code}",
                'customer_id' => $order->customer_id,
                'ref_sales_id' => $order->id,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        /** sales revenue credits */
        if ($sales_revenue_amount > 0) {
            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $payment_code,
                'transaction_date' => Carbon::today()->format('Y-m-d'),
                'transaction_type' => 'POS_SALE',
                'debit_account_id' => null,
                'credit_account_id' => 19, // Sales Revenue
                'debit_amt' => null,
                'credit_amt' => $sales_revenue_amount,
                'note' => "POS Sale - Sales revenue recorded for order {$order->order_code} (subtotal of items)",
                'customer_id' => $order->customer_id,
                'ref_sales_id' => $order->id,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        /** extra charge credits */
        if ($extra_charge_amount > 0) {
            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $payment_code,
                'transaction_date' => Carbon::today()->format('Y-m-d'),
                'transaction_type' => 'POS_SALE',
                'debit_account_id' => null,
                'credit_account_id' => 42, // Extra Charge
                'debit_amt' => null,
                'credit_amt' => $extra_charge_amount,
                'note' => "POS Sale - Extra charge collected for order {$order->order_code} (service/handling fees)",
                'customer_id' => $order->customer_id,
                'ref_sales_id' => $order->id,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        /** delivery charge credits */
        if ($delivery_charge_amount > 0) {
            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $payment_code,
                'transaction_date' => Carbon::today()->format('Y-m-d'),
                'transaction_type' => 'POS_SALE',
                'debit_account_id' => null,
                'credit_account_id' => 43, // Delivery Charge
                'debit_amt' => null,
                'credit_amt' => $delivery_charge_amount,
                'note' => "POS Sale - Delivery charge collected for order {$order->order_code}",
                'customer_id' => $order->customer_id,
                'ref_sales_id' => $order->id,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        /** Inventory account **/
        $cogs = 0;
        foreach ($cart as $item) {
            $product = ProductPurchaseOrderProductUnit::where('product_id', $item['product_id'])->where('sale_id', $order->id)->first();
            if ($product) {
                $purchasePrice = $product->price ?? 0;
                $cogs += $purchasePrice * ($item['qty'] ?? 0);
            }
        }

        if ($cogs > 0) {
            /** cost of goods sold debit */
            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $payment_code,
                'transaction_date' => Carbon::today()->format('Y-m-d'),
                'transaction_type' => 'COGS_POS_SALE',
                'debit_account_id' => 23, // expense cost of goods sold account
                'credit_account_id' => null,
                'debit_amt' => $cogs,
                'credit_amt' => null,
                'note' => "COGS - Cost of goods sold recorded for POS order {$order->order_code}",
                'customer_id' => $order->customer_id,
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'ref_sales_id' => $order->id,
            ]);

            /** stock out credit */
            AcTransaction::create([
                'store_id' => $user->store_id ?? null,
                'payment_code' => $payment_code,
                'transaction_date' => Carbon::today()->format('Y-m-d'),
                'transaction_type' => 'COGS_POS_SALE',
                'debit_account_id' => null,
                'credit_account_id' => 8, // asset Inventory account
                'debit_amt' => null,
                'credit_amt' => $cogs,
                'note' => "Stock Out - Inventory reduced for sold products in POS order {$order->order_code}",
                'customer_id' => $order->customer_id,
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'ref_sales_id' => $order->id,
            ]);
        }

        return '';
    }

    /**
     * Get payment account ID for a payment method
     * First tries to get from payment_type_id, then falls back to helper function
     */
    protected function getPaymentAccountIdForMethod($method, $paymentTypeId = null)
    {
        // If payment_type_id is provided, get account from that
        if ($paymentTypeId) {
            $account = AcAccount::where('paymenttypes_id', $paymentTypeId)
                ->where('status', 'active')
                ->first();

            if ($account) {
                return $account->id;
            }
        }

        // Fallback to helper function
        if (function_exists('getPaymentAccountId')) {
            return getPaymentAccountId($method);
        }

        return null;
    }

    public function customerSource()
    {
        $sources = CustomerSourceType::select('id', 'title')->where('status', 'active')->get();
        return response()->json(['success' => true, 'data' => $sources]);
    }
    public function deliveryMethods()
    {
        $methods = ProductOrderDeliveryMethod::select('id', 'title')->where('status', 'active')->get();
        return response()->json(['success' => true, 'data' => $methods]);
    }
    public function outlets()
    {
        $outlets = Outlet::select('id', 'title')->where('status', 'active')->get();
        return response()->json(['success' => true, 'data' => $outlets]);
    }
    public function courierMethods()
    {
        $methods = ProductOrderCourierMethod::select('id', 'title')->where('status', 'active')->get();
        return response()->json(['success' => true, 'data' => $methods]);
    }
    public function updateUserTarget($order)
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 1. Get monthly target (sum of all daily targets set for this month)
        $monthlyTarget = UserSalesTarget::where('user_id', $user->id)
            ->whereBetween('date', [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth()
            ])
            ->sum('target');

        // 2. Get actual sales this month (POS orders only)
        $monthlySales = ProductOrder::where('creator', $user->id)
            ->where('order_source', 'pos')
            ->whereBetween('created_at', [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth()->endOfDay()
            ])
            ->sum('subtotal');

        // 3. Get today's sales only
        $todaySales = ProductOrder::where('creator', $user->id)
            ->where('order_source', 'pos')
            ->whereBetween('created_at', [
                $today->copy()->startOfDay(),
                $today->copy()->endOfDay()
            ])
            ->sum('subtotal');

        // 4. Find or create today's target record
        $todayTarget = UserSalesTarget::firstOrCreate(
            [
                'user_id' => $user->id,
                'date'    => $today,
            ],
            [
                // Only set when creating new record
                'target'  => 0,           // will be updated below if needed
                'completed' => $todaySales,
                'remains' => 0,           // will be calculated below
            ]
        );

        // 5. Calculate remaining target for the month after today
        $monthRemainingTarget = max(0, $monthlyTarget - $monthlySales);

        // 6. Update today's record
        // completed = actual sales done TODAY
        // remains   = what is still needed for the rest of the month (after today)
        $todayTarget->update([
            'completed' => $todaySales,
            'remains'   => $monthRemainingTarget,
        ]);

        // Optional improvement: if you want to auto-distribute remaining target to future days
        // (only when creating new record or when monthly target changes)
        // You can add logic here later — for now keeping it simple as per your request

        return $todayTarget; // optional — for debugging or chaining
    }
}
