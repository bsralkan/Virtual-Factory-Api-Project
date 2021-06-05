<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            $orderItems = $request->order_items;

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

    public function get($orderId){
        $order = DB::table("orders")->where("order_id", $orderId)->get();

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
}
