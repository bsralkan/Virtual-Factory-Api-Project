<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class productController extends Controller
{

    public function insert(Request $request){
        try {
            $product = new Product();
            $product->product_id = $request->product_id;
            $product->product_name = $request->product_name;
            $product->product_type = $request->product_type;
            $product->is_salable = $request->is_salable;

            $product->save();

            $data = array(
                "status" => "true",
                "data" => array(
                    "product_id" => $product->product_id,
                    "product_name" => $product->product_name,
                    "product_type" => $product->product_type,
                    "is_salable" => $product->is_salable
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
        $productList = DB::table("products")->get();

        $data = array([
            "status" => "true",
            "data" => $productList
        ]);

        return $data;
    }
    public function get($productId){
        $product = DB::table("products")->where("product_id", $productId)->get();

        if($product->count()>0){
            $data = array([
                "status" => "true",
                "data" => $product
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
