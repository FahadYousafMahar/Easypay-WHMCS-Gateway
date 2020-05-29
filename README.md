# easypay-whmcs-gateway
EasyPay (EasyPaisa) Payment Gateway for WHMCS

https://marketplace.whmcs.com/product/4968

![Contributors](https://img.shields.io/github/contributors/fahadyousafmahar/easypay-whmcs-gateway.svg)
![License](https://img.shields.io/github/license/fahadyousafmahar/easypay-whmcs-gateway.svg)
![Repo Size](https://img.shields.io/github/repo-size/fahadyousafmahar/easypay-whmcs-gateway.svg)

### Description
EasyPay Module for WHMCS (Web Host Manager Complete Solution) to integrate EasyPaisa online payments.

The gateway enables EasyPaisa Token Payments, Mobile Account Payments & Visa/Master Card Payments for WHMCS.

### System Requirements

1- EasyPaisa Merchant Account (https://easypay.easypaisa.com.pk)

2- WHMCS >= 7.x.x  - Obviously the best Web Hosting Automation Solution (https://whmcs.com)

### Installing

```
1- Download the EasyPay.php and callback/EasyPay.php.
2- Upload the EasyPay.php to WHMCS-Directory/modules/gateways/ (here)
3- Upload the callback/EasyPay.php to WHMCS-Directory/modules/gateways/callback/ (here)
4- Go to : WHMCS Admin Area >> Setup >> Payments >> Payment Gateways .
5- Click 'Manage Existing gateways' tab
6- Fill in EasyPay form with Display Name, Account ID, Secret Hash Key:
    Display Name    : EasyPay
    Account ID      : XXXX
    Secret Hash Key : XXXXYYYYZZZZ
    Test Mode 		: Yes/No
7- Activate the gateway & Your WHMCS setup is done.
8- Now move to EasyPaisa Merchant portal and set IPN Handler to https://YourWHMCSinstallationDIRECTORY/modules/gateways/callback/EasyPay.php
```

Now your clients can pay with EasyPay - EasyPaisa.

If you are stuck anywhere or need any help you can raise an issue.
### Screenshot

![WHMCS EasyPay Module ScreenShot](https://raw.githubusercontent.com/FahadYousafMahar/easypay-whmcs-gateway/master/scrnshot.PNG)

## Authors

* **Fahad Yousaf Mahar** - [WebIT.pk](https://webit.pk)
* **Shahrukh A. Khan** - [Shaz3e.com](https://www.shaz3e.com)

See also the list of [contributors](https://github.com/FahadYousafMahar/easypay-whmcs-gateway/graphs/contributors) who participated in this project.

## License

This project is licensed under the GNU LGPLv3 License - see the [LICENSE](LICENSE) file for details

## Acknowledgments

* WHMCS Developer Documentation(https://developers.whmcs.com)
