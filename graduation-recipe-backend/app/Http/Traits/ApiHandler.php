<?php

namespace App\Http\Traits;

use GrahamCampbell\ResultType\Success;

trait ApiHandler{

    public function SuccessMessage($msg){
        return response()->json([
            'status' => true,
            'msg'=> $msg,
        ]);
    }

        public function ErrorMessage($msg){
        return response()->json([
            'status' => false,
            'msg'=> $msg,
        ]);
    }

        public function ReturnData($key , $value , $msg){
        return response()->json([
            'status' => true,
            'msg'=> $msg,
            $key => $value,
        ]);
    }

}
