<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Http\Resources\BlogResource;

class BlogController extends Controller
{
    const AUTHORIZATION_TOKEN = 'GenericCommerceV1-SBW7583837NUDD82';
    public function getAllBlogCategories(Request $request){
        if ($request->header('Authorization') == BlogController::AUTHORIZATION_TOKEN) {

            $caetegories = BlogCategory::where('status', 1)->orderBy('serial', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $caetegories
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getAllBlogs(Request $request){
        if ($request->header('Authorization') == BlogController::AUTHORIZATION_TOKEN) {

            $data = DB::table('blogs')
                        ->leftJoin('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                        ->select('blogs.*', 'blog_categories.name as blog_category_name', 'blog_categories.slug as category_slug')
                        ->where('blogs.status', 1)
                        ->orderBy('blogs.id', 'desc')
                        ->paginate(6);

            return response()->json([
                'success' => true,
                'data' => BlogResource::collection($data)->resource
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'message' => "Authorization Token is Invalid"
            ], 422);
        }
    }

    public function getCategoryWiseBlogs(Request $request){
        if ($request->header('Authorization') == BlogController::AUTHORIZATION_TOKEN) {

            $data = DB::table('blogs')
                        ->leftJoin('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                        ->select('blogs.*', 'blog_categories.name as blog_category_name', 'blog_categories.slug as category_slug')
                        ->where('blogs.status', 1)
                        ->where('blog_categories.slug', $request->category_slug)
                        ->orderBy('blogs.id', 'desc')
                        ->paginate(15);

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

    public function blogDetails(Request $request, $slug){
        if ($request->header('Authorization') == BlogController::AUTHORIZATION_TOKEN) {

            $data = DB::table('blogs')
                        ->leftJoin('blog_categories', 'blogs.category_id', '=', 'blog_categories.id')
                        ->select('blogs.*', 'blog_categories.name as blog_category_name', 'blog_categories.slug as category_slug')
                        ->where('blogs.slug', $slug)
                        ->first();

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
}
