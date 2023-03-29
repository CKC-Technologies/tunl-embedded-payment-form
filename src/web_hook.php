<?php

// get json payload
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// do stuff with the data - full example reference below
// at minimum you will likely want to store the following items
// in your database to be able to perform future actions
$transaction_id = $data["ttid"];
$vault_id = $data["vaultId"];
$orderNum = $data["ordernum"];

// if there is an error you can respond with any error code
// if the status code is not 200 the embedded form api
// will attempt to void the transaction.
if ($some_potential_error){
    http_response_code(500);
    exit();
}

// returing data is not required 
// will ONLY be displayed when using 'debug_mode'
echo json_encode($data);

// FULL RESPONSE EXAMPLE/REFERENCE:
//$data = {
//     "ttid": "999999999",
//     "type": "PREAUTH",
//     "card": "VISA",
//     "account": "XXXXXXXXXXXX1111",
//     "expdate": "0324",
//     "authnum": "999999",
//     "batchnum": "",
//     "cardholdername": "Card Holder",
//     "avs": "BAD",
//     "cv": "BAD",
//     "ptrannum": "1680033739",
//     "clerkid": "iDep Embed Form",
//     "stationid": "",
//     "comments": "My Custom Comments",
//     "amount": "123.45",
//     "timestamp": "2023-03-28 20:02:29 +0000",
//     "verbiage": "APPROVED",
//     "code": "1",
//     "phardcode": "SUCCESS",
//     "entrymode": "M",
//     "tax": "0.00",
//     "examount": "0.00",
//     "ordernum": "MyCustomReference1680033739",
//     "custref": "",
//     "balance": null,
//     "unsettled": false,
//     "vaultId": 99999,
//     "contact": {
//         "id": null,
//         "merchantId": 0,
//         "firstName": null,
//         "lastName": null,
//         "email": null,
//         "companyName": null,
//         "street": null,
//         "city": null,
//         "state": null,
//         "zip": null,
//         "homePhone": null,
//         "cellPhone": null,
//         "officePhone": null,
//         "createdDate": null,
//         "modifiedDate": null,
//         "createdBy": null,
//         "modifiedBy": null,
//         "active": false,
//         "locations": null,
//         "accountNumber": 0,
//         "accounts": []
//     },
//     "contactAccount": {
//         "id": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
//         "contactId": null,
//         "merchantId": 9999,
//         "vaultId": 99999,
//         "name": "Card Holder AK7O",
//         "description": null,
//         "cardholder": "Card Holder",
//         "account": "XXXXXXXXXXXX1111",
//         "expdate": "0324",
//         "billingStreet": "2200 Oak St.",
//         "billingZip": "49203"
//     },
//     "recurringSchedule": {
//         "id": null,
//         "accountId": null,
//         "contactId": null,
//         "merchantId": 0,
//         "transactionAmount": 0,
//         "startDate": null,
//         "endDate": null,
//         "installments": 0,
//         "installmentsRemaining": 0,
//         "processEvery": 0,
//         "processInterval": 0,
//         "description": null,
//         "notificationDays": 0,
//         "enableUserAdminNotifications": 0,
//         "enableContactNotifications": 0,
//         "lastPaymentDate": null,
//         "nextPaymentDate": null,
//         "status": 0,
//         "surchargeOn": false,
//         "contact": null,
//         "account": null
//     },
//     "surcharge": {
//         "id": null,
//         "merchantId": 0,
//         "maximumTransaction": 0,
//         "allowUserDisabling": false,
//         "recurringSurcharges": false,
//         "refundTransactionSurcharges": false,
//         "receiptHeader": "",
//         "receiptFooter": "",
//         "active": false,
//         "rules": [],
//         "createdDate": null,
//         "modifiedDate": null,
//         "createdBy": null,
//         "modifiedBy": null,
//         "calculatedSurchargeAmount": 0,
//         "transactionAmountWithSurchargeAmount": 0,
//         "calculatedSurchargeMessage": null
//     },
//     "surchargeAmount": 0,
//     "invoice": {
//         "id": null,
//         "merchantId": 0,
//         "contactId": null,
//         "invoiceTitle": null,
//         "invoiceNumber": 0,
//         "taxType": null,
//         "taxRate": 0,
//         "totalLatePaymentFee": 0,
//         "notificationLastRunDay": 0,
//         "totalAmount": 0,
//         "totalCost": 0,
//         "totalDiscount": 0,
//         "totalTax": 0,
//         "totalPaid": 0,
//         "totalDue": 0,
//         "invoiceReferenceNumber": null,
//         "dueDate": null,
//         "dueDateReminder": false,
//         "dueDateReminderNotificationDays": 0,
//         "allowLatePayment": false,
//         "allowPartialPayment": false,
//         "latePaymentFee": 0,
//         "expirationDate": null,
//         "missedPaymentReminderNotificationDays": 0,
//         "status": null,
//         "invoicePayments": [],
//         "invoiceLineItems": [],
//         "attachments": [],
//         "contact": {
//             "id": null,
//             "merchantId": 0,
//             "firstName": null,
//             "lastName": null,
//             "email": null,
//             "companyName": null,
//             "street": null,
//             "city": null,
//             "state": null,
//             "zip": null,
//             "homePhone": null,
//             "cellPhone": null,
//             "officePhone": null,
//             "createdDate": null,
//             "modifiedDate": null,
//             "createdBy": null,
//             "modifiedBy": null,
//             "active": false,
//             "locations": null,
//             "accountNumber": 0,
//             "accounts": []
//         },
//         "merchant": {
//             "id": 0,
//             "email": null,
//             "merchantDba": null,
//             "merchantName": null,
//             "address": null,
//             "city": null,
//             "state": null,
//             "zipCode": null,
//             "phone": null,
//             "payLink": null
//         },
//         "createdDate": null,
//         "modifiedDate": null,
//         "createdBy": null,
//         "modifiedBy": null,
//         "surchargeOn": false,
//         "totalSurcharge": 0
//     },
//     "levelIIIProcessingRequested": null,
//     "levelIIIDataValid": null,
//     "levelIIIValidationFailureDetails": null
// }


?>