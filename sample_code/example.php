<?php

require_once("miqdaad.php");

$miqdaad = new miqdaad\get_response();
$arr_obj = $miqdaad->object;

print_r($arr_obj);
print($arr_obj['msisdn']);

?>