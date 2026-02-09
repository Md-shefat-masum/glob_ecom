<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\MediaFile;
use App\Models\PackageProduct;
use App\Models\PackageProductItem;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\ProductVariant;
use App\Models\ProductVariantCombination;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class PackageProductController extends Controller
{
    /**
     * Display package products listing page
     */
    public function index()
    {
        return view('backend.package_product.index');
    }

    /**
     * Get package products data for DataTable
     */
    public function getData(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $data = DB::table('package_products')
            ->leftJoin('products', 'package_products.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->select(
                'package_products.id as package_id',
                'package_products.package_code',
                'package_products.title',
                'package_products.slug',
                'package_products.status',
                'package_products.visibility',
                'package_products.package_price',
                'package_products.compare_at_price',
                'package_products.calculated_savings_amount',
                'package_products.created_at',
                'products.id as product_id',
                'products.image',
                'products.status as product_status',
                'categories.name as category_name',
                'brands.name as brand_name'
            )
            ->orderByDesc('package_products.created_at')
            ->get();

        return DataTables::of($data)
            ->addColumn('image', function ($row) {
                $imagePath = $row->image ? url($row->image) : url('assets/images/default-product.png');
                return '<img src="' . $imagePath . '" class="gridProductImage" style="width: 50px; height: 50px; object-fit: cover;">';
            })
            ->addColumn('price', function ($row) {
                $price = '৳' . number_format($row->package_price ?? 0, 2);
                if ($row->compare_at_price && $row->compare_at_price > $row->package_price) {
                    $price .= '<br><small class="text-muted"><del>৳' . number_format($row->compare_at_price, 2) . '</del></small>';
                }
                return $price;
            })
            ->addColumn('status', function ($row) {
                $statusClass = $row->status === 'active' ? 'badge-success' : ($row->status === 'draft' ? 'badge-secondary' : 'badge-warning');
                $statusLabel = Str::title($row->status);
                return '<span class="badge ' . $statusClass . '">' . $statusLabel . '</span>';
            })
            ->addColumn('package_items_count', function ($row) {
                $count = PackageProductItem::where('package_id', $row->package_id)->count();
                return '<span class="badge badge-info">' . $count . ' items</span>';
            })
            ->addColumn('action', function ($row) {
                $btn = '<a href="' . url('package-products/' . $row->product_id . '/edit') . '" class="btn btn-sm btn-warning mb-1"><i class="fas fa-edit"></i> Edit</a>';
                $btn .= ' <a href="javascript:void(0)" data-id="' . $row->product_id . '" class="btn btn-sm btn-danger mb-1 deleteBtn"><i class="fas fa-trash"></i> Delete</a>';
                return $btn;
            })
            ->addIndexColumn()
            ->rawColumns(['image', 'price', 'status', 'package_items_count', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new package product
     */
    public function create()
    {
        $statuses = [
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
        ];

        $visibilityOptions = [
            ['value' => 'private', 'label' => 'Private (internal only)'],
            ['value' => 'public', 'label' => 'Public'],
            ['value' => 'scheduled', 'label' => 'Scheduled'],
        ];

        $categories = Category::where('status', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('backend.package_product.create', [
            'statuses' => $statuses,
            'visibilityOptions' => $visibilityOptions,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created package product
     */
    public function store(Request $request): JsonResponse
    {
        $payload = $request->all();

        $validator = Validator::make($payload, [
            'overview.title' => ['required', 'string', 'max:255'],
            'overview.slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
            'overview.package_code' => ['nullable', 'string', 'max:40'],
            'overview.tagline' => ['nullable', 'string', 'max:255'],
            'overview.hero_headline' => ['nullable', 'string', 'max:255'],
            'overview.hero_subheadline' => ['nullable', 'string', 'max:500'],
            'overview.hero_cta_label' => ['nullable', 'string', 'max:120'],
            'overview.hero_cta_link' => ['nullable', 'string', 'max:255'],
            'media.hero_image_id' => ['nullable', 'exists:media_files,id'],
            'media.gallery' => ['nullable', 'array'],
            'media.gallery.*.id' => ['required_with:media.gallery', 'exists:media_files,id'],
            'pricing.package_price' => ['required', 'numeric', 'min:0'],
            'pricing.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'pricing.allow_compare_override' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.variant_combination_id' => ['nullable', 'exists:product_variant_combinations,id'],
            'items.*.color_id' => ['nullable', 'exists:colors,id'],
            'items.*.size_id' => ['nullable', 'exists:product_sizes,id'],
            'info.status' => ['required', 'in:draft,active,inactive'],
            'info.visibility' => ['required', 'in:private,public,scheduled'],
            'info.publish_at' => ['nullable', 'date'],
            'info.short_description' => ['nullable', 'string'],
            'info.description' => ['nullable', 'string'],
            'info.highlights' => ['nullable', 'array', 'max:6'],
            'seo.meta_title' => ['nullable', 'string', 'max:255'],
            'seo.meta_keywords' => ['nullable', 'string', 'max:500'],
            'seo.meta_description' => ['nullable', 'string'],
            'seo.meta_image_id' => ['nullable', 'exists:media_files,id'],
        ], [
            'overview.slug.regex' => 'Slug may only contain lowercase letters, numbers and hyphen.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $overview = $validated['overview'];
        $pricing = $validated['pricing'];
        $itemsPayload = $validated['items'];
        $mediaPayload = $validated['media'] ?? [];
        $infoPayload = $validated['info'] ?? [];
        $seoPayload = $validated['seo'] ?? [];

        $packagePrice = (float) ($pricing['package_price'] ?? 0);
        $comparePrice = isset($pricing['compare_at_price']) ? (float) $pricing['compare_at_price'] : null;

        $mediaHeroId = $mediaPayload['hero_image_id'] ?? null;
        $galleryMedia = collect($mediaPayload['gallery'] ?? [])->pluck('id')->filter()->values();

        if (!$mediaHeroId) {
            return response()->json([
                'success' => false,
                'message' => 'Package hero image is required.',
            ], 422);
        }

        $heroMedia = MediaFile::find($mediaHeroId);
        if (!$heroMedia) {
            return response()->json([
                'success' => false,
                'message' => 'Hero image not found.',
            ], 422);
        }

        $items = [];
        $itemsTotal = 0;
        $compareTotal = 0;

        foreach ($itemsPayload as $index => $itemPayload) {
            /** @var Product $childProduct */
            $childProduct = Product::where('id', $itemPayload['product_id'])
                ->where('is_package', 0)
                ->first();

            if (!$childProduct) {
                return response()->json([
                    'success' => false,
                    'message' => "Selected product no longer exists or is not eligible for packages.",
                ], 422);
            }

            $variantSnapshot = [];
            $variantCombinationId = $itemPayload['variant_combination_id'] ?? null;
            $productVariantId = $itemPayload['product_variant_id'] ?? null;

            if ($variantCombinationId) {
                $combination = ProductVariantCombination::where('id', $variantCombinationId)
                    ->where('product_id', $childProduct->id)
                    ->first();

                if (!$combination) {
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid variant combination selected for {$childProduct->name}.",
                    ], 422);
                }

            }

            if ($productVariantId) {
                $legacyVariant = ProductVariant::where('id', $productVariantId)
                    ->where('product_id', $childProduct->id)
                    ->first();

                if (!$legacyVariant) {
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid variant selected for {$childProduct->name}.",
                    ], 422);
                }
            }

            if (!empty($itemPayload['color_id'])) {
                $variantSnapshot['Color'] = optional(Color::find($itemPayload['color_id']))->name;
            }

            if (!empty($itemPayload['size_id'])) {
                $variantSnapshot['Size'] = optional(ProductSize::find($itemPayload['size_id']))->name;
            }

            if (!empty($combination?->variant_values)) {
                foreach ($combination->variant_values as $key => $value) {
                    $variantSnapshot[Str::title(str_replace('_', ' ', $key))] = $value;
                }
            }

            $unitPrice = isset($itemPayload['unit_price'])
                ? (float) $itemPayload['unit_price']
                : ($childProduct->discount_price && $childProduct->discount_price > 0
                    ? $childProduct->discount_price
                    : $childProduct->price);

            $compareAtPrice = isset($itemPayload['compare_at_price'])
                ? (float) $itemPayload['compare_at_price']
                : ($childProduct->price ?? $unitPrice);

            $quantity = (int) $itemPayload['quantity'];

            $itemsTotal += $unitPrice * $quantity;
            $compareTotal += $compareAtPrice * $quantity;

            $items[] = [
                'product' => $childProduct,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'compare_at_price' => $compareAtPrice,
                'product_variant_id' => $productVariantId,
                'variant_combination_id' => $variantCombinationId,
                'color_id' => $itemPayload['color_id'] ?? null,
                'size_id' => $itemPayload['size_id'] ?? null,
                'variant_snapshot' => array_filter($variantSnapshot),
            ];
        }

        if ($comparePrice === null || empty($pricing['allow_compare_override'])) {
            $comparePrice = $compareTotal;
        }

        if ($comparePrice < $packagePrice) {
            return response()->json([
                'success' => false,
                'message' => 'Compare price must be greater than or equal to package selling price.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Generate unique package code
            $packageCode = $overview['package_code'] ?? null;
            if ($packageCode) {
                $packageCode = strtoupper(trim($packageCode));
                if ($packageCode === '') {
                    $packageCode = $this->generatePackageCode();
                } elseif (PackageProduct::where('package_code', $packageCode)->exists()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors' => [
                            'overview.package_code' => ['The package code has already been taken.'],
                        ],
                    ], 422);
                }
            } else {
                $packageCode = $this->generatePackageCode();
            }

            $baseSlug = $overview['slug'] ?? $overview['title'] ?? 'package';
            $productSlug = $this->uniqueProductSlug($baseSlug);
            $packageSlug = $this->uniquePackageSlug($baseSlug);

            // Create storefront product entry for compatibility
            $product = new Product();
            $product->name = $overview['title'];
            $product->slug = $productSlug;
            $product->category_id = $infoPayload['category_id'] ?? null;
            $product->short_description = $infoPayload['short_description'] ?? null;
            $product->description = $infoPayload['description'] ?? null;
            $product->price = $comparePrice;
            $product->discount_price = $packagePrice;
            $product->stock = 0;
            $product->min_order_qty = 1;
            $product->max_order_qty = null;
            $product->status = ($infoPayload['status'] ?? 'draft') === 'active' ? 1 : 0;
            $product->is_package = 1;
            $product->has_variant = 0;
            $product->tags = null;
            $product->meta_title = $seoPayload['meta_title'] ?? null;
            $product->meta_keywords = $seoPayload['meta_keywords'] ?? null;
            $product->meta_description = $seoPayload['meta_description'] ?? null;
            $product->created_at = now();
            $product->created_by = Auth::id();

            // Attach hero image
            $product->image = $heroMedia->file_path;
            $heroMedia->markAsPermanent();

            if ($galleryMedia->isNotEmpty()) {
                $galleryFiles = MediaFile::whereIn('id', $galleryMedia)->get();
                $galleryPaths = $galleryFiles->pluck('file_path')->filter()->values()->toArray();
                $product->multiple_images = !empty($galleryPaths) ? json_encode($galleryPaths) : null;

                // Mark gallery files permanent
                MediaFile::whereIn('id', $galleryMedia)->update([
                    'is_temp' => false,
                    'temp_token' => null,
                ]);
            }

            $product->save();

            // Compute savings
            $savingsAmount = max(0, $comparePrice - $packagePrice);
            $savingsPercent = $comparePrice > 0 ? round(($savingsAmount / $comparePrice) * 100, 2) : 0;

            // Create marketing package entry
            $package = PackageProduct::create([
                'product_id' => $product->id,
                'package_code' => $packageCode,
                'title' => $overview['title'],
                'slug' => $packageSlug,
                'tagline' => $overview['tagline'] ?? null,
                'status' => $infoPayload['status'] ?? 'draft',
                'visibility' => $infoPayload['visibility'] ?? 'private',
                'publish_at' => !empty($infoPayload['publish_at']) ? Carbon::parse($infoPayload['publish_at']) : null,
                'package_price' => $packagePrice,
                'compare_at_price' => $comparePrice,
                'calculated_savings_amount' => $savingsAmount,
                'calculated_savings_percent' => $savingsPercent,
                'pricing_breakdown' => [
                    'items_total' => round($itemsTotal, 2),
                    'compare_total' => round($compareTotal, 2),
                    'package_price' => round($packagePrice, 2),
                    'savings_amount' => round($savingsAmount, 2),
                    'savings_percent' => $savingsPercent,
                    'items_count' => count($items),
                ],
                'hero_section' => [
                    'headline' => $overview['hero_headline'] ?? $overview['title'],
                    'subheadline' => $overview['hero_subheadline'] ?? null,
                    'cta_label' => $overview['hero_cta_label'] ?? null,
                    'cta_link' => $overview['hero_cta_link'] ?? null,
                    'media_id' => $heroMedia->id,
                    'media_url' => $heroMedia->url,
                ],
                'content_blocks' => $infoPayload['content_blocks'] ?? [],
                'primary_media_id' => $heroMedia->id,
                'gallery_media_ids' => $galleryMedia->values()->toArray(),
                'short_description' => $infoPayload['short_description'] ?? null,
                'description' => $infoPayload['description'] ?? null,
                'meta_title' => $seoPayload['meta_title'] ?? null,
                'meta_description' => $seoPayload['meta_description'] ?? null,
                'meta_keywords' => $seoPayload['meta_keywords'] ?? null,
                'meta_image_id' => $seoPayload['meta_image_id'] ?? null,
                'landing_settings' => [
                    'highlights' => $infoPayload['highlights'] ?? [],
                    'feature_list' => $infoPayload['features'] ?? [],
                ],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Persist items
            foreach ($items as $position => $item) {
                PackageProductItem::create([
                    'package_product_id' => $product->id,
                    'package_id' => $package->id,
                    'product_id' => $item['product']->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'variant_combination_id' => $item['variant_combination_id'],
                    'color_id' => $item['color_id'],
                    'size_id' => $item['size_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'compare_at_price' => $item['compare_at_price'],
                    'variant_snapshot' => $item['variant_snapshot'],
                    'position' => $position + 1,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Package product created successfully.',
                'redirect' => route('PackageProducts.Edit', $product->id),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create package product.',
                'error_details' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
            ], 500);
        }
    }

    /**
     * Show the form for editing a package product
     */
    public function edit($id)
    {
        $product = Product::where('id', $id)
            ->where('is_package', 1)
            ->firstOrFail();

        $package = PackageProduct::with(['heroMedia', 'metaImage', 'items.product'])
            ->where('product_id', $product->id)
            ->firstOrFail();

        $statuses = [
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'active', 'label' => 'Active'],
            ['value' => 'inactive', 'label' => 'Inactive'],
        ];

        $visibilityOptions = [
            ['value' => 'private', 'label' => 'Private (internal only)'],
            ['value' => 'public', 'label' => 'Public'],
            ['value' => 'scheduled', 'label' => 'Scheduled'],
        ];

        $categories = Category::where('status', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $heroPreview = optional($package->heroMedia)->url
            ?? ($product->image ? asset($product->image) : null);

        $galleryMediaIds = $package->gallery_media_ids ?? [];
        $galleryMedia = !empty($galleryMediaIds)
            ? MediaFile::whereIn('id', $galleryMediaIds)->get()->keyBy('id')
            : collect();

        $gallery = collect($galleryMediaIds)->map(function ($id) use ($galleryMedia) {
            $media = $galleryMedia->get($id);
            return [
                'id' => $id,
                'preview' => optional($media)->url,
                'token' => null,
            ];
        })->values()->all();

        while (count($gallery) < 4) {
            $gallery[] = ['id' => null, 'preview' => null, 'token' => null];
        }

        $items = $package->items()
            ->with('product')
            ->orderBy('position')
            ->get()
            ->map(function (PackageProductItem $item) {
                $product = $item->product;
                if (!$product) {
                    return null;
                }

                $matrix = $this->prepareProductVariantMatrix($product);
                $imageUrl = $product->image
                    ? asset($product->image)
                    : asset('assets/images/default-product.png');
                $basePrice = $product->discount_price && $product->discount_price > 0
                    ? $product->discount_price
                    : ($product->price ?? 0);
                $unitPrice = $item->unit_price ?? $basePrice;
                $compareAtPrice = $item->compare_at_price ?? ($product->price ?? $unitPrice);
                $snapshot = $item->variant_snapshot ?? [];

                return [
                    'key' => 'existing-' . $item->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'image_url' => $imageUrl,
                    'variant_type' => $matrix['variant_type'],
                    'variant_options' => [
                        'combinations' => $matrix['combinations'],
                        'legacy_variants' => $matrix['legacy_variants'],
                        'colors' => $matrix['colors'],
                        'sizes' => $matrix['sizes'],
                    ],
                    'variant_combination_id' => $item->variant_combination_id,
                    'product_variant_id' => $item->product_variant_id,
                    'color_id' => $item->color_id,
                    'size_id' => $item->size_id,
                    'variant_snapshot' => $snapshot,
                    'variant_snapshot_text' => $this->formatSnapshotText($snapshot),
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $unitPrice,
                    'compare_at_price' => (float) $compareAtPrice,
                ];
            })
            ->filter()
            ->values();

        $pricingBreakdown = $package->pricing_breakdown ?? [];
        $allowCompareOverride = isset($pricingBreakdown['compare_total'])
            ? (float) $pricingBreakdown['compare_total'] !== (float) $package->compare_at_price
            : false;

        $packageState = [
            'overview' => [
                'title' => $package->title ?? $product->name,
                'package_code' => $package->package_code,
                'slug' => $package->slug,
                'tagline' => $package->tagline,
                'hero_headline' => $package->hero_section['headline'] ?? null,
                'hero_subheadline' => $package->hero_section['subheadline'] ?? null,
                'hero_cta_label' => $package->hero_section['cta_label'] ?? null,
                'hero_cta_link' => $package->hero_section['cta_link'] ?? null,
            ],
            'pricing' => [
                'package_price' => (float) $package->package_price,
                'compare_at_price' => (float) $package->compare_at_price,
                'allow_compare_override' => $allowCompareOverride ? 1 : 0,
            ],
            'media' => [
                'hero' => [
                    'id' => $package->primary_media_id,
                    'preview' => $heroPreview,
                    'token' => null,
                ],
                'gallery' => array_slice($gallery, 0, 4),
            ],
            'items' => $items,
            'info' => [
                'status' => $package->status,
                'visibility' => $package->visibility,
                'publish_at' => optional($package->publish_at)?->format('Y-m-d\TH:i'),
                'category_id' => $product->category_id,
                'short_description' => $package->short_description,
                'description' => $package->description,
                'highlights' => $package->landing_settings['highlights'] ?? [],
                'content_blocks_raw' => $package->content_blocks
                    ? json_encode($package->content_blocks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                    : '',
            ],
            'seo' => [
                'meta_title' => $package->meta_title,
                'meta_keywords' => $package->meta_keywords,
                'meta_description' => $package->meta_description,
                'meta_image' => [
                    'id' => $package->meta_image_id,
                    'preview' => optional($package->metaImage)->url,
                    'token' => null,
                ],
            ],
            'timestamp' => now()->toIso8601String(),
        ];

        return view('backend.package_product.edit', [
            'product' => $product,
            'packageState' => $packageState,
            'statuses' => $statuses,
            'visibilityOptions' => $visibilityOptions,
            'categories' => $categories,
        ]);
    }

    /**
     * Update a package product
     */
    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::where('id', $id)
            ->where('is_package', 1)
            ->firstOrFail();

        $package = PackageProduct::where('product_id', $product->id)->firstOrFail();

        $payload = $request->all();

        $validator = Validator::make($payload, [
            'overview.title' => ['required', 'string', 'max:255'],
            'overview.slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
            'overview.package_code' => ['nullable', 'string', 'max:40'],
            'overview.tagline' => ['nullable', 'string', 'max:255'],
            'overview.hero_headline' => ['nullable', 'string', 'max:255'],
            'overview.hero_subheadline' => ['nullable', 'string', 'max:500'],
            'overview.hero_cta_label' => ['nullable', 'string', 'max:120'],
            'overview.hero_cta_link' => ['nullable', 'string', 'max:255'],
            'media.hero_image_id' => ['nullable', 'exists:media_files,id'],
            'media.gallery' => ['nullable', 'array'],
            'media.gallery.*.id' => ['required_with:media.gallery', 'exists:media_files,id'],
            'pricing.package_price' => ['required', 'numeric', 'min:0'],
            'pricing.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'pricing.allow_compare_override' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.variant_combination_id' => ['nullable', 'exists:product_variant_combinations,id'],
            'items.*.color_id' => ['nullable', 'exists:colors,id'],
            'items.*.size_id' => ['nullable', 'exists:product_sizes,id'],
            'info.status' => ['required', 'in:draft,active,inactive'],
            'info.visibility' => ['required', 'in:private,public,scheduled'],
            'info.publish_at' => ['nullable', 'date'],
            'info.short_description' => ['nullable', 'string'],
            'info.description' => ['nullable', 'string'],
            'info.highlights' => ['nullable', 'array', 'max:6'],
            'seo.meta_title' => ['nullable', 'string', 'max:255'],
            'seo.meta_keywords' => ['nullable', 'string', 'max:500'],
            'seo.meta_description' => ['nullable', 'string'],
            'seo.meta_image_id' => ['nullable', 'exists:media_files,id'],
        ], [
            'overview.slug.regex' => 'Slug may only contain lowercase letters, numbers and hyphen.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $overview = $validated['overview'];
        $pricing = $validated['pricing'];
        $itemsPayload = $validated['items'];
        $mediaPayload = $validated['media'] ?? [];
        $infoPayload = $validated['info'] ?? [];
        $seoPayload = $validated['seo'] ?? [];

        $packagePrice = (float) ($pricing['package_price'] ?? 0);
        $comparePrice = isset($pricing['compare_at_price']) ? (float) $pricing['compare_at_price'] : null;

        $mediaHeroId = $mediaPayload['hero_image_id'] ?? $package->primary_media_id;
        if (!$mediaHeroId) {
            return response()->json([
                'success' => false,
                'message' => 'Package hero image is required.',
            ], 422);
        }

        $heroMedia = MediaFile::find($mediaHeroId);
        if (!$heroMedia) {
            return response()->json([
                'success' => false,
                'message' => 'Hero image not found.',
            ], 422);
        }

        $galleryMedia = collect($mediaPayload['gallery'] ?? [])->pluck('id')->filter()->values();

        $items = [];
        $itemsTotal = 0;
        $compareTotal = 0;

        foreach ($itemsPayload as $index => $itemPayload) {
            $childProduct = Product::where('id', $itemPayload['product_id'])
                ->where('is_package', 0)
                ->first();

            if (!$childProduct) {
                return response()->json([
                    'success' => false,
                    'message' => "Selected product no longer exists or is not eligible for packages.",
                ], 422);
            }

            $variantSnapshot = [];
            $variantCombinationId = $itemPayload['variant_combination_id'] ?? null;
            $productVariantId = $itemPayload['product_variant_id'] ?? null;

            if ($variantCombinationId) {
                $combination = ProductVariantCombination::where('id', $variantCombinationId)
                    ->where('product_id', $childProduct->id)
                    ->first();

                if (!$combination) {
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid variant combination selected for {$childProduct->name}.",
                    ], 422);
                }
            }

            if ($productVariantId) {
                $legacyVariant = ProductVariant::where('id', $productVariantId)
                    ->where('product_id', $childProduct->id)
                    ->first();

                if (!$legacyVariant) {
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid variant selected for {$childProduct->name}.",
                    ], 422);
                }
            }

            if (!empty($itemPayload['color_id'])) {
                $variantSnapshot['Color'] = optional(Color::find($itemPayload['color_id']))->name;
            }

            if (!empty($itemPayload['size_id'])) {
                $variantSnapshot['Size'] = optional(ProductSize::find($itemPayload['size_id']))->name;
            }

            if (!empty($combination?->variant_values)) {
                foreach ($combination->variant_values as $key => $value) {
                    $variantSnapshot[Str::title(str_replace('_', ' ', $key))] = $value;
                }
            }

            $unitPrice = isset($itemPayload['unit_price'])
                ? (float) $itemPayload['unit_price']
                : ($childProduct->discount_price && $childProduct->discount_price > 0
                    ? $childProduct->discount_price
                    : $childProduct->price);

            $compareAtPrice = isset($itemPayload['compare_at_price'])
                ? (float) $itemPayload['compare_at_price']
                : ($childProduct->price ?? $unitPrice);

            $quantity = (int) $itemPayload['quantity'];

            $itemsTotal += $unitPrice * $quantity;
            $compareTotal += $compareAtPrice * $quantity;

            $items[] = [
                'product' => $childProduct,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'compare_at_price' => $compareAtPrice,
                'product_variant_id' => $productVariantId,
                'variant_combination_id' => $variantCombinationId,
                'color_id' => $itemPayload['color_id'] ?? null,
                'size_id' => $itemPayload['size_id'] ?? null,
                'variant_snapshot' => array_filter($variantSnapshot),
            ];
        }

        if ($comparePrice === null || empty($pricing['allow_compare_override'])) {
            $comparePrice = $compareTotal;
        }

        if ($comparePrice < $packagePrice) {
            return response()->json([
                'success' => false,
                'message' => 'Compare price must be greater than or equal to package selling price.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $packageCode = $overview['package_code'] ?? $package->package_code;
            if ($packageCode) {
                $packageCode = strtoupper(trim($packageCode));
                if ($packageCode === '') {
                    $packageCode = $this->generatePackageCode();
                } elseif (
                    PackageProduct::where('package_code', $packageCode)
                        ->where('id', '!=', $package->id)
                        ->exists()
                ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors' => [
                            'overview.package_code' => ['The package code has already been taken.'],
                        ],
                    ], 422);
                }
            } else {
                $packageCode = $package->package_code ?? $this->generatePackageCode();
            }

            $baseSlug = $overview['slug'] ?? $package->slug ?? $overview['title'] ?? 'package';
            $productSlug = $this->uniqueProductSlug($baseSlug, $product->id);
            $packageSlug = $this->uniquePackageSlug($baseSlug, $package->id);

            $product->name = $overview['title'];
            $product->slug = $productSlug;
            $product->category_id = $infoPayload['category_id'] ?? null;
            $product->short_description = $infoPayload['short_description'] ?? null;
            $product->description = $infoPayload['description'] ?? null;
            $product->price = $comparePrice;
            $product->discount_price = $packagePrice;
            $product->status = ($infoPayload['status'] ?? 'draft') === 'active' ? 1 : 0;
            $product->meta_title = $seoPayload['meta_title'] ?? null;
            $product->meta_keywords = $seoPayload['meta_keywords'] ?? null;
            $product->meta_description = $seoPayload['meta_description'] ?? null;
            $product->image = $heroMedia->file_path;
            $product->multiple_images = null;

            $heroMedia->markAsPermanent();

            if ($galleryMedia->isNotEmpty()) {
                $galleryFiles = MediaFile::whereIn('id', $galleryMedia)->get();
                $galleryPaths = $galleryFiles->pluck('file_path')->filter()->values()->toArray();
                $product->multiple_images = !empty($galleryPaths) ? json_encode($galleryPaths) : null;

                MediaFile::whereIn('id', $galleryMedia)->update([
                    'is_temp' => false,
                    'temp_token' => null,
                ]);
            }

            $product->save();

            $savingsAmount = max(0, $comparePrice - $packagePrice);
            $savingsPercent = $comparePrice > 0 ? round(($savingsAmount / $comparePrice) * 100, 2) : 0;

            $package->update([
                'package_code' => $packageCode,
                'title' => $overview['title'],
                'slug' => $packageSlug,
                'tagline' => $overview['tagline'] ?? null,
                'status' => $infoPayload['status'] ?? 'draft',
                'visibility' => $infoPayload['visibility'] ?? 'private',
                'publish_at' => !empty($infoPayload['publish_at']) ? Carbon::parse($infoPayload['publish_at']) : null,
                'package_price' => $packagePrice,
                'compare_at_price' => $comparePrice,
                'calculated_savings_amount' => $savingsAmount,
                'calculated_savings_percent' => $savingsPercent,
                'pricing_breakdown' => [
                    'items_total' => round($itemsTotal, 2),
                    'compare_total' => round($compareTotal, 2),
                    'package_price' => round($packagePrice, 2),
                    'savings_amount' => round($savingsAmount, 2),
                    'savings_percent' => $savingsPercent,
                    'items_count' => count($items),
                ],
                'hero_section' => [
                    'headline' => $overview['hero_headline'] ?? $overview['title'],
                    'subheadline' => $overview['hero_subheadline'] ?? null,
                    'cta_label' => $overview['hero_cta_label'] ?? null,
                    'cta_link' => $overview['hero_cta_link'] ?? null,
                ],
                'content_blocks' => $infoPayload['content_blocks'] ?? null,
                'primary_media_id' => $heroMedia->id,
                'gallery_media_ids' => $galleryMedia->all(),
                'short_description' => $infoPayload['short_description'] ?? null,
                'description' => $infoPayload['description'] ?? null,
                'meta_title' => $seoPayload['meta_title'] ?? null,
                'meta_description' => $seoPayload['meta_description'] ?? null,
                'meta_keywords' => $seoPayload['meta_keywords'] ?? null,
                'meta_image_id' => $seoPayload['meta_image_id'] ?? null,
                'landing_settings' => [
                    'highlights' => $infoPayload['highlights'] ?? [],
                ],
                'updated_by' => Auth::id(),
            ]);

            PackageProductItem::where('package_product_id', $product->id)->delete();

            foreach ($items as $position => $itemData) {
                PackageProductItem::create([
                    'package_product_id' => $product->id,
                    'package_id' => $package->id,
                    'product_id' => $itemData['product']->id,
                    'product_variant_id' => $itemData['product_variant_id'],
                    'variant_combination_id' => $itemData['variant_combination_id'],
                    'color_id' => $itemData['color_id'],
                    'size_id' => $itemData['size_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'compare_at_price' => $itemData['compare_at_price'],
                    'variant_snapshot' => $itemData['variant_snapshot'],
                    'position' => $position + 1,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Package updated successfully.',
                'redirect' => route('PackageProducts.Edit', $product->id),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update package product.',
                'error_details' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
            ], 500);
        }
    }

    /**
     * Remove a package product
     */
    public function destroy($id)
    {
        $product = Product::where('id', $id)->where('is_package', 1)->firstOrFail();
        $package = PackageProduct::where('product_id', $product->id)->first();

        PackageProductItem::where('package_product_id', $product->id)->delete();

        if ($package) {
            $package->delete();
        }

        if ($product->image && file_exists(public_path($product->image))) {
            @unlink(public_path($product->image));
        }

        $product->delete();

        return response()->json(['success' => 'Package Product deleted successfully.']);
    }

    /**
     * Show package items management page
     */
    public function manageItems($id)
    {
        $package = Product::where('id', $id)->where('is_package', 1)->firstOrFail();
        $packageItems = PackageProductItem::where('package_product_id', $id)
            ->with(['product', 'color', 'size'])
            ->orderBy('position')
            ->get();

        $products = Product::where('is_package', 0)
            ->where('status', 1)
            ->select('id', 'name', 'price', 'discount_price', 'image')
            ->get();

        return view('backend.package_product.manage_items', compact('package', 'packageItems', 'products'));
    }

    /**
     * Add new item to package
     */
    public function addItem(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:product_sizes,id',
        ]);

        $package = Product::where('id', $id)->where('is_package', 1)->firstOrFail();

        $existingItem = PackageProductItem::where('package_product_id', $package->id)
            ->where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->where('size_id', $request->size_id)
            ->first();

        if ($existingItem) {
            return back()->withErrors(['product_id' => 'This product with selected variant is already added to the package.']);
        }

        $packageEntity = PackageProduct::where('product_id', $package->id)->first();

        PackageProductItem::create([
            'package_product_id' => $package->id,
            'package_id' => optional($packageEntity)->id,
            'product_id' => $request->product_id,
            'color_id' => $request->color_id,
            'size_id' => $request->size_id,
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Product added to package successfully');
    }

    /**
     * Update existing package item
     */
    public function updateItem(Request $request, $packageId, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:product_sizes,id',
        ]);

        $item = PackageProductItem::findOrFail($itemId);
        $item->update([
            'quantity' => $request->quantity,
            'color_id' => $request->color_id,
            'size_id' => $request->size_id,
        ]);

        return back()->with('success', 'Package item updated successfully');
    }

    /**
     * Remove item from package
     */
    public function removeItem($packageId, $itemId)
    {
        $item = PackageProductItem::findOrFail($itemId);
        $item->delete();

        return response()->json(['success' => 'Item removed from package successfully.']);
    }

    /**
     * Search catalog products for package builder.
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $term = trim($request->get('q', ''));
        $limit = (int) $request->get('limit', 20);

        $query = Product::query()
            ->where('status', 1)
            ->where('is_package', 0);

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                    ->orWhere('sku', 'like', '%' . $term . '%')
                    ->orWhere('code', 'like', '%' . $term . '%')
                    ->orWhere('barcode', 'like', '%' . $term . '%');
            });
        }

        $products = $query->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(function (Product $product) {
                $hasCombos = $product->variantCombinations()->active()->exists();
                $legacyVariants = $product->variants()->exists();
                $variantType = $hasCombos ? 'combination' : ($legacyVariants ? 'legacy' : 'simple');

                $image = $product->image ? asset($product->image) : asset('assets/images/default-product.png');
                $effectivePrice = $product->discount_price && $product->discount_price > 0
                    ? $product->discount_price
                    : $product->price;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'sku' => $product->sku,
                    'variant_type' => $variantType,
                    'price' => $product->price,
                    'discount_price' => $product->discount_price,
                    'effective_price' => $effectivePrice,
                    'image_url' => $image,
                    'total_stock' => $product->totalStock() ?? 0,
                ];
            });

        return response()->json([
            'success' => true,
            'results' => $products,
        ]);
    }

    /**
     * Variant matrix for selected product (legacy + combination).
     */
    public function productMatrix($productId): JsonResponse
    {
        $product = Product::where('id', $productId)
            ->where('is_package', 0)
            ->firstOrFail();

        $matrix = $this->prepareProductVariantMatrix($product);

        return response()->json([
            'success' => true,
            'variant_type' => $matrix['variant_type'],
            'product' => $matrix['product'],
            'combinations' => $matrix['combinations'],
            'legacy_variants' => $matrix['legacy_variants'],
            'colors' => $matrix['colors'],
            'sizes' => $matrix['sizes'],
            'total_stock' => $matrix['product']['total_stock'],
            'has_variants' => $matrix['variant_type'] !== 'simple',
        ]);
    }

    /**
     * Get product variants (legacy endpoint retained for backward compatibility)
     */
    public function getProductVariants($productId)
    {
        return $this->productMatrix($productId);
    }

    /**
     * Get specific variant stock for AJAX
     */
    public function getVariantStock(Request $request, $productId)
    {
        $colorId = $request->color_id;
        $sizeId = $request->size_id;

        $query = DB::table('product_variants')
            ->where('product_id', $productId);

        if ($colorId) {
            $query->where('color_id', $colorId);
        }

        if ($sizeId) {
            $query->where('size_id', $sizeId);
        }

        $stock = $query->sum('stock');

        return response()->json([
            'stock' => $stock
        ]);
    }

    protected function prepareProductVariantMatrix(Product $product): array
    {
        $comboVariants = $product->variantCombinations()->active()->get();

        $combinations = $comboVariants->map(function (ProductVariantCombination $combination) use ($product) {
            $attributes = $combination->variant_values ?? [];

            return [
                'id' => $combination->id,
                'combination_key' => $combination->combination_key,
                'attributes' => $attributes,
                'display' => $this->formatVariantDisplay($attributes),
                'price' => $combination->price ?? null,
                'discount_price' => $combination->discount_price ?? null,
                'additional_price' => $combination->additional_price ?? 0,
                'stock' => $combination->stock ?? 0,
                'sku' => $combination->sku ?? null,
                'barcode' => $combination->barcode ?? null,
                'image_url' => $combination->image
                    ? asset($combination->image)
                    : ($product->image ? asset($product->image) : asset('assets/images/default-product.png')),
            ];
        })->values()->toArray();

        $legacyVariantsCollection = $product->variants()
            ->with(['color', 'size'])
            ->get();

        $legacyVariants = $legacyVariantsCollection->map(function (ProductVariant $variant) {
            return [
                'id' => $variant->id,
                'color_id' => $variant->color_id,
                'color_name' => optional($variant->color)->name,
                'size_id' => $variant->size_id,
                'size_name' => optional($variant->size)->name,
                'stock' => $variant->stock ?? 0,
            ];
        })->values()->toArray();

        $colors = collect($legacyVariants)
            ->filter(fn ($variant) => !empty($variant['color_id']) && !empty($variant['color_name']))
            ->unique('color_id')
            ->map(function ($variant) {
                return [
                    'id' => $variant['color_id'],
                    'name' => $variant['color_name'],
                ];
            })
            ->values()
            ->toArray();

        $sizes = collect($legacyVariants)
            ->filter(fn ($variant) => !empty($variant['size_id']) && !empty($variant['size_name']))
            ->unique('size_id')
            ->map(function ($variant) {
                return [
                    'id' => $variant['size_id'],
                    'name' => $variant['size_name'],
                ];
            })
            ->values()
            ->toArray();

        $variantType = !empty($combinations)
            ? 'combination'
            : (!empty($legacyVariants) ? 'legacy' : 'simple');

        return [
            'variant_type' => $variantType,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'effective_price' => $product->discount_price && $product->discount_price > 0
                    ? $product->discount_price
                    : $product->price,
                'image_url' => $product->image ? asset($product->image) : asset('assets/images/default-product.png'),
                'total_stock' => $product->totalStock() ?? 0,
            ],
            'combinations' => $combinations,
            'legacy_variants' => $legacyVariants,
            'colors' => $colors,
            'sizes' => $sizes,
        ];
    }

    protected function formatVariantDisplay(?array $attributes): string
    {
        if (empty($attributes)) {
            return 'Variant';
        }

        return collect($attributes)
            ->filter(fn ($value) => filled($value))
            ->values()
            ->implode(' • ');
    }

    protected function formatSnapshotText(?array $snapshot): string
    {
        if (empty($snapshot)) {
            return '';
        }

        return collect($snapshot)
            ->filter(fn ($value) => filled($value))
            ->values()
            ->implode(' • ');
    }

    /**
     * Generate a unique package code.
     */
    protected function generatePackageCode(): string
    {
        $prefix = 'PKG-' . now()->format('ym');
        do {
            $code = $prefix . '-' . Str::upper(Str::random(4));
        } while (PackageProduct::where('package_code', $code)->exists());

        return $code;
    }

    /**
     * Ensure product slug unique against products table.
     */
    protected function uniqueProductSlug(string $baseSlug, ?int $ignoreProductId = null): string
    {
        $slug = Str::slug($baseSlug);
        if ($slug === '') {
            $slug = Str::slug(Str::random(8));
        }
        if ($slug === '') {
            $slug = 'package-' . Str::random(6);
        }
        $original = $slug;
        $suffix = 1;

        while (
            Product::where('slug', $slug)
                ->when($ignoreProductId, fn ($query, $ignoreProductId) => $query->where('id', '!=', $ignoreProductId))
                ->exists()
        ) {
            $slug = $original . '-' . $suffix++;
        }

        return $slug;
    }

    /**
     * Ensure package slug unique against package_products table.
     */
    protected function uniquePackageSlug(string $baseSlug, ?int $ignorePackageId = null): string
    {
        $slug = Str::slug($baseSlug);
        if ($slug === '') {
            $slug = Str::slug(Str::random(8));
        }
        if ($slug === '') {
            $slug = 'marketing-package-' . Str::random(6);
        }
        $original = $slug;
        $suffix = 1;

        while (
            PackageProduct::where('slug', $slug)
                ->when($ignorePackageId, fn ($query, $ignorePackageId) => $query->where('id', '!=', $ignorePackageId))
                ->exists()
        ) {
            $slug = $original . '-' . $suffix++;
        }

        return $slug;
    }
}

