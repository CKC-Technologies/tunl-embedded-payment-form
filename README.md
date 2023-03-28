# Tunl Embeddable Form Documentation

Before attempting to embed our hosted payment form in your web application
you will need an account on our TUNL merchant platform.  https://tunl.com/contact/

Once you have an account you will need to create an API and Secret.  
This can be performed by clicking the Settings (Gear) Icon in the top of the sidebar
on the left and scrolling down to the API Keys section and clicking "Create API Key"

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
- `src/index.php`
- `src/kitchen-sink.php`

However, keep in mind the webhook functionality is not able to be tested when using the localhost.

If you want to test the webhook feature using the quickstart docker-compose approach you will need to run it in an environment that is publicly available and behind SSL and a domain you have control of.

# Process overview

The steps to embed - Coming Soon!



# Troubleshooting

### Recieve 400 Error: Malfored Request Body

Make sure the request to the `get-card-form-url.php` contains all the required properties:

- api_key
- secret
- iframe_referer

More Troubleshooting coming soon!

