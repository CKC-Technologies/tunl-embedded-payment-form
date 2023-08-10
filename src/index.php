<?php
require_once('./secrets.php');
require_once("./tunl-embed-sdk.php");
$tunl_sdk = new TunlEmbedSDK;

$tunl_form_options = array(
    "api_key" => $tunl_api_key, // from secrets.php
    "secret" => $tunl_secret,   // from secrets.php
    "iframe_referer" => "https://localhost:8082/",
    "tunl_sandbox" => true, // set this if using a test tunl account
);

$form = $tunl_sdk->get_form_url($tunl_form_options);

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
