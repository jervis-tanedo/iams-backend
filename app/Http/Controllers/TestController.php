<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    //
    public function postTest(Request $request){
        $user = $request->all();
        $hasRole = Auth::hasRole('uplb-iams', 'admin');
        return response()->json([
            'test' => Auth::user()->token,
            'token' => Auth::token(),
            // 'hasRole' => $hasRole,
            'id' => Auth::user()->token->sub,
            // 'idtwo' => Auth::id()
        ], 200);
    }

    public function getTest(){
        return response()->json([
            'test' => 'This is get test'
        ], 200);
    }
}
