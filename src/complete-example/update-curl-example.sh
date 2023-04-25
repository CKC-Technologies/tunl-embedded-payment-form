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
    "amount": "1000.105",
    "action": "sale",
    "comments": "set on update endpoint"
  }
}
EOF