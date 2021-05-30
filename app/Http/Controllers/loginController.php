<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;
use Illuminate\Support\Facades\DB;

class loginController extends Controller
{
    public function login(Request $request){

        $user = DB::table('users')->where('username', $request->username)->where('password', $request->password)->first();

        if($user){
            $data = array(
                "status"=>"true",
                "data"=>array(
                    "username" => $user->username,
                    "name" => $user->name,
                    "surname" => $user->surname,
                    "email" => $user->email
                )
            );
            return $data;
        }else{
            return "false";
        }
    }

    public function register(Request $request){

        $user = new Models\User();
        $user->username = $request->username;
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->password = $request->password;

        $user->save();

        $data = array(
            "status" => "true",
            "data" => array(
                "username" => $user->username,
                "name" => $user->name,
                "surname" => $user->surname,
                "email" => $user->email
            )
        );

        return $data;
    }
}
