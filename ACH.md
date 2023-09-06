# ACH Guide


# What's New

- API Endpoints
	- Merchant Onboarding
		- Basic Info
		- Beneficial Owners
		- Company Documents Upload/Download
		- Ben Owner Documents Uploiad/Download
	- Funding Sources Powered By Plaid
		- Instant Verification via Plaid - REQUIRES integrating with Plaid Link - See Below
		- Add / Delete Funding Sources to your merchant account (requires integrating with Plaid Link)
		- Add / Delete Funding Sources for Contacts (requires integrating with Plaid Link)
	- Transfers
		- ACH Credit
		- ACH Debit
	- Reporting
		- Unsettled Transfers
		- Settled Transfers
		- Failed Transfers
- Dashboard (Tunl UI)
	- Merchant Onboading Application **(NOT COMPLETE)**
	- Merchant Funding Sources **(NOT COMPLETE)**
	- Contact Funding Sources (View/Delete ONLY)
	- Transfers **(NOT COMPLETE)**
	- Reporting
		- Unsettled Transfers
		- Settled Transfers
		- Failed Transfers

---

# Getting Started

In order to begin testing the new ACH System we need to create and process a merchant application.  This creates an account that funding sources and customers (and their funding sources) can be added to.  Currently all of the above is a manual process for testing.  Reach out to us and we will setup everything you need to begin testing ach transfer and reporting API endpoints.


---

# Transfers

In order to initiate a transfer you need the following items

- Your Merchant ID
- Contact ID (See Below How to Find the Contact ID)
- The Contact's Funding Source Vault ID (See below how to find the funding source ID)

##### Endpoint: `POST /api/payments/ach/merchant/{merchantId}`

##### Payload Example:
```json
{
    "action": "sale",
    "contactId": "3f17eccb-44dc-4d65-9aa0-5636e1ecadec",
    "useVault": true,
    "vaultId": 7997,
    "amount": "6.00"
}
```


JavaScript Example:
```javascript

const host = "https://test-api.tunl.com/api"
const merchantId = "6329";
const url = `${host}/payments/ach/merchant/${merchantId}`;

const options = {
  method: 'POST',
  headers: {
    Authorization: 'Bearer xxxxxxxxxxxxxxxxxxxxxxxx',
    'Content-Type': 'application/json'
  },
  body: `
	{
		"action": "sale",
		"contactId": "3f17eccb-44dc-4d65-9aa0-5636e1ecadec",
		"useVault": true,
		"vaultId": 7997,
		"amount": "6.00"
	}
  `
};

fetch(url, options)
  .then(res => res.json())
  .then(json => console.log(json))
  .catch(err => console.error('error:' + err));
```

### Response

