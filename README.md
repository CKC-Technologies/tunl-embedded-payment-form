# Tunl Embeddable Form Documentation

![image](https://user-images.githubusercontent.com/2927894/230530809-f0659d75-6509-4e6c-bc32-8c647d00bbcc.png)

The code in this repo currently uses PHP but could very easily be ported into other languages.  Eventually, there will be more code examples and samples in this repo that demonstrate use in other languages.

## Table of Contents

- [Pre-Reqs](#pre-reqs)
- [Quick Start](#quick-start)
- [Complete Integration Guide](https://github.com/CKC-Technologies/tunl-embedded-payment-form/tree/main/src/complete-example)
- [Process Overview](#process-overview)
  - [Security Warning](#-security-warning-)
  - [Peek under the hood](#a-peek-under-the-hood)
- [`/get-card-form-url.php` Options](#embedget-card-form-urlphp-options)
  - [Example curl call](#example-curl-call)
  - [Tunl Form Options](#tunl-form-options)
  - [Payment Data Options](#payment-data-options)
- [`/get-ach-form-url.php` Options](#embedget-ach-form-urlphp-options)
  - [ACH Description](#ach-description)
- [`/update-payment-data.php`](#embedupdate-payment-dataphp)
  - [Important Security Note](#important--you-should-never-pass-this-server_secret-to-the-clientbrowser-this-is-a-temporary-value-that-only-lasts-the-life-of-the-form-but-knowledge-of-the-server-secret-enables-modifying-payment-data-such-as-the-amount-to-be-charged--it-should-only-be-stored-on-your-server-in-some-kind-of-session-variable)
  - [Example curl call](#example-curl-call-1)
- [Tunl Frontend SDK Methods](#tunl-frontend-sdk-methods)
  - [Include Client Side Scripts](#import--install)
  - [`getFrameURL`](#getframeurlurl-string-options-fetchoptions)
  - [`mount`](#mountcssselector-string-options-mountoptions)
  - [`setFocus`](#setfocus)
  - [`setPaymentData`](#setpaymentdatapaymentdata-object)
  - [`checkValidity`](#checkvalidity)
  - [`addEventListener`](#addeventlistenertype-string-listener-function)
  - [`submit`](#submit)
- [Larger Example](#larger-example)
  - [Client Side HTML](#client-side-html)
  - [Client Side Javascript](#client-side-javascript)
  - [PHP Backend](#php-backend)
- [WebHooks](#webhooks)
  - [Overview](#overview)
  - [Enabling Your WebHook](#enabling-your-webhook)
  - [Custom Client Responses](#replacing-the-standard-response-entirely-with-your-own-custom-response)
  - [Example Transaction Data](#example-transaction-data)
  - [Example Error Data](#example-error-data)
  - [Complete WebHook Example](#complete-webhook-example-with-comments)
- [Custom CSS Styling](#custom-css-styling)
  - [Default](#default-styling)
  - [Unstyled](#unstyled)
  - [HTML Structure and Selectors](#html-structure-and-selectors)
  - [Basic Customization](#basic-customization)
  - [Further Improvement](#further-improvement)
  - [Full Reference Default CSS](#full-default-css)
- [Dual Vaulting](#dual-vaulting)
  - [Overview](#overview-1)
  - [Providers](#providers)
    - [RepayOnline](#repayonline)
    - [BridgePay](#bridgepay)
  - [Exception Responses](#exception-responses)
  - [Disabling Tunl Vault](#disabling-tunl-vault)
  - [Provider Order (Priority)](#additional-vault-provider-order)
- [Troubleshooting](#troubleshooting)
  - [400 Malformed Request Body](#receive-400-error-malformed-request-body)
  - [Bad API Key and Secret](#bad-api-key-and-secret-combo)
  - [Unauthorized](#unauthorized)
  - [Access restricted to specific domain](#access-to-this-page-is-restricted-to-specific-domains-and-must-be-embedded-in-an-iframe)
  - [Frame Refused to Connect](#paymenttunlcom-refused-to-connect-and-other-iframe-issues)
  - [Domain is not authorized to embed](#this-domain-is-not-authorized-to-embed-this-page-in-an-iframe)
  - [Card Authentication Failed](#card-authentication-failed)
  - [Bad web hook response](#unable-to-complete-transaction-bad-web-hook-response)

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
- https://localhost:8082/complete-example

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Complete Integration Guide

### [Click here for our complete step by step integration guide](https://github.com/CKC-Technologies/tunl-embedded-payment-form/tree/main/src/complete-example)

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Process Overview

Steps involved:
- Craft the options to customize the embedded form
- Generate a unique URL (similar to Stripe's "Create Payment Intent")
- Use the generated url in an iframe

Condensed Example in PHP:

```php
<?php
require_once("./tunl-embed-sdk.php");
$tunl_sdk = new TunlEmbedSDK;

$tunl_form_options = array(
    "api_key" => "apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "secret" => "xxxxxxxxxxxxxxxxxxxxxxxxxx",
    "iframe_referer" => "https://localhost:8082/",
    "tunl_sandbox" => true, // set this if using a test tunl account api keys
    "allow_client_side_sdk" => true
);

// get the embeddable form url and client secret (similiar to Stripe's create payment intent)
$tunl_client_secrets = $tunl_sdk->get_form_url($tunl_form_options);

// respond to the request appropriately using JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($tunl_client_secrets);
?>
```

This could be called from a client side fetch to retreive the unique url and then dynamically render the iframe.  This code could also be modified to accept a JSON body that would allow some custom options to be passed in. This is demonstrated in our [Complete Example](https://github.com/CKC-Technologies/tunl-embedded-payment-form/tree/main/src/complete-example) 

#### !!! Security Warning !!!

> Keep in mind, this is potentially a sensitive operation and you should review for secure implementation.  For example, the `iframe_referer` should always be a statically set value that is a domain you own.  

> It should *_NOT_* be allowed to be set dynamically via JSON options passed in.  This parameter helps to ensure that the form is ONLY allowed to be embedded on your site/application.

The code above is the bare minimum. This will get you a url to embed the form in an iframe, but there really isn't any context to this form.  The form rendered for the URL generated in the code above will look like this:

![image](https://user-images.githubusercontent.com/2927894/228838375-834b7849-ef64-402b-ab8d-cfbf9d439a6c.png)

This basic form will process a `verify` only transaction for `$0.01` and then immediately void it. This is obviously not very useful except for quick testing to make sure you can connect to your Tunl account.  In the [next section](#all-available-options) we will see how to customize our form and add more context.  Things like, card holder name, amount, transaction type, etc.

>Side Note: You can find this voided preauth under [Settled Reports](https://test.tunl.com/payments/settled) in your Tunl Account.  Sort by timestamp descending and filter by VOID_PREAUTH.

![image](https://user-images.githubusercontent.com/2927894/228840005-b052e7dc-b598-4a43-994e-f54ab1b8a677.png)

Alternatively you could modify this code to be completely Server Side Rendered.  Checkout [`src/index.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/index.php) for an example that uses this technique.

### A Peek Under the Hood

The [`tunl-embed-sdk.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/tunl-embed-sdk.php) is nothing fancy at present.  It just contains all the boilerplate to do CURL calls and a wrapper method to get the form url and client secret.  To illustrate, here is a command line version of the CURL call being made by

`$tunl_sdk->get_form_url($tunl_form_options)`

```bash
curl -X POST https://test-payment.tunl.com/embed/get-card-form-url.php \
   -H 'Content-Type: application/json' \
   -d '{"api_key":"apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx","secret":"xxxxxxxxxxxxxxxxxxxxxxxxxx","iframe_referer":"https://localhost:8082/"}'
```

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# `/embed/get-card-form-url.php` Options

Below are all of the available options.

The only ones that are required are:
- `api_key` - Your Tunl API Key
- `secret`  - Your Tunl API Secret
- `iframe_referer` - Your Domain URL
- `tunl_sandbox` - Not strictly require, but commonly needed during development
- `allow_client_side_sdk` - Not strictly required, but almost always what you want

```php

$payment_data = array(
    'amount' => '123.45',
    'cardholdername' => 'Card Holder',
    'action' => 'verify', // could be sale, preauth, or verify
    'ordernum' => 'My Custom Reference: ' . time(),
    'comments' => 'My Custom Comments',
    'street' => '2200 Oak St.',
    'zip' => '49203',
);

$tunl_form_options = array(
    "api_key" => "apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "secret" => "xxxxxxxxxxxxxxxxxxxxxxxxxx",
    "iframe_referer" => "https://localhost:8082/",
    "allow_client_side_sdk" => true,
    "disable_captcha" => false,
    "tunl_sandbox" => true,
    "payment_data" => $payment_data,
    "web_hook" => "https://localhost:8082/web_hook.php",
    "custom_style_url" => "https://localhost:8082/custom-embed.css",
    "debug_mode" => true,
    "showCardHolderField" => false,
    "showStreetField" => false,
    "showZipField" => false,
);

```

### Example Curl Call:

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

All other parameters are optional but allow much more control over the output.

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# `/embed/get-ach-form-url.php` Options

### ACH Description

The options for the ACH form are identical to the Card Form, the only difference is the endpoint that you call to get an ACH form.

The ACH Form provides several convenience features

- Provides all the legal copy that is required.
- Automatically Creates Contacts if they don't exist (based on email)
- Instant Account Verification via Plaid
- Automatically Adds a Funding Source to an existing contact if exists (base on email)
- Automatically initiates a transfer against the newly added funding source.
- Returns all of the above info for you to store in your own integration to process future transfers against the new funding source.

### Differences vs the Credit Card Form:

Currently this form provides the Initial Onboarding flow and transfer functionality. 
 
Returning customers that have already added a funding source via this form is **NOT SUPPORTED**

Currently as the integrator you will need to provide any "Returning Customer" checkout feature in your application.

We recommened onboarding your customer via your application and storing the funding source details in your database. This will allow you to display those funding sources via your application.  The customer could then select from the list and initiate future transfers.

However, if all that is needed is onboarding the customer/contact and getting a vault token/id that can be used to process future transfers via your integration/service then this form provides exactly everything you need.

If all you need to do is onboard and get a vault id back without initating a transfer you can leave out the `payment_data` key or set it to `null` (Shown below)

### Example ACH Curl Call:

```bash
#!/bin/bash

# Production URL
# API_URL="https://payment.tunl.com/embed/get-ach-form-url.php"

API_URL="https://test-payment.tunl.com/embed/get-ach-form-url.php"
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
    "allow_client_side_sdk": true,
    "payment_data" => null,
}
EOF
```

All other parameters are optional but allow much more control over the output.

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

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
            <td>allow_client_side_sdk</td>
            <td>false</td>
            <td>Allows the embedded form to be interacted with using the Tunl Frontend SDK.  <a href="https://github.com/CKC-Technologies/tunl-embedded-payment-form/tree/main/src/complete-example">Complete Example Available Here</a> </td>
        </tr>
        <tr>
            <td>disable_captcha</td>
            <td>false</td>
            <td> Disables the Captcha System for testing purposes, this switch ONLY works in our TEST environments. </td>
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
            <td id="return_server_token">return_server_token</td>
            <td>false</td>
            <td>If set to true, the info returned from this API call will include a <code>server_secret</code> property.  This server secret can be used to perform updates to the embedded form payment data after it has already been loaded and presented to the user.  <br><br><strong>!!!IMPORTANT!!!  You need to be careful not to pass this secret to the client/browser!!! This is a temporary value that only lasts the life of the form.  It should only be stored on your server in some kind of session variable.</strong> </td>
        </tr>
        <tr>
            <td>show_card_holder_field</td>
            <td>false</td>
            <td>Force our built in Card Holder Name input field to be displayed in our embedded form.  This is helpful, but for full control and customizeability we recommend that you implement your own fields and styling.  You can then set this data using <a href="#setpaymentdatapaymentdata-object"><code>setPaymentData</code></a> in our client library.</td>
        </tr>
        <tr>
            <td>show_street_field</td>
            <td>false</td>
            <td>Force our built in Street input field to be displayed in our embedded form. This is helpful, but for full control and customizeability we recommend that you implement your own fields and styling.  You can then set this data using <a href="#setpaymentdatapaymentdata-object"><code>setPaymentData</code></a> in our client library</td>
        </tr>
        <tr>
            <td>show_zip_field</td>
            <td>false</td>
            <td>Force our built in Zip Code input field to be displayed in our embedded form. This is helpful, but for full control and customizeability we recommend that you implement your own fields and styling.  You can then set this data using <a href="#setpaymentdatapaymentdata-object"><code>setPaymentData</code></a> in our client library</td>
        </tr>
    </tbody>   
</table>

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

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
            <td>The amount to be charged (or pre-authorized). <br><br> Keep in mind that a HOLD for whatever amount you specify here will be applied to the user's card.  If you are just trying to 'verify' that a card is real and can be charged, it is best to leave this setting at the default 0.01.</td>
        </tr>
        <tr>
            <td>cardholdername</td>
            <td>null</td>
            <td>The name printed on the physical credit card.</td>
        </tr>
        <tr>
            <td>action</td>
            <td>"verify"</td>
            <td>The type of payment transaction to post. This can be <code>preauth</code>, <code>sale</code>, or <code>verify</code> <br><br>If <code>verify</code> is set it will run a <code>preauth</code> transaction and immediately void it.  This allows you to verify card holder data without committing to a preauth or sale type transaction. </td>
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

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# `/embed/update-payment-data.php`

This endpoint allows you to update payment data (including `action` and `amount`) for an embedded form after it has already been loaded and displayed to the end user.  In order to use this endpoint, you need the `server_secret` that can optionally be returned by the [`/get-card-form-url.php`](#embedget-card-form-urlphp-options) call.  For this value to be returned in that call you need to set the [`return_server_token`](#user-content-return_server_token) option to `true`.

#### !!!IMPORTANT!!!  You should NEVER pass this `server_secret` to the client/browser!!! This is a temporary value that only lasts the life of the form, but knowledge of the server secret enables modifying payment data such as the `amount` to be charged.  It should only be stored on your server in some kind of session variable.

### Example curl call

```bash
#!/bin/bash

# Production URL
# API_URL="https://payment.tunl.com/embed/update-payment-data.php"

API_URL="https://test-payment.tunl.com/embed/update-payment-data.php"
API_KEY="apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx"
SECRET="xxxxxxxxxxxxxxxxxxxxxxxxxx"

curl -X POST $API_URL \
-H 'Content-Type: application/json; charset=utf-8' \
--data-binary @- << EOF
{
  "server_secret": "bf51fbeccef9df9c49b28c5967ac279b51f5ef9dafbba59a7f7210ad5252c34f03f2f187edb0053d",
  "payment_data": {
    "amount": "1000.10",
    "action": "sale",
    "comments": "set on update endpoint"
  }
}
EOF
```

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Tunl Frontend SDK Methods

We provide an OPTIONAL frontend sdk library to allow for client side interaction.  This allows you to gain even more control over the end user experience and provide a seamless payment form/fields that integrate perfectly with your own applications/solutions.

Our [complete integration guide](https://github.com/CKC-Technologies/tunl-embedded-payment-form/tree/main/src/complete-example) goes into step by step detail using the client library.

## Import / Install

Just include the following script in your head tag:

```html
<script src="https://payment.tunl.com/embed/assets/tunl-embed-sdk.js"></script>
```

For the bleeding edge development version use:

```html
<script src="https://test-payment.tunl.com/embed/assets/tunl-embed-sdk.js"></script>
```

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

## Methods

### `getFrameUrl(URL: string, options: FetchOptions)`

#### Description

This method will call your [server end point](https://github.com/CKC-Technologies/tunl-embedded-payment-form/tree/main/src/complete-example#step-2---create-your-server-endpoint) to retrieve the unique iframe url and client secret.

If you do not want to use this method, you can call the endpoint yourself and pass the data directly into the `mount` function via the `options` argument.  See `mount` function documentation below for more details.

#### Params

```
URL: string - this can be a FQDN and path or a simple relative path

Options: [FetchOptions object](https://developer.mozilla.org/en-US/docs/Web/API/fetch)
```

#### Examples

```javascript
// tell the Tunl SDK about Your Server Side endpoint url
// the following are all valid URL inputs
  await tunl.getFrameURL("create.php");
  await tunl.getFrameURL("/create.php");
  await tunl.getFrameURL("/create.php?order_id=1000");  // can also pass in query params
  await tunl.getFrameURL("relative/path/create.php");
  await tunl.getFrameURL("/absolute/path/create.php");
  await tunl.getFrameURL("https://your.domain.com/create.php");
  
  // Example with options
  await tunl.getFrameURL("create.php", {
    method: "POST",
    headers: myHeaders,
    body: JSON.stringify(data)
  });
```

#### Returns

This method doesn't actually return anything, but the server endpoint that it calls should return an object the looks like the one below.  The frontend library automatically handles this information.  There is no need to perform any intermediate manipulation of this information.

```json
{
    "url": "https://test-payment.tunl.com/embed/load-embedded-form.php?one-time-use-code=e862721da6a0547f39cda1a7ea7475f8268e1ceb8d23b90209dd9a60a78635842f1379275f51c5d8",
    "shared_secret": "07d687b5fd040e61f4af3fa3b13457b8d7d8234f1422f437bd2006ffe56a28671521a814cf06f460",
    "msg": "SUCCESS"
}
```

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### `mount(cssSelector: string, options?: MountOptions)`

#### Description

This will "mount" the embedded form in the iframe.

#### Params

```
cssSelector: string - any valid css selector that can be passed into `document.querySelector`
                       the selected element is expected to be an <IFRAME> node/element
                       
options?: MountOptions -  {
                            url?:               string (iFrame URL),
                            shared_secret?:     string,
                            disableAutoResize?: boolean,
                          }
```

#### Examples

```javascript
 // mount the embedded form in the iframe
  await tunl.mount("#tunl-frame"); // selects an iframe with the id of "tunl-frame"
  await tunl.mount(".tunl-frame"); // selects an iframe with a class of "tunl-frame"
  await tunl.mount("iframe");      // selects the first <iframe> on the page
  
        // how to manually call the create.php endpoint and use the results
  // await tunl.getFrameURL("create.php");
  const fetchResp = await fetch("create.php");
  const frameData = await fetchResp.json();
  await tunl.mount("#tunl-frame", frameData);
  
  // or build the options object yourself:
  await tunl.mount("#tunl-frame", {
    url: frameData.url,
    shared_secret: frameData.shared_secret,
    disableAutoResize: true,  // prevent the iframe from controlling its own height
  });
  
```

#### A note on the `disableAutoResize`

Our iframe disables overflow (scrollbars) to prevent strange CSS bugs from causing them.  We control height internally by default because we have validation messages that drive the height of the iframe larger and smaller depending on current validation state.  If you would like full control over this behavior you can disable it here and listen for `resize` events.  See the `addEventListener` for more info.

#### Returns

NONE

While this method does not return anything, if you use the `await` keyword it will wait to return until the iframe is ready.  This can be helpful for rendering a loading div/image before calling the `mount` method and then hiding the loader immediately after the `mount` method returns.

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### `setFocus()`

#### Description

This will set the focus on the first input inside the payment iframe.

#### Params

NONE

#### Examples

```javascript
  // mount the embedded form in the iframe
  await tunl.mount("iframe");
  
  // then immediately set focus on the first input in the iframe
  await tunl.setFocus();
```

#### Returns

NONE

While this method does not return anything, if you use the `await` keyword it will wait to return until the iframe input focus is ready.  This can be helpful for rendering a loading div/image before calling the `mount` method and then hiding the loader immediately after the `mount` method returns.

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### `setPaymentData(paymentData: object)`

#### Description

This method is used to set payment details such as card holder name, street, zip, and comments directly on the client.  Technically the iframe does make a server side request to update its server side state with the new payment details, but that is all handled for you.  As this is a sensitive operation, the details that are allowed to be updated are limited to the items shown in the example below.

#### Params

All properties of the object are optional

```typescript
interface paymentData {
  cardholdername?: string;
  ordernum?: string;
  comments?: string;
  street?: string;
  zip?: string;
}
```

#### Examples

```javascript
    // helper function to get values from named <input> elements
    const getVal = (name) => {
      return document.querySelector(`[name="${name}"]`).value;
    };

    // set additional payment data
    const results = await tunl.setPaymentData({
      cardholdername: getVal("cardholdername"),
      street: getVal("street"),
      zip: getVal("zip"),
      comments: getVal("comments"),
    });
    
    console.log(results)
```

#### Returns

This method returns a JSON Object.  This isn't particularly useful for anything other than debugging or confirming expectations.  However, it is a good idea to wrap this in your usual error handling strategy as it is a network call under the hood, so of course the usual failure modes are possible.

```json
{
    "status": "SUCCESS",
    "msg": "Successfully updated payment data.",
    "payment_data": {
        "action": "verify",
        "terminalId": 0,
        "ordernum": 1680831905,
        "comments": "comments",
        "amount": "0.01",
        "tax": 0,
        "examount": 0,
        "street": "street",
        "zip": "zip",
        "cv": "",
        "expdate": "",
        "account": "",
        "cardholdername": "Zach",
        "custref": null,
        "clerkid": "iDep Embed Form",
        "autovault": "Y",
        "vaultAccount": true,
        "accountId": null,
        "contactId": null
    }
}
```

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### `checkValidity()`

#### Description

This will check if the payment form inside the iframe is valid or not.  This is not required as the submit function will automatically validate and report errors.  This function is provided to allow for edge cases where your integration may require advanced knowledge of the form's validity before attempting to submit.

#### Params

NONE

#### Examples

```javascript
async function testCheck(){
  const results = await tunl.checkValidity().catch((err) => err);
  console.log(results);
}
```

#### Returns

Success Response:

```json
{
    "status": "SUCCESS",
    "msg": "Form entry is valid."
}
```

Error Response:

```json
{
    "error": "FORM_NOT_VALID",
    "msg": "Form entry is not valid, please correct errors",
    "errors": [
        {
            "input": "account",
            "error": "Field is required"
        },
        {
            "input": "expdate",
            "error": "Field is required"
        },
        {
            "input": "cv",
            "error": "Field is required"
        }
    ],
    "msgID": "db63eebc-ec02-4734-82fe-74801904dfed"
}
```

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### `addEventListener(type: string, listener: function)`

#### Description

This will add an event listener to the tunl payment iframe.  The events that are currently available are:

- `paymentFormBecameValid` - fires when the form is complete and valid
- `paymentFormBecameInvalid` - fires if the form subsequently becomes INVALID
- `resize` - fires when the internal body height of the iframe changes.

#### Params

```
type:       A case-sensitive string representing the event type to listen for.

listener:   The callback function to be fired in response to the event.
```

#### Examples

```javascript
tunl.addEventListener("paymentFormBecameValid", (ev) => console.log(ev))
tunl.addEventListener("paymentFormBecameInvalid", (ev) => console.log(ev))
tunl.addEventListener("resize", (msgData) => {
  document.querySelector('#tunl-frame').style.height = msgData.bodyHeight.toString() + "px";
});
```

#### Listener Callback Arguments

```
event: an object containing basic info from the event
```

Example `paymentFormBecameValid` Event Object:

```json
{
    "event": "paymentFormBecameValid",
    "msg": "Form is complete and valid.",
    "msgID": "event"
}
```

Example `paymentFormBecameInvalid` Event Object

```json
{
    "event": "paymentFormBecameInvalid",
    "msg": "Form is no longer valid!",
    "msgID": "event"
}
```



---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### `submit()`

#### Description

This method will attempt to submit the tunl payment form.  The embedded form has its own client side validation that must pass before it will actually submit.  If validation fails this method will return an error.  Other errors could occur as well, but this one will likely be the most common.  On success it will return transaction information that you should store in your database.  This transaction information is completely sanitized and safe to store.

#### Params

NONE

#### Examples

```javascript
    // request a form submission and capture the results
    const results = await tunl.submit().catch((err) => err);

    // handle success or failure to your liking
    if (results.status === "SUCCESS") {
      document.querySelector("button").style.display = "none";
      document.getElementById("tunl-frame").style.display = "none";
      document.getElementById("success").style.display = "";
    }

    if (results.status !== "SUCCESS") {
      document.getElementById("error").style.display = "";
      document.getElementById("error").innerText =
        results.msg || "Unknown Error";
    }
```

#### Returns

Full Success Response:

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
    "transaction_code": "1",
    "vault_token": "088acc40-c28f-4084-a3d2-b801b9c4fccb",
    "webhook_response": [],
    "cardholdername": "Testing Client Set",
    "street": "client set street",
    "zip": "49203",
    "comments": "client set comments",
    "void_ttid": "309574334",
    "void_phardcode": "SUCCESS",
    "void_verbiage": "SUCCESS".
    "void_code": "1"
}
```

Error response:

```json
{
    "error": "FORM_NOT_VALID",
    "msg": "Form entry is not valid, please correct errors",
    "msgID": "a414b0ab-0502-4c21-8efa-cd1dfb485305"
}
```
---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Larger Example

A full sample of this example is available in less than 100 lines of code in the [`src/client-side-example.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/client-side-example.php), but we are going to break that down piece by piece here.

In this example, we create a front end client that has a few fields to gather some info from the customer.  This code will not render a very pretty page, but it cuts right to the core of the intention.

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

In the code above, the User will fill out their details and click the `Make Payment` button.  This button will call some javascript to generate our unique embeddable form url.  We can then udpate the iframe in our mock modal and display it to the User to fill out their credit card details.

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

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

The `start` function collects the data from the html input fields and stores them in `payment_data` const.  It then passes this data into the `get_form_url` function that we see just below.  

This function just POST's this data back to the page we are already on (which is actually a php page as can be seen in the full example code: [`src/client-side-example.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/client-side-example.php) and then simply returns the parsed JSON directly to the caller.

The `start` function uses these results to update the `src` attribute on the iframe on our html and removes the `display: none` style from our modal.  The User can now see the credit card form as shown in the image below.

![image](https://user-images.githubusercontent.com/2927894/228682312-9c5c8054-f9a5-4534-a90e-3251c8bbc5a0.png)

Not exactly a modal, but you can easily imagine that part!

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

#### PHP Backend

```php
    require_once('./secrets.php');
    require_once("./tunl-embed-sdk.php");
    $tunl_sdk = new TunlEmbedSDK;

    // get json payload
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $amount = get_amount_from_order($data['ordernum']);

    $payment_data = array(
        'amount' => $amount,
        'cardholdername' => $data['cardholdername'] ?? null,
        'action' => 'verify',
        'ordernum' => $data['ordernum'] ?? null,
        'comments' => $data['comments'] ?? null,
        'street' => $data['street'] ?? null,
        'zip' => $data['zip'] ?? null,
    );

    $tunl_form_options = array(
        "api_key" => $tunl_api_key, // from secrets.php
        "secret" => $tunl_secret,   // from secrets.php
        "iframe_referer" => "https://localhost:8082/",
        "tunl_sandbox" => true, // set this if using a test tunl account api keys
        "allow_client_side_sdk" => true
        "payment_data" => $payment_data,
        // "web_hook" => "https://localhost:8082/web_hook.php",
        "custom_style_url" => "https://localhost:8082/custom-embed.css",
        // "debug_mode" => true,
    );

    $form = $tunl_sdk->get_form_url($tunl_form_options);
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

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# WebHooks

### Overview

WebHooks allow you to handle more advanced transaction scenarios.  WebHooks will be called either on a transaction failure (and include error info) OR on transaction success (and include transaction data).  WebHook must respond with JSON data.  Any response from your webhook is passed thru back to the client for your own use on the client side.  Optionally, you can disable the standard response all together as shown below.

### Enabling Your WebHook

Your webhook can be enabled by setting the `web_hook` [tunl form option](https://github.com/CKC-Technologies/tunl-embedded-payment-form#tunl-form-options).

Example in your PHP backend:

```php
$tunl_form_options = array(
    ...
    "web_hook" => "https://yoursite.com/web_hook.php",
    ...
);
```

### Replacing the standard response entirely with your own custom response.

To disable the standard response make sure to set a property named `only_return_webhook_response_to_client` to `true` in your webhook json response.  Here is an example in PHP:

```php
$newData = array(
    'only_return_webhook_response_to_client' => true,
    'other_data' => $data
);
echo json_encode($newData);
```

The above let's you choose when and what messages should be sent to the client/browser directly from your webhook, any others sent back without this parameter set will include the standard responses from our embedded form server.

---

Or disable it entirely via the create URL call in the options.  This will disable the standard response completely and ONLY respond with data directly from your web_hook.

```php
$tunl_form_options = array(
    ...
    "web_hook" => "https://yoursite.com/web_hook.php",
    "only_return_webhook_response_to_client" => true,
    ...
);
```

### Example Transaction Data

```json
{
  "data": {
    {
      "status": "SUCCESS",
      "msg": "Sale processed successfully.",
      "embedded_form_action": "sale",
      "transaction_ttid": "311489097",
      "transaction_amount": "6545.00",
      "transaction_authnum": "647828",
      "transaction_timestamp": "2023-04-25 01:29:38 +0000",
      "transaction_ordernum": "1682386177",
      "transaction_type": "SALE",
      "transaction_phardcode": "SUCCESS",
      "transaction_verbiage": "APPROVED",
      "transaction_code": "1",
      "vault_token": "244cac1d-1893-440f-8ba0-16cf48be2524",
      "webhook_response": [],
      "cardholdername": "Zach",
      "street": "",
      "zip": "",
      "comments": ""
      ... lot's more!
    }
  },
  "status": 200,
  "curl_error": "",
  "curl_errno": 0
}

```

### Example Error Data:

```json
{
  "data": {
    "message": "BAD CID",
    "code": "PaymentException"
  },
  "status": 400,
  "curl_error": "",
  "curl_errno": 0
}
```

### Complete WebHook Example (with comments)

```php
<?php

// get json payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// if this web hook is called with a transaction error
if ($data['status'] !== 200){
    // perform your own custom error processing
    // optionally respond with custom response
    handleErr($data);
    exit();
}

// do stuff with the data
// at minimum you will likely want to store the following items
// in your database to be able to perform future actions
$transaction_id = $data["transaction_ttid"];
$vault_id = $data["vault_token"];
$orderNum = $data["transaction_ordernum"];

$some_potential_error_inside_the_webhook = false;
// handle any errors in your own code
if ($some_potential_error_inside_the_webhook){

    // respond with any code other than 200
    // Tunl API will attempt to void the transaction.
    http_response_code(500);
    handleErr(array("test" => "test"));
    exit();
}

// returned data is passed thru back to the client
echo json_encode(array(
    "status" => "SUCCESS",
    "msg" => "Your Success Message",

    // you can disable the standard response if you want full control. 
    // (Or set this in the createUrl Options)
    // 'only_return_webhook_response_to_client' => true,
    
    "data" => [ /* YOUR WEBHOOK RESPONSE DATA GOES HERE */ ]
));

function handleErr($data){
    echo json_encode(array(
        "status" => "ERROR",
        "msg" => "Your Error Message",

        // Example only: be careful about passing unhandled error data back to the client.
        // https://cheatsheetseries.owasp.org/cheatsheets/Error_Handling_Cheat_Sheet.html
        "data" => $data 
    ));
}

?>
```

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Custom CSS Styling

### Default Styling

This form comes with some sensible default styling.  You have already seen this in several of the images in this readme, but for the sake of completeness, here it is again:

![image](https://user-images.githubusercontent.com/2927894/230530715-8fc29265-7c36-4d71-8600-6626bf04a8ba.png)


This default styling is great for getting started, but likely at odds with your brand and site styles.  In order to get started applying custom styles we will need to set the `custom_style_url` option in our [Tunl Form Options](#tunl-form-options).

[Click here to view the full default css rules](#full-default-css)

# Unstyled

Let's start with a completely unstyled look to see what we are working with. Create an empty CSS file in your project or in a publicly available uri on your domain. Then set the `custom_style_url` option to point directly to it.

```php
$tunl_form_options = array(
    ...
    "custom_style_url" => "https://localhost:8082/custom-embed2.css",
    ...
);
```

We should now see something like this:

![image](https://user-images.githubusercontent.com/2927894/228873005-fc1a7472-434d-4049-8215-4ec05cd32a91.png)

Woof, not very pretty.  Let's see how we can improve this.

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### HTML Structure and Selectors

Below is what the underlying HTML looks like.  You can see the we have plenty of class selectors, id's, name attributes and wrapper divs to use for styling.

```html
<body class="tunl-embedded-body">
    <div class="tunl-embedded-form-wrapper">
        <form class="tunl-embedded-form" id="tunl_form" method="post">

            <div class="tunl-field-group ccname-group">
                <label for="tunl_cc_name">Card Holder Name</label>
                <input type="text" name="cardholdername" id="tunl_cc_name">
            </div>

            <div class="tunl-field-group ccno-group">
                <label for="tunl_cc_no">Credit Card No</label>
                <input type="text" name="account" id="tunl_cc_no">
            </div>

            <div class="tunl-field-group expire-group">
                <label for="tunl_cc_expires">Expiration</label>
                <input type="text" name="expdate" id="tunl_cc_expires">
            </div>

            <div class="tunl-field-group cvv-group">
                <label for="tunl_cc_cvv">CVV</label>
                <input type="text" name="cv" id="tunl_cc_cvv">
            </div>
            
            <div class="tunl-field-group combo-error-group">
                <p class="error-message" style="padding: 5px; height: 55px;"></p>
                <p class="error-message-height-gauge"></p>
            </div>

            <div class="tunl-field-group submit-group">
                <button>Submit</button>
            </div>
            
        </form>
    </div>
</body>
```

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Basic customization

An incredible improvement in style can be had in very few lines of CSS.  For Example:

```css
* {
  box-sizing: border-box;
}

body {
    margin: 0px;
    font-family: arial;
    overflow: hidden;
}

input,
button {
  display: block;
  margin-bottom: 10px;
  width: 100%;
  border-radius: 5px;
  border: 1px solid gray;
  padding: 10px;
}

```

Will turn the above 1990's form into the results shown below:

![image](https://user-images.githubusercontent.com/2927894/228888764-67c1c61a-52a5-4996-8531-fefb53229b82.png)

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Further Improvement

Throw in some CSS Grid magic (or flexbox) and you can really do anything.

```css
* {
  box-sizing: border-box;
}

body {
  margin: 0px;
  font-family: arial;
  overflow: hidden;
}

.tunl-embedded-form {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  column-gap: 10px;
}

.ccname-group {
  grid-column: span 6;
}
.ccno-group {
  grid-column: span 3;
}
.expire-group {
  grid-column: span 2;
}
.cvv-group {
  grid-column: span 1;
}
.submit-group {
  grid-column: span 6;
}

label {
  display: block;
  width: 100%;
}

input,
button {
  display: block;
  border: 1px solid grey;
  border-radius: 5px;
  padding: 5px;
  margin-bottom: 15px;
  box-shadow: 1px 1px 5px -1px grey;
  width: 100%;
}
```

The css above adds some box-shadow and CSS grid to render the following result:

![image](https://user-images.githubusercontent.com/2927894/228891875-38885034-2f19-4256-8e1e-500b376ad8c9.png)


[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Full Default CSS

If you prefer to start with the current default styling and make small tweaks then starting with the default css is the best idea.  Below is the full default css as of Apr 6, 2023.  If this becomes out of sync you can always use your browser inspect to grab the most current CSS the default iframe is downloading.

```css
* {
  box-sizing: border-box;
}

body {
  margin: 0px;
  font-family: arial;
  overflow: hidden;
}

.tunl-embedded-form {
  display: grid;
  grid-template-columns: repeat(120, 1fr);
  /* column-gap: 10px; */
  align-items: end;
}

.ccname-group {
  grid-column: span 120;
}
.combo-error-group {
  grid-column: span 120;
}
.ccno-group {
  grid-column: span 85;
}
.expire-group {
  grid-column: span 20;
}
.cvv-group {
  grid-column: span 15;
}
.submit-group {
  grid-column: span 120;
}

/* .expire-group label, .cvv-group label {text-align: center;} */
.ccno-group input,
.expire-group input,
.cvv-group input {
  margin-left: 0px;
  margin-right: 0px;
  padding-left: 0px;
  padding-right: 0px;
}

.ccno-group .error-message,
.expire-group .error-message,
.cvv-group .error-message {
  margin: 0px;
}

.error-message-height-gauge {
  position: absolute;
  transform: translateX(-10000px);
  width: 100%;
}

.error-message.show,
.error-message-height-gauge {
  padding: 5px 5px;
}

.error-message {
  height: 0px;
  padding: 0px 5px;
  transition: all 0.3s;
  overflow: hidden;
}

.error-message,
.error-message-height-gauge {
  margin: 0px 0px 11px;
  color: red;
  border-radius: 5px;
  font-size: 10pt;
  white-space: pre-line;
}

.ccno-group.default-card-icon:before {
  background-image: url(https://test-payment.tunl.com/embed/assets/code.svg);
}

.ccno-group.visa-card-icon:before {
  background-image: url(https://test-payment.tunl.com/embed/assets/visa.svg);
}

.ccno-group.mastercard-card-icon:before {
  background-image: url(https://test-payment.tunl.com/embed/assets/mastercard.svg);
}

.ccno-group.amex-card-icon:before {
  background-image: url(https://test-payment.tunl.com/embed/assets/amex.svg);
}

.ccno-group.discover-card-icon:before {
  background-image: url(https://test-payment.tunl.com/embed/assets/discover.svg);
}

.ccno-group:before {
  background-size: contain;
  background-repeat: no-repeat;
  position: absolute;
  width: 30px;
  display: block;
  height: 26px;
  content: "";
  transform: translate(15px, 26px);
}

.ccno-group input {
  border-bottom-right-radius: 0px;
  border-top-right-radius: 0px;
  border-right: 0px;
  padding-left: 55px;
}

.expire-group input {
  border-radius: 0px;
  border-left: 0px;
  border-right: 0px;
}

.cvv-group input {
  border-bottom-left-radius: 0px;
  border-top-left-radius: 0px;
  border-left: 0px;
}

label {
  display: block;
  width: 100%;
  line-height: 14pt;
  font-size: 12pt;
}

input.invalid {
  color: red;
}

input:focus {
  outline: none;
}

input,
button {
  display: block;
  border: 1px solid grey;
  border-radius: 5px;
  padding: 10px;
  width: 100%;
  height: 36px;
}

.tunl-field-group {
  position: relative;
}

```

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Dual Vaulting

### Overview

Dual Vaulting allows you to add additional supported providers to vault (tokenize) card data.

The example below shows the basic additional config parameters to setup additional providers.

```diff
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
    "allow_client_side_sdk": true,
+   "additional_vault_providers": [
+     { ... another provider },
+     { ... another provider }
+   ]
}
EOF
```
---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Providers

We currently support RepayOnline, but additional providers are generally easy to add, please inquire if you would like to see a new provider added to the list!

### RepayOnline

The example below shows how to configure RepayOnline as an additional vault provider:

```diff
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
    "allow_client_side_sdk": true,
+   "additional_vault_providers": [
+     {
+       "provider": "repayonline",
+       "api-version": "1",
+       "rg-api-secure-token": "xxxxxxx",
+       "rg-api-user": "User_Name",
+       "sandbox": true
+     }
+   ]
}
EOF
```

## Receving the vault tokens

The rest of the process for integration is identical, the only new part will be the availability of the additional vault token(s) inside the response payload.

The current response includes a `vault_token` property that contains the Tunl Vault Token.

The additional RepayOnline token info will be provided inside the `additional_vault_tokens` property in the response payload as shown below:

```diff
{
    "status": "SUCCESS",
    "msg": "Sale processed successfully.",
    ...
    "vault_token": "b26aad1c-5ec3-49a5-9702-671875cf2630",
+   "additional_vault_tokens": [
+     {
+       "provider": "repayonline",
+       "token": "1234567890",
+       "full_response": {
+           "card_token_key": 1082478257,
+           "exp_date": "0324",
+           "name_on_card": "Zach",
+           "street": "",
+           "zip": "",
+           "last4": "1111",
+           "card_type": "VISA",
+           "is_eligible_for_disbursement": false,
+           "customer_id": null,
+           "custom_fields": [],
+           "nickname": null,
+           "bin": "411111",
+           "external_payment_token": null,
+           "card_info": {
+               "brand": "VISA",
+               "type": "CREDIT"
+           }
+        }
+     }
+   ]
    ...
}
```
---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### BridgePay

The example below shows how to configure BridgePay as an additional vault provider:

```diff
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
    "allow_client_side_sdk": true,
+   "additional_vault_providers": [
+     {
+       "provider": "bridgepay",
+       "username": "asdf",
+       "password": "asdf",
+       "merchantAccountCode": "123123123",
+       "invoiceNumber": "InvoiceNumber123",
+       "transIndustryType": "DM",
+       "storedCredential": "InitialUnscheduled",
+       "networkReferenceNumber": "60319733",
+       "holderType": "P",
+       "accountType": "R",
+       "sandbox": true
+     }
+   ]
}
EOF
```

## Receving the vault tokens

The rest of the process for integration is identical, the only new part will be the availability of the additional vault token(s) inside the response payload.

The current response includes a `vault_token` property that contains the Tunl Vault Token.

The additional RepayOnline token info will be provided inside the `additional_vault_tokens` property in the response payload as shown below:

```diff
{
    "status": "SUCCESS",
    "msg": "Sale processed successfully.",
    ...
    "vault_token": "b26aad1c-5ec3-49a5-9702-671875cf2630",
+   "additional_vault_tokens": [
+     {
+       "provider": "bridgepay",
+       "token": "1000000010261111",
+       "expirationDate": "1234",
+       "full_response": {
+           "cardType": "Visa",
+           "token": "1000000010261111",
+           "authorizationCode": "118192",
+           "referenceNumber": "345694081",
+           "gatewayResult": "00000",
+           "authorizedAmount": 1234,
+           "originalAmount": 1234,
+           "expirationDate": "1234",
+           "cvResult": "N",
+           "cvMessage": "Not matches",
+           "isCommercialCard": "False",
+           "gatewayTransID": "4434790404",
+           "gatewayMessage": "A01 - Approved",
+           "internalMessage": "Approved: 118192 (approval code)",
+           "transactionDate": "20230803",
+           "remainingAmount": 0,
+           "isoCountryCode": "840",
+           "isoCurrencyCode": "USD",
+           "isoTransactionDate": "2023-08-03T21:58:54.523",
+           "isoRequestDate": "2023-08-03T21:58:54.523",
+           "networkReferenceNumber": "345694081",
+           "merchantCategoryCode": "5999",
+           "networkMerchantId": "123123123",
+           "networkTerminalId": "10001",
+           "maskedPAN": "************1111",
+           "responseTypeDescription": "auth",
+           "cardClass": "Credit",
+           "cardModifier": "None",
+           "cardHolderName": "Test",
+           "providerResponseMessage": "Approved",
+           "organizationId": "57182",
+           "merchantAccountCode": "14043001",
+           "requestType": "004",
+           "responseCode": "00000",
+           "responseDescription": "Successful Request"
+        }
+     }
+   ]
    ...
}
```
---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Exception Responses

Errors returned from the additional vault providers will be returned as shown below:

```diff
{
    "status": "SUCCESS",
    "msg": "Sale processed successfully.",
    ...
    "vault_token": "b26aad1c-5ec3-49a5-9702-671875cf2630",
    "additional_vault_tokens": [
      {
        "provider": "repayonline",
-       "token": "1234567890"
+       "error": true,
+       "error_code": 400,
+       "error_msg": "Something Bad Happened!",
+       "error_obj": { ... error object }
      },
      { ... other token },
      { ... other token }
    ]
    ...
}
```

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Disabling Tunl Vault

If you are configuring additional vault providers and would like to disable the tunl vault (to make the other providers primary) then you can set the autovault to 'N' in the `payment_data` object.  In the example below, only the repayonline vault provider will be executed:

```diff
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
    "allow_client_side_sdk": true,
    "additional_vault_providers": [
      {
        "provider": "repayonline",
        "api-version": "1",
        "rg-api-secure-token": "xxxxxxx",
        "rg-api-user": "User_Name",
        "sandbox": true
      },
      { ... another provider },
      { ... another provider }
    ],
+   "payment_data": {
+       "autovault": "N"
+   }
}
EOF
```

### Additional Vault Provider Order

Additional vault providers will be processed in the order that they appear in the `additional_vault_providers` array.

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

# Troubleshooting

### Receive 400 Error: Malformed Request Body

Make sure the request to the `get-card-form-url.php` contains all the following required properties:

- api_key
- secret
- iframe_referer

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Bad API Key and Secret Combo

> Error: call to URL https://test-payment.tunl.com/embed/get-card-form-url.php failed with status 401, response Bad API Key and Secret Combo, curl_error , curl_errno 0

Make sure that you have typed in your api key and secret correctly. Additionally, if you are using an API Key and Secret that was created using a Tunl Test Account (from https://test.tunl.com) then you will need to set the `tunl_sandbox` option to `true`

```php
$tunl_form_options = array(
    "api_key" => "apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "secret" => "xxxxxxxxxxxxxxxxxxxxxxxxxx",
    "tunl_sandbox" => true, // set this if using a test tunl account api keys
    "allow_client_side_sdk" => true
);
```

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Unauthorized

This typically happens when trying to use the generated URL incorrectly.  If you generate the URL and use it immediately in an iframe, this should never happen.  The generated URL employs the use of a `one-time-use-code` that is unique and expires in 1 minute.

Scenarios that an `Unauthorized` error would typically happen:
- Attempting to use the generated URL more than once
- Attempting to use a one-time-use-code that does not exist
- Not using the generated URL (or one-time-use-code) within 1 minute

Example generated URL for reference: https://test-payment.tunl.com/embed/load-embedded-form.php?one-time-use-code=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Access to this page is restricted to specific domains and must be embedded in an iframe.

This message occurs when trying to access the embedded form page directly or from a domain that has not been authorized by the `iframe_referer` parameter.

---

### payment.tunl.com refused to connect (and other iframe issues)

![image](https://user-images.githubusercontent.com/2927894/228860538-ec0aad32-3e2a-4772-b3eb-d83b09fd9b99.png)

> Refused to frame 'https://payment.tunl.com/' because an ancestor violates the following Content Security Policy directive: "frame-ancestors https://localhost:8082/".

This can occur when the `iframe_referer` is not set properly.  Make sure this option is set the the domain that will be hosting the iframe.  This will be a domain that you own.

```php
$tunl_form_options = array(
    ...
    "iframe_referer" => "https://your.domain.com",
    ...
);
```

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### This domain is not authorized to embed this page in an iframe.

This message can occur for the same reasons as the previous item.  The `iframe_referer` is likely not set correctly.

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Card Authentication Failed

These messages are usually triggered by the tunl gateway.  There should be an additional message to help clue the user as the why the authentication failed.  These are usually things like:

- DECLINED
- UNSUPPORTED CARD TYPE
- EXPIRATION DATE MUST BE IN FUTURE
- BAD CID

The list of possible messages here is the top 4, with DECLINED being the most common.  You can view more information about these failures in the Tunl Application under Reports->Failed: https://test.tunl.com/payments/failed

---

[Back to Table of Contents](#table-of-contents)

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

&nbsp;

### Unable to complete Transaction. Bad Web Hook Response.

If your webhook responds with anything else other than `200` this message will be displayed to the user.  It is recommended to setup some error handling and logging in your web_hook so that you can review what might have happened in these situations.

