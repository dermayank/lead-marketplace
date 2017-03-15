<?php

$wploadPath = explode('/wp-content/', dirname(__FILE__));
include_once(str_replace('wp-content/' , '', $wploadPath[0] . '/wp-load.php'));


$educash_helper_path = explode('/inc/',dirname(__FILE__));
include_once(str_replace('/inc','',$educash_helper_path[0].'/frontend/class-EduCash-Helper.php'));


global $wpdb;


session_start();
if(isset($_POST['amount']) && isset($_POST['status']) && isset($_POST['txnid']) && isset($_POST['email']) && isset($_SESSION['userid']) && isset($_SESSION['rate']))
{
  if(!empty($_POST['amount']) && !empty($_POST['status']) && !empty($_POST['txnid']) && !empty($_POST['email']) && !empty($_SESSION['userid']) && !empty($_SESSION['rate']))
  {

    $status=$_POST["status"];
    $amount=$_POST["amount"];
    $txnid=$_POST["txnid"];
    $email=$_POST["email"];
    $userid = $_SESSION["userid"];
    $rate = $_SESSION["rate"];
    $educash = $amount/$rate;
          if($status == "success"){

              $email_setting_options = get_option('edugorilla_email_setting2');
              $email_subject = stripslashes($email_setting_options['subject']);
              $email_body = stripslashes($email_setting_options['body']);
              $email_body = str_replace("{educash}", $educash, $email_body);
              $to = $email;
              $headers = array('Content-Type: text/html; charset=UTF-8');
              $value = wp_mail($to,$email_subject,$email_body,$headers);


              $eduCashHelper = new EduCash_Helper();
              $eduCashHelper->addEduCashToUser($userid, $educash, $status);

              echo "<h3>Thank You. Your order status is ". $status .".</h3>";
              echo "<h4>Your Transaction ID for this transaction is ".$txnid.".</h4>";
              echo "<h4>We have received a payment of Rs. " . $amount . ". Soon you will be allocated expected educash.</h4>";

          }
          else{
            echo "<h3>Your order status is ". $status .".</h3>";
            echo "<h4>Your transaction id for this transaction is ".$txnid.". You may retry making the payment.</h4>";
          }
  }
}
else{
  echo "you are not allowed to view this page";
}

session_destroy();
?>
