<?php

// get json payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// if this web hook is called with a transaction error
if ($data['status'] !== 200){
    // perform your own custom error processing
    // optionally respond with custom response
    handleErr($data);
    exit();
}

// do stuff with the data
// at minimum you will likely want to store the following items
// in your database to be able to perform future actions
$transaction_id = $data["transaction_ttid"];
$vault_id = $data["vault_token"];
$orderNum = $data["transaction_ordernum"];

$some_potential_error_inside_the_webhook = false;
// handle any errors in your own code
if ($some_potential_error_inside_the_webhook){

    // respond with any code other than 200
    // Tunl API will attempt to void the transaction.
    http_response_code(400);
    handleErr(array("test" => "test"));
    exit();
}

// returned data is passed thru back to the client
echo json_encode(array(
    "status" => "SUCCESS",
    "msg" => "Your Success Message",

    // you can disable the standard response if you want full control. 
    // (Or set this in the createUrl Options)
    // 'only_return_webhook_response_to_client' => true,
    
    "data" => [ /* YOUR WEBHOOK RESPONSE DATA GOES HERE */ ]
));

function handleErr($data){
    echo json_encode(array(
        "status" => "ERROR",
        "msg" => "Your Error Message",

        // Example only: be careful about passing unhandled error data back to the client.
        // https://cheatsheetseries.owasp.org/cheatsheets/Error_Handling_Cheat_Sheet.html
        "data" => $data 
    ));
}

?>
