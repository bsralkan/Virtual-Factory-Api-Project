<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class operationController extends Controller
{
    public function insert(Request $request){
        try {
            $operation = new Operation();
            $operation->operation_name = $request->operation_name;
            $operation->product_type = $request->product_type;

            $operation->save();

            $data = array(
                "status" => "true",
                "data" => array(
                    "operation_name" => $operation->operation_name,
                    "product_type" => $operation->product_type
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
        $operationList = DB::table("operations")->get();

        $data = array([
            "status" => "true",
            "data" => $operationList
        ]);

        return $data;
    }

    public function get($opId){
        $operation = DB::table("operations")->where("operation_id", $opId)->get();

        if($operation->count()>0){
            $data = array([
                "status" => "true",
                "data" => $operation
            ]);

            return $data;
        }else{
            $data = array([
                "status" => "false",
                "data" => "Operation bulunamadı"
            ]);

            return $data;
        }
    }
}
