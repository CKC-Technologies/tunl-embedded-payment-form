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

###### Payload Example:
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
