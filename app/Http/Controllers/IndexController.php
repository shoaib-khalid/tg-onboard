<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        
        if (!$satisfy) return redirect('/failed');

        // set to sessions
        if(!session('userid')) { $request->session()->put('userid',$data['userid']); } 
        if(!session('phonenumber')) { $request->session()->put('phonenumber',$data['phonenumber']); } 
        if(!session('botname')) { $request->session()->put('botname',$data['botname']); } 
        if(!session('botuname')) { $request->session()->put('botuname',$data['botuname']); } 

        @include './includes/ApiWrappers/Templates.php';
        @include './includes/ApiWrappers/Start.php';
        @include './includes/Wrappers/Templates.php';

        // include './Settings/Templates.php';
        @include './MTProtoTools/MTProto.php';
        // include './MTProtoTools/ResponseInfo.php';
        // include './MTProtoTools/MyTelegramOrgWrapper.php';

        $userid = session('userid');
        $phonenumber = session('phonenumber');
        $botname = session('botname');
        $botuname = session('botuname');

        $MadelineProto = new \danog\MadelineProto\API('./sessions/session.' . $phonenumber);
        $MadelineProto->start();
        $me = $MadelineProto->getSelf();
        // $MadelineProto->logger($me);
        
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
            return redirect('/failed');
        }

        return redirect('/bottoken');
    }

    function logout(){
        $data = $request->input();

        // print_r($data);
        if (isset($data['phonenumber'])){

            $phonenumber = $data['phonenumber'];
            $pattern = '/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/i';
            if (!preg_match($pattern, $phonenumber)){ // Outputs 1
                return redirect('/failed');
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
        
            foreach(glob("./sessions/session.$phonenumber*") as $f) {
                unlink($f);
            }
        
            // response
            $MadelineProto->echo('OK, done!');
        
        } else {
            return "failed";
        }
    }
}
