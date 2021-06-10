<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Schedule;
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

            DB::table("basket")->where("customer_id", $request->customer_id)->delete();
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

    public function getOrderSchedules($orderID){

        DB::table("schedule")->truncate();

        $response = DB::table('order_items')->where("order_id", $orderID)->get();
        $order = DB::table('orders')->where("order_id", $orderID)->get();

        $mytime = \Carbon\Carbon::now();
        $deadLine = \Carbon\Carbon::parse($order[0]->deadline);

        $diff_in_minutes = $deadLine->diffInMinutes($mytime);

        $resultList = array();
        $totalDuration = 0;

        foreach ($response as $item) {
            $tempList = $this->getAllSubProducts($item->product_id);
            $resultList = array_merge($resultList, $tempList);
        }

        $resList = array();


        foreach ($resultList as $item) {

            $product = DB::table("products")->where("product_id", $item->sub_product_id)->get();
            $operation = DB::table("operations")->where("product_type", $product[0]->product_type)->get();
            $wcoperations = DB::table("work_center_operation")->where("operation_id", $operation[0]->operation_id)->get();
            $wcoperation = array();

            $count = count($wcoperations);
            $i = 0;

            foreach ($wcoperations as $wcop) {
                $workcenter = DB::table("work_centers")->where("work_center_id", $wcop->work_center_id)->get();
                $schedule = DB::table("schedule")->where("work_center", $workcenter[0]->work_center_name)->orderByDesc('id')->get();

                $duration = ($item->amount * 1000) / $wcop->speed;

                $totalDuration += $duration;

                $wcoperation = array(
                    "wc_opr_id" => $wcop->wc_opr_id,
                    "work_center_id" => $wcop->work_center_id,
                    "operation_id" => $wcop->operation_id,
                    "speed" => $wcop->speed
                );

                if(count($schedule)>0){
                    if(++$i === $count){
                        $endd = 1223312312312;
                        foreach ($wcoperations as $wcoperation) {
                            $workcenter1 = DB::table("work_centers")->where("work_center_id", $wcoperation->work_center_id)->get();
                            $schedule = DB::table("schedule")->where("work_center", $workcenter[0]->work_center_name)->orderByDesc('id')->get();
                            if($schedule[0]->end < $endd){
                                $workcenterLast = $workcenter1;
                            }
                        }
                        //$schedule = DB::table("schedule")->where("work_center", $workcenter[0]->work_center_name)->orderBy('end')->get();
                        $start = $schedule[0]->end;
                        $scheduleDb = new Schedule();
                        $scheduleDb->start = $start;
                        $scheduleDb->end = intval($duration + $start);
                        $scheduleDb->work_center = $workcenterLast[0]->work_center_name;
                        $scheduleDb->product_id = $item->sub_product_id;
                        $scheduleDb->save();
                        break;
                    }

                }else{
                    $scheduleDb = new Schedule();
                    $scheduleDb->start = 0;
                    $scheduleDb->end = $duration;
                    $scheduleDb->work_center = $workcenter[0]->work_center_name;
                    $scheduleDb->product_id = $item->sub_product_id;
                    $scheduleDb->save();
                    break;
                }

            }

            $temp = array(
                "item" => $item,
                "product" => $product,
                "operation" => $operation,
                "wcOp" => $wcoperation,
                "duration" => $duration
            );
            $resList [] = $temp;
        }

        $isReachable = 0;

        if($diff_in_minutes < $totalDuration){
            $isReachable = 0;
        }else{
            $isReachable = 1;
        }
        return $isReachable;

    }

    public function getOrdersWithStatus()
    {
        $orders = DB::table("orders")->get();
        $result = array();

        foreach ($orders as $order) {
            $isReachable = $this->getOrderSchedules($order->order_id);
            $data = array(
                "isReachable" => $isReachable,
                "order" => $order
            );
            $result[] = $data;
        }
        return $result;
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
