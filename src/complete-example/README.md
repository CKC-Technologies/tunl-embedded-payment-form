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

At this point if you open your browser and point it to your `index.html` you should see this result:

![image](https://user-images.githubusercontent.com/2927894/230185387-4055f303-f6bb-45bf-81c7-edef3f15545e.png)
