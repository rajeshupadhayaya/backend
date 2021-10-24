<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ContactDetails as MailContactDetails;
use App\Mail\PostApproved as MailPostApproved;
use App\Models\Post;
use App\Models\ViewRequest as ViewRequestModel;
use App\User;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    //
    public $successStatus = 200;

    public function login(Request $request)
    {

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            if ($user->isadmin) {
                $token =  $user->createToken('MyApp')->accessToken;
                return response()->json(['admin_Token' => $token], $this->successStatus);
            } else {
                return response()->json(['error' => 'Unauthorised'], 401);
            }
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        $user->token()->revoke();

        $request->session()->flush();

        $request->session()->regenerate();

        return response()->json($this->successStatus);
    }

    public function getPost(Request $request)
    {

        $user = Auth::user();

        if ($user->isadmin) {
            $post = Post::with('user')->where(['approved' => false, 'show' => false])
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json(['data' => $post], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }


    public function getViewRequest(Request $request)
    {
        $user = Auth::user();
        if ($user->isadmin) {
            $post = ViewRequestModel::with('user', 'post')->where(['isapproved' => Null])->get();
            // $post = DB::table('view_request')
            //     ->join('users', 'users.id', '=', 'view_request.user_id')
            //     ->where('view_request.isapproved', '=', Null)
            //     ->orderBy('view_request.created_at', 'desc')
            //     ->select(['view_request.id', 'users.email', 'users.name', 'view_request.post_id'])
            //     ->get();

            return response()->json(['data' => $post], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function updatePost(Request $request)
    {

        $user = Auth::user();
        if ($user->isadmin) {
            $id = $request->get('post_id');
            $status = $request->get('status');

            $post = Post::with('user')->where('id', $id)->first();
            Log::debug($post);
            $post->update(['show' => $status, 'approved' => $status,  'updated_at' => now()]);
            // send mail to user regarding publishing of post.
            if ($status) {
                Mail::to($post->user->email)->send(new MailPostApproved($post->user, $post));
            }


            return response()->json($this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }


    public function approveviewrequest(Request $request)
    {
        $user = Auth::user();

        if ($user->isadmin) {
            //upate view_request and send mail to user with post detail
            $id = $request->get('req_id');
            $approved = $request->get('status');

            $viewRequest = ViewRequestModel::with('post.user', 'user')->find($id);

            $viewRequest->update(['isapproved' => $approved,  'updated_at' => now()]);
            Log::debug($viewRequest);
            // $post = Post::find($viewRequest->post->user_id)
            if ($approved) {
                // $user->notify(new ContactDetails($user, $viewRequest));
                Mail::to($viewRequest->user->email)->send(new MailContactDetails($user, $viewRequest));
            }
            // $post = DB::table('view_request')
            //     ->where('id', $id)
            //     ->update(['isapproved' => $approved, 'updated_at' => now()]);

            return response()->json($this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
}
