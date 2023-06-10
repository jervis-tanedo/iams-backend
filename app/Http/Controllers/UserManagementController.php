<?php

namespace App\Http\Controllers;

use App\Models\UserManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Auth;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use DB;
use Builder;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if($request->has('items')){
            $paginate = $request->input('items');
        } else {
            $paginate = 10;
        }

        $userLists = DB::table('people')->select('people.*',
            'first_name as firstName',
            'middle_name as middleName',
            'last_name as lastName')->orderBy('last_name', 'asc');
            if($request->has('first_name')) {
                if($request->input('first_name')!=='--'){
                    $userLists->where('first_name','ILIKE', '%'.$request->input('first_name').'%');
                }
                
            }
            if($request->has('last_name')) {
                if($request->input('last_name')!=='--'){
                    $userLists->where('last_name','ILIKE', '%'.$request->input('last_name').'%');
                }
            }
        $userLists = $userLists->paginate($paginate);
        return response()->json([
            'userLists' => $userLists
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Create data in keycloak and persons master data
        $data = [
            'firstname' => $request->get('firstname'),
            'lastname' => $request->get('lastname'),
            'middlename' => $request->get('middlename'),
            'birthdate' => $request->get('birthdate'),
            'phone' => $request->get('phone'),
            'civilStatus' => $request->get('civilStatus'),
            'region' => $request->get('region'),
            'province' => $request->get('province'),
            'city' => $request->get('city'),
            'barangay' => $request->get('barangay'),
            'house' => $request->get('house'),
            'email' => $request->get('email'),
            'uuid' => $request->get('uuid'),
            'sex' => $request->get('sex'),
        ];
        $email = $data['email'];
        $username = strstr($email, '@', true); //"username"
        $password = uniqid();
        $token = $request->bearerToken();
        try{
            $createUser = DB::table('people')->insert([
                'id' => $data['uuid'],
                'first_name' => $data['firstname'],
                'last_name' => $data['lastname'],
                'middle_name' => $data['middlename'],
                'sex' => $data['sex'],
                'date_of_birth' => $data['birthdate'],
                'civil_status' => $data['civilStatus'],
                'is_verified' => true,
                'is_active' => true,
            ]);
        } catch (Exception $e){
            echo $e;
        } 
        return response()->json([
            'Success' => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserManagement  $userManagement
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        $data = [
            'first_name' => $request->get('firstName'),
            'last_name' => $request->get('lastName'),
            'middle_name' => $request->get('middleName'),
            'email' => $request->get('email'),
            'date_of_birth' => $request->get('birthdate'),
        ];

        $getUser = DB::table('people')
        ->select('*')
        ->where('first_name', $data['first_name'])
        ->where('last_name', $data['last_name'])
        ->where('middle_name', $data['middle_name'])
        ->where('date_of_birth', $data['date_of_birth'])
        ->where('email', $data['email']);
        // return response()->json([
        //     'userFound' => $getUser
        // ], 200);
        var_dump($getUser);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserManagement  $userManagement
     * @return \Illuminate\Http\Response
     */
    public function edit(UserManagement $userManagement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserManagement  $userManagement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserManagement $userManagement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserManagement  $userManagement
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserManagement $userManagement)
    {
        //
        $user = $userManagement->all();
        $destroy = DB::table('people')->where('id', $user->id)->delete();
        return response()->json(['delete' => 'success'], 200);
    }

    public function findUsers(Request $request){
        $data = [
            'first_name' => $request->get('firstName'),
            'last_name' => $request->get('lastName'),
            'middle_name' => $request->get('middleName'),
            'date_of_birth' => $request->get('birthdate'),
        ];
        $challenge = DB::table('people')
                ->where('first_name', $data['first_name'])
                    ->where('last_name', $data['last_name'])
                    ->where('middle_name', $data['middle_name'])
                    ->where('date_of_birth', $data['date_of_birth'])
            ->orWhere(function($query) use ($data) {
                $query->where('first_name', $data['first_name'])
                      ->where('last_name', $data['middle_name'])
                      ->where('date_of_birth', $data['date_of_birth']);
            })
            ->get();        
        return response()->json([
            'userFound' => $challenge
        ], 200);
    }
}
