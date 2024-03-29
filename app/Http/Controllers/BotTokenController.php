<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class BotTokenController extends Controller
{
    /**
     * Load Bot Token Page by POST (www-form)
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function loadView(Request $request) {
        // get userid from url
        $userid = $request->userid;
        // get POST data body
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
     * Set webhook to telegram api by POST (JSON)
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function setWebhook (Request $request) {

        $reqinfo = [
            "METHOD" => "setWebhook",
            "PATH" => config('app.url') . $request->getRequestUri(),
            "HEADER" => $request->header(),
            "BODY" => $request->getContent()
        ];

        \Log::channel('csv')->info("Receive " . $reqinfo["METHOD"] . " Request",$reqinfo);

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

        $response = $this->setTelegramBotToken($botuname,$token);
        if ($response !== null) return response()->json($response,400);

        $response = $this->getUserServiceByRefId($botuname);
        if ($response !== null) return response()->json($response,400);

        $response = $this->setUserServiceChannel($botuname,$userid,$token);
        if ($response !== null) return response()->json($response,400);

        // if there's a madeline login session, log it out
        if(session('phonenumber')) {
            $phonenumber = session('phonenumber'); 

            $url = config('app.url');
            $endpoint = $url . "/logout";
            $header = [
                "Content-type" => "application/json",
                "Accept" => "*/*"
            ];
            $object = [
                'phonenumber' => $phonenumber,
                '_token' => csrf_token()
            ];
            
            \Log::channel('transaction')->debug("TGO <- PATH " . $endpoint);
            \Log::channel('transaction')->debug("TGO <- HEADER " . json_encode($header));
            \Log::channel('transaction')->debug("TGO <- BODY " . json_encode($object));
            $response = Http::withHeaders($header)->post($endpoint, $object);
            \Log::channel('transaction')->debug("TGO <- RESP " . $response);

            $reqinfo = [
                "PATH" => $endpoint,
                "HEADER" => $header,
                "BODY" => $object,
                "RESPONSE" => $response->body(),
                "RESPONSE_STATUS" => $response->status()
            ];
            \Log::channel('csv')->info("Logout MadelineSession",$reqinfo);
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

    // Bawah2 ni patut buat model TelgramModel & UserServiceModel ... tp malas.. hahaha

    private function setTelegramBotToken($botuname,$token){

        /**
         * Set Webhook
         */

        $endpoint = config('services.telegram.url') . "/bot" . $token . "/setwebhook";
        $webhookUrl = config('services.tgw.url') . "/incoming/" . $botuname;
        $query = ['url' => $webhookUrl];
        $parameters = urldecode(http_build_query($query));

        \Log::channel('transaction')->debug("Telegram <- PATH " . $endpoint);
        \Log::channel('transaction')->debug("Telegram <- PARAM " . $parameters);
        $response = Http::get($endpoint . "?" . $parameters);
        \Log::channel('transaction')->debug("Telegram <- RESP " . $response);

        $reqinfo = [
            "PATH" => $endpoint,
            "HEADER" => [],
            "PARAMETERS" => $parameters,
            "RESPONSE" => $response->body(),
            "RESPONSE_STATUS" => $response->status()
        ];
        \Log::channel('csv')->info("Set Telegram Webhook",$reqinfo);

        if (!$response["ok"]) {
            return [
                'system' => 'telegram',
                'action' => 'setwebhook',
                'status' => false,
                'system_response' => $response->json(),
                'description' => 'Set webhook failed, please make sure you have entered the correct token'
            ];
        }

        return null;
    }

    private function setUserServiceChannel($botuname,$userid,$token){

        /**
         * Update User Service
         */

        $url = config('services.userservice.url');
        $tokenBearer = config('services.userservice.token');
        $endpoint = $url . "/userChannels";
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
        
        \Log::channel('transaction')->debug("User Service <- PATH " . $endpoint);
        \Log::channel('transaction')->debug("User Service <- BODY " . json_encode($object));
        $response = Http::withHeaders($header)->post($endpoint, $object);
        \Log::channel('transaction')->debug("User Service <- RESP " . $response->status() . " " . $response);

        $reqinfo = [
            "PATH" => $endpoint,
            "HEADER" => $header,
            "BODY" => $object,
            "RESPONSE" => $response->body(),
            "RESPONSE_STATUS" => $response->status()
        ];
        \Log::channel('csv')->info("Save Telegram Token at User Service",$reqinfo);

        if ($response["status"] !== 201) {
            if ($response["status"] == 409) {
                $description = "This bot username is already registered in the symplified. 
                Contact admin if this happen t be a problem";
            } else {
                $description = "Backend problem, please contact system admin";
            }

            return [
                'system' => 'user-service',
                'action' => 'set userChannels',
                'status' => false,
                'system_response' => $response->json(),
                'description' => $description
            ];
        }

        return null;
    }

    private function getUserServiceByRefId($refId) {

        $url = config('services.userservice.url');
        $token = config('services.userservice.token');
        $endpoint = $url . "/userChannels";
        $data = [
            'refId' => $refId,
            'channelName' => 'telegram'
        ];
        $parameters = http_build_query($data);
        $header = [
            "Content-type" => "application/json",
            "Authorization" => "Bearer $token"
        ];
        
        // get token from user service
        \Log::channel('transaction')->debug("User Service <- PATH " . $endpoint);
        \Log::channel('transaction')->debug("User Service <- HEADER " . json_encode($header));
        \Log::channel('transaction')->debug("User Service <- PARAM " . $parameters);
        $response = Http::withHeaders($header)->get($endpoint . "?" . $parameters);
        \Log::channel('transaction')->debug("User Service <- RESP " . $response);

        $reqinfo = [
            "PATH" => $endpoint,
            "HEADER" => $header,
            "PARAMETERS" => $parameters,
            "RESPONSE" => $response->body(),
            "RESPONSE_STATUS" => $response->status()
        ];
        \Log::channel('csv')->info("Get Telegram Token from User Service",$reqinfo);

        if ($response["status"] !== 200) {
            $description = "User service give response !== 200";
            \Log::channel('transaction')->debug("User Service <- ERROR " . $description);
            return [
                'system' => 'user-service',
                'action' => 'get userChannels',
                'status' => false,
                'system_response' => $response->json(),
                'description' => $description
            ];
        }

        if (!empty($response["data"]["content"])){
            $description = "Ops !! Seems like the bot username is already registered in Symplified.";
            \Log::channel('transaction')->debug("User Service <- ERROR " . $description);
            return [
                'system' => 'user-service',
                'action' => 'get userChannels',
                'status' => false,
                'system_response' => $response->json(),
                'description' => $description
            ];
        }

        if (count($response["data"]["content"]) > 1){
            $description = "User service give response.data.content > 1";
            \Log::channel('transaction')->debug("User Service <- ERROR " . $description);
            return [
                'system' => 'user-service',
                'action' => 'get userChannels',
                'status' => false,
                'system_response' => $response->json(),
                'description' => $description
            ];
        }

        return null;
    }
}
