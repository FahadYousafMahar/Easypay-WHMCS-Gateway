# easypay-whmcs-gateway
EasyPay (EasyPaisa) Merchant Gateway for WHMCS 

### Description
Its EasyPay Module for WHMCS (Web Host Manager Complete Solution) to integrate EasyPaisa online payments.
The gateway enables EasyPaisa Token Payments, Mobile Account Payments & Visa/Master Card Payments for WHMCS clients.
You can save time by using this module.

### System Requirements

Things you need:

1- PHP >= 5.x.x Open Source Web Programming Language

2- MySQL >= 3.x.x Open Source Database Management System

3- WHMCS >= 7.x.x  - Obviously the best Web Hosting Automation Solution (https://whmcs.com)

### Installing

```
1- Download the EasyPay.php and callback/EasyPay.php.
2- Upload the EasyPay.php to WHMCS-Directory/modules/gateways/ (here)
3- Upload the Callback/EasyPay.php to WHMCS-Directory/modules/gateways/callback/ (here)
4- Go to : WHMCS Admin Area >> Setup >> Payments >> Payment Gateways .
5- Click 'Manage Existing gateways' tab
6- Fill in EasyPay form with Display Name, Account ID, Secret Hash Key, Callback URL :
    Display Name    : EasyPay
    Account ID      : XXXX
    Secret Hash Key : XXXXYYYYZZZZ
    Test Mode 		: Yes/No
    Callback URL    : https://YourWHMCSinstallationDIRECTORY/modules/callback/EasyPay.php
7- Activate the gateway & Your WHMCS setup is done.
8- Now move to Easy Pay Merchant portal and set IPN Handler to https://YourWHMCSinstallationDIRECTORY/modules/callback/EasyPay.php
```

Now your clients can pay with EasyPay - EasyPaisa.
If you are stuck anywhere or need any help you can ask me or feel free to check WHMCS Developer Documentation.
### Screenshot

![WHMCS EasyPay Module ScreenShot](https://raw.githubusercontent.com/FahadYousafMahar/easypay-whmcs-gateway/master/scrnshot.PNG)

## Built With

* [BRACKETS](http://www.brackets.io/) - The Best Code Editor in Web & World (Ranked by ME - the Author)

## Authors

* **Fahad Yousaf Mahar** - [WebIT.pk](https://webit.pk)

See also the list of [contributors](https://github.com/FahadYousafMahar/easypay-whmcs-gateway/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Acknowledgments

* WHMCS Developer Documentation(https://developers.whmcs.com)
* Brackets []
* My Keyboard
