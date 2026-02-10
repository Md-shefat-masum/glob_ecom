<?php

namespace App\Http\Controllers\ProductManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ChildCategory;
use App\Models\Brand;
use App\Models\ProductModel;
use App\Models\Unit;
use App\Models\Color;
use App\Models\ProductSize;
use App\Models\Flag;
use App\Models\ProductStockVariantGroup;
use App\Models\ProductStockVariantsGroupKey;
use App\Models\ProductVariantCombination;
use App\Models\ProductFilterAttribute;
use App\Models\ProductFilterAttributeMapping;
use App\Models\ProductUnitPricing;
use App\Models\MediaFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ProductManagementController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // dd(request()->all());
             try {
                $query = Product::query();
        
                // Apply optional session filters
                if (session('product_category_filter')) {
                    $query->where('category_id', session('product_category_filter'));
                }
        
                if (session('product_brand_filter')) {
                    $query->where('brand_id', session('product_brand_filter'));
                }
        
                if (session('product_status_filter') !== null) {
                    $query->where('status', session('product_status_filter'));
                }
        
                return DataTables::of($query)
                    ->addIndexColumn() // DT_RowIndex
                    ->editColumn('image', function ($row) {
                        if ($row->image) {
                            return '<img src="' . env('FILE_URL').'/'.($row->image) . '" 
                                        alt="' . $row->name . '" 
                                        class="img-thumbnail product-image-preview" 
                                        style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;">';
                        }
                        return '<div style="width: 60px; height: 60px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>';
                    })
                    ->editColumn('name', function ($row) {
                        $html = '<div><strong>' . $row->name . '</strong>';
                        if ($row->has_variant) {
                            $html .= ' <span class="badge bg-primary ms-1"><i class="fas fa-layer-group"></i> Variants</span>';
                        }
                        $html .= '</div>';
                        $html .= '<small class="text-muted">SKU: ' . ($row->sku ?? 'N/A') . ' | Code: ' . ($row->code ?? 'N/A') . '</small>';
                        return $html;
                    })
                    ->editColumn('price', function ($row) {
                        $html = '<strong class="text-success">৳' . number_format($row->price, 2) . '</strong>';
                        if ($row->discount_price > 0) {
                            $html .= '<br><small class="text-danger"><del>৳' . number_format($row->discount_price, 2) . '</del></small>';
                        }
                        return $html;
                    })
                    ->addColumn('unit_price', function ($row) {
                        $unitPricing = \App\Models\ProductUnitPricing::where('product_id', $row->id)->count();
                        if ($unitPricing > 0) {
                            return '<button type="button" 
                                        onclick="view_unit_prices(this)"
                                        class="btn btn-sm btn-info" 
                                        data-product-id="' . $row->id . '"
                                        data-product-name="' . $row->name . '">
                                        <i class="fas fa-list"></i> ' . $unitPricing . '
                                    </button>';
                        }
                        return '<span class="text-muted">—</span>';
                    })
                    ->addColumn('stock', function ($row) {
                        if ($row->has_variant) {
                            return '<button type="button" onclick="show_variant_stocks(this)"
                                        class="btn btn-sm btn-warning view-variant-stocks" 
                                        data-product-id="' . $row->id . '"
                                        data-product-name="' . $row->name . '">
                                        <i class="fas fa-boxes"></i> Variants
                                    </button>';
                        } else {
                            $badgeClass = $row->stock > $row->low_stock ? 'bg-success' : 'bg-danger';
                            return '<span class="badge ' . $badgeClass . '">' . $row->stock . '</span>';
                        }
                    })
                    ->editColumn('status', function ($row) {
                        return $row->status == 1
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-danger">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="btn-group" role="group">
                                    <a href="' . route('product-management.show', $row->id) . '" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="' . route('product-management.edit', $row->id) . '" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-product" data-id="' . $row->id . '" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>';
                    })
                    ->rawColumns(['image', 'name', 'price', 'unit_price', 'stock', 'status', 'action'])
                    ->make(true);
        
            } catch (\Exception $e) {
                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        $categories = Category::where('status', 1)->get();
        $brands = Brand::where('status', 1)->get();

        return view('backend.product_management.index', compact('categories', 'brands'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $brands = Brand::where('status', 1)->orderBy('name')->get();
        $units = Unit::where('status', 1)->orderBy('name')->get();

        $colors = [];
        $sizes = [];
        
        // Load colors from product_stock_variant_groups -> product_stock_variants_group_keys
        $colorGroup = ProductStockVariantGroup::where('slug', 'color')
            ->where('status', 1)
            ->first();
        
        if ($colorGroup) {
            $colors = ProductStockVariantsGroupKey::where('group_id', $colorGroup->id)
                ->where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('key_name')
                ->get()
                ->map(function ($key) {
                    return (object)[
                        'id' => $key->id,
                        'name' => $key->key_name,
                        'code' => $key->key_value,
                    ];
                });
        }
        
        // Load sizes from product_stock_variant_groups -> product_stock_variants_group_keys
        $sizeGroup = ProductStockVariantGroup::where('slug', 'size')
            ->where('status', 1)
            ->first();
        
        if ($sizeGroup) {
            $sizes = ProductStockVariantsGroupKey::where('group_id', $sizeGroup->id)
                ->where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('key_name')
                ->get()
                ->map(function ($key) {
                    return (object)[
                        'id' => $key->id,
                        'name' => $key->key_name,
                        'serial' => $key->sort_order,
                    ];
                });
        }
        
        $flags = Flag::orderBy('id')->get();
        $models = ProductModel::where('status', 1)->orderBy('name')->get();
        
        // Get variant groups with their keys
        $variantGroups = ProductStockVariantGroup::getAllWithKeys();
        
        // Load category variant mapping from config
        $categoryVariantMap = config('product_variant_categories', []);

        return view('backend.product_management.create', compact(
            'categories', 'brands', 'units', 'colors', 'sizes', 'flags', 'models', 'variantGroups', 'categoryVariantMap'
        ));
    }

    /**
     * Display the specified product
     */
    public function show($id)
    {
        $product = Product::with(['category', 'subcategory', 'brand', 'unit'])->findOrFail($id);
        $variantCombinations = ProductVariantCombination::where('product_id', $id)->get();
        $unitPricing = \App\Models\ProductUnitPricing::with('unit')->where('product_id', $id)->get();
        $filterAttributes = ProductFilterAttribute::where('product_id', $id)->get();
        
        return view('backend.product_management.show', compact(
            'product', 'variantCombinations', 'unitPricing', 'filterAttributes'
        ));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $brands = Brand::where('status', 1)->orderBy('name')->get();
        $units = Unit::where('status', 1)->orderBy('name')->get();

        $colors = [];
        $sizes = [];
        // Load colors from product_stock_variant_groups -> product_stock_variants_group_keys
        $colorGroup = ProductStockVariantGroup::where('slug', 'color')
            ->where('status', 1)
            ->first();
        
        if ($colorGroup) {
            $colors = ProductStockVariantsGroupKey::where('group_id', $colorGroup->id)
                ->where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('key_name')
                ->get()
                ->map(function ($key) {
                    return (object)[
                        'id' => $key->id,
                        'name' => $key->key_name,
                        'code' => $key->key_value,
                    ];
                });
        }
        
        // Load sizes from product_stock_variant_groups -> product_stock_variants_group_keys
        $sizeGroup = ProductStockVariantGroup::where('slug', 'size')
            ->where('status', 1)
            ->first();
        
        if ($sizeGroup) {
            $sizes = ProductStockVariantsGroupKey::where('group_id', $sizeGroup->id)
                ->where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('key_name')
                ->get()
                ->map(function ($key) {
                    return (object)[
                        'id' => $key->id,
                        'name' => $key->key_name,
                        'serial' => $key->sort_order,
                    ];
                });
        }
        
        $flags = Flag::orderBy('id')->get();
        $models = ProductModel::where('status', 1)->orderBy('name')->get();
        
        // Get variant groups with their keys
        $variantGroups = ProductStockVariantGroup::getAllWithKeys();
        
        // Load category variant mapping from config
        $categoryVariantMap = config('product_variant_categories', []);

        return view('backend.product_management.edit', compact(
            'product', 'categories', 'brands', 'units', 'colors', 'sizes', 'flags', 'models', 'variantGroups', 'categoryVariantMap'
        ));
    }

    /**
     * Get complete product data for editing (API endpoint)
     */
    public function getProductData($id)
    {
        try {
            $product = Product::with(['category', 'subcategory', 'childCategory', 'brand', 'model', 'unit'])
                ->findOrFail($id);
            
            // Get unit pricing
            $unitPricing = ProductUnitPricing::with('unit')
                ->where('product_id', $id)
                ->orderBy('unit_value', 'asc')
                ->get()
                ->map(function ($pricing) {
                    return [
                        'unit_id' => $pricing->unit_id,
                        'unit_title' => $pricing->unit_title,
                        'unit_value' => $pricing->unit_value,
                        'unit_label' => $pricing->unit_label,
                        'price' => $pricing->price,
                        'discount_price' => $pricing->discount_price,
                        'discount_percent' => $pricing->discount_percent,
                        'reward_points' => $pricing->reward_points,
                        'is_default' => $pricing->is_default,
                    ];
                });
            
            // Get variant combinations
            $variantCombinations = ProductVariantCombination::where('product_id', $id)
                ->orderBy('combination_key', 'asc')
                ->get()
                ->map(function ($variant) use ($id) {
                    $imageId = null;
                    $imageUrl = null;
                    
                    if ($variant->image) {
                        // Try to find the media file by path
                        $mediaFile = MediaFile::where('file_path', $variant->image)->first();
                        if ($mediaFile) {
                            $imageId = $mediaFile->id;
                            $imageUrl = url("/media/load/{$mediaFile->id}");
                        } else {
                            $imageUrl = asset( $variant->image);
                        }
                    }
                    
                    // Calculate present stock from product_stocks table
                    $presentStockQuery = DB::table('product_stocks')
                        ->where('product_id', $id)
                        ->where('has_variant', 1)
                        ->where('status', 'active');

                    if (Schema::hasColumn('product_stocks', 'variant_combination_id')) {
                        $presentStockQuery->where(function ($query) use ($variant) {
                            $query->where('variant_combination_id', $variant->id)
                                ->orWhere(function ($subQuery) use ($variant) {
                                    $subQuery->whereNull('variant_combination_id')
                                        ->where('variant_combination_key', $variant->combination_key);
                                });
                        });
                    } else {
                        $presentStockQuery->where('variant_combination_key', $variant->combination_key);
                    }

                    $presentStock = $presentStockQuery->sum('qty');
                    
                    return [
                        'id' => $variant->id,
                        'combination_key' => $variant->combination_key,
                        'variant_values' => $variant->variant_values,
                        'price' => $variant->price,
                        'discount_price' => $variant->discount_price,
                        'additional_price' => $variant->additional_price,
                        'stock' => 0, // Return 0 in edit mode - user can add new stock
                        'present_stock' => $presentStock ?? 0, // Current stock from product_stocks
                        'low_stock_alert' => $variant->low_stock_alert,
                        'sku' => $variant->sku,
                        'barcode' => $variant->barcode,
                        'image_id' => $imageId,
                        'image_url' => $imageUrl,
                        'image_path' => $variant->image,
                    ];
                });

            
            // Get filter attributes
            $filterAttributes = ProductFilterAttribute::where('product_id', $id)
                ->get()
                ->pluck('selected_values', 'group_slug')
                ->toArray();
            
            // Parse JSON fields
            $attributes = $product->attributes ? json_decode($product->attributes, true) : [];
            $shippingInfo = $product->shipping_info ? json_decode($product->shipping_info, true) : [];
            $taxInfo = $product->tax_info ? json_decode($product->tax_info, true) : [];
            
            // Get gallery images
            $galleryImages = [];
            if ($product->multiple_images) {
                $galleryPaths = json_decode($product->multiple_images, true);
                if (is_array($galleryPaths)) {
                    foreach ($galleryPaths as $path) {
                        // Try to find the media file by path
                        $mediaFile = MediaFile::where('file_path', $path)->first();
                        
                        $galleryImages[] = [
                            'id' => $mediaFile ? $mediaFile->id : null,
                            'url' => $mediaFile ? url("/media/load/{$mediaFile->id}") : asset( $path),
                            'path' => $path,
                            'token' => null
                        ];
                    }
                }
            }
            
            // Extract variant group selections from combinations
            $selectedVariantGroups = [];
            $selectedStockGroupSlugs = [];
            
            if (!empty($variantCombinations) && count($variantCombinations) > 0) {
                $firstCombo = $variantCombinations[0];
                if (isset($firstCombo['variant_values']) && is_array($firstCombo['variant_values'])) {
                    foreach ($firstCombo['variant_values'] as $groupSlug => $value) {
                        if ($groupSlug !== 'color' && $groupSlug !== 'size') {
                            $selectedStockGroupSlugs[] = $groupSlug;
                        }
                    }
                }
                
                // Get all unique values per group
                foreach ($variantCombinations as $combo) {
                    if (isset($combo['variant_values']) && is_array($combo['variant_values'])) {
                        foreach ($combo['variant_values'] as $groupSlug => $valueName) {
                            if (!isset($selectedVariantGroups[$groupSlug])) {
                                $selectedVariantGroups[$groupSlug] = [];
                            }
                            // We need to find the ID from the value name
                            // This will be handled on frontend
                        }
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'code' => $product->code,
                        'sku' => $product->sku,
                        'barcode' => $product->barcode,
                        'hsn_code' => $product->hsn_code,
                        'category_id' => $product->category_id,
                        'subcategory_id' => $product->subcategory_id,
                        'childcategory_id' => $product->childcategory_id,
                        'brand_id' => $product->brand_id,
                        'model_id' => $product->model_id,
                        'unit_id' => $product->unit_id,
                        'flag_id' => $product->flag_id,
                        'short_description' => $product->short_description,
                        'description' => $product->description,
                        'specification' => $product->specification,
                        'warranty_policy' => $product->warrenty_policy,
                        'size_chart' => $product->size_chart,
                        'video_url' => $product->video_url,
                        'tags' => $product->tags,
                    'contact_number' => $product->contact_number,
                    'contact_description' => $product->contact_description,
                    'need_contact_during_order' => $product->need_contact_during_order ?? 0,
                    'availability_status' => $product->availability_status ?? 'in_stock',
                        'status' => $product->status,
                        'is_demo' => $product->is_demo,
                        'is_package' => $product->is_package,
                        'product_image_id' => $this->getMediaFileId($product->image),
                        'product_image_url' => $this->getImageUrl($product->image),
                        'gallery_image_ids' => '',
                        'gallery_images' => $galleryImages,
                    ],
                            'related' => $this->buildRelatedResponse($product),
                            'notification' => $this->buildNotificationResponse($product),
                    'pricing' => [
                        'price' => $product->price,
                        'discount_price' => $product->discount_price,
                        'discount_percent' => $product->discount_parcent,
                        'reward_points' => $product->reward_points,
                        'stock' => 0, // Always return 0 in edit mode - user can add new stock
                        'present_stock' => $product->stock ?? 0, // Current stock from database
                        'low_stock' => $product->low_stock,
                        'min_order_qty' => $product->min_order_qty,
                        'max_order_qty' => $product->max_order_qty,
                        'has_unit_based_price' => 0,  // Check if unit pricing exists
                    ],
                    'unit_pricing' => $unitPricing,
                    'has_variants' => $product->has_variant == 1,
                    'variant_combinations' => $variantCombinations,
                    'filter_attributes' => $filterAttributes,
                    'selected_stock_group_slugs' => $selectedStockGroupSlugs,
                    'selected_filter_group_slugs' => array_keys($filterAttributes),
                    'attributes' => $attributes,
                    'shipping_info' => $shippingInfo,
                    'tax_info' => $taxInfo,
                    'meta_info' => [
                        'title' => $product->meta_title,
                        'keywords' => $product->meta_keywords,
                        'description' => $product->meta_description,
                    ],
                    'special_offer' => [
                        'is_special' => $product->special_offer,
                        'offer_end_time' => $product->offer_end_time,
                    ],
                    'faq' => $product->faq ?: [],
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading product data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product in storage
     */
    public function store(Request $request)
    {
        // return dd($request->all());
        // Validate required fields
        try {
            $productInput = $request->input('product', []);
            if (array_key_exists('slug', $productInput)) {
                $productInput['slug'] = $this->normalizeSlug($productInput['slug']);
                $request->merge(['product' => $productInput]);
            }

            $slugRules = ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'];

            $request->validate([
                'product.name' => 'required|string|max:255',
                'product.slug' => array_merge($slugRules, ['unique:products,slug']),
                'product.category_id' => 'required|exists:categories,id',
                'product.product_image_id' => 'required|exists:media_files,id',
                'pricing.price' => 'required|numeric|min:0',
                'related.similar' => ['nullable', 'array'],
                'related.similar.*' => ['integer', 'exists:products,id'],
                'related.recommended' => ['nullable', 'array'],
                'related.recommended.*' => ['integer', 'exists:products,id'],
                'related.addons' => ['nullable', 'array'],
                'related.addons.*.product_id' => ['required', 'integer', 'exists:products,id'],
                'related.addons.*.is_default' => ['nullable', 'boolean'],
                'notification.title' => ['nullable', 'string', 'max:255'],
                'notification.description' => ['nullable', 'string'],
                'notification.button_text' => ['nullable', 'string', 'max:150'],
                'notification.button_url' => ['nullable', 'string', 'max:255'],
                'notification.image_id' => ['nullable', 'integer', 'exists:media_files,id'],
                'notification.is_show' => ['nullable', 'boolean'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'backup_data' => $request->all()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Extract nested data
            $productData = $request->input('product', []);
            $pricingData = $request->input('pricing', []);
            $unitPricing = $request->input('unit_pricing', []);
            $hasVariants = $request->input('has_variants', false);
            $variantCombinations = $request->input('variant_combinations', []);
            $filterAttributes = $request->input('filter_attributes', []);
            $attributes = $request->input('attributes', []);
            $shippingInfo = $request->input('shipping_info', []);
            $taxInfo = $request->input('tax_info', []);
            $metaInfo = $request->input('meta_info', []);
            $specialOffer = $request->input('special_offer', []);
            $metadata = $request->input('_metadata', []);
            
            // Initialize notification and related data early
            $relatedData = [];
            $notificationData = [];

            // Duplicate submission prevention
            $sessionId = $metadata['session_id'] ?? null;
            $submissionAttempt = $metadata['submission_attempt'] ?? 1;
            
            if ($sessionId && $submissionAttempt > 1) {
                // Check if this session already created a product
                $existingProduct = Product::where('created_by', Auth::id())
                    ->where('name', $productData['name'])
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->first();
                
                if ($existingProduct) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'Duplicate submission detected. Product already created.',
                        'product_id' => $existingProduct->id,
                        'redirect' => route('product-management.index')
                    ], 409);
                }
            }

            // Get media files
            $productImage = MediaFile::findOrFail($productData['product_image_id']);
            
            // Prepare related and notification data
            try {
                $relatedData = $this->prepareRelatedData($request->input('related', []), null);
            } catch (\Exception $e) {
                $relatedData = [];
            }
            
            try {
                $notificationData = $this->prepareNotificationData($request->input('notification', []));
            } catch (\Exception $e) {
                $notificationData = [
                    'title' => null,
                    'description' => null,
                    'button_text' => null,
                    'button_url' => null,
                    'image_id' => null,
                    'is_show' => false,
                ];
            }
            
            // Create product
            $product = new Product();
            $product->name = $productData['name'];
            $product->slug = $productData['slug'];
            $product->code = $productData['code'] ?? null;
            $product->sku = $productData['sku'] ?? null;
            $product->barcode = $productData['barcode'] ?? null;
            $product->hsn_code = $productData['hsn_code'] ?? null;
            $product->category_id = $productData['category_id'];
            $product->subcategory_id = $productData['subcategory_id'] ?? null;
            $product->childcategory_id = $productData['childcategory_id'] ?? null;
            $product->brand_id = $productData['brand_id'] ?? null;
            $product->model_id = $productData['model_id'] ?? null;
            $product->unit_id = $productData['unit_id'] ?? null;
            $product->flag_id = $productData['flag_id'] ?? null;
            
            // Pricing data
            $product->price = $pricingData['price'] ?? 0;
            $product->discount_price = $pricingData['discount_price'] ?? 0;
            $product->discount_parcent = $pricingData['discount_percent'] ?? 0;
            $product->reward_points = $pricingData['reward_points'] ?? 0;
            
            // Stock - will be updated later via updateProductStockTotal()
            // Set initial values, actual stock comes from product_stocks table
            $product->stock = 0;
            $product->low_stock = $hasVariants ? 0 : ($pricingData['low_stock'] ?? 10);
            
            $product->min_order_qty = $pricingData['min_order_qty'] ?? 1;
            $product->max_order_qty = $pricingData['max_order_qty'] ?? null;
            
            // Content
            $product->short_description = $productData['short_description'] ?? null;
            $product->description = $productData['description'] ?? null;
            $product->specification = $productData['specification'] ?? null;
            $product->warrenty_policy = $productData['warranty_policy'] ?? null;
            $product->size_chart = $productData['size_chart'] ?? null;
            $product->video_url = $productData['video_url'] ?? null;
            $product->tags = $productData['tags'] ?? null;
            $product->contact_number = $productData['contact_number'] ?? null;
            $product->contact_description = $productData['contact_description'] ?? null;
            $product->availability_status = $productData['availability_status'] ?? 'in_stock';
            $product->related_similar_products = !empty($relatedData['similar']) ? $relatedData['similar'] : null;
            $product->related_recommended_products = !empty($relatedData['recommended']) ? $relatedData['recommended'] : null;
            $product->related_addon_products = !empty($relatedData['addons']) ? $relatedData['addons'] : null;
            $product->notification_title = isset($notificationData['title']) ? $notificationData['title'] : null;
            $product->notification_description = isset($notificationData['description']) ? $notificationData['description'] : null;
            $product->notification_button_text = isset($notificationData['button_text']) ? $notificationData['button_text'] : null;
            $product->notification_button_url = isset($notificationData['button_url']) ? $notificationData['button_url'] : null;
            $product->notification_is_show = isset($notificationData['is_show']) && $notificationData['is_show'] ? 1 : 0;
            
            // Special offer
            $product->special_offer = $specialOffer['is_special'] ?? 0;
            $product->offer_end_time = $specialOffer['offer_end_time'] ?? null;
            
            // Variant flag
            $product->has_variant = $hasVariants ? 1 : 0;
            
            // Meta information
            $product->meta_title = $metaInfo['title'] ?? null;
            $product->meta_keywords = $metaInfo['keywords'] ?? null;
            $product->meta_description = $metaInfo['description'] ?? null;
            
            // JSON fields
            $product->attributes = !empty($attributes) ? json_encode($attributes) : null;
            $product->shipping_info = !empty($shippingInfo) ? json_encode($shippingInfo) : null;
            $product->tax_info = !empty($taxInfo) ? json_encode($taxInfo) : null;
            
            // FAQ data
            $faqData = $request->input('faq', []);
            if (!empty($faqData) && is_array($faqData)) {
                // Filter out empty FAQ items
                $faqData = array_filter($faqData, function($item) {
                    return !empty($item['question']) && !empty($item['answer']);
                });
                $product->faq = !empty($faqData) ? array_values($faqData) : null;
            } else {
                $product->faq = null;
            }
            
            // Flags and status
            $product->status = $productData['status'] ?? 1;
            $product->is_package = $productData['is_package'] ?? 0;
            $product->is_demo = $productData['is_demo'] ?? 0;
            $product->created_by = Auth::id();

            // Handle media files (FilePond uploads)
            $product->image = $productImage->file_path;
            
            // Mark product image as permanent
            $productImage->markAsPermanent();
            
            // Handle notification image
            if (isset($notificationData['image_id']) && !empty($notificationData['image_id'])) {
                $notificationMedia = MediaFile::find($notificationData['image_id']);
                if ($notificationMedia) {
                    $product->notification_image_id = $notificationMedia->id;
                    $product->notification_image_path = $notificationMedia->file_path;
                    $notificationMedia->markAsPermanent();
                } else {
                    $product->notification_image_id = null;
                    $product->notification_image_path = null;
                }
            } else {
                $product->notification_image_id = null;
                $product->notification_image_path = null;
            }
            
            // Handle gallery images
            if (!empty($productData['gallery_image_ids'])) {
                $galleryIds = explode(',', $productData['gallery_image_ids']);
                $galleryImages = MediaFile::whereIn('id', $galleryIds)->get();
                
                $galleryPaths = $galleryImages->pluck('file_path')->toArray();
                $product->multiple_images = !empty($galleryPaths) ? json_encode($galleryPaths) : null;
                
                // Mark gallery images as permanent
                MediaFile::whereIn('id', $galleryIds)->update([
                    'is_temp' => false,
                    'temp_token' => null,
                ]);
            }

            $product->save();

            // Handle unit pricing if provided
            if (!empty($unitPricing) && is_array($unitPricing)) {
                foreach ($unitPricing as $pricing) {
                    if (!empty($pricing['unit_id'])) {
                        ProductUnitPricing::create([
                            'product_id' => $product->id,
                            'unit_id' => $pricing['unit_id'],
                            'unit_title' => $pricing['unit_title'] ?? null,
                            'unit_value' => $pricing['unit_value'] ?? 1,
                            'unit_label' => $pricing['unit_label'] ?? null,
                            'price' => $pricing['price'] ?? 0,
                            'discount_price' => $pricing['discount_price'] ?? 0,
                            'discount_percent' => $pricing['discount_percent'] ?? 0,
                            'reward_points' => $pricing['reward_points'] ?? 0,
                            'is_default' => $pricing['is_default'] ?? 0,
                            'status' => 1,
                        ]);
                    }
                }
            }

            // Handle variants (creates combinations without stock)
            if ($hasVariants && !empty($variantCombinations)) {
                foreach ($variantCombinations as $combination) {
                    if (!empty($combination['combination_key'])) {
                        // Create variant combination record
                        $variantCombo = ProductVariantCombination::create([
                            'product_id' => $product->id,
                            'combination_key' => $combination['combination_key'],
                            'variant_values' => $combination['variant_values'] ?? [],
                            'price' => $combination['price'] ?? null,
                            'discount_price' => $combination['discount_price'] ?? null,
                            'additional_price' => $combination['additional_price'] ?? 0,
                            'stock' => 0, // Stock will be managed via Stock Adjustment module
                            'low_stock_alert' => $combination['low_stock_alert'] ?? null,
                            'sku' => $combination['sku'] ?? null,
                            'barcode' => $combination['barcode'] ?? null,
                            'image' => $combination['image_path'] ?? null,
                            'status' => 1,
                        ]);

                        // Mark variant image as permanent if exists
                        if (!empty($combination['image_id'])) {
                            MediaFile::where('id', $combination['image_id'])
                                ->update([
                                    'is_temp' => false,
                                    'temp_token' => null,
                                ]);
                        }
                    }
                }
            }

            // Handle filter-related attributes (frontend filtering only)
            if (!empty($filterAttributes) && is_array($filterAttributes)) {
                foreach ($filterAttributes as $groupSlug => $values) {
                    if (!empty($values) && is_array($values)) {
                        ProductFilterAttribute::create([
                            'product_id' => $product->id,
                            'group_slug' => $groupSlug,
                            'selected_values' => $values
                        ]);
                    }
                }
            }
            
            $this->syncFilterAttributeMappings($product, $filterAttributes);

            // Update variant combination stocks if product has variants
            if ($hasVariants) {
                $this->updateVariantCombinationStocks($product->id);
            }

            // Update product's total stock based on stock entries
            $this->updateProductStockTotal($product->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully!',
                'product_id' => $product->id,
                'redirect' => route('product-management.edit', ['id' => $product->id])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage(),
                'backup_data' => $request->all(),
                'error_details' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ]
            ], 500);
        }
    }

    /**
     * Update the specified product in storage
     */
    public function update(Request $request, $id)
    {
        // Validate required fields
        try {
            $productInput = $request->input('product', []);
            if (array_key_exists('slug', $productInput)) {
                $productInput['slug'] = $this->normalizeSlug($productInput['slug']);
                $request->merge(['product' => $productInput]);
            }

            $slugRules = ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'];

            $request->validate([
                'product.name' => 'required|string|max:255',
                'product.slug' => array_merge($slugRules, [Rule::unique('products', 'slug')->ignore($id)]),
                'product.category_id' => 'required|exists:categories,id',
                'pricing.price' => 'required|numeric|min:0',
                'related.similar' => ['nullable', 'array'],
                'related.similar.*' => ['integer', 'exists:products,id'],
                'related.recommended' => ['nullable', 'array'],
                'related.recommended.*' => ['integer', 'exists:products,id'],
                'related.addons' => ['nullable', 'array'],
                'related.addons.*.product_id' => ['required', 'integer', 'exists:products,id'],
                'related.addons.*.is_default' => ['nullable', 'boolean'],
                'notification.title' => ['nullable', 'string', 'max:255'],
                'notification.description' => ['nullable', 'string'],
                'notification.button_text' => ['nullable', 'string', 'max:150'],
                'notification.button_url' => ['nullable', 'string', 'max:255'],
                'notification.image_id' => ['nullable', 'integer', 'exists:media_files,id'],
                'notification.is_show' => ['nullable', 'boolean'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Find existing product
            $product = Product::findOrFail($id);
            
            // Extract nested data
            $productData = $request->input('product', []);
            $pricingData = $request->input('pricing', []);
            $unitPricing = $request->input('unit_pricing', []);
            $hasVariants = $request->input('has_variants', false);
            $variantCombinations = $request->input('variant_combinations', []);
            $filterAttributes = $request->input('filter_attributes', []);
            $attributes = $request->input('attributes', []);
            $shippingInfo = $request->input('shipping_info', []);
            $taxInfo = $request->input('tax_info', []);
            $metaInfo = $request->input('meta_info', []);
            $specialOffer = $request->input('special_offer', []);

            $productSlug = $this->normalizeSlug($productData['slug'] ?? ($productData['name'] ?? ''));
            if ($productSlug === '') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Product URL is required.',
                    'errors' => [
                        'product.slug' => ['Product URL is required.']
                    ]
                ], 422);
            }
            $productData['slug'] = $productSlug;

            // Initialize notification and related data early
            $relatedData = [];
            $notificationData = [];

            // Prepare related and notification data
            try {
                $relatedData = $this->prepareRelatedData($request->input('related', []), $product->id);
            } catch (\Exception $e) {
                $relatedData = [];
            }
            
            try {
                $notificationData = $this->prepareNotificationData($request->input('notification', []));
            } catch (\Exception $e) {
                $notificationData = [
                    'title' => null,
                    'description' => null,
                    'button_text' => null,
                    'button_url' => null,
                    'image_id' => null,
                    'is_show' => false,
                ];
            }

            // Update product basic info
            $product->name = $productData['name'];
            $product->slug = $productSlug;
            $product->code = $productData['code'] ?? null;
            $product->sku = $productData['sku'] ?? null;
            $product->barcode = $productData['barcode'] ?? null;
            $product->hsn_code = $productData['hsn_code'] ?? null;
            $product->category_id = $productData['category_id'];
            $product->subcategory_id = $productData['subcategory_id'] ?? null;
            $product->childcategory_id = $productData['childcategory_id'] ?? null;
            $product->brand_id = $productData['brand_id'] ?? null;
            $product->model_id = $productData['model_id'] ?? null;
            $product->unit_id = $productData['unit_id'] ?? null;
            $product->flag_id = $productData['flag_id'] ?? null;
            
            // Pricing data
            $product->price = $pricingData['price'] ?? 0;
            $product->discount_price = $pricingData['discount_price'] ?? 0;
            $product->discount_parcent = $pricingData['discount_percent'] ?? 0;
            $product->reward_points = $pricingData['reward_points'] ?? 0;
            
            // Stock - will be recalculated via updateProductStockTotal()
            // Don't set from pricing data, it's managed via product_stocks entries
            // Only update low_stock threshold
            $product->low_stock = $hasVariants ? 0 : ($pricingData['low_stock'] ?? 10);
            
            $product->min_order_qty = $pricingData['min_order_qty'] ?? 1;
            $product->max_order_qty = $pricingData['max_order_qty'] ?? null;
            
            // Content
            $product->short_description = $productData['short_description'] ?? null;
            $product->description = $productData['description'] ?? null;
            $product->specification = $productData['specification'] ?? null;
            $product->warrenty_policy = $productData['warranty_policy'] ?? null;
            $product->size_chart = $productData['size_chart'] ?? null;
            $product->video_url = $productData['video_url'] ?? null;
            $product->tags = $productData['tags'] ?? null;
            $product->contact_number = $productData['contact_number'] ?? null;
            $product->contact_description = $productData['contact_description'] ?? null;
            $product->availability_status = $productData['availability_status'] ?? 'in_stock';
            $product->related_similar_products = !empty($relatedData['similar']) ? $relatedData['similar'] : null;
            $product->related_recommended_products = !empty($relatedData['recommended']) ? $relatedData['recommended'] : null;
            $product->related_addon_products = !empty($relatedData['addons']) ? $relatedData['addons'] : null;
            $product->notification_title = isset($notificationData['title']) ? $notificationData['title'] : null;
            $product->notification_description = isset($notificationData['description']) ? $notificationData['description'] : null;
            $product->notification_button_text = isset($notificationData['button_text']) ? $notificationData['button_text'] : null;
            $product->notification_button_url = isset($notificationData['button_url']) ? $notificationData['button_url'] : null;
            $product->notification_is_show = isset($notificationData['is_show']) && $notificationData['is_show'] ? 1 : 0;
            
            // Special offer
            $product->special_offer = $specialOffer['is_special'] ?? 0;
            $product->offer_end_time = $specialOffer['offer_end_time'] ?? null;
            
            // Variant flag
            $product->has_variant = $hasVariants ? 1 : 0;
            
            // Meta information
            $product->meta_title = $metaInfo['title'] ?? null;
            $product->meta_keywords = $metaInfo['keywords'] ?? null;
            $product->meta_description = $metaInfo['description'] ?? null;
            
            // JSON fields
            $product->attributes = !empty($attributes) ? json_encode($attributes) : null;
            $product->shipping_info = !empty($shippingInfo) ? json_encode($shippingInfo) : null;
            $product->tax_info = !empty($taxInfo) ? json_encode($taxInfo) : null;
            
            // FAQ data
            $faqData = $request->input('faq', []);
            if (!empty($faqData) && is_array($faqData)) {
                // Filter out empty FAQ items
                $faqData = array_filter($faqData, function($item) {
                    return !empty($item['question']) && !empty($item['answer']);
                });
                $product->faq = !empty($faqData) ? array_values($faqData) : null;
            } else {
                $product->faq = null;
            }
            
            // Status
            $product->status = $productData['status'] ?? 1;
            $product->is_package = $productData['is_package'] ?? 0;
            $product->is_demo = $productData['is_demo'] ?? 0;
            $product->updated_by = Auth::id();

            // Handle media files if changed
            if (!empty($productData['product_image_id'])) {
                $productImage = MediaFile::findOrFail($productData['product_image_id']);
                $product->image = $productImage->file_path;
                $productImage->markAsPermanent();
            }
            
            if (isset($notificationData['image_id']) && !empty($notificationData['image_id'])) {
                $notificationMedia = MediaFile::find($notificationData['image_id']);
                if ($notificationMedia) {
                    $product->notification_image_id = $notificationMedia->id;
                    $product->notification_image_path = $notificationMedia->file_path;
                    $notificationMedia->markAsPermanent();
                } else {
                    $product->notification_image_id = null;
                    $product->notification_image_path = null;
                }
            } else {
                $product->notification_image_id = null;
                $product->notification_image_path = null;
            }
            
            // Handle gallery images
            if (!empty($productData['gallery_image_ids'])) {
                $galleryIds = explode(',', $productData['gallery_image_ids']);
                $galleryImages = MediaFile::whereIn('id', $galleryIds)->get();
                
                $galleryPaths = $galleryImages->pluck('file_path')->toArray();
                $product->multiple_images = !empty($galleryPaths) ? json_encode($galleryPaths) : null;
                
                MediaFile::whereIn('id', $galleryIds)->update([
                    'is_temp' => false,
                    'temp_token' => null,
                ]);
            }

            $product->save();

            // Handle unit pricing
            ProductUnitPricing::where('product_id', $id)->delete();
            if (!empty($unitPricing) && is_array($unitPricing)) {
                foreach ($unitPricing as $pricing) {
                    if (!empty($pricing['unit_id'])) {
                        ProductUnitPricing::create([
                            'product_id' => $product->id,
                            'unit_id' => $pricing['unit_id'],
                            'unit_title' => $pricing['unit_title'] ?? null,
                            'unit_value' => $pricing['unit_value'] ?? 1,
                            'unit_label' => $pricing['unit_label'] ?? null,
                            'price' => $pricing['price'] ?? 0,
                            'discount_price' => $pricing['discount_price'] ?? 0,
                            'discount_percent' => $pricing['discount_percent'] ?? 0,
                            'reward_points' => $pricing['reward_points'] ?? 0,
                            'is_default' => $pricing['is_default'] ?? 0,
                            'status' => 1,
                        ]);
                    }
                }
            }

            // Handle variants
            // IMPORTANT: Variants with stock entries cannot be deleted
            if ($hasVariants && !empty($variantCombinations)) {
                // Get existing variants
                $existingVariants = ProductVariantCombination::where('product_id', $id)->get()->keyBy('combination_key');
                $newCombinationKeys = collect($variantCombinations)->pluck('combination_key')->filter()->toArray();
                
                // Process each combination from the form
                foreach ($variantCombinations as $combination) {
                    if (!empty($combination['combination_key'])) {
                        $combinationKey = $combination['combination_key'];

                        $variantRecord = null;

                        // Check if variant exists
                        if (isset($existingVariants[$combinationKey])) {
                            // Update existing variant
                            $existingVariants[$combinationKey]->update([
                                'variant_values' => $combination['variant_values'] ?? [],
                                'price' => $combination['price'] ?? null,
                                'discount_price' => $combination['discount_price'] ?? null,
                                'additional_price' => $combination['additional_price'] ?? 0,
                                // DON'T update stock directly - it's calculated from product_stocks
                                'low_stock_alert' => $combination['low_stock_alert'] ?? null,
                                'sku' => $combination['sku'] ?? null,
                                'barcode' => $combination['barcode'] ?? null,
                                'image' => $combination['image_path'] ?? null,
                                'status' => 1,
                            ]);

                            $variantRecord = $existingVariants[$combinationKey];
                        } else {
                            // Create new variant
                            $variantRecord = ProductVariantCombination::create([
                                'product_id' => $product->id,
                                'combination_key' => $combinationKey,
                                'variant_values' => $combination['variant_values'] ?? [],
                                'price' => $combination['price'] ?? null,
                                'discount_price' => $combination['discount_price'] ?? null,
                                'additional_price' => $combination['additional_price'] ?? 0,
                                'stock' => 0, // Stock will be managed via Stock Adjustment module
                                'low_stock_alert' => $combination['low_stock_alert'] ?? null,
                                'sku' => $combination['sku'] ?? null,
                                'barcode' => $combination['barcode'] ?? null,
                                'image' => $combination['image_path'] ?? null,
                                'status' => 1,
                            ]);

                            // Ensure new variant available for removal checks
                            $existingVariants[$combinationKey] = $variantRecord;
                        }


                        // Mark variant image as permanent if exists
                        if (!empty($combination['image_id'])) {
                            MediaFile::where('id', $combination['image_id'])
                                ->update([
                                    'is_temp' => false,
                                    'temp_token' => null,
                                ]);
                        }
                    }
                }
                
                // Check for variants that were removed from the form
                foreach ($existingVariants as $existingVariant) {
                    if (!in_array($existingVariant->combination_key, $newCombinationKeys)) {
                        // Check if variant has stock entries
                        $hasStockQuery = DB::table('product_stocks')
                            ->where('product_id', $id)
                            ->where('has_variant', 1);

                        if (Schema::hasColumn('product_stocks', 'variant_combination_id')) {
                            $hasStockQuery->where(function ($query) use ($existingVariant) {
                                $query->where('variant_combination_id', $existingVariant->id)
                                    ->orWhere('variant_combination_key', $existingVariant->combination_key);
                            });
                        } else {
                            $hasStockQuery->where('variant_combination_key', $existingVariant->combination_key);
                        }

                        $hasStock = $hasStockQuery->exists();
                        
                        $hasLogQuery = DB::table('product_stock_logs')
                            ->where('product_id', $id)
                            ->where('has_variant', 1);

                        if (Schema::hasColumn('product_stock_logs', 'variant_combination_id')) {
                            $hasLogQuery->where(function ($query) use ($existingVariant) {
                                $query->where('variant_combination_id', $existingVariant->id)
                                    ->orWhere('variant_combination_key', $existingVariant->combination_key);
                            });
                        } else {
                            $hasLogQuery->where('variant_combination_key', $existingVariant->combination_key);
                        }

                        $hasLog = $hasLogQuery->exists();
                        
                        if ($hasStock || $hasLog) {
                            // Cannot delete - variant has stock history
                            // Mark as inactive instead
                            $existingVariant->update(['status' => 0]);
                        } else {
                            // Safe to delete - no stock history
                            $existingVariant->delete();
                        }
                    }
                }
            }

            // Handle filter attributes
            ProductFilterAttribute::where('product_id', $id)->delete();
            if (!empty($filterAttributes) && is_array($filterAttributes)) {
                foreach ($filterAttributes as $groupSlug => $values) {
                    if (!empty($values) && is_array($values)) {
                        ProductFilterAttribute::create([
                            'product_id' => $product->id,
                            'group_slug' => $groupSlug,
                            'selected_values' => $values
                        ]);
                    }
                }
            }
            $this->syncFilterAttributeMappings($product, $filterAttributes);


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully!',
                'product_id' => $product->id
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Product update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage(),
                'error_details' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ]
            ], 500);
        }
    }

    /**
     * Add stock entry for a product or variant
     * This method ADDS stock, not replaces it - allowing stock accumulation
     * Also creates log entry in product_stock_logs for tracking
     * 
     * @param int $productId
     * @param float $quantity
     * @param bool $hasVariant
     * @param array|null $variantData
     * @return void
     */
    private function addStockEntry($productId, $quantity, $hasVariant = false, $variantData = null, $warehouseData = [], $variantCombinationId = null)
    {
        // Only create stock entry if quantity > 0
        if ($quantity <= 0) {
            return;
        }

        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        $stockData = [
            'product_id' => $productId,
            'has_variant' => $hasVariant,
            'qty' => $quantity,
            'date' => now()->format('Y-m-d'),
            'status' => 'active',
            'creator' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Prepare log data
        $logData = [
            'product_id' => $productId,
            'product_name' => $product->name,
            'has_variant' => $hasVariant,
            'quantity' => $quantity,
            'type' => 'initial', // Mark as initial stock entry
            'status' => 1,
            'creator' => Auth::id(),
            'slug' => Str::slug($product->name . '-initial-' . time()) . '-' . uniqid(),
            'warehouse_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $warehouseId = $warehouseData['warehouse_id'] ?? null;
        if ($warehouseId) {
            $stockData['product_warehouse_id'] = $warehouseId;
            $logData['warehouse_id'] = $warehouseId;
        }

        if (!empty($warehouseData['warehouse_room_id'])) {
            $stockData['product_warehouse_room_id'] = $warehouseData['warehouse_room_id'];
        }

        if (!empty($warehouseData['warehouse_room_cartoon_id'])) {
            $stockData['product_warehouse_room_cartoon_id'] = $warehouseData['warehouse_room_cartoon_id'];
        }

        if ($hasVariant) {
            if ($variantCombinationId) {
                $stockData['variant_combination_id'] = $variantCombinationId;
                $logData['variant_combination_id'] = $variantCombinationId;
            }

            if ($variantData) {
                $stockData['variant_combination_key'] = $variantData['combination_key'] ?? null;
                $logData['variant_combination_key'] = $variantData['combination_key'] ?? null;
            }
        }

        if ($hasVariant && $variantData) {
            // For variant products
            $stockData['variant_combination_key'] = $variantData['combination_key'] ?? null;
            $stockData['variant_sku'] = $variantData['sku'] ?? null;
            $stockData['variant_barcode'] = $variantData['barcode'] ?? null;
            $stockData['variant_data'] = json_encode($variantData['variant_values'] ?? []);
            $stockData['variant_price'] = $variantData['price'] ?? null;
            $stockData['variant_discount_price'] = $variantData['discount_price'] ?? null;
            $stockData['slug'] = Str::slug(($variantData['product_name'] ?? 'product') . '-' . ($variantData['combination_key'] ?? 'variant')) . '-' . time() . '-' . uniqid();
            
            // Add variant info to log
            $logData['variant_combination_key'] = $variantData['combination_key'] ?? null;
            $logData['variant_sku'] = $variantData['sku'] ?? null;
            $logData['variant_data'] = json_encode($variantData['variant_values'] ?? []);
        } else {
            // For non-variant products
            $stockData['slug'] = Str::slug(($product ? $product->name : 'product') . '-stock') . '-' . time() . '-' . uniqid();
        }

        // Insert stock entry
        DB::table('product_stocks')->insert($stockData);
        
        // Insert stock log entry for tracking
        DB::table('product_stock_logs')->insert($logData);
    }

    /**
     * Update product stock column based on stock entries
     * 
     * @param int $productId
     * @return void
     */
    private function updateProductStockTotal($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        // Calculate total stock from stock entries
        $totalStock = DB::table('product_stocks')
            ->where('product_id', $productId)
            ->where('status', 'active')
            ->sum('qty');

        // Update product's stock column
        $product->stock = $totalStock;
        $product->save();
    }

    /**
     * Update variant combination stock based on product_stocks table
     * 
     * @param int $productId
     * @return void
     */
    private function updateVariantCombinationStocks($productId)
    {
        // Get all variant combinations for this product
        $variants = ProductVariantCombination::where('product_id', $productId)->get();

        foreach ($variants as $variant) {
            // Calculate total stock for this variant combination
            $variantStockQuery = DB::table('product_stocks')
                ->where('product_id', $productId)
                ->where('has_variant', 1)
                ->where('status', 'active');

            if (Schema::hasColumn('product_stocks', 'variant_combination_id')) {
                $variantStockQuery->where(function ($query) use ($variant) {
                    $query->where('variant_combination_id', $variant->id)
                        ->orWhere(function ($subQuery) use ($variant) {
                            $subQuery->whereNull('variant_combination_id')
                                ->where('variant_combination_key', $variant->combination_key);
                        });
                });
            } else {
                $variantStockQuery->where('variant_combination_key', $variant->combination_key);
            }

            $variantStock = $variantStockQuery->sum('qty');

            // Update variant combination stock
            $variant->stock = $variantStock;
            $variant->save();
        }
    }

    /**
     * Helper: Get media file ID from file path
     */
    private function getMediaFileId($filePath)
    {
        if (!$filePath) return null;
        
        $mediaFile = MediaFile::where('file_path', $filePath)->first();
        return $mediaFile ? $mediaFile->id : null;
    }

    /**
     * Helper: Get image URL from file path
     */
    private function getImageUrl($filePath)
    {
        if (!$filePath) return null;
        
        // First check if file is registered in MediaFile table
        $mediaFile = MediaFile::where('file_path', $filePath)->first();
        if ($mediaFile) {
            return url("/media/load/{$mediaFile->id}");
        }
        
        // If not registered, check if file exists physically
        $fullPath = public_path($filePath);
        if (file_exists($fullPath)) {
            // Try to register it in MediaFile table for future use
            try {
                $fileSize = filesize($fullPath);
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                $mimeType = mime_content_type($fullPath) ?: 'image/' . $extension;
                
                // Get image dimensions if it's an image
                $width = null;
                $height = null;
                if (strpos($mimeType, 'image/') === 0) {
                    try {
                        $imageInfo = getimagesize($fullPath);
                        if ($imageInfo) {
                            $width = $imageInfo[0];
                            $height = $imageInfo[1];
                        }
                    } catch (\Exception $e) {
                        // Ignore dimension errors
                    }
                }
                
                // Register in MediaFile table
                $mediaFile = MediaFile::create([
                    'folder_path' => dirname($filePath),
                    'file_path' => $filePath,
                    'domain_url' => url('/'),
                    'full_url' => asset($filePath),
                    'file_name' => basename($filePath),
                    'original_name' => basename($filePath),
                    'size' => $fileSize,
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'width' => $width,
                    'height' => $height,
                    'disk' => 'public',
                    'uploader_type' => null,
                    'uploader_id' => null,
                    'file_type' => strpos($mimeType, 'image/') === 0 ? 'image' : 'file',
                    'is_temp' => false,
                    'temp_token' => null,
                    'metadata' => [
                        'auto_registered' => true,
                    ],
                ]);
                
                return url("/media/load/{$mediaFile->id}");
            } catch (\Exception $e) {
                // If registration fails, fallback to asset path
                Log::warning('Failed to auto-register media file', [
                    'file_path' => $filePath,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Fallback to asset path
        return asset($filePath);
    }

    /**
     * Normalize product slug strings.
     */
    private function normalizeSlug(?string $slug): string
    {
        if ($slug === null) {
            return '';
        }

        $slug = trim($slug);
        if ($slug === '') {
            return '';
        }

        return Str::slug($slug, '-');
    }

    /**
     * Prepare related product payload for persistence.
     */
    private function prepareRelatedData(array $related, ?int $currentProductId = null): array
    {
        $currentId = $currentProductId ? (int) $currentProductId : null;

        $similar = collect($related['similar'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0 && $id !== $currentId)
            ->unique()
            ->values()
            ->all();

        $recommended = collect($related['recommended'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0 && $id !== $currentId)
            ->unique()
            ->values()
            ->all();

        $addons = collect($related['addons'] ?? [])
            ->map(function ($addon) use ($currentId) {
                $productId = isset($addon['product_id']) ? (int) $addon['product_id'] : null;
                if (!$productId || $productId === $currentId) {
                    return null;
                }
                return [
                    'product_id' => $productId,
                    'is_default' => !empty($addon['is_default']),
                ];
            })
            ->filter()
            ->unique('product_id')
            ->values()
            ->all();

        return [
            'similar' => $similar,
            'recommended' => $recommended,
            'addons' => $addons,
        ];
    }

    /**
     * Build related product response for frontend consumption.
     */
    private function buildRelatedResponse(Product $product): array
    {
        $similar = $this->mapRelationProducts($product->related_similar_products ?? []);
        $recommended = $this->mapRelationProducts($product->related_recommended_products ?? []);
        $addons = $this->mapAddonRelationProducts($product->related_addon_products ?? []);

        return [
            'similar' => $similar,
            'recommended' => $recommended,
            'addons' => $addons,
        ];
    }

    /**
     * Map product IDs to minimal product representations.
     */
    private function mapRelationProducts(array $ids): array
    {
        $idCollection = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($idCollection->isEmpty()) {
            return [];
        }

        $products = Product::whereIn('id', $idCollection)->get()->keyBy('id');

        return $idCollection->map(function ($id) use ($products) {
            /** @var Product|null $product */
            $product = $products->get($id);
            if (!$product) {
                return null;
            }

            $price = $product->discount_price && $product->discount_price > 0
                ? $product->discount_price
                : ($product->price ?? 0);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $price,
            ];
        })->filter()->values()->all();
    }

    /**
     * Map addon relation data to frontend structure.
     */
    private function mapAddonRelationProducts(array $addons): array
    {
        $addonCollection = collect($addons);
        if ($addonCollection->isEmpty()) {
            return [];
        }

        $ids = $addonCollection->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $products = Product::whereIn('id', $ids)->get()->keyBy('id');

        return $addonCollection->map(function ($addon) use ($products) {
            $productId = isset($addon['product_id']) ? (int) $addon['product_id'] : null;
            if (!$productId) {
                return null;
            }

            /** @var Product|null $product */
            $product = $products->get($productId);
            if (!$product) {
                return null;
            }

            $price = $product->discount_price && $product->discount_price > 0
                ? $product->discount_price
                : ($product->price ?? 0);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $price,
                'is_default' => !empty($addon['is_default']),
            ];
        })->filter()->unique('id')->values()->all();
    }

    /**
     * Prepare notification payload for persistence.
     */
    private function prepareNotificationData(array $notification): array
    {
        $title = isset($notification['title']) ? trim($notification['title']) : null;
        $description = isset($notification['description']) ? trim($notification['description']) : null;
        $buttonText = isset($notification['button_text']) ? trim($notification['button_text']) : null;
        $buttonUrl = isset($notification['button_url']) ? trim($notification['button_url']) : null;

        $imageId = isset($notification['image_id']) ? (int) $notification['image_id'] : null;
        if ($imageId !== null && $imageId <= 0) {
            $imageId = null;
        }

        $isShow = false;
        if (array_key_exists('is_show', $notification)) {
            $isShow = filter_var($notification['is_show'], FILTER_VALIDATE_BOOLEAN);
        }

        return [
            'title' => $title !== '' ? $title : null,
            'description' => $description !== '' ? $description : null,
            'button_text' => $buttonText !== '' ? $buttonText : null,
            'button_url' => $buttonUrl !== '' ? $buttonUrl : null,
            'image_id' => $imageId,
            'is_show' => $isShow,
        ];
    }

    /**
     * Build notification response for frontend consumption.
     */
    private function buildNotificationResponse(Product $product): array
    {
        $imageId = $product->notification_image_id;
        $imagePath = $product->notification_image_path;
        $imageUrl = $imagePath ? $this->getImageUrl($imagePath) : null;

        if (!$imageId && $imagePath) {
            $media = MediaFile::where('file_path', $imagePath)->first();
            if ($media) {
                $imageId = $media->id;
            }
        }

        return [
            'title' => $product->notification_title,
            'description' => $product->notification_description,
            'button_text' => $product->notification_button_text,
            'button_url' => $product->notification_button_url,
            'image_id' => $imageId,
            'image_url' => $imageUrl,
            'image_path' => $imagePath,
            'is_show' => (bool) $product->notification_is_show,
        ];
    }

    /**
     * Search products for related selectors.
     */
    public function searchProducts(Request $request)
    {
        $term = trim((string) $request->input('term', ''));
        $exclude = collect($request->input('exclude'))
            ->flatten()
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $limit = (int) $request->input('limit', 20);
        if ($limit <= 0 || $limit > 50) {
            $limit = 20;
        }

        $query = Product::query()
            ->select(['id', 'name', 'price', 'discount_price', 'sku', 'is_package'])
            ->where('status', '!=', 0);

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                    ->orWhere('sku', 'like', '%' . $term . '%')
                    ->orWhere('code', 'like', '%' . $term . '%');
            });
        }

        if (!empty($exclude)) {
            $query->whereNotIn('id', $exclude);
        }

        // Exclude package products from suggestions
        $query->where('is_package', 0);

        $products = $query
            ->orderBy('name')
            ->limit($limit)
            ->get();

        $results = $products->map(function (Product $product) {
            $price = $product->discount_price && $product->discount_price > 0
                ? $product->discount_price
                : ($product->price ?? 0);

            return [
                'id' => $product->id,
                'text' => $product->name,
                'price' => (float) $price,
                'sku' => $product->sku,
            ];
        });

        return response()->json([
            'results' => $results,
        ]);
    }

    /**
     * Get subcategories by category
     */
    public function getSubcategories($categoryId)
    {
        $subcategories = Subcategory::where('category_id', $categoryId)
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        
        return response()->json($subcategories);
    }

    /**
     * Get child categories by subcategory
     */
    public function getChildCategories($subcategoryId)
    {
        $childCategories = ChildCategory::where('subcategory_id', $subcategoryId)
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        
        return response()->json($childCategories);
    }

    /**
     * Get models by brand
     */
    public function getModelsByBrand($brandId)
    {
        $models = ProductModel::where('brand_id', $brandId)
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        
        return response()->json($models);
    }

    /**
     * Get unit prices for a product
     */
    public function getUnitPrices($productId)
    {
        try {
            $unitPrices = \App\Models\ProductUnitPricing::with('unit')
                ->where('product_id', $productId)
                ->orderBy('unit_value', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $unitPrices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading unit prices'
            ], 500);
        }
    }

    /**
     * Get variant stocks for a product
     */
    public function getVariantStocks($productId)
    {
        try {
            $variantStocks = ProductVariantCombination::where('product_id', $productId)
                ->orderBy('combination_key', 'asc')
                ->get()
                ->map(function ($variant) use ($productId) {
                    $imageUrl = null;
                    
                    if ($variant->image) {
                        // Try to find the media file by path
                        $mediaFile = MediaFile::where('file_path', $variant->image)->first();
                        if ($mediaFile) {
                            // Use media controller endpoint
                            $imageUrl = url("/media/load/{$mediaFile->id}");
                        } else {
                            // Fallback to storage path
                            $imageUrl = asset( $variant->image);
                        }
                    }
                    
                    // Calculate present stock from product_stocks table
                    $presentStockQuery = DB::table('product_stocks')
                        ->where('product_id', $productId)
                        ->where('has_variant', 1)
                        ->where('status', 'active');

                    if (Schema::hasColumn('product_stocks', 'variant_combination_id')) {
                        $presentStockQuery->where(function ($query) use ($variant) {
                            $query->where('variant_combination_id', $variant->id)
                                ->orWhere(function ($subQuery) use ($variant) {
                                    $subQuery->whereNull('variant_combination_id')
                                        ->where('variant_combination_key', $variant->combination_key);
                                });
                        });
                    } else {
                        $presentStockQuery->where('variant_combination_key', $variant->combination_key);
                    }

                    $presentStock = $presentStockQuery->sum('qty');
                    
                    return [
                        'id' => $variant->id,
                        'combination_key' => $variant->combination_key,
                        'variant_values' => $variant->variant_values,
                        'price' => $variant->price,
                        'discount_price' => $variant->discount_price,
                        'additional_price' => $variant->additional_price,
                        'stock' => $presentStock ?? 0, // Calculate from product_stocks table
                        'low_stock_alert' => $variant->low_stock_alert,
                        'sku' => $variant->sku,
                        'barcode' => $variant->barcode,
                        'image' => $imageUrl, // Use transformed URL
                        'image_path' => $variant->image, // Keep original path
                        'status' => $variant->status,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $variantStocks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading variant stocks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get variant group keys by group ID
     */
    public function getVariantGroupKeys($groupId)
    {
        $keys = ProductStockVariantsGroupKey::getByGroup($groupId);
        return response()->json($keys);
    }

    /**
     * Get all variant groups with their keys
     */
    public function getVariantGroups()
    {
        $groups = ProductStockVariantGroup::getAllWithKeys();
        return response()->json($groups);
    }

    /**
     * Check if a slug is available
     */
    public function checkSlug(Request $request)
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255'],
            'ignore_id' => ['nullable', 'integer', 'exists:products,id'],
        ]);

        $normalized = $this->normalizeSlug($validated['slug']);
        if ($normalized === '') {
            return response()->json([
                'success' => false,
                'message' => 'Product URL must include letters or numbers.',
                'errors' => [
                    'slug' => ['Product URL must include letters or numbers.']
                ]
            ], 422);
        }

        $query = Product::where('slug', $normalized);
        if (!empty($validated['ignore_id'])) {
            $query->where('id', '!=', $validated['ignore_id']);
        }

        $exists = $query->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'slug' => $normalized,
                'available' => !$exists,
            ],
            'message' => $exists ? 'This product URL is already in use.' : 'Product URL is available.',
        ]);
    }

    /**
     * Generate PDF for product details
     */
    public function generatePDF($id)
    {
        $product = Product::with(['category', 'subcategory', 'childCategory', 'brand', 'model', 'unit'])->findOrFail($id);
        $variantCombinations = ProductVariantCombination::where('product_id', $id)->get();
        $unitPricing = ProductUnitPricing::with('unit')->where('product_id', $id)->get();
        $filterAttributes = ProductFilterAttribute::where('product_id', $id)->get();
        $generalInfo = DB::table('general_infos')->where('id', 1)->first();
        
        $data = compact('product', 'variantCombinations', 'unitPricing', 'filterAttributes', 'generalInfo');
        
        try {
            $pdf = PDF::loadView('backend.product_management.pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'product-' . $product->code . '-' . date('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            // If PDF package is not installed, return HTML view that can be printed
            return view('backend.product_management.show', $data)
                ->with('message', 'PDF generation not available. Please print this page using your browser.');
        }
    }

    /**
     * Sync filter attribute mappings so frontend filters can work with category, brand & product scopes.
     *
     * @param \App\Models\Product $product
     * @param array|null $filterAttributes
     * @return void
     */
    protected function syncFilterAttributeMappings(Product $product, $filterAttributes): void
    {
        ProductFilterAttributeMapping::where('product_id', $product->id)->delete();

        if (empty($filterAttributes) || !is_array($filterAttributes)) {
            return;
        }

        $groupSlugs = array_keys($filterAttributes);
        if (empty($groupSlugs)) {
            return;
        }

        $groups = ProductStockVariantGroup::whereIn('slug', $groupSlugs)->pluck('id', 'slug');
        if ($groups->isEmpty()) {
            return;
        }

        $valueIds = [];
        foreach ($filterAttributes as $values) {
            if (!is_array($values)) {
                continue;
            }

            foreach ($values as $valueId) {
                $intVal = (int) $valueId;
                if ($intVal > 0) {
                    $valueIds[] = $intVal;
                }
            }
        }

        $valueIds = array_values(array_unique($valueIds));
        if (empty($valueIds)) {
            return;
        }

        $keyGroupMap = ProductStockVariantsGroupKey::whereIn('id', $valueIds)->pluck('group_id', 'id');
        if ($keyGroupMap->isEmpty()) {
            return;
        }

        $targets = $this->buildFilterAttributeTargets($product);
        if (empty($targets)) {
            $targets[] = ['product_id' => $product->id];
        }

        $timestamp = now();
        $rows = [];

        foreach ($filterAttributes as $groupSlug => $values) {
            if (!isset($groups[$groupSlug]) || !is_array($values)) {
                continue;
            }

            $groupId = (int) $groups[$groupSlug];
            $uniqueValues = array_values(array_unique(array_map('intval', $values)));

            foreach ($uniqueValues as $valueId) {
                if ($valueId <= 0) {
                    continue;
                }

                if ((int) ($keyGroupMap[$valueId] ?? 0) !== $groupId) {
                    continue;
                }

                foreach ($targets as $target) {
                    $rows[] = array_merge([
                        'variant_group_id' => $groupId,
                        'variant_key_id' => $valueId,
                        'category_id' => null,
                        'subcategory_id' => null,
                        'childcategory_id' => null,
                        'brand_id' => null,
                        'product_id' => null,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ], $target);

                    if (count($rows) >= 500) {
                        ProductFilterAttributeMapping::insert($rows);
                        $rows = [];
                        $timestamp = now();
                    }
                }
            }
        }

        if (!empty($rows)) {
            ProductFilterAttributeMapping::insert($rows);
        }
    }

    /**
     * Prepare the scope rows for filter attribute mappings.
     *
     * @param \App\Models\Product $product
     * @return array<int, array<string, int>>
     */
    protected function buildFilterAttributeTargets(Product $product): array
    {
        $targets = [];

        if (!empty($product->category_id)) {
            $targets[] = ['category_id' => (int) $product->category_id];
        }

        if (!empty($product->subcategory_id)) {
            $targets[] = ['subcategory_id' => (int) $product->subcategory_id];
        }

        if (!empty($product->childcategory_id)) {
            $targets[] = ['childcategory_id' => (int) $product->childcategory_id];
        }

        if (!empty($product->brand_id)) {
            $targets[] = ['brand_id' => (int) $product->brand_id];
        }

        // Always track at product level to support direct product filters
        $targets[] = ['product_id' => (int) $product->id];

        return $targets;
    }

    /**
     * Store a new category via AJAX
     */
    public function storeCategory(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:categories,slug',
                'status' => 'required|in:0,1'
            ]);

            // Generate slug if not provided
            $slug = $request->slug;
            if (empty($slug)) {
                $slug = Str::slug($request->name);
                // Ensure uniqueness
                $originalSlug = $slug;
                $counter = 1;
                while (Category::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Prepare data for mass assignment
            $categoryData = [
                'name' => $request->name,
                'slug' => $slug,
                'status' => (int) $request->status, // Cast to integer
                'featured' => 0, // Default value
                'show_on_navbar' => 1, // Default value
                'serial' => 1, // Default value
            ];

            $category = Category::create($categoryData);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Category creation failed: ' . $e->getMessage());
            Log::error('Category creation stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a new subcategory via AJAX
     */
    public function storeSubcategory(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:subcategories,slug',
                'status' => 'required|in:0,1'
            ]);

            // Generate slug if not provided
            $slug = $request->slug;
            if (empty($slug)) {
                $slug = Str::slug($request->name);
                // Ensure uniqueness
                $originalSlug = $slug;
                $counter = 1;
                while (Subcategory::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Prepare data for mass assignment
            $subcategoryData = [
                'category_id' => (int) $request->category_id,
                'name' => $request->name,
                'slug' => $slug,
                'status' => (int) $request->status, // Cast to integer
                'featured' => 0, // Default value
            ];

            $subcategory = Subcategory::create($subcategoryData);

            return response()->json([
                'success' => true,
                'message' => 'Subcategory created successfully!',
                'subcategory' => [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'slug' => $subcategory->slug,
                    'category_id' => $subcategory->category_id
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Subcategory creation failed: ' . $e->getMessage());
            Log::error('Subcategory creation stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subcategory. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a new child category via AJAX
     */
    public function storeChildCategory(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'subcategory_id' => 'required|exists:subcategories,id',
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:child_categories,slug',
                'status' => 'required|in:0,1'
            ]);

            // Verify that subcategory belongs to the category
            $subcategory = Subcategory::find($request->subcategory_id);
            if (!$subcategory || $subcategory->category_id != $request->category_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected subcategory does not belong to the selected category.',
                    'errors' => ['subcategory_id' => ['Invalid subcategory for the selected category.']]
                ], 422);
            }

            // Generate slug if not provided
            $slug = $request->slug;
            if (empty($slug)) {
                $slug = Str::slug($request->name);
                // Ensure uniqueness
                $originalSlug = $slug;
                $counter = 1;
                while (ChildCategory::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Prepare data for mass assignment
            $childCategoryData = [
                'category_id' => (int) $request->category_id,
                'subcategory_id' => (int) $request->subcategory_id,
                'name' => $request->name,
                'slug' => $slug,
                'status' => (int) $request->status, // Cast to integer
            ];

            $childCategory = ChildCategory::create($childCategoryData);

            return response()->json([
                'success' => true,
                'message' => 'Child category created successfully!',
                'childCategory' => [
                    'id' => $childCategory->id,
                    'name' => $childCategory->name,
                    'slug' => $childCategory->slug,
                    'category_id' => $childCategory->category_id,
                    'subcategory_id' => $childCategory->subcategory_id
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Child category creation failed: ' . $e->getMessage());
            Log::error('Child category creation stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create child category. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a new brand via AJAX
     */
    public function storeBrand(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:brands,slug',
                'status' => 'required|in:0,1'
            ]);

            // Generate slug if not provided
            $slug = $request->slug;
            if (empty($slug)) {
                $slug = Str::slug($request->name);
                // Ensure uniqueness
                $originalSlug = $slug;
                $counter = 1;
                while (Brand::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Prepare data for mass assignment
            $brandData = [
                'name' => $request->name,
                'slug' => $slug,
                'status' => (int) $request->status, // Cast to integer
                'featured' => 0, // Default value
                'serial' => 1, // Default value
            ];

            $brand = Brand::create($brandData);

            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully!',
                'brand' => [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'slug' => $brand->slug
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Brand creation failed: ' . $e->getMessage());
            Log::error('Brand creation stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create brand. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a new model via AJAX
     */
    public function storeModel(Request $request)
    {
        try {
            $request->validate([
                'brand_id' => 'required|exists:brands,id',
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:255',
                'slug' => 'nullable|string|max:255|unique:product_models,slug',
                'status' => 'required|in:0,1'
            ]);

            // Generate slug if not provided
            $slug = $request->slug;
            if (empty($slug)) {
                $slug = Str::slug($request->name);
                // Ensure uniqueness
                $originalSlug = $slug;
                $counter = 1;
                while (ProductModel::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Prepare data for mass assignment
            $modelData = [
                'brand_id' => (int) $request->brand_id,
                'name' => $request->name,
                'code' => $request->code,
                'slug' => $slug,
                'status' => (int) $request->status, // Cast to integer
            ];

            $model = ProductModel::create($modelData);

            return response()->json([
                'success' => true,
                'message' => 'Model created successfully!',
                'model' => [
                    'id' => $model->id,
                    'name' => $model->name,
                    'code' => $model->code,
                    'slug' => $model->slug,
                    'brand_id' => $model->brand_id
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Model creation failed: ' . $e->getMessage());
            Log::error('Model creation stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create model. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a new unit via AJAX
     */
    public function storeUnit(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:units,name',
                'status' => 'required|in:0,1'
            ]);

            // Prepare data for mass assignment
            $unitData = [
                'name' => $request->name,
                'status' => (int) $request->status, // Cast to integer
            ];

            $unit = Unit::create($unitData);

            return response()->json([
                'success' => true,
                'message' => 'Unit created successfully!',
                'unit' => [
                    'id' => $unit->id,
                    'name' => $unit->name
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unit creation failed: ' . $e->getMessage());
            Log::error('Unit creation stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create unit. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Get unit prices for a product
     */
    // public function getUnitPrices($productId)
    // {
    //     $unitPrices = ProductUnitPricing::with('unit')
    //         ->where('product_id', $productId)
    //         ->where('status', 1)
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $unitPrices
    //     ]);
    // }

    /**
     * Get variant stocks for a product
     */
    // public function getVariantStocks($productId)
    // {
    //     $variants = ProductVariantCombination::where('product_id', $productId)
    //         ->orderBy('id', 'asc')
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $variants
    //     ]);
    // }
    
    /**
     * Delete a product
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Delete related data
            ProductUnitPricing::where('product_id', $id)->delete();
            ProductVariantCombination::where('product_id', $id)->delete();
            ProductFilterAttribute::where('product_id', $id)->delete();
            
            // Delete product images if they exist
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
            
            if ($product->multiple_images) {
                $images = json_decode($product->multiple_images, true);
                if (is_array($images)) {
                    foreach ($images as $image) {
                        if (file_exists(public_path($image))) {
                            unlink(public_path($image));
                        }
                    }
                }
            }
            
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync products from API
     */
    public function syncProductsFromApi()
    {
        try {
            // Run the seeder using Artisan
            Artisan::call('db:seed', [
                '--class' => 'ProductApiSyncSeeder'
            ]);
            
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Products synced successfully from API!',
                'output' => $output
            ]);
            
        } catch (\Exception $e) {
            Log::error('Product API sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error syncing products: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sync categories from API
     */
    public function syncCategoriesFromApi()
    {
        try {
            // Run the seeder using Artisan
            Artisan::call('db:seed', [
                '--class' => 'CategoryApiSyncSeeder'
            ]);
            
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Categories synced successfully from API!',
                'output' => $output
            ]);
            
        } catch (\Exception $e) {
            Log::error('Category API sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error syncing categories: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sync brands from API
     */
    public function syncBrandsFromApi()
    {
        try {
            // Run the seeder using Artisan
            Artisan::call('db:seed', [
                '--class' => 'BrandApiSyncSeeder'
            ]);
            
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Brands synced successfully from API!',
                'output' => $output
            ]);
            
        } catch (\Exception $e) {
            Log::error('Brand API sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error syncing brands: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sync customers from API
     */
    public function syncCustomersFromApi()
    {
        try {
            // Run the seeder using Artisan
            Artisan::call('db:seed', [
                '--class' => 'CustomerApiSyncSeeder'
            ]);
            
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Customers synced successfully from API!',
                'output' => $output
            ]);
            
        } catch (\Exception $e) {
            Log::error('Customer API sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error syncing customers: ' . $e->getMessage()
            ], 500);
        }
    }
}

