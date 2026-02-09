<?php

namespace App\Http\Controllers;

use App\Models\ProductStockVariantGroup;
use App\Models\ProductStockVariantsGroupKey;
use App\Models\ProductStockVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VariantManagementController extends Controller
{
    /**
     * Display the variant management page
     */
    public function index()
    {
        $totalGroups = ProductStockVariantGroup::count();
        $totalKeys = ProductStockVariantsGroupKey::count();
        $totalProducts = DB::table('product_variant_combinations')
            ->select('product_id')
            ->distinct()
            ->count();
        
        // Calculate total value from variant combinations
        $totalValue = DB::table('product_variant_combinations as pvc')
            ->join('products as p', 'pvc.product_id', '=', 'p.id')
            ->sum(DB::raw('pvc.stock * COALESCE(pvc.price, p.price)'));

        return view('backend.variant_management.index', compact(
            'totalGroups',
            'totalKeys',
            'totalProducts',
            'totalValue'
        ));
    }

    /**
     * Get all variant groups
     */
    public function getGroups()
    {
        $groups = ProductStockVariantGroup::withCount(['keys'])
            ->with(['keys' => function($query) {
                $query->where('status', 1)->select('id', 'group_id', 'key_name');
            }])
            ->orderBy('sort_order')
            ->get();

        // Add product count for each group
        foreach ($groups as $group) {
            $keyNames = $group->keys->pluck('key_name')->toArray();
            
            if (count($keyNames) > 0) {
                // Count products that have combinations using any of these keys
                $group->products_count = DB::table('product_variant_combinations')
                    ->where(function($query) use ($keyNames) {
                        foreach ($keyNames as $keyName) {
                            $query->orWhereRaw("JSON_SEARCH(variant_values, 'one', ?) IS NOT NULL", [$keyName]);
                        }
                    })
                    ->distinct('product_id')
                    ->count('product_id');
            } else {
                $group->products_count = 0;
            }
        }

        return response()->json($groups);
    }

    /**
     * Store a new variant group
     */
    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:product_stock_variant_groups,name',
            'description' => 'nullable|string',
            'is_stock_related' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $group = ProductStockVariantGroup::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_fixed' => 0, // Dynamic groups
            'is_stock_related' => $request->is_stock_related ?? 1,
            'sort_order' => $request->sort_order ?? 0,
            'status' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variant group created successfully!',
            'group' => $group
        ]);
    }

    /**
     * Update variant group
     */
    public function updateGroup(Request $request, $id)
    {
        $group = ProductStockVariantGroup::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100|unique:product_stock_variant_groups,name,' . $id,
            'description' => 'nullable|string',
            'is_stock_related' => 'boolean',
            'sort_order' => 'integer',
            'status' => 'boolean'
        ]);

        $group->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_stock_related' => $request->is_stock_related ?? 1,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status ?? 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variant group updated successfully!',
            'group' => $group
        ]);
    }

    /**
     * Delete variant group
     */
    public function deleteGroup($id)
    {
        $group = ProductStockVariantGroup::findOrFail($id);

        // Check if group has keys with products
        $keyNames = ProductStockVariantsGroupKey::where('group_id', $id)
            ->pluck('key_name')
            ->toArray();

        if (count($keyNames) > 0) {
            $hasProducts = DB::table('product_variant_combinations')
                ->where(function($query) use ($keyNames) {
                    foreach ($keyNames as $keyName) {
                        $query->orWhereRaw("JSON_SEARCH(variant_values, 'one', ?) IS NOT NULL", [$keyName]);
                    }
                })
                ->exists();

            if ($hasProducts) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete this group. It has products associated with its keys.'
                ], 400);
            }
        }

        // Delete all keys first
        ProductStockVariantsGroupKey::where('group_id', $id)->delete();
        
        // Delete group
        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variant group deleted successfully!'
        ]);
    }

    /**
     * Get keys by group ID
     */
    public function getKeysByGroup($groupId)
    {
        $group = ProductStockVariantGroup::findOrFail($groupId);
        
        $keys = ProductStockVariantsGroupKey::where('group_id', $groupId)
            ->orderBy('sort_order')
            ->get();

        // Add product count for each key by checking variant_values JSON
        foreach ($keys as $key) {
            // Count products in product_variant_combinations that use this key
            $key->products_count = DB::table('product_variant_combinations')
                ->whereRaw("JSON_SEARCH(variant_values, 'one', ?) IS NOT NULL", [$key->key_name])
                ->distinct('product_id')
                ->count('product_id');
        }

        return response()->json($keys);
    }

    /**
     * Store a new variant key
     */
    public function storeKey(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:product_stock_variant_groups,id',
            'key_name' => 'required|string|max:100',
            'key_value' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'integer'
        ]);

        // Check if key name is unique within the group
        $exists = ProductStockVariantsGroupKey::where('group_id', $request->group_id)
            ->where('key_name', $request->key_name)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Key name already exists in this group!'
            ], 400);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/variant_keys'), $imageName);
            $imagePath = 'uploads/variant_keys/' . $imageName;
        }

        $key = ProductStockVariantsGroupKey::create([
            'group_id' => $request->group_id,
            'key_name' => $request->key_name,
            'key_value' => $request->key_value,
            'image' => $imagePath,
            'sort_order' => $request->sort_order ?? 0,
            'status' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variant key created successfully!',
            'key' => $key
        ]);
    }

    /**
     * Update variant key
     */
    public function updateKey(Request $request, $id)
    {
        $key = ProductStockVariantsGroupKey::findOrFail($id);

        $request->validate([
            'key_name' => 'required|string|max:100',
            'key_value' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order' => 'integer',
            'status' => 'boolean'
        ]);

        // Check if key name is unique within the group (excluding current key)
        $exists = ProductStockVariantsGroupKey::where('group_id', $key->group_id)
            ->where('key_name', $request->key_name)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Key name already exists in this group!'
            ], 400);
        }

        $imagePath = $key->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($key->image && file_exists(public_path($key->image))) {
                unlink(public_path($key->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/variant_keys'), $imageName);
            $imagePath = 'uploads/variant_keys/' . $imageName;
        }

        $key->update([
            'key_name' => $request->key_name,
            'key_value' => $request->key_value,
            'image' => $imagePath,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->status ?? 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variant key updated successfully!',
            'key' => $key
        ]);
    }

    /**
     * Delete variant key
     */
    public function deleteKey($id)
    {
        $key = ProductStockVariantsGroupKey::findOrFail($id);

        // Check if key has products by searching in variant_values JSON
        $hasProducts = DB::table('product_variant_combinations')
            ->whereRaw("JSON_SEARCH(variant_values, 'one', ?) IS NOT NULL", [$key->key_name])
            ->exists();

        if ($hasProducts) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this key. It has products associated with it.'
            ], 400);
        }

        // Delete image if exists
        if ($key->image && file_exists(public_path($key->image))) {
            unlink(public_path($key->image));
        }

        $key->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variant key deleted successfully!'
        ]);
    }

    /**
     * Get variant products with details
     */
    public function getVariantProducts(Request $request)
    {
        $query = DB::table('product_variant_combinations as pvc')
            ->join('products as p', 'pvc.product_id', '=', 'p.id')
            ->select(
                'pvc.id',
                'pvc.product_id',
                'p.name as product_name',
                'p.sku as product_sku',
                'pvc.combination_key',
                'pvc.variant_values',
                'pvc.sku',
                'pvc.barcode',
                'pvc.stock',
                'pvc.price',
                'pvc.discount_price',
                DB::raw('COALESCE(pvc.price, p.price) as effective_price'),
                DB::raw('(pvc.stock * COALESCE(pvc.price, p.price)) as total_value'),
                'pvc.image'
            );

        if ($request->has('group_id') && $request->group_id) {
            // Get keys for this group
            $keyNames = ProductStockVariantsGroupKey::where('group_id', $request->group_id)
                ->pluck('key_name')
                ->toArray();
            
            if (count($keyNames) > 0) {
                $query->where(function($q) use ($keyNames) {
                    foreach ($keyNames as $keyName) {
                        $q->orWhereRaw("JSON_SEARCH(variant_values, 'one', ?) IS NOT NULL", [$keyName]);
                    }
                });
            }
        }

        if ($request->has('key_id') && $request->key_id) {
            $key = ProductStockVariantsGroupKey::find($request->key_id);
            if ($key) {
                $query->whereRaw("JSON_SEARCH(variant_values, 'one', ?) IS NOT NULL", [$key->key_name]);
            }
        }

        $products = $query->orderBy('p.name')
            ->get();

        // Parse variant_values to show readable format
        foreach ($products as $product) {
            $variantValues = json_decode($product->variant_values, true);
            $product->variant_display = [];
            
            if (is_array($variantValues)) {
                foreach ($variantValues as $group => $value) {
                    $product->variant_display[] = ucfirst($group) . ': ' . $value;
                }
            }
            $product->variant_text = implode(', ', $product->variant_display);
        }

        return response()->json($products);
    }

    /**
     * Detach variant from product
     */
    public function detachVariant(Request $request, $variantId)
    {
        DB::beginTransaction();
        try {
            $variant = DB::table('product_variant_combinations')->where('id', $variantId)->first();

            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant combination not found!'
                ], 404);
            }

            $productId = $variant->product_id;

            // Delete variant image if exists
            if ($variant->image && file_exists(public_path($variant->image))) {
                unlink(public_path($variant->image));
            }

            // Delete related stock entries
            DB::table('product_stocks')
                ->where('product_id', $productId)
                ->where('has_variant', 1)
                ->where('variant_combination_key', $variant->combination_key)
                ->delete();

            // Delete the variant combination
            DB::table('product_variant_combinations')->where('id', $variantId)->delete();

            // Recalculate product stock from remaining variants
            $totalStock = DB::table('product_variant_combinations')
                ->where('product_id', $productId)
                ->sum('stock');

            // Update product stock
            DB::table('products')
                ->where('id', $productId)
                ->update([
                    'stock' => $totalStock,
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Variant detached successfully! Product stock recalculated.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error detaching variant: ' . $e->getMessage()
            ], 500);
        }
    }
}

