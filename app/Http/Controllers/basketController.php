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
            $basket->product_id = $request->product_id;
            $basket->product_name = $request->product_name;
            $basket->product_type = $request->product_type;

            $basket->save();

            $data = array(
                "status" => "true",
                "data" => array(
                    "product_id" => $basket->product_id,
                    "product_name" => $basket->product_name,
                    "product_type" => $basket->product_type,
                    "is_salable" => $basket->is_salable
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
