<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use App\Database\FirestoreDB;
use Illuminate\Support\Facades\Hash;

use App\Includes\FunctionsClass;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // this will return the login view
        return view('auth.login');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $firestore = new FirestoreDB();
        $firestore2 = new FirestoreDB();

        $username =  $request->input('username');
        $hashed_pw = Hash::make($request->input('password'));

        $credentials = ['username' => $username, 'password' => $hashed_pw];
        $firestore -> createDocument('user_table', $credentials);        

        $user_type = $request->input('user_type');        
        $data = ['status' => 'Available', 'userType' => $user_type, 'username' => $username];
        $firestore2 -> createRespondersDocument('Responders', $data);

        return view('auth.login');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Guard $auth_guard)
    {
        if($auth_guard -> validate()) {
            $user = $auth_guard -> user();
            $uid = $user -> getAuthIdentifier();
            session() -> put ('username', $uid);

            $functionsClass = new FunctionsClass();
            $user_details = $functionsClass->getUserDetails();
            return view('pages.home')->with('user_details', $user_details);
        }
        else {
            return view('auth.login');
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
