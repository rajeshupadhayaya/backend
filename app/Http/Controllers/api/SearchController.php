<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class SearchController extends Controller
{
    public $successStatus = 200;

    public function findJob(Request $request)
    {
        $keyword = $request->get('query');
        $post = DB::table('post')
            ->whereRaw("`title` like '%" . $keyword . "%' and `approved` = 1 and `show` = 1")
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'description', 'created_at']);

        return response()->json(['data' => $post], $this->successStatus);
    }
}
