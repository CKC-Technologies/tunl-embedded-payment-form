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