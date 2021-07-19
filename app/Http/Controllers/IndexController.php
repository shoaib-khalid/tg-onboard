<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IndexController extends Controller
{
    /**
     * Load Index Page
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function loadView(Request $request){
        $userid = $request->userid;

        \Log::channel('transaction')->info("Logger Works");

        if (!isset($userid)) {
            return view("welcome");
        }

        return view("index",['userid'=>$userid]);
    }

    /**
     * Load Index Page if parameter POST
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    function start(Request $request){

        $data = $request->input();

        // redirect back to get page is one of the variable unset
        $satisfy = false;
        // Input checking
        if ((isset($data['userid']) && isset($data['phonenumber']) && isset($data['botname']) && isset($data['botuname'])) ||
            (session('userid') && session('phonenumber') && session('botname') && session('botuname'))) { 
            $satisfy = true;
        }
        
        if (!$satisfy) {
            $error="Requirement not satisfied";
            $description="userid => " . session('userid') . ",phonenumber => " . session('phonenumber') . ",botname => " . session('botname') . ",botuname => " . session('botuname');
            return view("failed",[
                'error' => $error,
                'description' => $description,
                'userid' => session('userid')
            ]);
        };

        // set to sessions
        if(!session('userid')) { $request->session()->put('userid',$data['userid']); } 
        if(!session('phonenumber')) { 
            $sanitised_phonenumber = preg_replace('/[^0-9]/', '', $data['phonenumber']);
            $request->session()->put('phonenumber',$sanitised_phonenumber); 
        } 
        if(!session('botname')) { $request->session()->put('botname',$data['botname']); } 
        $revised = "";
        if(!session('botuname')) { 
            $revised = $data['botuname'];
            if ($revised[0] !== '@') {
                $revised = '@'.$revised;
            }
            $request->session()->put('botuname',$revised); 
        }

        session()->save();

        $userid = session('userid');
        $phonenumber = session('phonenumber');
        $botname = session('botname');
        $botuname = session('botuname');
        $botunamenoalias = ltrim($botuname, '@');

        @include __DIR__.'/includes/ApiWrappers/Templates.php';
        @include __DIR__.'/includes/ApiWrappers/Start.php';
        @include __DIR__.'/includes/Wrappers/Templates.php';

        @include __DIR__.'/includes/Settings/Templates.php';
        @include __DIR__.'/includes/Settings/Lang.php';
        // @include './MTProtoTools/MTProto.php';
        // include './MTProtoTools/ResponseInfo.php';
        // include './MTProtoTools/MyTelegramOrgWrapper.php';
        
        $settings = [
            'logger' => [
                'param' => public_path().'/logs/Madeline.log'
            ]
        ];

        $MadelineProto = new \danog\MadelineProto\API('./sessions/session.' . $phonenumber,$settings);
        $MadelineProto->start();
        $me = $MadelineProto->getSelf();
        $MadelineProto->logger($me);

        $Bool = $MadelineProto->account->checkUsername(['username' => $botunamenoalias]);

        if ($Bool === false) {
            $error="Bot Username already exists";
            $description="Please choose another bot Username";
            $uid = session('userid');

            $request->session()->forget('userid');
            $request->session()->forget('phonenumber');
            $request->session()->forget('botname');
            $request->session()->forget('botuname');
            session_unset();

            return view("failed",[
                'error' => $error,
                'description' => $description,
                'userid' => $uid
            ]);
        }

        if (!$me['bot']) {
            $MadelineProto->messages->sendMessage(['peer' => '@BotFather', 'message' => "/start"]);
            sleep(2);
            $MadelineProto->messages->sendMessage(['peer' => '@BotFather', 'message' => "/newbot"]);
            sleep(2);
            $MadelineProto->messages->sendMessage(['peer' => '@BotFather', 'message' => $botname]);
            sleep(2);
            $MadelineProto->messages->sendMessage(['peer' => '@BotFather', 'message' => $botuname]);
            sleep(5);
        } else {
            return redirect("/bottoken?userid=$userid&status=failed");
        }

        return redirect("/bottoken?userid=$userid&status=success");
    }

    function logout(Request $request){
        $data = $request->input();

        // print_r($data);
        if (isset($data['phonenumber'])){

            $phonenumber = $data['phonenumber'];
            $pattern = '/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/i';
            if (!preg_match($pattern, $phonenumber)){ // Outputs 1
                $error="Wrong Phonenumber Format";
                $description="Wrong Phonenumber Format";
                return view("failed",[
                    'error' => $error,
                    'description' => $description,
                    'userid' => session('userid')
                ]);
            }    
        
            // initiate madeline proto
            $MadelineProto = new \danog\MadelineProto\API('./sessions/session.'.$phonenumber);
        
            // log user out
            $MadelineProto->logout();
        
            $MadelineProto->async(true);
            $MadelineProto->loop(function() use ($MadelineProto) {
                yield $MadelineProto->logout();
            });
        
            $pid = exec("pgrep -f session.$phonenumber | tr '\n' ' '");
        
            exec("kill -9 $pid");
        
            foreach(glob(public_path()."/sessions/session.$phonenumber*") as $f) {
                unlink($f);
            }
        
            // response
            $MadelineProto->echo('OK, done!');
        
        } else {
            return "failed";
        }
    }
}
