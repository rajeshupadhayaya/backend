<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Notifications\EmailVerify;
use App\User;
use App\Models\VerifyUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    public $successStatus = 200;
    public $unAuthorizeStatus = 401;
    public $badDataStatus = 402;
    public $notFound = 404;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function login(Request $request)
    {

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $token =  $user->createToken('MyApp')->accessToken;
            return response()->json(['access_token' => $token, "user" => $user], $this->successStatus);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
    public function sociallogin(Request $request)
    {
        $googleToken = $request->googleToken;
        // Log::debug($googleToken);
        try {

            $client = new \GuzzleHttp\Client(['headers' => ['Authorization' => 'Bearer ' . $googleToken]]);
            $client_req = $client->get('https://www.googleapis.com/oauth2/v3/userinfo?alt=json');
            $response = json_decode($client_req->getBody()->getContents());
            Log::debug(['resp', $response]);
            if ($response) {
                $user = User::where(['email' => $response->email])->first();
                Log::debug(["user", $user]);
                if (!$user) {
                    $input = [
                        'email' => $response->email,
                        'image' => $response->picture,
                        'name' => $response->name,
                        'isemailverified' => true,
                        'issocial' => true
                    ];
                    $user = User::create($input);
                }

                $access_token =  $user->createToken('MyApp')->accessToken;
                return response()->json(['access_token' => $access_token], $this->successStatus);
            } else {
                return response()->json(['error' => 'Unauthorised'], 401);
            }
        } catch (Exception $e) {
            Log::info($e);
            return response()->json(['error' => 'Unauthorised'], 401);
        }
        return 200;
    }
    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function register(Request $request)
    {
        /*$validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
        */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $access_token =  $user->createToken('MyApp')->accessToken;
        // $success['name'] =  $user->name;
        return response()->json(['access_token' => $access_token], $this->successStatus);
    }
    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */

    public function logout()
    {
        $user = Auth::user();

        $user->token()->revoke();

        return response()->json($this->successStatus);
    }

    public function validateUser()
    {
        $user = Auth::user();
        return response()->json(['user' => $user], $this->successStatus);
    }

    public function updateDetails(Request $request)
    {
        $user = Auth::user();
        $params = $request->except(['email', 'id', 'isadmin', 'isemailverified', 'isnumberverified', 'isSocial']);

        $user->update($params);

        return response()->json(['success' => "Profile Update Successfully", 'user' => $user], $this->successStatus);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'min:4|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:4'
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json(['error' => "Your current password does not matches with the password you provided. Please try again"], $this->badDataStatus);
        }

        if (strcmp($request->get('current_password'), $request->get('password')) == 0) {
            return response()->json(['error' => "New Password cannot be same as your current password. Please choose a different password"], $this->badDataStatus);
        }

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json(["success" => "Password changed successfully !"]);
    }

    public function generateVerifyEmail(Request $request)
    {
        $user = Auth::user();

        VerifyUser::where(['user_id' => $user->id])->delete();

        VerifyUser::create([
            'user_id' => $user->id,
            'token' => sha1(time())
        ]);

        $user->notify(new EmailVerify($user));
        return response()->json(["success" => "We have sent a verification link to your registered Email Id."]);
    }
    public function verifyEmail(Request $request)
    {

        $user_id = $request->id;
        $token = $request->token;
        $isToken = VerifyUser::where(['user_id' => $user_id, 'token' => $token, 'active' => true])->first();

        if ($isToken) {
            $isToken->update(['active' => false]);
            $user = User::find($user_id);
            $user->isemailverified  = true;
            $user->save();
            return response()->json(["success" => "Thank you for verifying your Email id, Please login <a href=/login>here</a>"], $this->successStatus);
        } else {
            return response()->json(["error" => "Link Expired, Please login <a href=/login>here</a>"], $this->notFound);
        }
    }

    public function myPost(Request $request)
    {
        $user = Auth::user();
        $post = Post::where('user_id', '=', $user->id)
            ->orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $post], $this->successStatus);
    }
}
