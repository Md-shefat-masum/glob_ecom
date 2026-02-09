<style>
    /* Container for product cards */
    .live-search-results, .product-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: stretch;
    }

    .modern-product-card {
        display: flex;
        flex-direction: column;
        padding: 12px;
        margin-bottom: 8px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        width: 100%;
        max-width: 280px;
        flex-grow: 1;
        flex-shrink: 0;
        flex-basis: 200px;
    }

    .modern-product-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #007bff;
    }

    .product-image-container {
        position: relative;
        margin-bottom: 10px;
        width: 100%;
    }

    .product-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #f1f3f4;
    }

    .discount-badge {
        position: absolute;
        top: 4px;
        right: 4px;
        background: linear-gradient(45deg, #ff4757, #ff3742);
        color: white;
        font-size: 9px;
        font-weight: 600;
        padding: 2px 5px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(255, 71, 87, 0.3);
    }

    .product-details {
        flex: 1;
        width: 100%;
    }

    .product-header {
        margin-bottom: 6px;
    }

    .product-name {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 3px 0;
        line-height: 1.2;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 34px;
    }

    .product-code {
        font-size: 10px;
        color: #6c757d;
        font-weight: 500;
    }

    .variant-status {
        margin-bottom: 6px;
    }

    .badge {
        font-size: 9px;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 500;
    }

    .badge-secondary {
        background-color: #6c757d;
        color: white;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .price-section {
        margin-bottom: 6px;
    }

    .original-price {
        text-decoration: line-through;
        color: #6c757d;
        font-size: 11px;
        margin-right: 4px;
    }

    .discounted-price {
        color: #e74c3c;
        font-weight: 700;
        font-size: 14px;
    }

    .regular-price {
        color: #2c3e50;
        font-weight: 600;
        font-size: 14px;
    }

    .stock-info {
        font-size: 10px;
        margin-bottom: 8px;
    }

    .stock-available {
        color: #28a745;
        font-weight: 500;
    }

    .stock-out {
        color: #dc3545;
        font-weight: 500;
    }

    .action-section {
        width: 100%;
    }

    .btn-add-cart,
    .btn-stock-out {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 32px;
        border-radius: 6px;
        border: none;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: white;
        gap: 4px;
    }

    .btn-add-cart {
        background: linear-gradient(135deg, #007bff, #0056b3);
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.2);
    }

    .btn-add-cart:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0, 123, 255, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-add-cart.variant-btn {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        box-shadow: 0 2px 6px rgba(40, 167, 69, 0.2);
    }

    .btn-add-cart.variant-btn:hover {
        box-shadow: 0 3px 8px rgba(40, 167, 69, 0.3);
    }

    .btn-stock-out {
        background: linear-gradient(135deg, #6c757d, #545b62);
        cursor: not-allowed;
        opacity: 0.7;
    }

    .btn-add-cart i,
    .btn-stock-out i {
        font-size: 12px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .modern-product-card {
            padding: 10px;
            max-width: 100%;
        }

        .product-image {
            height: 100px;
        }

        .product-name {
            font-size: 13px;
            height: 30px;
        }

        .btn-add-cart,
        .btn-stock-out {
            height: 28px;
            font-size: 10px;
        }
    }
</style>

@foreach ($products as $product)
    @php
        $variants = DB::table('product_variants')
            ->leftJoin('products', 'product_variants.product_id', 'products.id')
            ->leftJoin('colors', 'product_variants.color_id', 'colors.id')
            ->leftJoin('product_sizes', 'product_variants.size_id', 'product_sizes.id')
            ->select(
                'product_variants.*',
                'products.name as product_name',
                'products.image as product_image',
                'colors.name as color_name',
                'product_sizes.name as size_name',
            )
            ->where('product_variants.product_id', $product->id)
            ->get();

        $totalStock = $product->stock;
        $productPrice = $product->price;
        $productDiscountPrice = $product->discount_price;

        if (count($variants) > 0) {
            $totalStock = 0;
            $variantMinDiscountPriceArray = [];
            $variantMinPriceArray = [];

            foreach ($variants as $variant) {
                $totalStock = $totalStock + $variant->stock;
                $variantMinDiscountPriceArray[] = $variant->discounted_price;
                $variantMinPriceArray[] = $variant->price;
            }

            $productDiscountPrice = min($variantMinDiscountPriceArray);
            $productPrice = min($variantMinPriceArray);
        }
    @endphp

    <div class="live_search_item modern-product-card" style="flex-grow:1;">
        <div class="product-image-container">
            <img loading="lazy" src="{{ url($product->image) }}" alt="{{ $product->name }}" class="product-image"
                style="object-position: top center;">
            @if ($productDiscountPrice > 0)
                <span class="discount-badge">
                    {{ round((($productPrice - $productDiscountPrice) / $productPrice) * 100) }}% OFF
                </span>
            @endif
        </div>

        <div class="product-details">
            <div class="product-header">
                <h6 class="product-name">{{ $product->name }}</h6>
                <span class="product-code">#{{ $product->code }}</span>
            </div>

            <div class="variant-status">
                @if (count($variants) == 0)
                    <span class="badge badge-secondary">
                        <i class="fas fa-box"></i> Simple Product
                    </span>
                @else
                    <span class="badge badge-success">
                        <i class="fas fa-layer-group"></i> {{ count($variants) }} Variants
                    </span>
                @endif
            </div>

            <div class="price-section">
                @if ($productDiscountPrice > 0)
                    <span class="original-price">৳{{ number_format($productPrice, 2) }}</span>
                    <span class="discounted-price">৳{{ number_format($productDiscountPrice, 2) }}</span>
                @else
                    <span class="regular-price">৳{{ number_format($productPrice, 2) }}</span>
                @endif
            </div>

            <div class="stock-info">
                @if ($totalStock > 0)
                    <span class="stock-available">
                        <i class="fas fa-check-circle"></i> {{ $totalStock }} in stock
                    </span>
                @else
                    <span class="stock-out">
                        <i class="fas fa-times-circle"></i> Out of stock
                    </span>
                @endif
            </div>
        </div>

        <div class="action-section">
            @if ($totalStock)
                @if (count($variants) == 0)
                    <button onclick="addToCart({{ $product->id }},0,0)" class="btn-add-cart">
                        <i class="fas fa-cart-plus"></i>
                        <span>Add to Cart</span>
                    </button>
                @else
                    <button onclick="showVariant({{ $product->id }})" class="btn-add-cart variant-btn">
                        <i class="fas fa-cog"></i>
                        <span>Select Variant</span>
                    </button>
                @endif
            @else
                <button class="btn-stock-out" disabled>
                    <i class="fas fa-ban"></i>
                    <span>Stock Out</span>
                </button>
            @endif
        </div>
    </div>
@endforeach
