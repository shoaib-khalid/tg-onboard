<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';

ob_start();
include('test-template.php');
$new_template = ob_get_contents();
ob_end_clean();

$MadelineProto = new \danog\MadelineProto\API('session.madeline');

// $MadelineProto->setWebTemplate($new_template);
// $MadelineProto->start();

$account_Authorizations = $MadelineProto->account->getAuthorizations();

print "<pre>";
var_dump($account_Authorizations);
print "</pre>";