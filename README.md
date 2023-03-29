# Tunl Embeddable Form Documentation

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

Basic Steps involved:
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




# Troubleshooting

### Receive 400 Error: Malformed Request Body

Make sure the request to the `get-card-form-url.php` contains all the required properties:

- api_key
- secret
- iframe_referer

More Troubleshooting coming soon!

