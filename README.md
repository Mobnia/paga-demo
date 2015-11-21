# [Paga] API Demo for Startup Weekend Lagos

## [Paga] API
[Paga] provides the [Paga Connect] API which enables developers integrate the [Paga] payment platform into their applications.

## Demo
This demo uses the [Paga Connect] API to allow users pay for products with either a Verve, Visa or Master Card.
This use case is especially useful when providing payment options for users who may not have a paga account.

To run this demo, create a credentials.json file in the root folder. A sample file is shown below
```json
{
  "paga_credentials" : {
    "principal" : "",
    "client_id": "XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
    "scope": "MERCHANT_PAYMENT MONEY_TRANSFER",
    "response_type" : "code",
    "auth_url": "https://mypaga.com/paga-webservices/oauth2/authorization/"
  }
}
```

You can view this demo live [here](http://paga-demo.mobnia.com "Paga API Demo")

## Other Use Cases
The card payment option is by no means the only option available via the [Paga Connect] platform.
With a proper Oauth 2 flow completed, it is possible to perform more operations which include:
 * Making Payments into a Merchants Account
 * Checking The User's Balance
 * Transferring Money to the User's Paga Wallet
etc.

For a full list of the available options check the  [Paga Connect] documentation.

> ###### This demo is brought to you by [Mobnia] &copy;2015



[Paga]: http://www.mypaga.com "Paga"
[Paga Connect]: https://mypaga.atlassian.net/wiki/display/PagaConnect/Paga+Connect "Paga Connect"
[Mobnia]: http://www.mobnia.com "Mobnia"