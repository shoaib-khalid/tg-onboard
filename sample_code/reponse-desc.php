<?php

function getResponseDesc($response_code) {

    switch ($response_code) {
        case "0000" : $result = "Successful"; break;
        case "0001" : $result = "Missing required parameters"; break;
        case "0002" : $result = "Password mismatched"; break;
        case "0003" : $result = "User account not exist"; break;
        case "0004" : $result = "Merchant info not exist"; break;
        case "0005" : $result = "Merchant status not approved"; break;
        case "0006" : $result = "Merchant status suspended"; break;
        case "0007" : $result = "Status out of scope"; break;
        case "0008" : $result = "Token key mismatched"; break;
        case "0009" : $result = "Access Not Granted"; break;
        case "0010" : $result = "Amount empty or less than 1"; break;
        case "0011" : $result = "Product registration failed"; break;
        case "0012" : $result = "Ref No more than 40 characters, limit is 40"; break;
        case "0013" : $result = "Insufficient Balance"; break;
        case "0014" : $result = "Shopping cart empty"; break;
        default  : $result = "Unable to be determined response code"; 
    }
    return $result;
}
 
?>