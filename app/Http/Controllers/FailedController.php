<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FailedController extends Controller
{
    //
    function loadView(){
        $error="";
        $description="";
        return view("failed",[
            'error' => $error,
            'description' => $description,
            'userid' => ''
        ]);
    }
}
