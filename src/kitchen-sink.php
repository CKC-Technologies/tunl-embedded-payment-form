<?php
require_once('./secrets.php');
require_once("./ideposit-embed-sdk.php");
$ideposit_sdk = new iDeposit_SDK;

$payment_data = array(
    'amount' => '123.45',
    'cardholdername' => 'Card Holder',
    'action' => 'preauth', // could be sale, preauth, or return
    'ordernum' => 'My Custom Reference: ' . time(),
    'comments' => 'My Custom Comments',
    'street' => '2200 Oak St.',
    'zip' => '49203',

);

$tunl_form_options = array(
    "api_key" => $tunl_api_key,
    "secret" => $tunl_secret,
    "iframe_referer" => "https://ideposit.zwco.cc/",
    "payment_data" => $payment_data,
    "web_hook" => "https://ideposit.zwco.cc/web_hook.php",
    "custom_style_url" => "https://ideposit.zwco.cc/custom-embed.css?sdf",
    "test_mode" => true
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