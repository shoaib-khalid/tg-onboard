<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FailedController extends Controller
{
    //
    function loadView(){
        return view("failed");
    }
}
