<?php

namespace App\Http\Controllers;

use App\Models\Workcenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class workcenterController extends Controller
{
    public function insert(Request $request){
        try {
            $workcenter = new Workcenter();
            $workcenter->work_center_name = $request->work_center_name;
            if($request->active == "true"){
                $workcenter->active = 1;
            }else{
                $workcenter->active = 0;
            }


            $workcenter->save();


            $data = array(
                "status" => "true",
                "data" => array(
                    "work_center_name" => $workcenter->work_center_name,
                    "active" => $workcenter->active
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
        $workcenter = DB::table("work_centers")->get();

        $data = array([
            "status" => "true",
            "data" => $workcenter
        ]);

        return $data;
    }

    public function get($wcId){
        $workCenters = DB::table("work_centers")->where("work_center_id", $wcId)->get();

        if($workCenters->count()>0){
            $data = array([
                "status" => "true",
                "data" => $workCenters
            ]);

            return $data;
        }else{
            $data = array([
                "status" => "false",
                "data" => "Workcenter bulunamadı"
            ]);

            return $data;
        }
    }
}
