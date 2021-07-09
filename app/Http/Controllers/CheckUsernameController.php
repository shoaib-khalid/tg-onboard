<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckUsernameController extends Controller
{
    //
    function check(Request $request){
        $botuname = $request->botuname;
        $phonenumber = $request->phonenumber;

        if (!isset($botuname) || !isset($phonenumber)) {
            $description = "botuname or phonenumber is empty";
            return response()->json([
                'system' => 'telegram-onboard',
                'action' => 'check bot username',
                'status' => false,
                'system_response' => null,
                'description' => $description
            ],400);
        }

        // $settings = [ 'app_info' => [ 'api_id' => 3552175, 'api_hash' => 'e58f4e4ad3461b6b2a9cd34d92efacfc'] ];
        $MadelineProto = new \danog\MadelineProto\API('./sessions/session.' . $phonenumber);
        // $MadelineProto->phoneLogin("60148317192");
        $MadelineProto->start();

        $Bool = $MadelineProto->account->checkUsername(['username' => $botuname]);

        if ($Bool === false) {
            $description = "Bot Username Taken";
            return response()->json([
                'system' => 'telegram-onboard',
                'action' => 'check bot username',
                'status' => false,
                'system_response' => [
                    'username_available' => $Bool
                ],
                'description' => $description
            ],400);
        }

        $description = "Bot Username Available";
        return response()->json([
            'system' => 'telegram-onboard',
            'action' => 'check bot username',
            'status' => true,
            'system_response' => [
                'username_available' => $Bool
            ],
            'description' => $description
        ],200);
        
    }
}
