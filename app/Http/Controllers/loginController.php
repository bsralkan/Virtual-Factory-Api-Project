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
            $data = array(
                "status"=>"false",
                "data"=>array(
                    "error" => "Kullanıcı veya şifre yanlış"
                )
            );
            return $data;
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

    public function customerLogin(Request $request){

        $customer = DB::table('customers')->where('customerName', $request->customerName)->where('password', $request->password)->first();

        if($customer){
            $data = array(
                "status"=>"true",
                "data"=>array(
                    "username" => $customer->customerName,
                    "name" => $customer->name,
                    "surname" => $customer->surname,
                    "email" => $customer->email
                )
            );
            return $data;
        }else{
            $data = array(
                "status"=>"false",
                "data"=>array(
                    "error" => "Kullanıcı veya şifre yanlış"
                )
            );
            return $data;
        }
    }

    public function customerRegister(Request $request){

        $customer = new Models\customer();
        $customer->customerName = $request->customerName;
        $customer->name = $request->name;
        $customer->surname = $request->surname;
        $customer->email = $request->email;
        $customer->password = $request->password;

        $customer->save();

        $data = array(
            "status" => "true",
            "data" => array(
                "customerName" => $customer->customerName,
                "name" => $customer->name,
                "surname" => $customer->surname,
                "email" => $customer->email
            )
        );

        return $data;
    }
}
