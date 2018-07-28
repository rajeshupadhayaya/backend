<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use DB;

class AdminController extends Controller
{
    //
    public $successStatus = 200;

    public function login(Request $request){ 

        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            if($user->isadmin){
            	$token=  $user->createToken('MyApp')-> accessToken; 
            	return response()->json(['admin_Token' => $token], $this-> successStatus); 	
            }
            else{ 
            	return response()->json(['error'=>'Unauthorised'], 401); 
        	}
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    public function logout() 
    { 
        $user = Auth::user();
        
        $user->token()->revoke();

        $request->session()->flush();

        $request->session()->regenerate();

        return response()->json($this-> successStatus);
        
    }

    public function getPost(Request $request){ 
        
        $user = Auth::user(); 

        if($user->isadmin){
			$post = DB::table('post')
       	        ->where('approved','=',false,'and','show','=',false)
                ->orderBy('created_at', 'desc')
                ->get(['id','title','email','description','created_at']);    
            return response()->json(['data'=> $post], $this-> successStatus); 
        }
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
    

    public function getViewRequest(Request $request) 
    { 
        $user = Auth::user(); 
        if($user->isadmin){
			$post = DB::table('view_request')
				->join('users', 'users.id', '=', 'view_request.user_id')
       	        ->where('view_request.isapproved','=',Null)
                ->orderBy('view_request.created_at', 'desc')
                ->select(['view_request.id','users.email','users.name', 'view_request.post_id' ])
                ->get();

            return response()->json(['data'=> $post], $this-> successStatus); 
        }
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        }
    } 

    public function approvePost(Request $request) 
    { 

        $user = Auth::user(); 
        if($user->isadmin){
            $id = $request->get('post_id');
            $approved = $request->get('status');

			$post = DB::table('post')
       	        ->where('id',$id)
                ->update(['show' => $approved, 'approved' => $approved,  'updated_at' => now() ]);
// send mail to user regarding publishing of post.

            return response()->json($this-> successStatus); 
        }
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        }
    }

    public function approveviewrequest(Request $request) 
    { 
    	

        $user = Auth::user(); 

        if($user->isadmin){
//upate view_request and send mail to user with post detail
            $id = $request->get('req_id');
            $approved = $request->get('status');
            
    		$post = DB::table('view_request')
   	        		->where('id',$id)
            		->update(['isapproved' => $approved, 'updated_at' => now()]);


        	return response()->json($this-> successStatus); 	

			
        }
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        }
    }
}
