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
        $response = DB::table('order_items')
            ->join('products', 'products.product_id', '=', 'order_items.product_id')
            ->join('operations', 'products.product_type', '=', 'operations.product_type')
            ->join('work_center_operation', 'work_center_operation.operation_id', '=', 'operations.operation_id')
            ->select('order_items.*', 'order_items.amount', 'work_center_operation.speed')
            //->where('customers.wsi', '=', $wsi)
            ->get();
        return $response;
    }
}
