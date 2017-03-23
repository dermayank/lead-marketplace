<?php
session_start();
// End point - change to https://secure.payu.in for LIVE mode
$PAYU_BASE_URL = "https://test.payu.in";

// Merchant key here as provided by Payu
    $MERCHANT_KEY = $_POST['txnid'];

// Merchant Salt as provided by Payu

    $SALT = $_POST['saltid'];//"ddKr3Wsb";


if(isset($_POST['userid']) &&isset($_POST['rate'])){
  if(!empty($_POST['userid']) && !empty($_POST['rate'])){
    $_POST['amount']=$_POST['amount']*$_POST['rate'];
    $_SESSION['rate']=$_POST['rate'];
    $_SESSION['userid']=$_POST['userid'];
  }
}

$action = '';

$posted = array();
if(!empty($_POST)) {
    //print_r($_POST);
  foreach($_POST as $key => $value) {
    $posted[$key] = $value;

  }
}

$formError = 0;

if(empty($posted['txnid'])) {
  // Generate random transaction id
  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
  $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
  if(
          empty($posted['key'])
          || empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
          || empty($posted['productinfo'])
          || empty($posted['surl'])
          || empty($posted['furl'])
		  || empty($posted['service_provider'])
  ) {
    $formError = 1;
  } else {
    //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
	$hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';
	foreach($hashVarsSeq as $hash_var) {
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }

    $hash_string .= $SALT;


    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
  }
} elseif(!empty($posted['hash'])) {
  $hash = $posted['hash'];
  $action = $PAYU_BASE_URL . '/_payment';
}
?>

  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
  </script>

  <body onload="submitPayuForm()">
    <form action="<?php echo $action; ?>" method="post" name="payuForm">
      <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
      <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />

          <input type="hidden" name="amount" value="<?php echo $posted['amount'] ?>" />
          <input type="hidden" name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" />
          <input type="hidden" name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" />
          <input type="hidden" name="phone" value="<?php echo (empty($posted['phone'])) ? '123456789' : $posted['phone']; ?>" />
          <input type="hidden" name="saltid" id="saltid" value="<?php echo $_POST['saltid']; ?>" type="hidden"/>
          <input name="productinfo" type="hidden" value="<?php echo "Purchase Of ".$_POST['amount']." educash "; ?>"/>
          <input type="hidden" name="surl" value="<?php echo (empty($posted['furl'])) ? 'success' : $posted['furl']; ?>" size="64" />
          <input type="hidden" name="furl" value="<?php echo (empty($posted['furl'])) ? 'failed' : $posted['furl']; ?>" size="64" />


          <input type="hidden" name="service_provider" value="payu_paisa" size="64" />
          <input name="lastname" id="lastname" value="" type="hidden" />
          <input name="curl" value="" type="hidden"/>
          <input name="address1" value="" type="hidden" />
          <input name="address2" value="" type="hidden" />
          <input name="city" value="" type="hidden" />
          <input name="state" value="" type="hidden" />
          <input name="country" value="" type="hidden" />
          <input name="zipcode" value="" type="hidden" />
          <input name="udf1" value="" type="hidden" />
          <input name="udf2" value="" type="hidden" />
          <input name="udf3" value="" type="hidden" />
          <input name="udf4" value="" type="hidden" />
          <input name="udf5" value="" type="hidden" />
          <input name="pg" type="hidden" value="" />

          <h1>Transaction in progress, do not press refesh or back button </h1>

    </form>
  </body>
