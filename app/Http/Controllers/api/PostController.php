<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Notifications\ViewRequest;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PostController extends Controller
{
    public $successStatus = 200;
    public $notFound = 404;


    public function getPost(Request $request)
    {

        $query = $request->get('query');

        if ($query == '') {

            $post = Post::where('approved', '=', true, 'and', 'show', '=', true)
                ->orderBy('created_at', 'desc')
                ->get(['id', 'title', 'description', 'slug', 'created_at']);
        } else {

            $post = Post::whereRaw("`title` like '%" . $query . "%' and `approved` = 1 and `show` = 1")
                ->orderBy('created_at', 'desc')
                ->get(['id', 'title', 'description', 'created_at']);
        }


        return response()->json(['data' => $post], $this->successStatus);
    }

    public function viewPost($slug)
    {

        // $slug = $request->get('slug');
        Log::debug($slug);
        $post = Post::where('slug', $slug)
            ->orderBy('created_at', 'desc')
            ->first(['id', 'title', 'description', 'slug', 'created_at']);

        if ($post) {

            return response()->json(['data' => $post], $this->successStatus);
        }
        return response()->json(['error' => "Data Not Found"], $this->notFound);
    }

    public function getPostdetails(Request $request)
    {
        $user = Auth::user();
        $id = $user->id;
        $postid = $request->get('post_id');

        $post = Post::find($postid);

        $data = array(
            'user_id' => $id,
            'post_id' => $postid,
            'created_at' => now()
        );

        DB::table('view_requests')->insert($data);

        $user->notify(new ViewRequest($user, $post));

        // return response()->json(['success' => $userdetail], $this-> successStatus); 
        return response()->json($this->successStatus);
    }

    public function createPost(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [

            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $data = array(
            'user_id' => $user->id,
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'created_at' => now()
        );

        Post::create($data);

        $success = 'Your Post is added and sent for approval. It will be available for public after approval.';

        return response()->json(['success' => $success], $this->successStatus);
    }
}
