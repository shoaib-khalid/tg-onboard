<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class BotTokenController extends Controller
{
    //
    function loadView() {

        // if there's a madeline login session, log it out
        if(session('phonenumber')) { 
            $phonenumber = session('phonenumber'); 

            $url = config('app.url');
            $endpoint = $url . "/logout";
            $object = [
                'phonenumber' => $phonenumber
            ];
            
            \Log::channel('transaction')->info("TGO <- PATH " . $endpoint);
            \Log::channel('transaction')->info("TGO <- BODY " . json_encode($object));
            $response = Http::post($endpoint, $object);
            \Log::channel('transaction')->info("TGO <- RESP " . $response);
        } 

        return view("bottoken",[
            'botuname' => session('botuname'),
            'userid' => session('userid')
        ]);
    }

    /**
     * Set webhook to telegram api
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function setWebhook (Request $request) {

        $validate = Validator::make(
            $request->all(), [ 
                'botuname' => 'required|string',
                'userid' => 'required|string',
                'token' => 'required|string'
            ]
        );

        if ($validate->fails()) {
            return response()->json(
                [
                    'system' => 'telegram-onboard',
                    'action' => 'setWebhook',
                    'status' => false,
                    'system_response' =>$validate->errors(),
                    'description' => 'Invalid parameters',
                    'status' => false
                ],
                400
            );
        }

        $botuname = $request['botuname'];
        $userid = $request['userid'];
        $token = $request['token'];

        /**
         * Set Webhook
         */
        $endpoint = config('services.telegram.url') . "/bot" . $token . "/setwebhook";
        $webhookUrl = config('services.tgw.url') . "/incoming/" . $botuname;
        $data = ['url' => $webhookUrl];
        $parameters = urldecode(http_build_query($data));

        \Log::channel('transaction')->info("Telegram <- PATH " . $endpoint);
        \Log::channel('transaction')->info("Telegram <- PARAM " . $parameters);
        $response = Http::get($endpoint . "?" . $parameters);
        \Log::channel('transaction')->info("Telegram <- RESP " . $response);

        if (!$response["ok"]) {
            return response()->json([
                'system' => 'telegram',
                'action' => 'setwebhook',
                'status' => false,
                'system_response' => $response->json(),
                'description' => 'Set webhook failed, please make sure you have entered the correct token'
            ],$response["error_code"]);
        }


        /**
         * Update User Service
         */

        $url = config('services.userservice.url');
        $tokenBearer = config('services.userservice.token');
        $endpoint = $url . "/v1/userChannels";
        $header = [
            "Content-type" => "application/json",
            "Authorization" => "Bearer $tokenBearer"
        ];
        $object = [
            'refId' => $botuname,
            'userId' => $userid,
            'channelName' => 'telegram',
            'token' => $token,
        ];
        
        \Log::channel('transaction')->info("User Service <- PATH " . $endpoint);
        \Log::channel('transaction')->info("User Service <- BODY " . json_encode($object));
        $response = Http::withHeaders($header)->post($endpoint, $object);
        \Log::channel('transaction')->info("User Service <- RESP " . $response->status() . " " . $response);

        if ($response["message"] !== "OK") {
            return response()->json([
                'system' => 'user-service',
                'action' => 'set userChannels',
                'status' => false,
                'system_response' => $response->json(),
                'description' => 'Backend problem, please contact system admin'
            ],$response["status"]);
        }

        return response()->json();
    }
}
