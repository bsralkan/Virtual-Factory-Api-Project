<?php

namespace App\Http\Controllers;

use App\Models\WorkcenterOperation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkcenterOperationController extends Controller
{
    public function insert(Request $request){
        try {
            $wcOperation = new WorkcenterOperation();
            $wcOperation->work_center_id = $request->work_center_id;
            $wcOperation->operation_id = $request->operation_id;
            $wcOperation->speed = $request->speed;

            $wcOperation->save();

            $data = array(
                "status" => "true",
                "data" => array(
                    "operation_name" => $wcOperation->work_center_id,
                    "product_type" => $wcOperation->operation_id,
                    "speed" => $wcOperation->speed
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
        $operationList = DB::table("work_center_operation")->get();

        $data = array([
            "status" => "true",
            "data" => $operationList
        ]);

        return $data;
    }

    public function get($wcOpId){
        $wcOperation = DB::table("work_center_operation")->where("wc_opr_id", $wcOpId)->get();

        if($wcOperation->count()>0){
            $data = array([
                "status" => "true",
                "data" => $wcOperation
            ]);

            return $data;
        }else{
            $data = array([
                "status" => "false",
                "data" => "Workcenter Operation bulunamadı"
            ]);

            return $data;
        }
    }
}
