<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompleteController extends Controller
{
    //
    function loadView(){
        $error="";
        $description="";
        return view("complete",[
            'error' => $error,
            'description' => $description
        ]);
    }
}
