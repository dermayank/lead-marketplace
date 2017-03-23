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
    $user = get_user_by( 'id', $userid );

          if($status == "success"){

              $eduCashHelper = new EduCash_Helper();
              $current_count = $eduCashHelper->getEduCashForUser($userid) + $educash;
              $user_cash = array("user_educash"=>$current_count,"users_id"=>$userid);
              update_option("user_educash_count",$user_cash);

              $url = get_home_url();
              $url = $url."/manage-leads";

              $email_setting_options = get_option('edugorilla_email_setting2');
              $email_subject = stripslashes($email_setting_options['subject']);
              $email_body = stripslashes($email_setting_options['body']);

              $email_body = str_replace("{ReceivedCount}", $educash, $email_body);
              $email_body = str_replace("{EduCashCount}", $current_count, $email_body);
              $email_body = str_replace("{EduCashUrl}","$url", $email_body);
              $email_body = str_replace("{Contact_Person}", $user->first_name, $email_body);
              $to = $email;
              $headers = array('Content-Type: text/html; charset=UTF-8');
              $value = wp_mail($to,$email_subject,$email_body,$headers);


              $eduCashHelper->addEduCashToUser($userid, $educash, $status);

              echo "<h2>Thank You. Your order status is ". $status .".</h2>";
              echo "<h2>Your Transaction ID for this transaction is ".$txnid.".</h2>";
              echo "<h2>We have received a payment of Rs. " . $amount . ". Soon you will be allocated ".$educash." educash.</h2>";

          }
          else{
            echo "<h2>Your order status is ". $status .".</h2>";
            echo "<h2>Your transaction id for this transaction is ".$txnid.". You may retry making the payment.</h2>";
          }
  }
}
else{?>
    <div><h1>Sorry, you are not allowed to view this page</h1></div>
    <?php
    $redirecting_url = get_home_url();
    echo '<script>location.href="'.$redirecting_url.'";</script>';

}

session_destroy();
?>
