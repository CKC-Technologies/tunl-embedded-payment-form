# Tunl Embeddable Form Documentation

Before attempting to embed our hosted payment form in your web application
you will need an account on our TUNL merchant platform.  https://tunl.com/contact/

Once you have an account you will need to create an API and Secret.  
This can be performed by clicking the Settings (Gear) Icon in the top of the sidebar
on the left and scrolling down to the API Keys section and clicking "Create API Key"

Already Have an account? Here are some quick links to create API Keys.

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

The steps to embed - Coming Soon!



# Troubleshooting

### Receive 400 Error: Malformed Request Body

Make sure the request to the `get-card-form-url.php` contains all the required properties:

- api_key
- secret
- iframe_referer

More Troubleshooting coming soon!

