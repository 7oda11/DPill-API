<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\api\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    //
    public function index()
    {
        $blogs = DB::select('select * from blogs');
        if ($blogs) {
            return response()->json([
                'message' => 'Pill data retrieved successfully',
                'blogs' => $blogs,
            ], 200);
        } else {
            return response()->json(['errorMessage' => 'blog not found'], 404);
        }
    }
    public function search(Request $request)
    {
        $title = $request->input('title');
        if (!$title) {
            return response()->json(['error' => 'title name not provided'], 400);
        }
        $titleWithWildcards = '%' . $title . '%';
        $blogs = DB::select('select * from blogs where title like ?', [$titleWithWildcards]);
        if ($blogs) {
            return response()->json([
                'message' => 'Pill data retrieved successfully',
                'blogs' => $blogs,
            ], 200);
        } else {
            return response()->json(['errorMessage' => 'blog not found'], 404);
        }
    }
    public function show(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return response()->json(['error' => 'id not provided'], 400);
        }
        $blog = Blog::where('id', $id)->first();
        if ($blog) {
            return response()->json([
                'message' => 'Pill data retrieved successfully',
                'blogs' => $blog,
            ], 200);
        } else {
            return response()->json(['errorMessage' => 'blog not found'], 404);
        }
    }
}
