<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index(Request $request) {
        $posts = Post::paginate(5);

        return response()->json([
            'status' => true,
            'message' => 'Posts fetched successfully',
            "data" => $posts
        ]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                "data" => $validator->errors()->all()
            ], 422);
        } 

        $post = Post::create([
            "title" => $request->title,
            "description" => $request->description,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Post created successfully",
            "data" => $post
        ]);
    }

    public function show(Request $request, $id) {
        $post = Post::find($id);

        return response()->json([
            "status" => true,
            "message" => "Post fetched successfully",
            "data" => $post
        ]);
    }

    public function update(Request $request, $id) 
    {
        $validator = Validator::make($request->all(), [
            "title" => "required|string|max:255",
            "description" => "required|string",
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                "data" => $validator->errors()->all()
            ], 422);
        }
        
        $post = Post::find($id);
    
        $post->title = $request->title;
        $post->description = $request->description;
        $post->save();
    
        return response()->json([
            "status" => true,
            "message" => "Post updated successfully",
            "data" => $post
        ]);
    }

    public function destroy(Request $request, $id) {
        $post = Post::find($id);
        $post->delete();

        return response()->json([
            "status" => true,
            "message" => "Post deleted successfully",
            "data" => null
        ]);
    }
    
}
