# Complete Integration Guide

![integrate-form](https://user-images.githubusercontent.com/2927894/230431161-14f1f7c3-e418-4bae-ad0b-619884ed2d17.gif)

# Table of Contents

- [Overview](#overview)
- [Components/Concepts](#components)
- [Starting Steps](#starting-steps)
  - [Step 1 - Download Tunl SDK (Optional)](#step-1---download-tunl-sdk-optional)
  - [Step 2 - Create Your Server Endpoint](#step-2---create-your-server-endpoint)
    - [ALTERNATIVE - SKIP THE SDK - GENERIC CURL EXAMPLE](#alternative---skip-the-sdk---generic-curl-example)
  - [Step 3 - Create Your Frontend Markup](#step-3---create-your-frontend-markup)
  - [Step 4 - Create Your Frontend Integration Script](#step-4---create-your-frontend-integration-script)
  - [Step 5 - Test the Results](#step-5---test-the-results)
- [Form Submission JSON Response Format](#response-format)
  - [Full Success Response Example](#full-success-response-example)
- [Going Further - Make it pretty!](#going-further)
  - [Adding CSS](#adding-css)
  - [Adding Message Divs](#adding-message-divs)
  - [Update your integration script](#update-your-integration-script)
  - [Results](#results)
- [Integrating with your own form](#integrating-with-your-form)
  - [HTML Additions](#html-additions)
  - [CSS Additions](#css-additions)
  - [Remove card holder name field from embedded form](#remove-card-holder-name-field-from-embedded-form)
  - [Update your integration script to use your data](#update-the-integration-script-to-include-your-data)
- [Creating a SALE action type form](#creating-a-sale-action-type-form)
- [Creating a PREAUTH action type form](#creating-a-preauth-action-type-form)
- [Charging a card using a Vault Token](#charging-a-card-using-a-vault-token)
  - [Use the SDK!](#use-the-sdk)
- [Completing a PREAUTH Transaction](#completing-a-pre-auth-transaction)
  - [Use the SDK!](#use-the-sdk-1)

## Overview

### Obtain API Keys

To begin, make sure you have your Tunl Account and API Keys ready.  For more info see the [Pre-Reqs](https://github.com/CKC-Technologies/tunl-embedded-payment-form#pre-reqs) Section in our main readme.

## Components

In this guide, there are 5 main components:

- The Server Side Tunl SDK (optional): [tunl-embed-sdk.php](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/tunl-embed-sdk.php)
- Your Server Side Endpoint: [create.php](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/complete-example/create.php)
- Your Frontend Markup: [index.html](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/complete-example/index.html)
- The Frontend Tunl SDK Library
- Your Frontend Itegration Script: [checkout.js](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/complete-example/checkout.js)

The links in the list above are to the complete example code in this directory.  This directory is good for a quick start if you don't want to completely build from scratch.  The rest of this guide is how to start from scratch.

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

## Starting Steps

Let's start with the bare minimum for each component (using PHP as our server language). All other frontend code works regardless of your chosen backend framework or language.

> Quick Start Tip: If you have docker and docker-compose setup on your dev machine, you can either clone this whole repo and run `docker-compose up` and begin experimenting... or if you would like a quick start from scratch we provide an [empty starter release](https://github.com/CKC-Technologies/tunl-embedded-payment-form/releases/tag/empty-docker-starter-2) that is just the docker files and an empty `src` directory for you to mold to your liking.

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Step 1 - Download Tunl SDK (Optional)

The Server Side SDK is optional as it is only a wrapper around the usual curl/fetch boilerplate.  If you already have your own solution for making POST requests, then refer to the [ALTERNATIVE - SKIP THE SDK](#alternative---skip-the-sdk---generic-curl-example) Example below

Download the [tunl-embed-sdk.php](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/tunl-embed-sdk.php) and place in the same folder as the following `create.php` file or in your include path.

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Step 2 - Create Your Server Endpoint

Your server endpoint is an important step as this is the beginning of a secure payment process.  

Stripe has the concept of creating a `payment intent`.  This is identical to that concept for all intents and purposes.  

The key difference here is our concept is centered around the idea of requesting a unique, one time use URL, that can be loaded into an iframe that is hosted on your site and domain.  This unique, one time use URL, will render a payment form (or hosted fields) that is/are connected to your Tunl account.  This form can be just the credit card fields (account no, expiration, cvv) or it can optionally host a card holder name field if you so choose.  You have full control over all the final output, including the ability to customize the style via your own CSS. 

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

// get the embeddable form url (similiar to Stripe's create payment intent)
$tunl_client_secrets = $tunl_sdk->get_form_url($tunl_form_options);

// respond to the request appropriately using JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($tunl_client_secrets);

?>
```

### ALTERNATIVE - SKIP THE SDK - GENERIC CURL EXAMPLE

```bash
#!/bin/bash

# Production URL
# API_URL="https://payment.tunl.com/embed/get-card-form-url.php"

API_URL="https://test-payment.tunl.com/embed/get-card-form-url.php"
API_KEY="apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx"
SECRET="xxxxxxxxxxxxxxxxxxxxxxxxxx"

curl -X POST $API_URL \
-H 'Content-Type: application/json; charset=utf-8' \
--data-binary @- << EOF
{
    "api_key": "$API_KEY",
    "secret": "$SECRET",
    "iframe_referer": "https://localhost:8082/",
    "tunl_sandbox": true,
    "allow_client_side_sdk": true
}
EOF
```

The options configured above leverage a lot of default parameters.  In particular, if no `payment_data` is provided, the embedded form will present a card holder name field in addition to the card number, expiration, and cv.  Submitting the form will process a `verify` only action.  `sale` and `preauth` actions are also available.  This is described in more detail in the options documentation in our main readme link below:

[View all available configuration options here](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/README.md#all-available-options)

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

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

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

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

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

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

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Response Format

The response returned from `await tunl.submit()` has the following structure:

```json
// successful response
{
    "status": "SUCCESS",
    "msg": "Card was successfully verified.",
    ...additional data
}

// error response
{
    "error": "FORM_NOT_VALID",
    "msg": "Form entry is not valid, please correct errors",
    ...additional data
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
    "transaction_ttid": "309574334",
    "transaction_amount": "0.01",
    "transaction_authnum": "522169",
    "transaction_timestamp": "2023-04-06 13:26:05 +0000",
    "transaction_ordernum": "ClientSetOrderNum",
    "transaction_type": "PREAUTH",
    "transaction_phardcode": "SUCCESS",
    "transaction_verbiage": "APPROVED",
    "vault_token": "088acc40-c28f-4084-a3d2-b801b9c4fccb",
    "webhook_response": [],
    "cardholdername": "Testing Client Set",
    "street": "client set street",
    "zip": "49203",
    "comments": "client set comments",
    "void_ttid": "309574334",
    "void_phardcode": "SUCCESS",
    "void_verbiage": "SUCCESS"
}
```

The full response includes several important details about the transaction that was processed, including your webhook response if you opt to do so (not required).

#### It is recommended to save this entire response in your database for future reference.

The `vault_token` enables you to use the Tunl Vault API to [charge the stored card at a later date and time](#charging-a-card-using-a-vault-token) without storing any sensitive card data on your own servers/databases. 

Likewise, if running a `preauth` type transaction the `transaction_ttid` is required to [complete the pre-auth and process the transaction.](#completing-a-pre-auth-transaction)  

Additionally the transaction id's `transaction_ttid` and `void_ttid` can be helpful references if there are any issues with specific transactions.

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Going Further

So far, our form works, but it is not very pretty and we are not providing our user with any feedback.  Let's add some CSS and some basic message divs to inform the user of errors or success.

### Adding CSS

`checkout.css`

```css
* {
  box-sizing: border-box;
}

body {
  font-family: Arial;
  max-width: 350px;
  margin: 0 auto;
}

#tunl-frame {
  border: none;
  height: 0px;
  overflow: hidden;
  width: 100%;
  transition: all 0.3s;
}

button {
  display: block;
  padding: 10px;
  width: 100%;
  border-radius: 5px;
  border: 1px solid grey;
}

#loader, #error, #success {
  padding: 10px;
  border-radius: 5px;
  width: 100%;
  text-align: center;
  border-radius: 5px;
  margin-bottom: 10px;
}

#loader {background-color: yellow;}
#error {background-color: red; color:white;}
#success {background-color: lime;}
```

Don't forget to include the CSS in your `index.html`

```html
<head>
  ...
  <link rel="stylesheet" href="checkout.css" />
  ...
</head>
```

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Adding Message Divs

Now let's add a `loading`, `error` and `success` div to our markup.

```diff
<body>
    <h1>Bare Minimum Demo</h1>

+   <div id="loader">Loading Tunl Embedded Form...</div>
+   <div id="success" style="display: none;">Card Successfully Verified</div>
+   <div id="error" style="display: none;">Error</div>

    <iframe id="tunl-frame"></iframe>
    <button style="display: block;">Verify Card</button>

</body>
```

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Update your integration script

And finally, add a few lines to our javascript:

```diff
(async function () {
  // create new TunlEmbed SDK instance
  const tunl = new TunlEmbed();

  // tell the Tunl SDK about Your Server Side endpoint url
  await tunl.getFrameURL("create.php");

  // mount the embedded form in the iframe
  await tunl.mount("#tunl-frame");
  
+ // Hide the loader after iframe is loaded
+ document.getElementById("loader").style.display = "none";

  // create a button click handler
  document.querySelector("button").addEventListener("click", async () => {

+   document.getElementById("error").style.display = "none";

    // request a form submission and capture the results
    const results = await tunl.submit().catch((err) => err);

    // handle success or failure to your liking
-   if (results.status === "SUCCESS") console.log("SUCCESS", results);
+   if (results.status === "SUCCESS") {
+     document.querySelector("button").style.display = "none";
+     document.getElementById("tunl-frame").style.display = "none";
+     document.getElementById("success").style.display = "";
+     document.getElementById("success").innerText = results.msg || "Unknown Success";
+   }

-   if (results.status !== "SUCCESS") console.log("ERROR", results);
+   if (results.status !== "SUCCESS") {
+     document.getElementById("error").style.display = "";
+     document.getElementById("error").innerText = results.msg || "Unknown Error";
+   }
  });
})();
```

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Results

Looks much better!

![baremindemo](https://user-images.githubusercontent.com/2927894/230372241-b4e2babe-ccf3-4ada-9a8d-b22635a451fa.gif)

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Integrating with Your Form

Now that we have some nicer styling and messages to provide the user some feedback.  It's time to look at how we can integrate our own form data with the hosted Tunl payment form.

Suppose you have a form that already includes Card holder name, street, address, zip, etc.  In these cases, you will likely handle these fields on your own and save them in the database along with the transaction response returned from our Tunl payment form.  But you most likely don't want the user to have to fill out the Card holder name twice.  Once in your form and again in the tunl payment form.  Let's look at how we solve this problem.

Let's first add some inputs to our markup and some css to make them look nice.

#### HTML additions:

```diff
<body>
    <h1>Bare Minimum Demo</h1>

    <div id="loader">Loading Tunl Embedded Form...</div>
    <div id="success" style="display: none;">Card Successfully Verified</div>
    <div id="error" style="display: none;">Error</div>

+   <label>Card Holder Name</label>
+   <input name="cardholdername"/>

+   <label>Street</label>
+   <input name="street"/>

+   <label>Zip</label>
+   <input name="zip"/>

+   <label>Comments</label>
+   <input name="comments"/>

    <iframe id="tunl-frame"></iframe>
    <button style="display: block;">Verify Card</button>

</body>
```

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

#### CSS Additions:

```diff
...
...

#loader {background-color: yellow;}
#error {background-color: red; color:white;}
#success {background-color: lime;}

+input, label {
+  display: block;
+}

+input {
+  padding: 10px;
+  border: 1px solid grey;
+  border-radius: 5px;
+  width: 100%;
+  margin-bottom: 10px;
+}
```

Our form now looks like this:

![image](https://user-images.githubusercontent.com/2927894/230427327-2c7dd633-b6c9-4813-84f8-c3477e7b6b94.png)

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

#### Remove Card Holder Name Field from embedded form

Let's remove the card holder name field from the embedded tunl payment form, by making the following changes to `create.php`

```diff
...

// create new SDK instance
$tunl_sdk = new TunlEmbedSDK;

+$payment_data = array(
+    "cardholdername" => "x" // Temporary value to hide this input
+);

$tunl_form_options = array(
    "api_key" => $tunl_api_key,
    "secret" => $tunl_secret,
    "iframe_referer" => "https://ideposit.zwco.cc/",
    // "tunl_sandbox" => true, // required if using test api keys
    "allow_client_side_sdk" => true,
+    "payment_data" => $payment_data
);

```

We now have a form that looks like this:

![image](https://user-images.githubusercontent.com/2927894/230428322-22d9974e-7ae0-49bd-b281-633c65577064.png)

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

#### Update the integration script to include your data

At this point, if we were to submit we would get an error as the `cardholdername` is not valid.  Let's update our client script `checkout.js` to pass the `cardholdername` directly to the iframe via the Tunl Frontend SDK

```diff

  ...

  // create a button click handler
  document.querySelector("button").addEventListener("click", async () => {
    document.getElementById("error").style.display = "none";
    
+   // helper function
+   const getVal = (name) => {
+     return document.querySelector(`[name="${name}"]`).value;
+   };

+   // set additional payment data
+   await tunl.setPaymentData({
+     cardholdername: getVal("cardholdername"),
+     street: getVal("street"),
+     zip: getVal("zip"),
+     comments: getVal("comments"),
+   });

    // request a form submission and capture the results
    const results = await tunl.submit().catch((err) => err);
    
    ...
```

and now we have a complete working form that is integrated with our Embedded Tunl Payment Form!!

![integrate-form](https://user-images.githubusercontent.com/2927894/230431161-14f1f7c3-e418-4bae-ad0b-619884ed2d17.gif)

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Creating a `Sale` Action Type Form

First let's make some modifications to our `create.php` to allow us to look up an order in a fake database.  These fake orders will have a field named `amount`.  We look up the amount of the order on the backend to prevent clients from setting their own prices.  This concept can be applied broadly to any application that needs to process sales.  The general idea is to process and order or cart first, committing it and all of its records of charges needing to be processed to your backend database.  After that, you can direct your user to a payment page that will then either already have this information stored in session variables or can be looked via an order id.

`create.php`

```diff
// create new SDK instance
$tunl_sdk = new TunlEmbedSDK;

+ $fakeOrdersDB = array(
+     "1000" => ['amount' => "123.45"],
+     "2000" => ['amount' => "223.45"],
+     "3000" => ['amount' => "323.45"],
+     "4000" => ['amount' => "423.45"],
+     "5000" => ['amount' => "523.45"],
+     "0" => ['amount' => "0.01"],
+ );
+ $orderID = $_GET['order_id'] ?? "0";
  $payment_data = array(
      "cardholdername" => "x", // Temporary value to hide this input
+     "amount" => $fakeOrdersDB[$orderID]['amount'],
+     "action" => "sale"
  );

// set configuration options
$tunl_form_options = array(
    "api_key" =>
```

Now we can add a url parameter with our order id to our `getFrameURL` call

`checkout.js`

```diff
(async function () {
  // create new TunlEmbed SDK instance
  const tunl = new TunlEmbed();

  // tell the Tunl SDK about Your Server Side endpoint url
- await tunl.getFrameURL("create.php");
+ await tunl.getFrameURL("create.php?order_id=1000");

  // mount the embedded form in the iframe
  await tunl.mount("#tunl-frame");
```

We should also update our button label as we are no longer just verifying the card info, we are actually going to process a sale!!

`index.html`

```diff
    <label>Comments</label>
    <input name="comments" />

    <iframe id="tunl-frame"></iframe>
-   <button style="display: block;">Verify Card</button>
+   <button style="display: block;">Make Payment</button>

</body>
```

Fill out the form and observe the different response we get.

![image](https://user-images.githubusercontent.com/2927894/230539849-80b1992d-828e-4224-8116-e240d7e61793.png)

If we investigate our Merchant Tunl Account under Reports->Unsettled we should see this transaction in the list.

![image](https://user-images.githubusercontent.com/2927894/230540882-1200e0e6-86f6-4355-b305-78bbf2084c25.png)


[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Creating a `PreAuth` Action Type Form

Processing a `preauth` transaction is identical to a `sale` with one teeny tiny adjustment:

`create.php`

```diff
$payment_data = array(
    "cardholdername" => "x", // Temporary value to hide this input
    "amount" => $fakeOrdersDB[$orderID]['amount'],
-   "action" => "sale"
+   "action" => "preauth"
);
```

PreAuth Results:

![image](https://user-images.githubusercontent.com/2927894/230541044-64d3e03e-4ff0-498d-9f2f-0d903c12f498.png)


Merchant Tunl Report:

![image](https://user-images.githubusercontent.com/2927894/230540770-e688a5c0-ddf2-4e31-ae5e-4dab3caf4453.png)


[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Charging a card using a vault token

Get a single use auth token with a 1 minute lifetime (not absolutely necessary, but a GREAT practice)

```bash
curl -X POST \
  'https://test-api.tunl.com/api/auth' \
  --header 'Content-Type: application/json' \
  --data-raw '{
  "username": "apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "password": "xxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "lifespan": 1,
  "scope": "PAYMENT_WRITE",
  "singleuse": true
}'
```

Reponse:

```json
{
  "token": "zzzzzzzzzzzzzzzzzzzzzzzzzzzzz",
  "user": {
  ...
  }
}
```

Use the token to post a vault payment:

```bash
# vault payment endpoint: /api/vault/token/{token}/payments

curl -X POST \
  'https://test-api.tunl.com/api/vault/token/xxxxxxxx-yyyy-zzzz-7777-xxxxxxxxxxxx/payments' \
  --header 'Authorization: Bearer zzzzzzzzzzzzzzzzzzzzzzzzzzzzz' \
  --header 'Content-Type: application/json' \
  --data-raw '{
  "amount": "987.65",
  "action": "sale",
  "ordernum": "asdf"
}'
```

Check your tunl account, you should see this transaction in your unsettle reports

![image](https://user-images.githubusercontent.com/2927894/230546508-069ab20e-4f71-47f4-9fdf-3c2c27534ef5.png)

# Use the SDK!

The SDK turns the multi step process described above into one simple step!  Below is our PHP SDK example:

```php
<?php

// require SDK
require_once("../tunl-embed-sdk.php");

// create new SDK instance
$tunl_sdk = new TunlEmbedSDK;

// set configuration options
$vault_payment_options = array(
    "api_key" => "apikey_b5a04055f48a4cf490665764a459de83",
    "secret" => "55f547c3f5e849f8bc02cbd5093fba7a",
    "vault_token" => $_GET['vault_token'],
    "amount" => $_GET['amount'],
    "ordernum" => $_GET['ordernum']
);

// get the embeddable form url (similiar to Stripe's create payment intent)
$vaultPayment = $tunl_sdk->processSaleWithVault($vault_payment_options);

// respond to the request appropriately using JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($vaultPayment);

?>
```

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Completing a Pre Auth Transaction

Get a multiuse token with a lifespan of 1 minute

Get a single use auth token with a 1 minute lifetime (not absolutely necessary, but a GREAT practice)

```bash
curl -X POST \
  'https://test-api.tunl.com/api/auth' \
  --header 'Content-Type: application/json' \
  --data-raw '{
  "username": "apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "password": "xxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "lifespan": 1,
  "scope": "PAYMENT_WRITE",
}'
```

Reponse:

```json
{
  "token": "zzzzzzzzzzzzzzzzzzzzzzzzzzzzz",
  "user": {
  ...
  }
}
```

Use the token lookup the transaction:

```bash
# get transaction payment endpoint: /api/payments/{transactionId}

curl -X GET \
  'https://test-api.tunl.com/api/payments/3216549870' \
  --header 'Authorization: Bearer zzzzzzzzzzzzzzzzzzzzzzzzzzzzz'
```

Use the token and the previous result to PATCH (Complete pre authorization) the transaction

```bash
# patch transaction payment endpoint: /api/payments/{transactionId}

curl -X PATCH \
  'https://test-api.tunl.com/api/payments/3216549870' \
  --header 'Authorization: Bearer zzzzzzzzzzzzzzzzzzzzzzzzzzzzz' \
  --header 'Content-Type: application/json' \
  --data-raw '{
  "ttid": "309628533",
  "transactionCategory": null,
  "type": "PREAUTH",
  "card": "VISA",
  "account": "XXXXXXXXXXXX1111",
  ...LOTS MORE!
}'
```

# Use the SDK!

The SDK turns the multi step process described above into one simple step!  Below is our PHP SDK example:

```php
<?php

// require SDK
require_once("../tunl-embed-sdk.php");

// create new SDK instance
$tunl_sdk = new TunlEmbedSDK;

// set configuration options
$preauth_payment_options = array(
    "api_key" => "apikey_b5a04055f48a4cf490665764a459de83",
    "secret" => "55f547c3f5e849f8bc02cbd5093fba7a",
    "ttid" => $_GET['ttid']
);

// get the embeddable form url (similiar to Stripe's create payment intent)
$completePreAuth = $tunl_sdk->completePreAuthorization($preauth_payment_options);

// respond to the request appropriately using JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($completePreAuth);

?>
```

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;
