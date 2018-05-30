<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use DSPTCH\FirestoreUser;
// use DSPTCH\Database\FirestoreDB;

class PagesController extends Controller
{
    public function register(){
        return view('auth.register');
    }
}