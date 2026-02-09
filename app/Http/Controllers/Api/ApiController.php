<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderProgressResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\GeneralInfoResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\FlagResource;
use App\Models\EmailConfigure;
use App\Models\User;
use App\Models\Category;
use App\Models\ChildCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\BillingAddress;
use App\Models\Brand;
use App\Models\ContactRequest;
use App\Models\Flag;
use App\Models\AboutUs;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderPayment;
use App\Models\OrderProgress;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\PromoCode;
use App\Models\SubscribedUsers;
use App\Models\ShippingInfo;
use App\Models\Subcategory;
use App\Models\WishList;
use App\Models\PaymentGateway;
use App\Models\ProductQuestionAnswer;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Image;


class ApiController extends BaseController
{
    const AUTHORIZATION_TOKEN = 'GenericCommerceV1-SBW7583837NUDD82';

    public function userProfileInfo()
    {

        $userInfo = User::where('id', auth()->user()->id)->first();

        $name = $userInfo->name;
        $email = $userInfo->email;
        $phone = $userInfo->phone;
        $image = $userInfo->image;
        $balance = $userInfo->balance;
        $address = $userInfo->address;
        $totalProductInWishList = WishList::where('user_id', $userInfo->id)->count();
        $totalOrders = Order::where('user_id', $userInfo->id)->count();
        $totalPendingOrders = Order::where('user_id', $userInfo->id)->where('order_status', 0)->count();
        $totalConfirmedOrders = Order::where('user_id', $userInfo->id)->where('order_status', 1)->count();

        $data = array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'balance' => $balance,
            'image' => $image,
            'address' => $address,
            'totalProductInWishList' => $totalProductInWishList,
            'totalOrders' => $totalOrders,
            'totalPendingOrders' => $totalPendingOrders,
            'totalConfirmedOrders' => $totalConfirmedOrders,
        );

