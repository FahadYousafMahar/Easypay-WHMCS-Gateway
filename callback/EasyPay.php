<?php
/**
 * WHMCS EasyPay Module Callback
 * This is IPN Handler for WHMCS EasyPay (EasyPaisa) Payment Module
 * (^-^) I've used my company name at many places in this code. Customize those strings before use
 * @author Fahad Yousaf Mahar (http://webit.pk/)
 * @license MIT
 */

// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

// Detect module name from filename.
$gatewayModuleName = basename(__FILE__, '.php');

// Fetch gateway configuration parameters.
$gatewayParams = getGatewayVariables($gatewayModuleName);
$systemUrl = $gatewayParams['systemurl'];
$accountId = $gatewayParams['accountID'];

/**
 * Test Mode yes/no
 * Added in v.1.1
 */
$testMode = $gatewayParams['testMode'];
if ( !$testMode ) {
    $confirmUrl = 'https://easypay.easypaisa.com.pk/easypay/Confirm.jsf';
}else{
    $confirmUrl = 'https://easypaystg.easypaisa.com.pk/easypay/Confirm.jsf';
}

// Die if module is not active.
if (!$gatewayParams['type']) {
    die("Module Not Activated");
} ?>

<?php if(isset($_REQUEST['auth_token'])){ ?>
<html>
<head>
  <title>Loading ...</title>
</head>
<body>
    <form action="<?php echo $confirmUrl;?>" method="POST" id="easyPayAuthForm">
        <input type="hidden" name="auth_token" value="<?php echo $_REQUEST['auth_token']; ?>">
        <input type="hidden" name="postBackURL" value="<?php echo $systemUrl.'modules/gateways/callback/EasyPay.php'; ?>">
        <button type="submit" name="pay" class="btn btn-success">Processing...</button>
    </form>
    <script>
        (function(){
            document.getElementById("easyPayAuthForm").submit();
        })();
    </script>
</body>
</html>

<?php exit; }
// Retrieve data returned in payment gateway callback
elseif (isset($_REQUEST['paymentToken']))
{ $invoiceId = $_REQUEST['orderRefNumber'];
?>
<html>
<head>
<title>Token Issued</title>
</head>
    <script language="javascript">
  setTimeout( function() { window.location.replace("<?php echo $systemUrl.'viewinvoice.php?id='.$invoiceId ?>"); }, 5000);
    </script>
<div class="content">
  <h2>Token Issued ... </h2>
    </div>
<div class="content">
  <h1>Token # <?php echo $_REQUEST['paymentToken'] ?></h1>
  <h2>EasyPaisa</h2>
  <p>Payments</p>
</div>
</html>
<?php exit; }

elseif(isset($_REQUEST['orderRefNumber']))
{
$invoiceId = $_REQUEST['orderRefNumber'];
if($_REQUEST['success']=="true"){
    $status='paymentsuccess=1';
}else{
    $status='paymentfailed=1';
} ?>
<script language="javascript">
  window.location.replace("<?php echo $systemUrl.'viewinvoice.php?id='.$invoiceId."&".$status ?>");
    </script>
<?php
}
if (isset($_REQUEST['url']))
{
// Matching if the URL == easypay.easypaisa.com.pk
$web = '/^https:\/\/easypay\.easypaisa\.com\.pk\//i';
$url = $_REQUEST['url'];
if (preg_match($web,$url)){
    // If the URL was easypay's original url then we should cURL it to extract Transaction Result.
    // Using cURL to extract EasyPay IPN JSON data sent to the Callback.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url); // using cURL @ $url to get transaction data.
    $result=curl_exec($ch);
    curl_close($ch);
    //Decoding JSON results into PHP object
    $obj = json_decode($result);
    $status = $obj->transaction_status;          // Payment Status
    $storeId = $obj->store_id;                   // Store ID
    $invoiceId = $obj->order_id;                 // Invoice ID
    $transactionId = $obj->transaction_id;       // Transaction ID
    $paymentAmount = $obj->transaction_amount;   // Transaction Amount
    $paymentMethod = $obj->payment_method;       // Payment Method

    // switching to see what payment method was used.
    switch($paymentMethod){
        case "OTC":
            $paymentMethod = "EasyPaisa Shop";
            break;
        case "CC":
            $paymentMethod = "Credit Card";
            break;
        case "MA":
            $paymentMethod = "EasyPaisa Mobile Account";
            break;
        default:
            $paymentMethod ;
    }
 }
    /**
 * Validate Callback Invoice ID.
 *
 * Checks invoice ID is a valid invoice number. Note it will count an
 * invoice in any status as valid.
 *
 * Performs a die upon encountering an invalid Invoice ID.
 *
 * Returns a normalised invoice ID.
 */
$invoiceId = checkCbInvoiceID($invoiceId, $gatewayParams['name']);

/**
 * Check Callback Transaction ID.
 *
 * Performs a check for any existing transactions with the same given
 * transaction number.
 *
 * Performs a die upon encountering a duplicate.
 */
checkCbTransID($transactionId."<br>".$paymentMethod);

/**
 * Log Transaction.
 *
 * Add an entry to the Gateway Log for debugging purposes.
 *
 * The debug data can be a string or an array. In the case of an
 * array it will be
 *
 * @param string $gatewayName        Display label
 * @param string|array $debugData    Data to log
 * @param string $transactionStatus  Status
 */
logTransaction($gatewayParams['name'], $_REQUEST, "PAID");

if ($status=="PAID" && $storeId == $accountId){
    /**
     * Add Invoice Payment.
     *
     * Applies a payment transaction entry to the given invoice ID.
     *
     * @param int $invoiceId         Invoice ID
     * @param string $transactionId  Transaction ID
     * @param float $paymentAmount   Amount paid (defaults to full balance)
     * @param float $paymentFee      Payment fee (optional)
     * @param string $gatewayModule  Gateway module name
     */
    addInvoicePayment(
        $invoiceId,
        $transactionId."<br>".$paymentMethod,
        $paymentAmount,
        0,
        $gatewayModuleName
    );
}
//header('Location: '.$systemUrl.'viewinvoice.php?id='.$invoiceId);
} ?>
