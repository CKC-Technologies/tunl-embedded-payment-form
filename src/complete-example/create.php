<?php

require_once('../secrets.php');
require_once("../tunl-embed-sdk.php");
$tunl_sdk = new TunlEmbedSDK;

$payment_data = array(
    'amount' => '123.45',
    'cardholdername' => 'x', // temporary value to disable card holder name field
    'action' => 'verify', // could be sale, preauth, or verify
    // 'ordernum' => 'My Custom Reference: ' . time(),
    // 'comments' => 'My Custom Comments',
    // 'street' => '2200 Oak St.',
    // 'zip' => '49203',
);

$tunl_form_options = array(
    "api_key" => $tunl_api_key,
    "secret" => $tunl_secret,
    "iframe_referer" => "https://localhost:8082/",
    "tunl_sandbox" => true,
    "payment_data" => $payment_data,
    "allow_client_side_sdk" => true,
    // "web_hook" => "https://localhost:8082/web_hook.php",
);

$form = $tunl_sdk->get_form_url($tunl_form_options);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($form);
exit();

?>