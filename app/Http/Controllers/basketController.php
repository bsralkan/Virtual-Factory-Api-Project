<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class basketController extends Controller
{

    public function insert(Request $request){
        try {
            $basket = new Basket();
            $basket->customer_id = $request->customer_id;
            $basket->product_id = $request->product_id;
            $basket->amount = $request->amount;

            $basket->save();

            $data = array(
                "status" => "true",
                "data" => array(
                    "customer_id" => $basket->customer_id,
                    "product_id" => $basket->product_id,
                    "amount" => $basket->amount
                )
            );
            return $data;
        }catch (\Exception $e){
            $data = array(
                "status" => "false",
                "data" => array(
                    "error" => "Error !"
                )
            );
            return $data;
        }
    }

    public function getAll(){
        $basketList = DB::table("basket")->get();

        $data = array([
            "status" => "true",
            "data" => $basketList
        ]);

        return $data;
    }
    public function get($customerId){
        $basket = DB::table("basket")->where("customer_id", $customerId)->get();

        if($basket->count()>0){
            $data = array([
                "status" => "true",
                "data" => $basket
            ]);

            return $data;
        }else{
            $data = array([
                "status" => "false",
                "data" => "Ürün bulunamadı"
            ]);

            return $data;
        }
    }
}