```json
{
  "ttid": "cd8433ce-31be-4069-a156-dde6a1b7d8b9",
  "transactionCategory": "ACH",
  "type": "SALE",
  "card": null,
  "account": "****************",
  "routingNumber": null,
  "accountType": "CHECKING",
  "expdate": null,
  "authnum": "2",
  "batchnum": null,
  "cardholdername": "Zach TestThree",
  "avs": null,
  "cv": null,
  "ptrannum": "7dbecd10-ba4c-ee11-8154-ee5b5eeb80f1",
  "clerkid": "Custom Clerk",
  "stationid": "Station ID",
  "comments": "My Custom Comments",
  "amount": "6.00",
  "timestamp": "2023-09-06 13:34:22 +0000",
  "verbiage": "PENDING",
  "code": "2",
  "phardcode": "PENDING",
  "entrymode": null,
  "tax": "1.00",
  "examount": "2.00",
  "ordernum": null,
  "custref": null,
  "balance": null,
  "unsettled": true,
  "vaultId": 0,
  "vaultToken": null,
  "contact": {
    "id": null,
    "merchantId": 0,
    "firstName": null,
    "lastName": null,
    "email": null,
    "companyName": null,
    "street": null,
    "city": null,
    "state": null,
    "zip": null,
    "homePhone": null,
    "cellPhone": null,
    "officePhone": null,
    "createdDate": null,
    "modifiedDate": null,
    "createdBy": null,
    "modifiedBy": null,
    "achEnabled": false,
    "active": false,
    "locations": null,
    "accountNumber": 0,
    "accounts": []
  },
  "contactAccount": {
    "id": null,
    "contactId": null,
    "merchantId": 0,
    "vaultId": 0,
    "name": null,
    "description": null,
    "cardholder": null,
    "account": null,
    "expdate": null,
    "billingStreet": null,
    "billingZip": null,
    "accountPaymentMethodType": null,
    "achAccountResponseDetails": null
  },
  "recurringSchedule": {
    "id": null,
    "accountId": null,
    "contactId": null,
    "merchantId": 0,
    "transactionAmount": 0.0,
    "startDate": null,
    "endDate": null,
    "installments": 0,
    "installmentsRemaining": 0,
    "processEvery": 0,
    "processInterval": 0,
    "description": null,
    "notificationDays": 0,
    "enableUserAdminNotifications": 0,
    "enableContactNotifications": 0,
    "lastPaymentDate": null,
    "nextPaymentDate": null,
    "status": 0,
    "surchargeOn": false,
    "contact": null,
    "account": null
  },
  "surcharge": {
    "id": null,
    "merchantId": 0,
    "maximumTransaction": 0.0,
    "allowUserDisabling": false,
    "recurringSurcharges": false,
    "refundTransactionSurcharges": false,
    "receiptHeader": "",
    "receiptFooter": "",
    "active": false,
    "rules": [],
    "createdDate": null,
    "modifiedDate": null,
    "createdBy": null,
    "modifiedBy": null,
    "calculatedSurchargeAmount": 0.0,
    "transactionAmountWithSurchargeAmount": 0.0,
    "calculatedSurchargeMessage": null
  },
  "surchargeAmount": 0.0,
  "invoice": {
    "id": null,
    "merchantId": 0,
    "contactId": null,
    "invoiceTitle": null,
    "invoiceNumber": 0,
    "taxType": null,
    "taxRate": 0.0,
    "totalLatePaymentFee": 0.0,
    "notificationLastRunDay": 0,
    "totalAmount": 0.0,
    "totalCost": 0.0,
    "totalDiscount": 0.0,
    "totalTax": 0.0,
    "totalPaid": 0.0,
    "totalDue": 0.0,
    "invoiceReferenceNumber": null,
    "dueDate": null,
    "dueDateReminder": false,
    "dueDateReminderNotificationDays": 0,
    "allowLatePayment": false,
    "allowPartialPayment": false,
    "latePaymentFee": 0.0,
    "expirationDate": null,
    "missedPaymentReminderNotificationDays": 0,
    "status": null,
    "invoicePayments": [],
    "invoiceLineItems": [],
    "attachments": [],
    "contact": {
      "id": null,
      "merchantId": 0,
      "firstName": null,
      "lastName": null,
      "email": null,
      "companyName": null,
      "street": null,
      "city": null,
      "state": null,
      "zip": null,
      "homePhone": null,
      "cellPhone": null,
      "officePhone": null,
      "createdDate": null,
      "modifiedDate": null,
      "createdBy": null,
      "modifiedBy": null,
      "achEnabled": false,
      "active": false,
      "locations": null,
      "accountNumber": 0,
      "accounts": []
    },
    "merchant": {
      "id": 0,
      "email": null,
      "merchantDba": null,
      "merchantName": null,
      "address": null,
      "city": null,
      "state": null,
      "zipCode": null,
      "phone": null,
      "payLink": null
    },
    "createdDate": null,
    "modifiedDate": null,
    "createdBy": null,
    "modifiedBy": null,
    "surchargeOn": false,
    "totalSurcharge": 0.0
  },
  "levelIIIProcessingRequested": null,
  "levelIIIDataValid": null,
  "levelIIIValidationFailureDetails": null
}
```

---


## Transfer Payload Options

```json
{
  "action": "sale",      // 'sale' or 'return'
  "contactId": "string", // the id of the contact in Tunl
  "useVault": true,      // required
  "vaultId": 0,          // the vault ID of the funding source (required)
  "amount": "string",    // the dollar amount to transfer (required)
  "tax": "string",       // tax amount if any
  "examount": "string",  // extra amount if any (tip)
  "custref": "string",   // customer reference field (for your use)
  "clerkid": "string",   // clerkid (for your use)
  "stationid": "string", // station id (for your use)
  "comments": "string"   // comments (for your use)
}
```

---

# Listing Transfers

##### Endpoint `GET /api/payments/ach/merchant/{merchantId}/unsettled/{monthsBack}`
##### Endpoint `GET /api/payments/ach/merchant/{merchantId}/settled/{monthsBack}`
##### Endpoint `GET /api/payments/ach/merchant/{merchantId}/failed/{monthsBack}`

### Response

An array of transfers with the same structure shown above in what gets returned from initiating a transfer

---

# Retreiving a Transfer by ID

##### Endpoint `GET /api/payments/ach/merchant/{merchantId}/{transactionId}`

### Response

A single transfer with the same structure shown above in what gets returned from initiating a transfer

---

# Void Transfer

##### Endpoint `POST /api/payments/ach/merchant/{merchantId}/void/{transactionId}`

#### Body

None

### Response

A single transfer with the same structure shown above in what gets returned from initiating a transfer

---

# Find Contact ID's and Funding Source Vault ID's

##### Endpoint: `GET /api/merchants/{merchantId}/contacts`

Example Response (Truncated for clarity)

```json
[
  {
    "id": "3f17eccb-44dc-4d65-9aa0-5636e1ecadec",
    "merchantId": 6329,
    "firstName": "Zach",
    "lastName": "TestThree",
    "email": "8e460758-608b-41b7-9d62-9983b0a34c07@8e460758-608b-41b7-9d62-9983b0a34c07.com",
    ...
    "accounts": [
      {
        ////////////////////////////////////////////////////////////
        // "id": "8c418f07-7b4d-4457-b4ad-2ac79719b8a2",          //
        // "contactId": "3f17eccb-44dc-4d65-9aa0-5636e1ecadec",   //
        // "merchantId": 6329,                                    //
        // "vaultId": 7997,                                       //
        // "name": "Plaid Checking 0000",                         //
        ////////////////////////////////////////////////////////////
       ...
       ... Other Details ...
      },
      ...
      ... More Accounts (Funding Sources) ...
      ...
    ]
  },
  ...
  ... More Contacts ...
  ...
]
```
