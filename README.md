# Tunl Embeddable Form Documentation

![image](https://user-images.githubusercontent.com/2927894/228584754-deded60e-5a15-41da-9712-f5cb25db3d4f.png)

The code in this repo currently uses PHP, but could very easily be ported into other languages.  Eventually there will be more code examples and samples in this repo that demonstrate use in other languages.

Before attempting to embed our hosted payment form in your web application
you will need an account on our TUNL merchant platform.  https://tunl.com/contact/

Once you have an account you will need to create an API and Secret.  
This can be performed by clicking the Settings (Gear) Icon in the top of the sidebar
on the left and scrolling down to the API Keys section and clicking "Create API Key"

Already have an account? Here are some quick links to create API Keys.

- https://merchant.tunl.com/merchant/settings (Standard Accounts)
- https://test.tunl.com/merchant/settings (Test accounts only)

# Quick Start

This Repo is setup with docker and docker-compose.  You can quickly get started by cloning this repository to your local dev environment and running:

```bash
docker-compose up
```

Once running, you can update the `src/secrets.php` file with your
TUNL API Key and Secret.

Then you should be able to navigate to either
- https://localhost:8082/
- https://localhost:8082/kitchen-sink.php

these files can be found in the `src` folder respectively
- [`src/index.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/index.php)
- [`src/kitchen-sink.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/kitchen-sink.php)

However, keep in mind the webhook functionality is not able to be tested when using the localhost.

If you want to test the webhook feature using the quickstart docker-compose approach you will need to run it in an environment that is publicly available, behind SSL and a domain you have control of.

Alternatively, you could point the webhook setting directly to a public endpoint that is not in this project.  Take a look at the [`src/web_hook.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/web_hook.php) file for more info on how to structure your webhook to receive data back from the form.

# Process overview

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

#### Security Warning !

> Keep in mind, this is potentially a sensitive operation and you should review for secure implementation.  For example, the `iframe_referer` should always be a statically set value that is a domain you own.  

> It should *_NOT_* be allowed to be set dynamically via JSON options passed in.  This parameter helps to ensure that the form is ONLY allowed to be embedded on your site/application.

Alternatively you could modify this code to be completely Server Side Rendered.  Checkout [`src/index.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/index.php) for an example that uses this technique.

### A peek under the hood

The [`ideposit-embed-sdk.php`](https://github.com/CKC-Technologies/tunl-embedded-payment-form/blob/main/src/ideposit-embed-sdk.php) is nothing fancy at present.  It just contains all the boilerplate to do CURL calls and a wrapper method to get the form url.  To illustrate, here is a command line version of the CURL call being made by

`$ideposit_sdk->get_form_url($tunl_form_options)`

```bash
curl -X POST https://test-payment.tunl.com/embed/get-card-form-url.php \
   -H 'Content-Type: application/json' \
   -d '{"api_key":"apikey_xxxxxxxxxxxxxxxxxxxxxxxxxxx","secret":"xxxxxxxxxxxxxxxxxxxxxxxxxx","iframe_referer":"https://localhost:8082/"}'
```

### All available options

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
    "test_mode" => true
);

```

All other parameters are optional, but allow much more control over the output.

#### Tunl Form Options

<table>
    <thead>
        <tr>
            <td>Param</td>
            <td>Default</td>
            <td>Description</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>`api_key`</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>`secret`</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>`iframe_referer`</td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>

#### Payment Data Options


# Troubleshooting

### Receive 400 Error: Malformed Request Body

Make sure the request to the `get-card-form-url.php` contains all the required properties:

- api_key
- secret
- iframe_referer

More Troubleshooting coming soon!

