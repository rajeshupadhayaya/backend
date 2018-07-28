<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use DB;

class PostController extends Controller 
{
public $successStatus = 200;

    public function getPost(Request $request){ 
        
        $query = $request->get('query');

        if($query == ''){
            
            $post = DB::table('post')
                    ->where('approved','=',true,'and','show','=',true)
                    ->orderBy('created_at', 'desc')
                    ->get(['id','title','description','created_at']);    
        } else{
            
            $post = DB::table('post')
                    ->whereRaw("`title` like '%".$query."%' and `approved` = 1 and `show` = 1")
                    ->orderBy('created_at', 'desc')
                    ->get(['id','title','description','created_at']);
        }
        
        
        return response()->json(['data'=> $post], $this-> successStatus); 
         
    }
    
    public function viewPost(Request $request){ 
        
        $id = $request->get('query');

        $post = DB::table('post')
                ->where('id',$id)
                ->orderBy('created_at', 'desc')
                ->get(['id','title','description','created_at']);    
        
        return response()->json(['data'=> $post], $this-> successStatus); 
         
    }

    public function getPostdetails(Request $request) 
    { 
        $user = Auth::user(); 
        $id = $user->id;
        $postid = $request->get('post_id');

        // $userdetail = DB::table('post')->where('id',$id)->get(['email']);
        $data = array('user_id' => $id,
                'post_id' => $postid,
                'created_at' => now()
            );

        DB::table('view_request')->insert($data);
        // return response()->json(['success' => $userdetail], $this-> successStatus); 
        return response()->json($this-> successStatus); 
    } 

    public function createPost(Request $request)
    {
    	$user = Auth::user(); 
    	$email = $user['email'];

    	$validator = Validator::make($request->all(), [ 
            
            'title' => 'required', 
            'description' => 'required', 
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $data = array('email' => $email,
        		'title' => $request->get('title'),
        		'description' => $request->get('description'),
        		'created_at' => now()
        	);

        DB::table('post')->insert($data);

        $success = 'Your Post is added and sent for approval. It will be available for public after approval.';

        return response()->json(['success'=>$success], $this-> successStatus); 
    }
}