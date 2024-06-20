<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\api\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    //
    // public function index()
    // {
    //     $blogs = DB::select('select * from blogs');

    //     if ($blogs) {
    //         // Loop through each blog and convert the image path to a full asset URL
    //         foreach ($blogs as &$blog) {
    //             $blog->thumbnail = asset($blog->thumbnail);
    //         }

    //         return response()->json([
    //             'message' => 'Articles retrieved successfully',
    //             'blogs' => $blogs,
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'errorMessage' => 'No Articles exist',
    //             'statusCode' => 404,
    //         ], 404);
    //     }
    // }

    public function index()
    {
        $blogs = DB::select('select * from blogs');

        if ($blogs) {
            // Create an array to hold the customized blog objects
            $customBlogs = [];

            // Loop through each blog and construct the customized blog object
            foreach ($blogs as $blog) {
                $customBlogs[] = [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'photo' => asset($blog->thumbnail),
                    'content' => $blog->content,
                    'reference' => $blog->title,
                    'created_at' => $blog->created_at,
                    'updated_at' => $blog->updated_at,
                ];
            }

            return response()->json([
                'message' => 'Articles retrieved successfully',
                'blogs' => $customBlogs,
            ], 200);
        } else {
            return response()->json([
                'errorMessage' => 'No Articles exist',
                'statusCode' => 404,
            ], 404);
        }
    }




    public function search(Request $request)
    {
        $title = $request->input('title');
        if (!$title) {
            return response()->json([
                'errorMessage' => 'title name not provided',
                "statusCode" => 400,
            ], 400);
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
            return response()->json([
                'errorMessage' => 'id not provided',
                "statusCode" => 400
            ], 400);
        }
        $blog = Blog::where('id', $id)->first();
        if ($blog) {
            return response()->json([
                'message' => 'Pill data retrieved successfully',
                'blogs' => $blog,
            ], 200);
        } else {
            return response()->json([
                'errorMessage' => 'blog not found',
                "statusCode" => 400,
            ], 404);
        }
    }
}
