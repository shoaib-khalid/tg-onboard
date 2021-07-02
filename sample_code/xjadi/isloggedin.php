<?php

require_once 'madeline.php';
			
$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$MadelineProto->async(true);

$MadelineProto->loop(function() use ($MadelineProto) {
    if ($test = $MadelineProto->loggedIn()) {   
        var_dump($MadelineProto->account->getAuthorizations());
        //yield $number = yield $MadelineProto->readline('Enter a number: ');
        //yield $MadelineProto->echo("Number entered: $number\n");
        yield $MadelineProto->echo("Yes, Logged In\n");
        if (yield  $MadelineProto->getSelf()){
            yield $MadelineProto->echo("Yes\n");  
        } else {
            yield $MadelineProto->echo("No\n");
        }
        print "<pre>";
        var_dump(yield  $MadelineProto->getSelf());
        print "<pre>";
    } else{
        yield $MadelineProto->echo("Not Logged In\n");
    }
});

