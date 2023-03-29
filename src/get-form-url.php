<?php
/*  This file is an example of how to create an endpoint
    that receives parameter via a JSON POST body.
    The parameters that can be passed in here are

    'cardholdername' 
    'ordernum' 
    'comments' 
    'street' 
    'zip'

    This allows you to present the user with a form that
    collects these details and then generate an embeddable form
    that has all the appropriate details about the transaction.

    This is very similar to the way the "Create Payment Intent"
    Flow works in Stripe.
*/

if ($_SERVER['REQUEST_METHOD'] !== "POST")
    return method_not_allowed();

require_once('./secrets.php');
require_once("./ideposit-embed-sdk.php");
$ideposit_sdk = new iDeposit_SDK;

// get json payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$amount = get_amount_from_order($data['ordernum']);

$payment_data = array(
    'amount' => $amount,
    'cardholdername' => $data['cardholdername'] ?? null,
    'action' => 'preauth',
    'ordernum' => $data['ordernum'] ?? null,
    'comments' => $data['comments'] ?? null,
    'street' => $data['street'] ?? null,
    'zip' => $data['zip'] ?? null,
);

$tunl_form_options = array(
    "api_key" => $tunl_api_key,
    "secret" => $tunl_secret,
    "iframe_referer" => "https://localhost:8082/",
    "tunl_sandbox" => true,
    "payment_data" => $payment_data,
    // "web_hook" => "https://localhost:8082/web_hook.php",
    "custom_style_url" => "https://localhost:8082/custom-embed.css",
    "debug_mode" => true,
    "verify_only" => true // true is actually the default value
);

$form = $ideposit_sdk->get_form_url($tunl_form_options);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($form);

function get_amount_from_order($ordernum){
    // do something to get the payment amount from your database or backend
    // this prevents abuse of this endpoint and protects against bad actors setting their own amount
    
    // $amount = fetch_from_db($ordernum);
    // return $amount;
    return "123.45";
}

function method_not_allowed()
{
    http_response_code(405);
    echo "Method Not Allowed";
    exit();
}
?>