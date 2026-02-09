<div class="form-group card-search-input" style="position: relative">
    <input type="text" id="search_keyword" class="form-control" onkeyup="liveSearchProduct()" placeholder="Search by Product Name" />
    <i class="fa fa-search" style="position: absolute; top: 10px; right: 10px;"></i>
</div>
<div class="row">
    <div class="col-6">
        <select class="form-control w-100" id="product_category_id" data-toggle="select2" onchange="liveSearchProduct()">
            <option value="">All Catgories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6">
        <select class="form-control w-100" id="product_brand_id" data-toggle="select2" onchange="liveSearchProduct()">
            <option value="">All Brands</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
            @endforeach
        </select>
    </div>
</div>
