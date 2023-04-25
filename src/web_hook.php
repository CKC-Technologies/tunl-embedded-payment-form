<?php

// get json payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// if this web hook is called with a transaction error
if ($data['status'] !== "SUCCESS"){
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

// handle any errors in your own code
if ($some_potential_error_inside_the_webhook){

    // respond with any code other than 200
    // Tunl API will attempt to void the transaction.
    http_response_code(500);
    exit();
}

// returned data is passed thru back to the client
$newData = array(
    // you can disable the standard response if you want full control.
    'only_return_webhook_response_to_client' => true,
    'other_data' => $data
);
echo json_encode($newData);

function handleErr($data){
    echo json_encode(array(
        "some_custom" => "error response",

        // Example only: be careful about passing unhandled error data back to the client.
        // https://cheatsheetseries.owasp.org/cheatsheets/Error_Handling_Cheat_Sheet.html
        "data" => $data 
    ));
}

?>
