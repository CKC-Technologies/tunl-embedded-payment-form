<?php

// get json payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// do stuff with the data - full example reference below
// at minimum you will likely want to store the following items
// in your database to be able to perform future actions
$transaction_id = $data["transaction_ttid"];
$vault_id = $data["vault_token"];
$orderNum = $data["transaction_ordernum"];

// if there is an error you can respond with any error code
// if the status code is not 200 the embedded form api
// will attempt to void the transaction.
if ($some_potential_error){
    http_response_code(500);
    exit();
}

// returing data is not required 
// will ONLY be displayed when using 'debug_mode'
echo json_encode($data);

// FULL RESPONSE EXAMPLE/REFERENCE:
//$data = {
//    "status": "SUCCESS",
//    "msg": "Card was successfully verified.",
//    "embedded_form_action": "verify",
//    "transaction_ttid": "309574334",
//    "transaction_amount": "0.01",
//    "transaction_authnum": "522169",
//    "transaction_timestamp": "2023-04-06 13:26:05 +0000",
//    "transaction_ordernum": "ClientSetOrderNum",
//    "transaction_type": "PREAUTH",
//    "transaction_phardcode": "SUCCESS",
//    "transaction_verbiage": "APPROVED",
//    "vault_token": "088acc40-c28f-4084-a3d2-b801b9c4fccb",
//    "webhook_response": [],
//    "cardholdername": "Testing Client Set",
//    "street": "client set street",
//    "zip": "49203",
//    "comments": "client set comments",
//    "void_ttid": "309574334",
//    "void_phardcode": "SUCCESS",
//    "void_verbiage": "SUCCESS"
// }


?>
