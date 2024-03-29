<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();
        if($user->role_id == 3) return view('client_sales_rep');
        else return view('home');
    }

    public function ob_login(){
        $user = \Auth::user();
        //dd($user->username);
        $client_domain = session()->get('working_client.client_domain');
        $username = $user->username."@".$client_domain;
        $password = $user->ob_password;
        return view('ob_login', compact('username', 'password'));
    }
}
