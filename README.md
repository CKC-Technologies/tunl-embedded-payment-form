# Tunl Embeddable Form Documentation

![image](https://user-images.githubusercontent.com/2927894/228584754-deded60e-5a15-41da-9712-f5cb25db3d4f.png)

The code in this repo currently uses PHP but could very easily be ported into other languages.  Eventually, there will be more code examples and samples in this repo that demonstrate use in other languages.

## Contents

- [Pre-Reqs](#pre-reqs)
- [Quick Start](#quick-start)
- [Process Overview](#process-overview)
  - [Security Warning](#security-warning-)
  - [Peek under the hood](#a-peek-under-the-hood)
- [All Available Options](#all-available-options)
  - [Tunl Form Options](#tunl-form-options)
  - [Payment Data Options](#payment-data-options)
- [COMPLETE EXAMPLE](#complete-example)
  - [Client Side HTML](#client-side-html)
  - [Client Side Javascript](#client-side-javascript)
  - [PHP Backend](#php-backend)
- [Troubleshooting](#troubleshooting)

# Pre-Reqs

Before attempting to embed our hosted payment form in your web application,
you will need an account on our Tunl merchant platform.  https://tunl.com/contact/

Already have an account? Here are some quick links to create API Keys.

- https://merchant.tunl.com/merchant/settings (Production Accounts)
- https://test.tunl.com/merchant/settings (Test Accounts ONLY)

Once you have an account, you will need to log in to your Tunl dashboard (links provided above) and create an API Key and Secret.  
To create your keys, navigate to your Settings page by clicking on the gear icon in the upper left menu bar. Scroll down and select Create API Key.

IMPORTANT: Copy and save your Secret. Your Secret will be inaccessible once you navigate away from this page. If this happens, simply create another set of keys, and delete the inaccessible keys.

# Quick Start

This repo is setup with docker and docker-compose.  You can quickly get started by cloning this repository to your local dev environment and running:

```bash
docker-compose up
```

Once running, you can update the `src/secrets.php` file with your
Tunl API Key and Secret.

Then you should be able to navigate to either:
- https://localhost:8082/
- https://localhost:8082/kitchen-sink.php
- https://localhost:8082/client-side-example.php

These files can be found in the `src` folder respectively
- [`src/index.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/index.php)
- [`src/kitchen-sink.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/kitchen-sink.php)
- [`src/client-side-example.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/client-side-example.php)

However, keep in mind the webhook functionality is not able to be tested when using the localhost.

If you want to test the webhook feature using the quickstart docker-compose approach, you will need to run it in an environment that is publicly available, behind SSL and a domain you have control of.

Alternatively, you could point the webhook setting directly to a public endpoint that is not in this project.  Take a look at the [`src/web_hook.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/web_hook.php) file for more info on how to structure your webhook to receive data back from the form.

# Process Overview

BARE MINIMUM Steps involved:
- Craft the options to customize the embedded form
- Generate a unique URL (similar to Stripe's "Create Payment Intent")
- Use the generated url in an iframe

Condensed Example in PHP:

```php
<?php
require_once("./ideposit-embed-sdk.php");
$ideposit_sdk = new iDeposit_SDK;

$tunl_form_options = array(
    "api_key" => "apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "secret" => "xxxxxxxxxxxxxxxxxxxxxxxxxx",
    "iframe_referer" => "https://localhost:8082/",
);

$form = $ideposit_sdk->get_form_url($tunl_form_options);

echo $form['url'];
?>
```

The above code could be called from a client side fetch call to retreive the unique url and then dynamically render the iframe.  This code could also be modified to accept a JSON body that would allow some custom options to be passed in.  

#### !!! Security Warning !!!

> Keep in mind, this is potentially a sensitive operation and you should review for secure implementation.  For example, the `iframe_referer` should always be a statically set value that is a domain you own.  

> It should *_NOT_* be allowed to be set dynamically via JSON options passed in.  This parameter helps to ensure that the form is ONLY allowed to be embedded on your site/application.

Alternatively you could modify this code to be completely Server Side Rendered.  Checkout [`src/index.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/index.php) for an example that uses this technique.

### A Peek Under the Hood

The [`ideposit-embed-sdk.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/ideposit-embed-sdk.php) is nothing fancy at present.  It just contains all the boilerplate to do CURL calls and a wrapper method to get the form url.  To illustrate, here is a command line version of the CURL call being made by

`$ideposit_sdk->get_form_url($tunl_form_options)`

```bash
curl -X POST https://test-payment.tunl.com/embed/get-card-form-url.php \
   -H 'Content-Type: application/json' \
   -d '{"api_key":"apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx","secret":"xxxxxxxxxxxxxxxxxxxxxxxxxx","iframe_referer":"https://localhost:8082/"}'
```

# All Available Options

Below are all of the available options.

The only ones that are required are:
- `api_key` - Your Tunl API Key
- `secret`  - Your Tunl API Secret
- `iframe_referer` - Your Domain URL

```php

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
    "api_key" => "apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "secret" => "xxxxxxxxxxxxxxxxxxxxxxxxxx",
    "iframe_referer" => "https://localhost:8082/",
    "tunl_sandbox" => true,
    "payment_data" => $payment_data,
    "web_hook" => "https://localhost:8082/web_hook.php",
    "custom_style_url" => "https://localhost:8082/custom-embed.css",
    "debug_mode" => true,
    "verify_only" => true
);

```

All other parameters are optional but allow much more control over the output.

#### Tunl Form Options

<table>
    <thead>
        <tr>
            <th>Param</th>
            <th>Default</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>api_key</td>
            <td>null</td>
            <td>Your Tunl API Key</td>
        </tr>
        <tr>
            <td>secret</td>
            <td>null</td>
            <td>Your Tunl API Secret</td>
        </tr>
        <tr>
            <td>iframe_referer</td>
            <td>null</td>
            <td>Your Domain URL. ie: https://your.domain.com <br> This must be set to the domain you intend to host the embedded form on.</td>
        </tr>
        <tr>
            <td>tunl_sandbox</td>
            <td>false</td>
            <td>Selects the tunl api environment. <br> true = https://test-api.tunl.com <br> false = https://api.tunl.com <br><br> If you created your API keys using a test merchant account via https://test.tunl.com instead of https://merchant.tunl.com then make sure to set this parameter to <code>true</code></td>
        </tr>
        <tr>
            <td>payment_data</td>
            <td>[]</td>
            <td>Type: PHP Associative Array. See example in code snippet above under <code>$payment_data</code> <br> Additional Data to post to the tunl payments endpoint.  See below for info on the available options. </td>
        </tr>
        <tr>
            <td>web_hook</td>
            <td>null</td>
            <td>A url of the endpoint that you own/control to be called upon successful Tunl Payments API submission. <br> See <a href="https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/web_hook.php"><code>src/web_hook.php</code></a> for an example web hook.  </td>
        </tr>
        <tr>
            <td>custom_style_url</td>
            <td>null</td>
            <td>A url to your own custom stylesheet that will be used in the embedded form. <br> See <a href="https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/custom-embed.css"><code>src/custom-embed.css</code></a> for an example stylesheet.</td>
        </tr>
        <tr>
            <td>debug_mode</td>
            <td>false</td>
            <td>If set to true, puts PHP in an extreme error reporting mode.  Additional data will be displayed related to the embeded form as well. For example: instead of seeing a success page, you will see a prettified JSON object of all the transaction response data and any response returned by the web_hook</td>
        </tr>
        <tr>
            <td>verify_only</td>
            <td>true</td>
            <td>This parameter controls whether or not the transaction is immediately voided.  The default behavior is set to <code>true</code> and will immediately void transactions directly after creating them. This behavior allows you to verify user card data without commiting to charges.  If you would like to commit to the actual transaction then set this parameter to <code>false</code>. <br> Keep in mind that <code>preauth</code> transactions still require an additional step to complete the authorization and convert them to a sale.</td>
        </tr>
    </tbody>   
</table>

#### Payment Data Options

<table>
    <thead>
        <tr>
            <th>Param</th>
            <th>Default</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>amount</td>
            <td>"0.01"</td>
            <td>The amount to be charged (or pre-authorized)</td>
        </tr>
        <tr>
            <td>cardholdername</td>
            <td>null</td>
            <td>The name printed on the physical credit card.</td>
        </tr>
        <tr>
            <td>action</td>
            <td>"preauth"</td>
            <td>The type of payment transaction to post. This can be <code>preauth</code>, <code>sale</code>, or <code>return</code></td>
        </tr>
        <tr>
            <td>ordernum</td>
            <td>null</td>
            <td>An Order Number to add as a reference to this transaction.  If left blank the Tunl API will create its own order number.</td>
        </tr>
        <tr>
            <td>comments</td>
            <td>null</td>
            <td>Any freeform comments you would like to add to this transaction.</td>
        </tr>
        <tr>
            <td>street</td>
            <td>null</td>
            <td>The street of the billing address of the card holder.</td>
        </tr>
        <tr>
            <td>zip</td>
            <td>null</td>
            <td>The zip code of the billing address of the card holder.</td>
        </tr>
    </tbody>   
</table>

# Complete Example

A complete example is already available in less than 100 lines of code in the [`src/client-side-example.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/client-side-example.php), but we are going to break that down piece by piece here.

In the "complete example" we create a front end client that has a few fields to gather some info from the customer.  This code will not render a very pretty page, but it cuts right to the core of the intention.

#### Client Side HTML

```html
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

<script src="client-side.js"></script>
```

This HTML will render a form that looks like so:

![image](https://user-images.githubusercontent.com/2927894/228682190-d425278c-e3ab-45b7-a2b6-cdde903f2ddb.png)

In the code above, the User will fill out there details and click the `Make Payment` button.  This button will call some javascript to generate our unique embeddable form url.  We can then udpate the iframe in our mock modal and display it to the User to fill out their credit card details.

#### Client Side Javascript

The javascript in our example `client-side.js` looks like this:

```javascript
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
        const resp = await fetch("",
            {
                method: "POST", 
                body: JSON.stringify(payment_data)
            }
        )
        return await resp.json();
    }
```

The `start` function collects the data from the html input fields and stores them in `payment-data` const.  It then passes this data into the `get_form_url` function that we see just below.  

This function just POST's this data back to the page we are already on (which is actually a php page as can be seen in the full example: [`src/client-side-example.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/client-side-example.php) and then simply returns the parsed JSON directly to the caller.

The `start` function uses these results to update the `src` attribute on the iframe on our html and removes the `display: none` style from our modal.  The User can now see the credit card form as shown in the image below.

![image](https://user-images.githubusercontent.com/2927894/228682312-9c5c8054-f9a5-4534-a90e-3251c8bbc5a0.png)

Not exactly a modal, but you can easily imagine that part!

#### PHP Backend

```php
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

```

The above will look familiar as it is basically a copy and paste of "All Available Options".  The changes that have been made are to be able to receive input via a JSON POST request that takes in the parameters from our HTML form above.

Notice that we are doing a lookup in our own database to set the `amount` field.  This is important to make sure the amount cannot be tampered with by the client performing the request.  The specific implementation here will heavily depend on your own code structure, database, framework, etc; but, the stub function in [`src/client-side-example.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/client-side-example.php) looks like this:

```php
function get_amount_from_order($ordernum){
    // do something to get the payment amount from your database or backend
    // this prevents abuse of this endpoint and protects against bad actors setting their own amount

    // $amount = fetch_from_db($ordernum);
    // return $amount;
    return "123.45";
}
```


# Troubleshooting

### Receive 400 Error: Malformed Request Body

Make sure the request to the `get-card-form-url.php` contains all the following required properties:

- api_key
- secret
- iframe_referer

More troubleshooting coming soon!

