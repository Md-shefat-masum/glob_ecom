<?php

namespace App\Http\Controllers\StockManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductStockLog;
use App\Models\ProductVariantCombination;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of stock logs
     */
    public function index()
    {
        $logs = DB::table('product_stock_logs')
            ->leftJoin('products', 'product_stock_logs.product_id', '=', 'products.id')
            ->select(
                'product_stock_logs.*',
                'products.name as product_name',
                'products.code as product_code'
            )
            ->orderBy('product_stock_logs.created_at', 'desc')
            ->paginate(20);

        return view('backend.stock_management.index', compact('logs'));
    }

    /**
     * Show the form for creating a new stock adjustment
     */
    public function create()
    {
        return view('backend.stock_management.create');
    }

    /**
     * Search products for Ajax Select2
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');
        
        $products = Product::where('status', 1)
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'code', 'sku', 'stock')
            ->limit(20)
            ->get();

        $results = $products->map(function($product) {
            return [
                'id' => $product->id,
                'text' => "{$product->name} ({$product->code}) - Stock: {$product->stock}",
                'name' => $product->name,
                'code' => $product->code,
                'sku' => $product->sku,
                'stock' => $product->stock
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => false]
        ]);
    }

    /**
     * Get product details with variants if applicable
     */
    public function getProductDetails($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Check if product has variants
            $hasVariants = ProductVariantCombination::where('product_id', $id)
                ->where('status', 1)
                ->exists();

            $data = [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'sku' => $product->sku,
                'stock' => $product->stock,
                'has_variants' => $hasVariants,
                'variants' => []
            ];

            if ($hasVariants) {
                $variants = ProductVariantCombination::where('product_id', $id)
                    ->where('status', 1)
                    ->get()
                    ->map(function($variant) use ($id) {
                        // Calculate present stock from product_stocks table
                        $presentStock = DB::table('product_stocks')
                            ->where('product_id', $id)
                            ->where('has_variant', 1)
                            ->where('variant_combination_key', $variant->combination_key)
                            ->where('status', 'active')
                            ->sum('qty');

                        return [
                            'id' => $variant->id,
                            'combination_key' => $variant->combination_key,
                            'variant_values' => $variant->variant_values,
                            'sku' => $variant->sku,
                            'stock' => $variant->stock,
                            'present_stock' => $presentStock ?? 0,
                            'adjustment_qty' => 0
                        ];
                    });

                $data['variants'] = $variants;
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Store a newly created stock adjustment
     * Steps:
     * 1. Insert log entry based on type
     * 2. Calculate closing stock from all logs
     * 3. Update product_stocks with calculated closing stock
     * 4. Update product total stock and availability_status
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:sales,purchase,return,initial,transfer,waste,manual add',
            'description' => 'nullable|string|max:1000',
            'quantity' => 'required_if:has_variants,false|numeric|min:0',
            'variants' => 'required_if:has_variants,true|array'
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($request->product_id);
            $hasVariants = $request->has_variants ?? false;
            $type = $request->type;
            $description = $request->description;
            $creator = Auth::id();

            if ($hasVariants && !empty($request->variants)) {
                // Handle variant stock adjustments
                foreach ($request->variants as $variantData) {
                    if (isset($variantData['adjustment_qty']) && $variantData['adjustment_qty'] > 0) {
                        $variant = ProductVariantCombination::find($variantData['id']);
                        
                        if ($variant) {
                            // Step 1: Insert into product_stock_logs
                            DB::table('product_stock_logs')->insert([
                                'product_id' => $product->id,
                                'variant_combination_id' => $variant->id,
                                'has_variant' => 1,
                                'variant_combination_key' => $variant->combination_key,
                                'variant_sku' => $variant->sku,
                                'variant_data' => is_array($variant->variant_values) ? json_encode($variant->variant_values) : $variant->variant_values,
                                'product_name' => $product->name,
                                'quantity' => $variantData['adjustment_qty'],
                                'type' => $type,
                                'description' => $description,
                                'creator' => $creator,
                                'slug' => Str::slug($product->name . '-' . time()) . '-' . uniqid(),
                                'status' => 1,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);

                            // Step 2 & 3: Calculate closing stock from logs and update product_stocks
                            $this->updateProductStockFromLogs($product->id, $variant->id);
                        }
                    }
                }
            } else {
                // Handle single product stock adjustment
                // Step 1: Insert into product_stock_logs
                DB::table('product_stock_logs')->insert([
                    'product_id' => $product->id,
                    'has_variant' => 0,
                    'product_name' => $product->name,
                    'quantity' => $request->quantity,
                    'type' => $type,
                    'description' => $description,
                    'creator' => $creator,
                    'slug' => Str::slug($product->name . '-' . time()) . '-' . uniqid(),
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Step 2 & 3: Calculate closing stock from logs and update product_stocks
                $this->updateProductStockFromLogs($product->id, null);
            }

            // Step 4: Update product's total stock and availability_status
            $this->updateProductTotalStockAndAvailability($product->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock adjustment created successfully!',
                'redirect' => route('stock-adjustment.index')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating stock adjustment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate closing stock from logs and update product_stocks
     * This method calculates the closing stock based on ALL stock logs (not just adding/subtracting)
     */
    private function updateProductStockFromLogs($productId, $variantId = null)
    {
        try {
            // Calculate closing stock from logs based on event types
            // Stock IN: purchase, initial, manual add, return
            $stockIn = DB::table('product_stock_logs')
                ->where('product_id', $productId)
                ->when($variantId, function($query) use ($variantId) {
                    return $query->where('variant_combination_id', $variantId);
                })
                ->whereIn('type', ['purchase', 'initial', 'manual add', 'return'])
                ->sum('quantity') ?? 0;

            // Stock OUT: sales, waste, transfer
            $stockOut = DB::table('product_stock_logs')
                ->where('product_id', $productId)
                ->when($variantId, function($query) use ($variantId) {
                    return $query->where('variant_combination_id', $variantId);
                })
                ->whereIn('type', ['sales', 'waste', 'transfer'])
                ->sum('quantity') ?? 0;

            // Final closing stock
            $closingStock = $stockIn - $stockOut;

            // Find and update existing product_stocks entry (don't insert new)
            DB::table('product_stocks')
                ->where('product_id', $productId)
                ->when($variantId, function($query) use ($variantId) {
                    return $query->where('variant_combination_id', $variantId);
                })
                ->update([
                    'qty' => max(0, $closingStock),
                    'date' => now()->format('Y-m-d'),
                    'updated_at' => now(),
                ]);

        } catch (\Exception $e) {
            Log::error('Error in updateProductStockFromLogs', [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update product's total stock and availability_status from product_stocks
     */
    private function updateProductTotalStockAndAvailability($productId)
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                return;
            }

            // Calculate total stock from product_stocks
            $totalStock = DB::table('product_stocks')
                ->where('product_id', $productId)
                ->where('status', 'active')
                ->sum('qty') ?? 0;

            // Set availability_status
            $availabilityStatus = ($totalStock > 0) ? 'in_stock' : 'out_stock';

            // Update product
            $product->stock = (int) $totalStock;
            $product->availability_status = $availabilityStatus;
            $product->save();

        } catch (\Exception $e) {
            Log::error('Error in updateProductTotalStockAndAvailability', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
        }
    }
}

