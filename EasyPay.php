<?php
/**
 * WHMCS EasyPay Module
 * This is the EasyPay (EasyPaisa) Payment Module for WebIT.pk Billing Area
 *
 *   __          __  _    _____ _______      _
 *   \ \        / / | |  |_   _|__   __|    | |
 *    \ \  /\  / /__| |__  | |    | |  _ __ | | __
 *     \ \/  \/ / _ \ '_ \ | |    | | | '_ \| |/ /
 *      \  /\  /  __/ |_) || |_   | |_| |_) |   <
 *       \/  \/ \___|_.__/_____|  |_(_) .__/|_|\_\
 *                                    | |
 *                                    |_|
 *
 * @see http://webit.pk/
 * @copyright Copyright (c) WebIT.pk Limited 2017 - Onwards
 * @license MIT
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see http://docs.whmcs.com/Gateway_Module_Meta_Data_Parameters
 *
 * @return array
 */
function easypay_MetaData()
{
    return array(
        'DisplayName' => 'easypay',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false,
    );
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */
function easypay_config()
{
    return array(
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'easypay',
        ),
        // a text field type allows for single line text input
        'accountID' => array(
            'FriendlyName' => 'Account ID',
            'Type' => 'text',
            'Size' => '4',
            'Default' => '',
            'Description' => 'Enter your account ID here',
        ),
        'hashkey' => array(
            'FriendlyName' => 'Secret Hash Key',
            'Type' => 'text',
            'Size' => '32',
            'Default' => '',
            'Description' => 'Enter your Easy Paisa Merchant Secret Hash Key ',
        ),
        /**
         * Test Mode Option Added 
         * Author: Shaz3e
         * @url shaz3e.com
         */
        'testMode' => array(
            'FriendlyName' => 'Test Mode',
            'Type' => 'yesno',
            'Default' => '',
            'Description' => 'Tick to enable test mode (Optional)',
        ),
    );
}

/**
* getHashedRequest() Function to generate EasyPay Hash
**/
function getHashedRequest($hKey, $orderId, $amt, $autoRed, $email, $expiryDate, $storeId, $merchantConfirmPage) {
		$hashRequest = '';
		if(strlen($hKey) > 0 && (strlen($hKey) == 16 || strlen($hKey) == 24 || strlen($hKey) == 32 )) {
			// Create Parameter map
			error_log('Order INFO: '. $orderId);
			$paramMap = array();
			$paramMap['amount']  = $amt ;
			$paramMap['autoRedirect']  = $autoRed ;
			$paramMap['emailAddr']  = $email ;
			$paramMap['expiryDate'] = $expiryDate;
			//$paramMap['mobileNum']  = $phone ;
			$paramMap['orderRefNum']  = $orderId;
			//$paramMap['paymentMethod']  = $paymentMode;
			$paramMap['postBackURL'] = $merchantConfirmPage;
			$paramMap['storeId']  = $storeId;
			//Creating string to be encoded
			$mapString = '';
			foreach ($paramMap as $key => $val) {
				$mapString .=  $key.'='.$val.'&';
			}
			$mapString  = substr($mapString , 0, -1);
			error_log('MAPString: '.$mapString);

			// Encrypting mapString
			function pkcs5_pad($text, $blocksize) {

				$pad = $blocksize - (strlen($text) % $blocksize);
				return $text . str_repeat(chr($pad), $pad);

			}

			$alg = MCRYPT_RIJNDAEL_128; // AES
			$mode = MCRYPT_MODE_ECB; // ECB

			$iv_size = mcrypt_get_iv_size($alg, $mode);
			$block_size = mcrypt_get_block_size($alg, $mode);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_DEV_URANDOM);

			$mapString = pkcs5_pad($mapString, $block_size);
			$crypttext = mcrypt_encrypt($alg, $hKey, $mapString, $mode, $iv);
			$hashRequest = base64_encode($crypttext);
			error_log('hashReq: '.$hashRequest);
		}
		return $hashRequest;
	}

/**
 * Generation of Payment link.
 *
 * @return string
 */

function easypay_link($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $hashKey = $params['hashkey'];
    $testMode = $params['testMode'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    // $currencyCode = $params['currency'];

    //Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];


    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];            // System URL
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // EasyPay POST Parameters
    $storeId=$accountId;                           // setting Store ID equal to Account Id as given in Gateway Config
    $orderId=$invoiceId;                           // setting Order ID equal to Invoice Id
    $autoRedirect = 1;                             // Auto Redirection after invoice payment at Easypay Portal
	    $currentDate = new DateTime();
	    $currentDate->modify('+ 10 day');
    $expiryDate = $currentDate->format('Ymd His'); // Conversion for expiry date&time
    /**
     * Test Mode yes/no
     * Added in v.1.1
     */
    if ( !$testMode ) {
        $url = 'https://easypay.easypaisa.com.pk/easypay/Index.jsf';
        $confirmUrl = 'https://easypay.easypaisa.com.pk/easypay/Confirm.jsf';
    }else{
        $url = 'https://easypaystg.easypaisa.com.pk/easypay/Index.jsf';
        $confirmUrl = 'https://easypaystg.easypaisa.com.pk/easypay/Confirm.jsf';
    }
    $amount = number_format($amount,1,'.', ''); //Converting amount to one-decimal point
    $callback = $systemUrl.'modules/gateways/callback/EasyPay.php'; // Callback

    // Generation of MerchantHashedRequest
    $hash = getHashedRequest($hashKey, $orderId, $amount, $autoRedirect, $email, $expiryDate, $storeId, $callback);

	// Post Fields to be placed in HTML form on invoice page
    $postfields = array();
    $postfields['storeId'] = $storeId;
    $postfields['merchantHashedReq'] = $hash;
    $postfields['orderRefNum'] = $orderId;
    $postfields['expiryDate'] = $expiryDate;
    $postfields['amount'] = $amount;
    $postfields['emailAddr'] = $email;
    $postfields['autoRedirect'] = $autoRedirect;
    $postfields['paymentMethod'] = $paymentMode;
    $postfields['postBackURL'] = $callback;
    // Start of HTML form with action = EasyPay Portal URL
    $htmlOutput = '';
    $htmlOutput .= '<form action="'.$url.'" method="POST">';
    // Generation of <input> tags with Parameters to be sent to EasyPay
    foreach ($postfields as $k => $v) {
        $htmlOutput .= '<input type="hidden" name="' . $k . '" value="'. $v. '" />';
    }
     $htmlOutput .= '<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-credit-card"></i> '.$langPayNow.'</button>';
    // End HTML Form
    $htmlOutput .= '</form>';
    return $htmlOutput;
}
?>
