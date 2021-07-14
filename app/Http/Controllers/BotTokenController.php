<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class BotTokenController extends Controller
{
    //
    function loadView(Request $request) {

        $userid = $request->userid;
        $data = $request->input();

        $status = "";
        $message = "";

        if (!isset($userid)){
            $status = 'failed';
            $message = 'Missing user/merchant id. Close this window and try again from Symplified Merchant Portal';
        }

        if (isset($data['status'])){
            if ($data['status'] == 'success') {
                $status = 'success';
                $message = '@botfather have created the bot for you, now we need the 
                <span class="font-bold">token generated by @botfather</span> for us to 
                automate your bot. Copy and Paste token given by @botfather to below input field:';
             } else {
                $status = 'failed';
                $message = 'Bot registration failed, this might be cause by a lot of reasons. 
                            eg: Bot username is already taken, Prohibited bot name , Banned words and etc.
                            Please check your telegram and complete the telegram bot creation with @botfather';
             }
        }

        return view("bottoken",[
            'botuname' => session('botuname'),
            'userid' => $userid,
            'message' => $message,
            'status' => $status
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
        if ($botuname[0] !== "@") {
            $botuname = "@".$botuname;
        }
        $boturl = "https://t.me/". ltrim($botuname,'@');
        $userid = $request['userid'];
        $token = $request['token'];

        /**
         * Set Webhook
         */
        $endpoint = config('services.telegram.url') . "/bot" . $token . "/setwebhook";
        $webhookUrl = config('services.tgw.url') . "/incoming/" . $botuname;
        $query = ['url' => $webhookUrl];
        $parameters = urldecode(http_build_query($query));

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

        if ($response["status"] !== 201) {
            if ($response["status"] == 409) {
                $description = "This bot username is already registered in the symplified. 
                Contact admin if this happen t be a problem";
            } else {
                $description = "Backend problem, please contact system admin";
            }

            return response()->json([
                'system' => 'user-service',
                'action' => 'set userChannels',
                'status' => false,
                'system_response' => $response->json(),
                'description' => $description
            ],$response["status"]);

        }

        // if there's a madeline login session, log it out
        if(session('phonenumber')) { 
            $phonenumber = session('phonenumber'); 

            $url = config('app.url');
            $endpoint = $url . "/logout";
            $header = [
                "Content-type" => "application/json",
                "X-CSRF-Token" => csrf_token()
            ];
            $object = [
                'phonenumber' => $phonenumber
            ];
            
            \Log::channel('transaction')->info("TGO <- PATH " . $endpoint);
            \Log::channel('transaction')->info("TGO <- BODY " . json_encode($object));
            $response = Http::withHeaders($header)->post($endpoint, $object);
            \Log::channel('transaction')->info("TGO <- RESP " . $response);
        } 

        // log everything else out
        $request->session()->forget('userid');
        $request->session()->forget('phonenumber');
        $request->session()->forget('botname');
        $request->session()->forget('botuname');
        session_unset();

        return response()->json([
            'system' => 'telegram-onboard',
            'action' => 'set botToken',
            'status' => true,
            'system_response' => 'success',
            'description' => "Bot registration sucess. To access your $botuname go to <a class=\"underline\" href=\"$boturl\">$boturl</a>. Share with it others"
        ],200);
    }
}
