<?php

require_once('../secrets.php');
require_once("../tunl-embed-sdk.php");
$tunl_sdk = new TunlEmbedSDK;

$tunl_form_options = array(
    "api_key" => $tunl_api_key,
    "secret" => $tunl_secret,
    "iframe_referer" => "https://localhost:8082/",
    "tunl_sandbox" => true,
    "custom_style_url" => "https://localhost:8082/complete-example/custom-embed.css",
    "allow_client_side_sdk" => true
);

$form = $tunl_sdk->get_form_url($tunl_form_options);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($form);
exit();

?>