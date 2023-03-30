<?php
require_once('./secrets.php');
require_once("./ideposit-embed-sdk.php");
$ideposit_sdk = new iDeposit_SDK;

$payment_data = array(
    'amount' => '123.45',
    'cardholdername' => 'Card Holder',
    'action' => 'verify', // could be sale, preauth, or return
    'ordernum' => 'My Custom Reference: ' . time(),
    'comments' => 'My Custom Comments',
    'street' => '2200 Oak St.',
    'zip' => '49203',
);

$tunl_form_options = array(
    "api_key" => $tunl_api_key, // from secrets.php
    "secret" => $tunl_secret,   // from secrets.php
    "iframe_referer" => "https://localhost:8082/",
    "tunl_sandbox" => true,
    "payment_data" => $payment_data,
    // can't test webhooks with localhost
    // "web_hook" => "https://localhost:8082/web_hook.php",
    "custom_style_url" => "https://localhost:8082/custom-embed.css",
    "debug_mode" => true,
    "verify_only" => true // true is actually the default value
);

$form = $ideposit_sdk->get_form_url($tunl_form_options);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Test</title>
    <link href="test-style.css" rel="stylesheet" />
</head>

<body>
    <h5>Please Enter Credit Card Details Below</h5>
    <iframe src="<?php echo $form['url']; ?>"></iframe>
</body>

</html>