        return $this->sendResponse($data, 'User Profile Retrieved Successfully.');
    }

    public function userProfileUpdate(Request $request)
    {

        $userInfo = User::where('id', auth()->user()->id)->first();
        $userImage = $userInfo->image;
        if ($request->hasFile('image')) {

            if ($userImage && file_exists(public_path($userImage))) {
                unlink(public_path($userImage));
            }

            $get_image = $request->file('image');
            $image_name = str::random(5) . time() . '.' . $get_image->getClientOriginalExtension();
            $location = public_path('userProfileImages/');
            Image::make($get_image)->save($location . $image_name, 50);
            $userImage = "userProfileImages/" . $image_name;
        }

        $user_id = auth()->user()->id;
        $name = $request->name;
        $phone = $request->phone;
        $email = $request->email;
        $address = $request->address;
        $current_password = $request->current_password;
        $new_password = $request->new_password;

        if ($email != '' && $userInfo->email != $email) {
            $email_check = User::where('email', $email)->first();
            if ($email_check) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already used ! Please use another Email'
                ]);
            }
        }
        if ($phone != '' && $userInfo->phone != $phone) {
            $phone_check = User::where('phone', $phone)->first();
            if ($phone_check) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mobile No already used ! Please use another Mobile No'
                ]);
            }
        }

        if ($current_password != '' && $new_password != '') {
            if (Hash::check($current_password, $userInfo->password)) {
                User::where('id', $user_id)->update([
                    'password' => Hash::make($new_password),
                    'updated_at' => Carbon::now()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Your Current Password is Incorrect'
                ]);
            }
        }

        if (($email == '' || $email == NULL) && ($phone == '' || $phone == NULL)) {
            return response()->json([
                'success' => false,
                'message' => 'Both Email & Phone Cannot be Null'
            ]);
        } else {
            $userInfo->name = $name;
            $userInfo->phone = $phone;
            $userInfo->email = $email;
            $userInfo->image = $userImage;
            $userInfo->address = $address;
            $userInfo->updated_at = Carbon::now();
            $userInfo->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile Updated Successfully',
                'data' => $userInfo
            ]);
        }
    }

    public function getCategoryTree(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $categories = Category::orderBy('serial', 'asc')->where('status', 1)->get();
            return response()->json([
                'success' => true,
                'data' => CategoryResource::collection($categories)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getCategoryList(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $categories = Category::orderBy('serial', 'asc')->where('status', 1)->get();
            return response()->json([
                'success' => true,
                'data' => $categories
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getFeaturedSubcategory(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $subcategories = DB::table('subcategories')
                ->join('categories', 'subcategories.category_id', '=', 'categories.id')
                ->select('subcategories.*', 'categories.name as category_name')
                ->where('subcategories.status', 1)
                ->where('subcategories.featured', 1)
                ->orderBy('subcategories.name', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subcategories
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getSubcategoryOfCategory(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $subcategories = DB::table('subcategories')
                ->join('categories', 'subcategories.category_id', '=', 'categories.id')
                ->select('subcategories.*', 'categories.name as category_name')
                ->where('category_id', $request->category_id)
                ->where('subcategories.status', 1)
                ->orderBy('subcategories.name', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subcategories
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getChildcategoryOfSubcategory(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('child_categories')
                ->join('categories', 'child_categories.category_id', '=', 'categories.id')
                ->join('subcategories', 'child_categories.subcategory_id', '=', 'subcategories.id')
                ->select('child_categories.*', 'categories.name as category_name', 'subcategories.name as subcategory_name')
                ->where('child_categories.category_id', $request->category_id)
                ->where('child_categories.subcategory_id', $request->subcategory_id)
                ->where('child_categories.status', 1)
                ->orderBy('child_categories.name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllProducts(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                ->where('products.status', 1)
                ->orderBy('products.id', 'desc')
                ->paginate(16);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($data)->resource
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getRelatedProducts(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $prodInfo = Product::where('id', $request->product_id)->first();
            $brand_id = $prodInfo->brand_id;
            $categoryId = $prodInfo->category_id;

            $data = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                ->where('products.status', 1)
                ->when($brand_id, function ($query) use ($brand_id, $categoryId) {
                    if ($brand_id > 0)
                        return $query->where('products.brand_id', $brand_id);
                    else
                        return $query->where('products.category_id', $categoryId);
                })
                ->where('products.id', '!=', $request->product_id)
                ->inRandomOrder()
                ->skip(0)
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($data)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getYouMayLikeProducts(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $prodInfo = Product::where('id', $request->product_id)->first();
            $categoryId = $prodInfo->category_id;

            $data = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                ->where('products.status', 1)
                ->where('products.id', '!=', $request->product_id)
                ->where('products.category_id', $categoryId)
                ->skip(0)
                ->limit(6)
                ->inRandomOrder()
                ->get();

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($data)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function categoryWiseProducts(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $categoryInfo = Category::where('slug', $request->category_slug)->first();

            $data = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                ->where('categories.id', $categoryInfo ? $categoryInfo->id : 0)
                ->where('products.status', 1)
                ->orderBy('products.id', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($data)->resource
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function subcategoryWiseProducts(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $subCategoryInfo = Subcategory::where('slug', $request->subcategory_slug)->first();

            $data = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                ->where('subcategories.id', $subCategoryInfo ? $subCategoryInfo->id : 0)
                ->where('products.status', 1)
                ->orderBy('products.id', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($data)->resource
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function childcategoryWiseProducts(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $childCategoryInfo = ChildCategory::where('slug', $request->childcategory_slug)->first();

            $data = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                ->where('child_categories.id', $childCategoryInfo ? $childCategoryInfo->id : 0)
                ->where('products.status', 1)
                ->orderBy('products.id', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($data)->resource
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }


    public function productDetails(Request $request, $id)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                ->where('products.id', $id)
                ->orWhere('products.slug', $id)
                ->first();

            return response()->json([
                'success' => true,
                'data' => new ProductResource($data)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function flagWiseProducts(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                ->where('products.flag_id', $request->flag)
                ->where('products.status', 1)
                ->orderBy('products.id', 'desc')
                ->skip(0)
                ->limit(6)
                ->get();

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($data)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function featuredFlagWiseProducts(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = Flag::where('featured', 1)->where('status', 1)->get();

            return response()->json([
                'success' => true,
                'data' => FlagResource::collection($data)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function flagWiseAllProducts(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                ->where('products.flag_id', $request->flag)
                ->where('products.status', 1)
                ->orderBy('products.id', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($data)->resource
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllFlags(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $flags = Flag::orderBy('name', 'asc')->where('status', 1)->get();
            return response()->json([
                'success' => true,
                'data' => $flags
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllBrands(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $brands = Brand::orderBy('serial', 'asc')->where('status', 1)->get();
            return response()->json(['success' => true, 'data' => $brands], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function searchProducts(Request $request)
    { //post method
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            // Support both 'query' and 'search_keyword' parameters
            $searchKeyword = $request->input('query') ?? $request->input('search_keyword') ?? '';

            $brandInfo = Brand::where('slug', $request->brand_slug)->first();
            $brand_id = $brandInfo ? $brandInfo->id : 0;

            // Use Eloquent with relationships instead of raw query builder
            $products = \App\Models\Product::with([
                'category',
                'subcategory',
                'childCategory',
                'unit',
                'brand',
                'model',
                'variantCombinations' => function ($q) {
                    $q->where('status', 1)
                        ->select(
                            'id',
                            'product_id',
                            'combination_key',
                            'variant_values',
                            'price',
                            'discount_price',
                            'additional_price',
                            'stock',
                            'sku',
                            'product_warehouse_id',
                            'product_warehouse_room_id',
                            'product_warehouse_room_cartoon_id'
                        );
                },
                'unitPricing' => function ($q) {
                    $q->where('status', 1)
                        ->select('id', 'product_id', 'unit_id', 'unit_title', 'unit_value', 'unit_label', 'price', 'discount_price', 'discount_percent');
                }
            ])
                ->where('status', 1)
                ->where(function ($q) use ($searchKeyword) {
                    if ($searchKeyword) {
                        $q->where('name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('id', $searchKeyword)
                            ->orWhere('slug', $searchKeyword)
                            ->orWhere('tags', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('sku', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('barcode', 'LIKE', '%' . $searchKeyword . '%');
                    }
                })
                ->when($brand_id > 0, function ($query) use ($brand_id) {
                    return $query->where('brand_id', $brand_id);
                })
                ->orderBy('id', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($product) {
                    // Format variant combinations for easy display
                    $variants = $product->variantCombinations->map(function ($variant) use ($product) {
                        // variant_values is already cast to array in the model
                        $variantValues = $variant->variant_values ?? [];

                        // Normalize variant_values: handle cases where it might be a JSON string or numeric array
                        if (is_string($variantValues)) {
                            $variantValues = json_decode($variantValues, true) ?? [];
                        } elseif (is_array($variantValues) && !empty($variantValues) && is_numeric(array_key_first($variantValues))) {
                            // If it's a numeric array, try to decode the first element if it's a JSON string
                            $firstValue = reset($variantValues);
                            if (is_string($firstValue) && (strpos($firstValue, '{') === 0 || strpos($firstValue, '[') === 0)) {
                                $decoded = json_decode($firstValue, true);
                                if (is_array($decoded)) {
                                    $variantValues = $decoded;
                                }
                            }
                        }

                        // Ensure it's an associative array
                        if (!is_array($variantValues) || empty($variantValues)) {
                            $variantValues = [];
                        }

                        $variantName = collect($variantValues)->map(function ($value, $key) {
                            return ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
                        })->implode(' | ');

                        $effectivePrice = $variant->price ?? ($product->price + ($variant->additional_price ?? 0));

                        // Calculate stock from product_stocks (closing stock) for this variant
                        $variantStockQuery = DB::table('product_stocks')
                            ->where('product_id', $product->id)
                            ->where('has_variant', 1)
                            ->where('status', 'active');

                        // Check if variant_combination_id column exists and use it if available
                        if (Schema::hasColumn('product_stocks', 'variant_combination_id')) {
                            $variantStockQuery->where(function ($query) use ($variant) {
                                $query->where('variant_combination_id', $variant->id)
                                    ->orWhere('variant_combination_key', $variant->combination_key);
                            });
                        } else {
                            $variantStockQuery->where('variant_combination_key', $variant->combination_key);
                        }

                        $variantStock = $variantStockQuery->sum('qty') ?? 0;

                        return [
                            'id' => $variant->id,
                            'name' => $variantName ?: 'Variant',
                            'price' => $effectivePrice,
                            'discount_price' => $variant->discount_price ?? $product->discount_price,
                            'stock' => (int) $variantStock,
                            'sku' => $variant->sku,
                            'combination_key' => $variant->combination_key,
                            'variant_values' => $variantValues,
                            'warehouse_id' => $variant->product_warehouse_id,
                            'warehouse_room_id' => $variant->product_warehouse_room_id,
                            'warehouse_cartoon_id' => $variant->product_warehouse_room_cartoon_id,
                            'previous_stock' => $variantStock,
                        ];
                    });

                    // Format unit pricing for easy display
                    $unitPrices = $product->unitPricing->map(function ($unit) {
                        return [
                            'id' => $unit->id,
                            'unit_label' => $unit->unit_label,
                            'price' => $unit->price,
                            'discount_price' => $unit->discount_price,
                            'discount_percent' => $unit->discount_percent,
                            'unit_title' => $unit->unit_title,
                            'unit_value' => $unit->unit_value
                        ];
                    });

                    // Calculate root product stock: sum of all product_stocks for this product
                    // product_stocks contains closing stock, so we just sum all active stocks
                    $productStock = DB::table('product_stocks')
                        ->where('product_id', $product->id)
                        ->where('status', 'active')
                        ->sum('qty') ?? 0;

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'discount_price' => $product->discount_price,
                        'discount_parcent' => $product->discount_parcent,
                        'stock' => (int) $productStock,
                        'slug' => $product->slug,
                        'sku' => $product->sku,
                        'barcode' => $product->barcode,
                        'has_variant' => $product->has_variant,
                        'has_unit_price' => $unitPrices->count() > 0,
                        'variants' => $variants,
                        'unit_prices' => $unitPrices,
                        'previous_stock' => (int) $productStock,
                        // Include category and other details
                        'category_name' => $product->category->name ?? null,
                        'unit_name' => $product->unit->name ?? null,
                        'brand_name' => $product->brand->name ?? null,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => ['data' => $products] // Match the expected structure
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function searchLiveProducts(Request $request)
    { //post method
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $brand_slug = $request->brand_slug;
            $category_id = $request->category_id;
            $keyword = $request->search_keyword;

            if ($brand_slug != '' || $keyword != '' || $category_id) {

                $query = DB::table('products')
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                    ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                    ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                    ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                    ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                    ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                    ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                    ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                    ->where('products.status', 1)
                    ->where('products.name', 'LIKE', '%' . $keyword . '%')
                    ->when($category_id, function ($query) use ($category_id) {
                        if ($category_id > 0)
                            return $query->where('products.category_id', $category_id);
                    });

                if ($request->brand_slug) {
                    $brandInfo = Brand::where('slug', $brand_slug)->first();
                    $brand_id = $brandInfo ? $brandInfo->id : 0;
                    $query->where('products.brand_id', $brand_id);
                }

                $query->orderBy('products.id', 'desc')->skip(0)->limit(5);
                $data = $query->get();

                return response()->json([
                    'success' => true,
                    'data' => ProductResource::collection($data)
                ], 200);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => array()
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function searchProductsGet(Request $request)
    {  //get method
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            // Support both 'query' and 'search_keyword' parameters
            $searchKeyword = $request->input('query') ?? $request->input('search_keyword') ?? '';

            $brandInfo = Brand::where('slug', $request->brand_slug)->first();
            $brand_id = $brandInfo ? $brandInfo->id : 0;

            // Use Eloquent with relationships (same as POST method)
            $products = \App\Models\Product::with([
                'category',
                'subcategory',
                'childCategory',
                'unit',
                'brand',
                'model',
                'variantCombinations' => function ($q) {
                    $q->where('status', 1)
                        ->select(
                            'id',
                            'product_id',
                            'combination_key',
                            'variant_values',
                            'price',
                            'discount_price',
                            'additional_price',
                            'stock',
                            'sku',
                            'image',
                            'product_warehouse_id',
                            'product_warehouse_room_id',
                            'product_warehouse_room_cartoon_id'
                        );
                },
                'unitPricing' => function ($q) {
                    $q->where('status', 1)
                        ->select('id', 'product_id', 'unit_id', 'unit_title', 'unit_value', 'unit_label', 'price', 'discount_price', 'discount_percent');
                }
            ])
                ->where('status', 1)
                ->where(function ($q) use ($searchKeyword) {
                    if ($searchKeyword) {
                        $q->where('name', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('id', $searchKeyword)
                            ->orWhere('slug', $searchKeyword)
                            ->orWhere('tags', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('sku', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhere('barcode', 'LIKE', '%' . $searchKeyword . '%')
                            ->orWhereHas('category', function ($query) use ($searchKeyword) {
                                $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                            })
                            ->orWhereHas('subcategory', function ($query) use ($searchKeyword) {
                                $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                            })
                            ->orWhereHas('brand', function ($query) use ($searchKeyword) {
                                $query->where('name', 'LIKE', '%' . $searchKeyword . '%');
                            });
                    }
                })
                ->when($brand_id > 0, function ($query) use ($brand_id) {
                    return $query->where('brand_id', $brand_id);
                })
                ->orderBy('id', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($products)->resource
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function termsAndCondition(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('terms_and_policies')->where('id', 1)->select('terms')->first();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function privacyPolicy(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('terms_and_policies')->where('id', 1)->select('privacy_policy')->first();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function shippingPolicy(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('terms_and_policies')->where('id', 1)->select('shipping_policy')->first();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function returnPolicy(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('terms_and_policies')->where('id', 1)->select('return_policy')->first();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function aboutUs(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = AboutUs::where('id', 1)->first();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllFaq(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('faqs')->orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function generalInfo(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('general_infos')->where('id', 1)->first();

            return response()->json([
                'success' => true,
                'data' => new GeneralInfoResource($data)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllSliders(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('banners')->where('type', 1)->where('status', 1)->orderBy('serial', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllBanners(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('banners')->where('type', 2)->where('status', 1)->orderBy('serial', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getPromotionalBanner(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('promotional_banners')->where('id', 1)->first();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function submitContactRequest(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            ContactRequest::insert([
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
                'phone' => isset($request->phone) ? $request->phone : NULL,
                'company_name' => isset($request->company_name) ? $request->company_name : NULL,
                'status' => 0,
                'created_at' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Request is Submitted"
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function orderCheckout(Request $request)
    {

        date_default_timezone_set("Asia/Dhaka");

        $orderId = Order::insertGetId([
            'order_no' => time() . rand(100, 999),
            'user_id' => auth()->user()->id,
            'order_date' => date("Y-m-d H:i:s"),
            'estimated_dd' => date('Y-m-d', strtotime("+7 day", strtotime(date("Y-m-d")))),
            'payment_method' => NULL,
            'trx_id' => time() . str::random(5),
            'order_status' => 0,
            'sub_total' => 0,
            'coupon_code' => NULL,
            'discount' => 0,
            'delivery_fee' => 0,
            'vat' => 0,
            'tax' => 0,
            'total' => 0,
            'order_note' => isset($request->special_note) ? $request->special_note : '',
            'delivery_method' => isset($request->delivery_method) ? $request->delivery_method : '',
            'slug' => str::random(5) . time(),
            'created_at' => Carbon::now()
        ]);

        OrderProgress::insert([
            'order_id' => $orderId,
            'order_status' => 0,
            'created_at' => Carbon::now()
        ]);

        $index = 0;
        $totalOrderAmount = 0;

        // for a reason=== dont change the code
        $productIdArray = explode(",", $request->product_id[0]);
        $qtyArray = explode(",", $request->qty[0]);
        $unitPriceArray = explode(",", $request->unit_price[0]);
        $unitIdArray = explode(",", $request->unit_id[0]);

        // variants added later
        $colorIdArray = explode(",", $request->color_id[0]);
        $regionIdArray = explode(",", $request->region_id[0]);
        $simIdArray = explode(",", $request->sim_id[0]);
        $sizeIdArray = explode(",", $request->size_id[0]);
        $storageIdArray = explode(",", $request->storage_id[0]);
        $warrentyIdArray = explode(",", $request->warrenty_id[0]);
        $deviceConditionIdArray = explode(",", $request->device_condition_id[0]);

        foreach ($productIdArray as $productId) {

            $quantity = (float) trim($qtyArray[$index]);
            $unitPrice = (float) trim($unitPriceArray[$index]);
            $pId = (int) trim($productId);
            $unitId = (float) trim($unitIdArray[$index]);

            // variants added later | chosing default variant while no variant is selected although product has variant
            $prodInfo = Product::where('id', $pId)->first();
            $colorId = (float) trim($colorIdArray[$index]);
            $regionId = (float) trim($regionIdArray[$index]);
            $simId = (float) trim($simIdArray[$index]);
            $storageId = (float) trim($storageIdArray[$index]);
            $warrentyId = (float) trim($warrentyIdArray[$index]);
            $deviceConditionId = (float) trim($deviceConditionIdArray[$index]);
            $sizeId = (float) trim($sizeIdArray[$index]);

            if ($prodInfo->has_variant == 1) {
                $variants = ProductVariant::where('product_id', $prodInfo->id)->where('stock', '>', 0)->count();
                if ($variants > 0) {
                    if (!$colorId && !$regionId && !$simId && !$storageId && !$warrentyId && !$deviceConditionId && !$sizeId) {
                        $defaultVariant = ProductVariant::where('product_id', $prodInfo->id)->where('stock', '>', 0)->orderBy('price', 'desc')->first();
                        $colorId = $defaultVariant->color_id;
                        $regionId = $defaultVariant->region_id;
                        $simId = $defaultVariant->sim_id;
                        $sizeId = $defaultVariant->size_id;
                        $storageId = $defaultVariant->storage_type_id;
                        $warrentyId = $defaultVariant->warrenty_id;
                        $deviceConditionId = $defaultVariant->device_condition_id;
                        $unitPrice = $defaultVariant->discounted_price > 0 ? $defaultVariant->discounted_price : $defaultVariant->price;
                    }
                }
            }

            Product::where('id', $pId)->decrement("stock", $quantity);
            OrderDetails::insert([
                'order_id' => $orderId,
                'product_id' => $pId,

                // VARIANT
                'color_id' => $colorId,
                'region_id' => $regionId,
                'sim_id' => $simId,
                'size_id' => $sizeId,
                'storage_id' => $storageId,
                'warrenty_id' => $warrentyId,
                'device_condition_id' => $deviceConditionId,

                'qty' => $quantity,
                'unit_id' => $unitId,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
                'created_at' => Carbon::now()
            ]);

            $totalOrderAmount = $totalOrderAmount + ($quantity * $unitPrice);
            $index++;
        }


        // calculating coupon discount
        $discount = 0;
        $promoInfo = PromoCode::where('code', $request->coupon_code)->where('status', 1)->where('effective_date', '<=', date("Y-m-d"))->where('expire_date', '>=', date("Y-m-d"))->first();
        if ($promoInfo) {
            $alreadyUsed = Order::where('user_id', auth()->user()->id)->where('coupon_code', $request->coupon_code)->count();
            if ($alreadyUsed == 0) {
                if ($promoInfo->type == 1) {
                    $discount = $promoInfo->value;
                } else {
                    $discount = ($totalOrderAmount * $promoInfo->value) / 100;
                }
            }
        }
        // calculating coupon discount

        Order::where('id', $orderId)->update([
            'sub_total' => $totalOrderAmount,
            'coupon_code' => $request->coupon_code,
            'discount' => $discount,
            'total' => $totalOrderAmount - $discount,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Order is Submitted",
            'data' => new OrderResource(Order::where('id', $orderId)->first())
        ], 200);
    }

    public function orderCheckoutAppOnly(Request $request)
    {

        date_default_timezone_set("Asia/Dhaka");

        $orderId = Order::insertGetId([
            'order_no' => time() . rand(100, 999),
            'user_id' => auth()->user()->id,
            'order_date' => date("Y-m-d H:i:s"),
            'estimated_dd' => date('Y-m-d', strtotime("+7 day", strtotime(date("Y-m-d")))),
            'payment_method' => NULL,
            'trx_id' => time() . str::random(5),
            'order_status' => 0,
            'sub_total' => 0,
            'coupon_code' => NULL,
            'discount' => 0,
            'delivery_fee' => 0,
            'vat' => 0,
            'tax' => 0,
            'total' => 0,
            'order_note' => isset($request->special_note) ? $request->special_note : '',
            'delivery_method' => isset($request->delivery_method) ? $request->delivery_method : '',
            'slug' => str::random(5) . time(),
            'created_at' => Carbon::now()
        ]);

        OrderProgress::insert([
            'order_id' => $orderId,
            'order_status' => 0,
            'created_at' => Carbon::now()
        ]);

        $index = 0;
        $totalOrderAmount = 0;

        foreach ($request->product_id as $productId) {
            Product::where('id', $productId)->decrement("stock", (int) $request->qty[$index]);
            OrderDetails::insert([
                'order_id' => $orderId,
                'product_id' => $productId,

                // VARIANT
                'color_id' => isset($request->color_id[$index]) ? $request->color_id[$index] : null,
                'region_id' => isset($request->region_id[$index]) ? $request->region_id[$index] : null,
                'sim_id' => isset($request->sim_id[$index]) ? $request->sim_id[$index] : null,
                'size_id' => isset($request->size_id[$index]) ? $request->size_id[$index] : null,
                'storage_id' => isset($request->storage_id[$index]) ? $request->storage_id[$index] : null,
                'warrenty_id' => isset($request->warrenty_id[$index]) ? $request->warrenty_id[$index] : null,
                'device_condition_id' => isset($request->device_condition_id[$index]) ? $request->device_condition_id[$index] : null,

                'qty' => $request->qty[$index],
                'unit_id' => $request->unit_id[$index],
                'unit_price' => $request->unit_price[$index],
                'total_price' => (int) $request->qty[$index] * (float) $request->unit_price[$index],
                'created_at' => Carbon::now()
            ]);

            $totalOrderAmount = $totalOrderAmount + ((int)$request->qty[$index] * (float)$request->unit_price[$index]);
            $index++;
        }


        // calculating coupon discount
        $discount = 0;
        $promoInfo = PromoCode::where('code', $request->coupon_code)->where('status', 1)->where('effective_date', '<=', date("Y-m-d"))->where('expire_date', '>=', date("Y-m-d"))->first();
        if ($promoInfo) {
            $alreadyUsed = Order::where('user_id', auth()->user()->id)->where('coupon_code', $request->coupon_code)->count();
            if ($alreadyUsed == 0) {
                if ($promoInfo->type == 1) {
                    $discount = $promoInfo->value;
                } else {
                    $discount = ($totalOrderAmount * $promoInfo->value) / 100;
                }
            }
        }
        // calculating coupon discount

        Order::where('id', $orderId)->update([
            'sub_total' => $totalOrderAmount,
            'coupon_code' => $request->coupon_code,
            'discount' => $discount,
            'total' => $totalOrderAmount - $discount,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Order is Submitted",
            'data' => new OrderResource(Order::where('id', $orderId)->first())
        ], 200);
    }


    public function guestOrderCheckout(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            date_default_timezone_set("Asia/Dhaka");

            $orderId = Order::insertGetId([
                'order_no' => time() . rand(100, 999),
                // 'user_id' => auth()->user()->id,
                'order_date' => date("Y-m-d H:i:s"),
                'estimated_dd' => date('Y-m-d', strtotime("+7 day", strtotime(date("Y-m-d")))),
                'payment_method' => NULL,
                'trx_id' => time() . str::random(5),
                'order_status' => 0,
                'sub_total' => 0,
                'coupon_code' => NULL,
                'discount' => 0,
                'delivery_fee' => 0,
                'vat' => 0,
                'tax' => 0,
                'total' => 0,
                'order_note' => isset($request->special_note) ? $request->special_note : '',
                'delivery_method' => isset($request->delivery_method) ? $request->delivery_method : '',
                'slug' => str::random(5) . time(),
                'created_at' => Carbon::now()
            ]);

            OrderProgress::insert([
                'order_id' => $orderId,
                'order_status' => 0,
                'created_at' => Carbon::now()
            ]);

            $index = 0;
            $totalOrderAmount = 0;

            // for a reason=== dont change the code
            $productIdArray = explode(",", $request->product_id[0]);
            $qtyArray = explode(",", $request->qty[0]);
            $unitPriceArray = explode(",", $request->unit_price[0]);
            $unitIdArray = explode(",", $request->unit_id[0]);

            // variants added later
            $colorIdArray = explode(",", $request->color_id[0]);
            $regionIdArray = explode(",", $request->region_id[0]);
            $simIdArray = explode(",", $request->sim_id[0]);
            $storageIdArray = explode(",", $request->storage_id[0]);
            $warrentyIdArray = explode(",", $request->warrenty_id[0]);
            $deviceConditionIdArray = explode(",", $request->device_condition_id[0]);

            foreach ($productIdArray as $productId) {

                $quantity = (float) trim($qtyArray[$index]);
                $unitPrice = (float) trim($unitPriceArray[$index]);
                $pId = (int) trim($productId);
                $unitId = (float) trim($unitIdArray[$index]);

                // variants added later | chosing default variant while no variant is selected although product has variant
                $prodInfo = Product::where('id', $pId)->first();
                $colorId = (float) trim($colorIdArray[$index]);
                $regionId = (float) trim($regionIdArray[$index]);
                $simId = (float) trim($simIdArray[$index]);
                $storageId = (float) trim($storageIdArray[$index]);
                $warrentyId = (float) trim($warrentyIdArray[$index]);
                $deviceConditionId = (float) trim($deviceConditionIdArray[$index]);
                if ($prodInfo->has_variant == 1) {
                    $variants = ProductVariant::where('product_id', $prodInfo->id)->where('stock', '>', 0)->count();
                    if ($variants > 0) {
                        if (!$colorId && !$regionId && !$simId && !$storageId && !$warrentyId && !$deviceConditionId) {
                            $defaultVariant = ProductVariant::where('product_id', $prodInfo->id)->where('stock', '>', 0)->orderBy('price', 'desc')->first();
                            $colorId = $defaultVariant->color_id;
                            $regionId = $defaultVariant->region_id;
                            $simId = $defaultVariant->sim_id;
                            $storageId = $defaultVariant->storage_type_id;
                            $warrentyId = $defaultVariant->warrenty_id;
                            $deviceConditionId = $defaultVariant->device_condition_id;
                            $unitPrice = $defaultVariant->discounted_price > 0 ? $defaultVariant->discounted_price : $defaultVariant->price;
                        }
                    }
                }

                Product::where('id', $pId)->decrement("stock", $quantity);
                OrderDetails::insert([
                    'order_id' => $orderId,
                    'product_id' => $pId,

                    // VARIANT
                    'color_id' => $colorId,
                    'region_id' => $regionId,
                    'sim_id' => $simId,
                    'storage_id' => $storageId,
                    'warrenty_id' => $warrentyId,
                    'device_condition_id' => $deviceConditionId,

                    'qty' => $quantity,
                    'unit_id' => $unitId,
                    'unit_price' => $unitPrice,
                    'total_price' => $quantity * $unitPrice,
                    'created_at' => Carbon::now()
                ]);

                $totalOrderAmount = $totalOrderAmount + ($quantity * $unitPrice);
                $index++;
            }


            // calculating coupon discount
            $discount = 0;
            $promoInfo = PromoCode::where('code', $request->coupon_code)->where('status', 1)->where('effective_date', '<=', date("Y-m-d"))->where('expire_date', '>=', date("Y-m-d"))->first();
            if ($promoInfo) {
                if ($promoInfo->type == 1) {
                    $discount = $promoInfo->value;
                } else {
                    $discount = ($totalOrderAmount * $promoInfo->value) / 100;
                }
            }
            // calculating coupon discount

            Order::where('id', $orderId)->update([
                'sub_total' => $totalOrderAmount,
                'coupon_code' => $request->coupon_code,
                'discount' => $discount,
                'total' => $totalOrderAmount - $discount,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Order is Submitted",
                'data' => new OrderResource(Order::where('id', $orderId)->first())
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }


    public function shippingBillingInfo(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            ShippingInfo::where('order_id', $request->order_id)->delete();
            ShippingInfo::insert([
                'order_id' => $request->order_id,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'gender' => $request->gender,
                'address' => $request->address,
                'thana' => $request->thana,
                'post_code' => $request->post_code,
                'city' => $request->city,
                'country' => $request->country,
                'created_at' => Carbon::now()
            ]);

            BillingAddress::where('order_id', $request->order_id)->delete();
            BillingAddress::insert([
                'order_id' => $request->order_id,
                'address' => $request->billing_address,
                'post_code' => $request->billing_post_code,
                'thana' => $request->billing_thana,
                'city' => $request->billing_city,
                'country' => $request->billing_country,
                'created_at' => Carbon::now()
            ]);

            $deliveryCharge = 100;
            $districtWiseDeliveryCharge = DB::table('districts')->select('delivery_charge')->where('name', strtolower(trim($request->city)))->first();
            if ($districtWiseDeliveryCharge) {
                $deliveryCharge = $districtWiseDeliveryCharge->delivery_charge;
            }

            $orderInfo = Order::where('id', $request->order_id)->first();
            $orderInfo->delivery_fee = $orderInfo->delivery_method == 2 ? 0 : $deliveryCharge;
            $orderInfo->total = $orderInfo->total + ($orderInfo->delivery_method == 2 ? 0 : $deliveryCharge);
            $orderInfo->complete_order = 1;
            $orderInfo->save();


            if ($request->email) {
                SubscribedUsers::insert([
                    'email' => $request->email,
                    'created_at' => Carbon::now()
                ]);
            }


            // sending order email
            try {

                $emailConfig = EmailConfigure::where('status', 1)->orderBy('id', 'desc')->first();
                $userEmail = $request->email;

                if ($emailConfig && $userEmail) {
                    $decryption = "";
                    if ($emailConfig) {

                        $ciphering = "AES-128-CTR";
                        $options = 0;
                        $decryption_iv = '1234567891011121';
                        $decryption_key = "GenericCommerceV1";
                        $decryption = openssl_decrypt($emailConfig->password, $ciphering, $decryption_key, $options, $decryption_iv);

                        config([
                            'mail.mailers.smtp.host' => $emailConfig->host,
                            'mail.mailers.smtp.port' => $emailConfig->port,
                            'mail.mailers.smtp.username' => $emailConfig->email,
                            'mail.mailers.smtp.password' => $decryption != "" ? $decryption : '',
                            'mail.mailers.smtp.encryption' => $emailConfig ? ($emailConfig->encryption == 1 ? 'tls' : ($emailConfig->encryption == 2 ? 'ssl' : '')) : '',
                        ]);

                        Mail::to(trim($userEmail))->send(new OrderPlacedEmail($orderInfo));
                    }
                }
            } catch (\Exception $e) {
                // write code for handling error from here
            }
            // sending order email done


            return response()->json([
                'success' => true,
                'message' => "Order Info Updated",
                'data' => new OrderResource(Order::where('id', $request->order_id)->first())
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function orderPreview(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => "Order Preview",
            'data' => new OrderResource(Order::where('id', $request->order_id)->first())
        ], 200);
    }

    public function getMyOrders(Request $request)
    {
        $data = DB::table('orders')->where('user_id', auth()->user()->id)->where('complete_order', 1)->orderBy('id', 'desc')->paginate(100);
        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($data)->resource
        ], 200);
    }

    public function orderDetails($slug)
    {
        $data = DB::table('orders')->where('user_id', auth()->user()->id)->where('slug', $slug)->first();
        return response()->json([
            'success' => true,
            'data' => new OrderResource($data)
        ], 200);
    }

    public function orderProgress(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $order = DB::table('orders')->where('order_no', $request->order_no)->first();
            if ($order) {
                $data = DB::table('order_progress')->where('order_id', $order->id)->orderBy('id', 'asc')->get();
                return response()->json([
                    'success' => true,
                    'date' => OrderProgressResource::collection($data)
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "No Order Found"
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function orderCashOnDelivery(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $orderId = $request->order_id;
            $orderInfo = Order::where('id', $orderId)->first();
            $orderInfo->bank_tran_id = "Not Available (COD)";
            $orderInfo->payment_method = 1;
            $orderInfo->payment_status = 1; //success
            $orderInfo->save();

            OrderPayment::insert([
                'order_id' => $orderInfo->id,
                'payment_through' => "COD",
                'tran_id' => $orderInfo->tran_id,
                'val_id' => NULL,
                'amount' => $orderInfo->total,
                'card_type' => NULL,
                'store_amount' => $orderInfo->total,
                'card_no' => NULL,
                'status' => "VALID",
                'tran_date' => date("Y-m-d H:i:s"),
                'currency' => "BDT",
                'card_issuer' => NULL,
                'card_brand' => NULL,
                'card_sub_brand' => NULL,
                'card_issuer_country' => NULL,
                'created_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Order Payment is Successfull",
                'data' => new OrderResource(Order::where('id', $orderInfo->id)->first())
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function submitProductReview(Request $request)
    {

        $product_id = $request->product_id;
        $userId = auth()->user()->id;
        $rating = $request->rating;
        $review = $request->review;

        $reviewValidity = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('orders.user_id', $userId)
            ->where('order_details.product_id', $product_id)
            ->first();

        if ($reviewValidity) {
            $id = ProductReview::insertGetId([
                'product_id' => $product_id,
                'user_id' => $userId,
                'rating' => $rating,
                'review' => $review,
                'slug' => time() . str::random(5),
                'status' => 0,
                'created_at' => Carbon::now()
            ]);
            $data = ProductReview::where('id', $id)->first();

            return response()->json([
                'success' => true,
                'message' => "Prouduct Review Submitted",
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "You are not eligible to submit Review for this Product",
                'data' => NULL
            ], 200);
        }
    }

    public function submitProductQuestion(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            ProductQuestionAnswer::insert([
                'product_id' => $request->product_id,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'question' => $request->question,
                'slug' => time() . str::random(5),
                'created_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Question Submitted Successfully"
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllTestimonials(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('testimonials')->orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }


    public function subscriptionForUpdates(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = SubscribedUsers::where('email', $request->email)->first();

            if ($data) {
                return response()->json([
                    'success' => true,
                    'message' => "Already Subscribed"
                ], 200);
            } else {
                SubscribedUsers::insert([
                    'email' => $request->email,
                    'created_at' => Carbon::now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Successfully Subscribed"
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function featuredBrandWiseProducts(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $featuredBrands = Brand::where('featured', 1)->orderBy('serial', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => BrandResource::collection($featuredBrands)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getPaymentGateways(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $gateways = PaymentGateway::where('status', 1)->get();
            return response()->json([
                'success' => true,
                'data' => $gateways
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function bestSellingProduct(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            // SELECT products.id, count(order_details.product_id) as prod_count FROM products
            // LEFT JOIN order_details ON products.id = order_details.product_id
            // GROUP BY products.id ORDER BY prod_count DESC;


            $data = DB::table('products')
                ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty', DB::raw("count(order_details.product_id) as order_count"))
                ->where('products.status', 1)
                ->orderBy('order_count', 'desc')
                ->groupBy('products.id') //group is must needed, otherwise only one product will show
                ->skip(0)
                ->limit(12)
                ->get();

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($data)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }


    public function productsForYou(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $data = DB::table('products')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                ->where('products.status', 1)
                ->inRandomOrder()
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => ProductResource::collection($data)->resource
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function productsForYouLoggedIn(Request $request)
    {

        // SELECT products.id as product_id, name as product_name, COUNT(order_details.id) as order_count from products
        // LEFT JOIN order_details ON order_details.product_id = products.id

        // WHERE products.id NOT IN (SELECT product_id FROM order_details JOIN orders ON orders.id = order_details.order_id WHERE orders.user_id = 20)
        // AND
        // (
        //     products.category_id IN (SELECT category_id FROM products INNER JOIN order_details ON order_details.product_id = products.id INNER JOIN orders on orders.id = order_details.order_id WHERE orders.user_id = 20)
        //     OR
        //     products.subcategory_id IN (SELECT subcategory_id FROM products INNER JOIN order_details ON order_details.product_id = products.id INNER JOIN orders on orders.id = order_details.order_id WHERE orders.user_id = 20)
        //     OR
        //     products.childcategory_id IN (SELECT childcategory_id FROM products INNER JOIN order_details ON order_details.product_id = products.id INNER JOIN orders on orders.id = order_details.order_id WHERE orders.user_id = 20)
        //     OR
        //     products.id IN (SELECT product_id FROM wish_lists INNER JOIN users ON users.id = wish_lists.user_id WHERE wish_lists.user_id = 20)
        // )

        // GROUP BY products.id ORDER BY RAND( );


        // calculating already ordered products start
        $alreadyOrderedProducts = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.id')
            ->where('orders.user_id', auth()->user()->id)
            ->select('order_details.product_id')
            ->get();

        $alreadyOrdered = array();
        $index = 0;
        foreach ($alreadyOrderedProducts as $item) {
            $alreadyOrdered[$index] = $item->product_id;
            $index++;
        }
        // calculating already ordered products end


        // calculating already ordered products category start
        $similarOrderedProducts = DB::table('products')
            ->join('order_details', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_details.id')
            ->where('orders.user_id', auth()->user()->id)
            ->select('products.category_id', 'products.subcategory_id', 'products.childcategory_id')
            ->get();

        $similarCategories = array();
        $similarSubCategories = array();
        $similarChildCategories = array();

        $index = 0;
        foreach ($similarOrderedProducts as $item) {
            $similarCategories[$index] = $item->category_id;
            $similarSubCategories[$index] = $item->subcategory_id;
            $similarChildCategories[$index] = $item->childcategory_id;
            $index++;
        }

        // calculating already ordered products category end


        $data = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
            ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
            ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
            ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
            ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
            ->where('products.status', 1)

            // custom lagic for products you may like
            ->whereNotIn('products.id', $alreadyOrdered)
            ->orWhereIn('products.category_id', $similarCategories)
            ->orWhereIn('products.subcategory_id', $similarSubCategories)
            ->orWhereIn('products.childcategory_id', $similarChildCategories)

            ->inRandomOrder()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($data)->resource
        ], 200);
    }

    public function getdeliveryCharge(Request $request, $disctrict)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $deliveryCharge = 100;
            $districtWiseDeliveryCharge = DB::table('districts')->select('delivery_charge')->where('name', strtolower(trim($disctrict)))->first();
            if ($districtWiseDeliveryCharge) {
                $deliveryCharge = $districtWiseDeliveryCharge->delivery_charge;
            }

            return response()->json([
                'success' => true,
                'data' => $deliveryCharge
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllDistricts(Request $request)
    {
        $districts = DB::table('districts')
            ->leftJoin('divisions', 'districts.division_id', '=', 'divisions.id')
            ->select(
                'districts.id as district_id',
                'divisions.id as division_id',
                'districts.name as district_name',
                'divisions.name as division_name'
            )
            ->orderBy('districts.name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $districts
        ]);
    }

    public function getDistrictWiseThana(Request $request)
    {
        if ($request->header('Authorization') == ApiController::AUTHORIZATION_TOKEN) {

            $thana = DB::table('upazilas')->where('district_id', $request->district_id)->orderBy('name', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $thana
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getDistrictsWithThana(Request $request)
    {
        $thana = DB::table('districts')
            ->leftJoin('upazilas', 'upazilas.district_id', '=', 'districts.id')
            ->select('districts.id as district_id', 'districts.name as district_name', 'districts.bn_name as district_bn_name', 'districts.delivery_charge', 'upazilas.id as upazila_id', 'upazilas.name as upazila_name', 'upazilas.bn_name as upazila_bn_name')
            ->orderBy('districts.name', 'asc')
            ->orderBy('upazilas.name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $thana
        ]);
    }
}
