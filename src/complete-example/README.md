# Complete Integration Guide

![tunl-embedded-final-demo](https://user-images.githubusercontent.com/2927894/230153145-2dfb6ac8-7194-4282-87df-27da687d58aa.gif)

## Overview

### Obtain API Keys

To begin, make sure you have your Tunl Account and API Keys ready.  For more info see the [Pre-Reqs](https://github.com/CKC-Technologies/tunl-embedded-payment-form#pre-reqs) Section in our main readme.

### Integration Options

This embeddable form is able to be integrated in one of 2 main ways:

- SSR Server Side Rendered (no front end library required)
- Hybrid
  - Generate an embeddable URL to use in an iframe
  - Use the `tunl-embed.js` frontend library to interact on the client
  
In this guide we will focus on the Hybrid approach.  Our [Main Readme](https://github.com/CKC-Technologies/tunl-embedded-payment-form#tunl-embeddable-form-documentation) and our basic [index.php](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/index.php) has more details on the SSR technique.

## Concepts

In the hybrid approach, there are 5 main components that are required:

- The Server Side Tunl SDK: [tunl-embed-sdk.php](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/tunl-embed-sdk.php)
- Your Server Side Endpoint: [create.php](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/complete-example/create.php)
- Your Frontend Markup: [index.html](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/complete-example/index.html)
- The Frontend Tunl SDK Library (ie: [tunl-embed-sdk.js](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/tunl-embed-sdk.js) )
- Your Frontend Itegration Script: [checkout.js](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/complete-example/checkout.js)


The links in the list above are to the complete example code in this directory.  This directory is good for a quick start if you don't want to completely build from scratch.  The rest of this guide is how to start from scratch.

## Bare Minimum

Let's start with the bare minimum for each component (using PHP as our server language). All other frontend code works regardless of your chosen backend framework or language.

### Step 1 - Download Tunl SDK

Download the [tunl-embed-sdk.php](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/tunl-embed-sdk.php) and place in the same folder as the following `create.php` file or in your include path.

### Step 2 - Create Your Server Endpoint

`create.php`

```php
<?php

// require SDK
require_once("./tunl-embed-sdk.php");

// create new SDK instance
$tunl_sdk = new TunlEmbedSDK;

// set configuration options
$tunl_form_options = array(
    "api_key" => "apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "secret" => "xxxxxxxxxxxxxxxxxxxxxxxxxx",
    "iframe_referer" => "https://localhost:8082/",
    // "tunl_sandbox" => true, // required if using test api keys
    "allow_client_side_sdk" => true
);

// get the embeddable form url
$form = $tunl_sdk->get_form_url($tunl_form_options);

// respond to the request appropriately, 
// here we have chosen a JSON body response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($form);

?>
```

The options configured above leverage a lot of default parameters.  In particular, if no `payment_data` is provided, the embedded form will present a card holder name field in addition to the card number, expiration, and cv.  Submitting the form will process a `verify` only action.  `sale` and `preaiuth` actions are also available.  This is described in more detail in the options documentation in our main readme link below:

[View all available configuration options here](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/README.md#all-available-options)

### Step 3 - Create Your Frontend Markup

`index.html`

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Bare Minimum Demo</title>
    
    <!-- include the Tunl Frontend SDK -->
    <script src="https://test-payment.tunl.com/embed/assets/tunl-embed-sdk.js"></script>

    <!-- include Your Frontend Integration Script (and set "defer") -->
    <script src="checkout.js" defer></script>
</head>

<body>
    <h1>Bare Minimum Demo</h1>

    <iframe id="tunl-frame"></iframe>
    <button style="display: block;">Verify Card</button>

</body>

</html>
```

### Step 4 - Create Your Frontend Integration Script

`checkout.js`

```javascript
(async function () {
  // create new TunlEmbed SDK instance
  const tunl = new TunlEmbed();

  // tell the Tunl SDK about Your Server Side endpoint url
  await tunl.getFrameURL("create.php");

  // mount the embedded form in the iframe
  await tunl.mount("#tunl-frame");

  // create a button click handler
  document.querySelector("button").addEventListener("click", async () => {

    // request a form submission and capture the results
    const results = await tunl.submit().catch((err) => err);

    // handle success or failure to your liking
    if (results.status === "SUCCESS") console.log("SUCCESS", results);
    if (results.status !== "SUCCESS") console.log("ERROR", results);
    
  });
})();
```

### Step 5 - Test the results

At this point if you open your browser and point it to your `index.html` you should have a working payment form.  You can test this by clicking the `Verify Card` button.  An empty form should show some validation about the fields being required and the `tunl.submit()` call should return a response object for you to consume in your client side script.  Additionally filling the form out with valid card info should return a `SUCCESS` type response as shown in the images below.

<table>
  <tr>
    <td><a target="_blank" rel="noopener noreferrer nofollow" href="https://user-images.githubusercontent.com/2927894/230185387-4055f303-f6bb-45bf-81c7-edef3f15545e.png"><img src="https://user-images.githubusercontent.com/2927894/230185387-4055f303-f6bb-45bf-81c7-edef3f15545e.png" alt="image" style="max-width: 100%;"></a></td>
    <td><a target="_blank" rel="noopener noreferrer nofollow" href="https://user-images.githubusercontent.com/2927894/230185387-4055f303-f6bb-45bf-81c7-edef3f15545e.png"><img src="https://user-images.githubusercontent.com/2927894/230192677-d89b4178-211e-41f4-89cd-c71c2341703b.png" alt="image" style="max-width: 100%;"></a></td>
    <td><a target="_blank" rel="noopener noreferrer nofollow" href="https://user-images.githubusercontent.com/2927894/230185387-4055f303-f6bb-45bf-81c7-edef3f15545e.png"><img src="https://user-images.githubusercontent.com/2927894/230195511-d1f1f4a6-d305-472b-a7f5-91ececa1ddac.png" alt="image" style="max-width: 100%;"></a></td>
  </tr>
</table>

---

### Response Format

The response returned from `await tunl.submit()` has the following structure:

```json
// successful response
{
    "status": "SUCCESS",
    "msg": "Card was successfully verified.",
}

// error response
{
    "error": "FORM_NOT_VALID",
    "msg": "Form entry is not valid, please correct errors",
}
```

Checking for errors can be quite simple:

```javascript
// request a form submission and capture the results
const results = await tunl.submit().catch((err) => err);

// handle success or failure to your liking
if (results.status === "SUCCESS") console.log("SUCCESS", results);
if (results.status !== "SUCCESS") console.log("ERROR", results);
```

or if you prefer the `try-catch` pattern:
```javascript
try {
  const results = await tunl.submit()
  console.log(results)
}catch (e) {
  console.log(e)
}
```

---

### Full Success Response Example

```json
{
    "status": "SUCCESS",
    "msg": "Card was successfully verified.",
    "embedded_form_action": "verify",
    "transaction_ttid": "309492984",
    "transaction_amount": "0.01",
    "transaction_authnum": "962236",
    "transaction_timestamp": "2023-04-05 20:33:12 +0000",
    "transaction_ordernum": "1680726671",
    "transaction_type": "PREAUTH",
    "transaction_phardcode": "SUCCESS",
    "transaction_verbiage": "APPROVED",
    "vault_token": "0f2c75ae-2817-437b-ac0c-f71b0590095f",
    "webhook_response": [],
    "void_ttid": "309492984",
    "void_phardcode": "SUCCESS",
    "void_verbiage": "SUCCESS"
}
```

The full response includes several important details about the transaction that was processed, including your webhook response if you opt to do so (not required).

#### It is recommended to save this entire response in your database for future reference.

The `vault_token` enables you to use the Tunl Vault API to [charge the stored card at a later date and time](#charging-a-card-using-a-vault-token) without storing any sensitive card data on your own servers/databases. 

Likewise, if running a `preauth` type transaction the `transaction_ttid` is required to [complete the pre-auth and process the transaction.](#completing-a-pre-auth-transaction)  

Additionally the transaction id's `transaction_ttid` and `void_ttid` can be helpful references if there are any issues with specific transactions.


# Charging a card using a vault token

Coming Soon

# Completing a Pre Auth Transaction

Coming Soon
