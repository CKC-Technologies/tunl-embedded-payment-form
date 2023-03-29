<?php
if ($_SERVER['REQUEST_METHOD'] === "POST") {
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
        // "debug_mode" => true,
        "verify_only" => true // true is actually the default value
    );

    $form = $ideposit_sdk->get_form_url($tunl_form_options);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($form);
    exit();
}

function get_amount_from_order($ordernum){
    // do something to get the payment amount from your database or backend
    // this prevents abuse of this endpoint and protects against bad actors setting their own amount

    // $amount = fetch_from_db($ordernum);
    // return $amount;
    return "123.45";
}

function method_not_allowed(){
    http_response_code(405);
    echo "Method Not Allowed";
    exit();
}
?>

<style>
    input, button, modal {display: block; margin-bottom: 10px;}
    iframe {height: 500px; border: none;}
</style>

Card Holder Name    <input name="cardholdername" />
Order No            <input name="ordernum" />
Comment             <input name="comments" />
Street              <input name="street" />
Zip                 <input name="zip" />

<!-- example only, do not use inline event handlers like onclick in production -->
<button onclick="start()">Make Payment</button>

<modal style="display: none;">
    <iframe></iframe>
</modal>

<script>
    async function start() {
        const payment_data = {
            cardholdername: document.querySelector('[name=cardholdername]').value,
            ordernum: document.querySelector('[name=ordernum]').value,
            comments: document.querySelector('[name=comments]').value,
            street: document.querySelector('[name=street]').value,
            zip: document.querySelector('[name=zip]').value,
        }

        const form = await get_form_url(payment_data);
        document.querySelector("iframe").src = form.url;
        document.querySelector("modal").style.display = ''
    }

    async function get_form_url(payment_data) {
        const resp = await fetch("/get-form-url.php",
            {
                method: "POST", 
                body: JSON.stringify(payment_data)
            }
        )
        return await resp.json();
    }
</script>