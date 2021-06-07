<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psy\Util\Json;

class orderController extends Controller
{
    public function insert(Request $request){
        try {
            $order = new Order();
            $order->customer_id = $request->customer_id;
            $order->order_date = $request->order_date;
            $order->deadline = $request->deadline;

            $order->save();

            $orderItems = json_decode($request->order_items, true);

            foreach ($orderItems as $orderItem) {
                $order_item = new OrderItem();
                $order_item->order_id = $order->id;
                $order_item->product_id = $orderItem['product_id'];
                $order_item->amount = $orderItem['amount'];
                $order_item->save();
            }

            $data = array(
                "status" => "true",
                "data" => array(
                    "customer_id" => $order->customer_id,
                    "order_date" => $order->order_date,
                    "deadline" => $order->deadline,
                    "orderItems" => $orderItems
                )
            );
            return $data;
        }catch (\Exception $e){
            $data = array(
                "status" => "false",
                "data" => array(
                    "error" => $e->getMessage()
                )
            );
            return $data;
        }
    }

    public function getAll(){
        $orderList = DB::table("orders")->get();

        $data = array([
            "status" => "true",
            "data" => $orderList
        ]);

        return $data;
    }

    public function get($customerId){
        $order = DB::table("orders")->where("customer_id", $customerId)->get();

        if($order->count()>0){
            $data = array([
                "status" => "true",
                "data" => $order
            ]);

            return $data;
        }else{
            $data = array([
                "status" => "false",
                "data" => "Sipariş bulunamadı"
            ]);

            return $data;
        }
    }

    public function getOrderITems($orderId){
        $orderItems = DB::table("order_items")->where("order_id", $orderId)->get();

        if($orderItems->count()>0){
            $data = array([
                "status" => "true",
                "data" => $orderItems
            ]);

            return $data;
        }else{
            $data = array([
                "status" => "false",
                "data" => "Sipariş bulunamadı"
            ]);

            return $data;
        }
    }

    public function getOrderSchedules(){

        $operations = DB::table("operations")->get();
        $pId = 'ethernet05';
        $response = DB::table('order_items')->get();
        $resultList = array();
        foreach ($response as $item) {
            $tempList = $this->getAllSubProducts($item->product_id);
            $resultList = array_merge($resultList, $tempList);
        }
        //return $resultList;

        $resList = array();

        foreach ($resultList as $item) {
            $product = DB::table("products")->where("product_id", $item->sub_product_id)->get();
            $operation = DB::table("operations")->where("product_type", $product[0]->product_type)->get();
            $wcoperation = DB::table("work_center_operation")->where("operation_id", $operation[0]->operation_id)->get();
            $temp = array(
                "item" => $item,
                "product" => $product,
                "operation" => $operation,
                "wcOp" => $wcoperation
            );
            $resList = array_merge($resList, $temp);
        }

        return $resList;



        $productList = array();

        $product = DB::table("products")->where('product_id', $pId)->get();
        $productList = array_merge($productList, $product->toArray());

        foreach ($resultList as $item) {
            $product = DB::table("products")->where('product_id', $item->sub_product_id)->get();
            $productList = array_merge($productList, $product->toArray());
        }

        return $productList;
        $res = collect($productList)->groupBy('product_type');

        if(isset($res["twistedpair"])){

            return count($res["twistedpair"]);
        }else{
            return "urun yok";
        }


        $response = DB::table('order_items')
            ->join('products', 'products.product_id', '=', 'order_items.product_id')
            ->join('operations', 'products.product_type', '=', 'operations.product_type')
            ->join('work_center_operation', 'work_center_operation.operation_id', '=', 'operations.operation_id')
            ->select('order_items.*', 'order_items.amount', 'work_center_operation.speed')
            ->orderByDesc('work_center_operation.speed')
            ->get();
        return $response;
    }

    public function getAllSubProducts($pId)
    {
        $productIds = array();
        $res = $this->getSubProducts($pId);
        $control = false;

        if(count($res)>0){
            $control = true;
        }

        while ($control){
            if(count($res)>0){
                $productIds = array_merge($productIds, $res);
                $tempList=array();
                foreach ($res as $item) {
                    $temp = $this->getSubProducts($item->sub_product_id);
                    if(count($temp)>0){
                        $tempList = array_merge($tempList, $temp);
                    }
                }
                if(count($tempList) > 0){
                    $res = $tempList;
                }
                else{
                    $control = false;
                }
            }else{
                $control=false;
            }
        }
        return $productIds;

    }
    public function getSubProducts($product_id)
    {
        $sub_products = DB::table("sub_product_tree")->where("product_id", $product_id)->get();

        return $sub_products->toArray();
    }
}
