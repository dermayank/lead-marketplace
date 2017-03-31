<?php

$wploadPath = explode('/wp-content/', dirname(__FILE__));
include_once(str_replace('wp-content/' , '', $wploadPath[0] . '/wp-load.php'));

$educash_helper_path = explode('/inc/',dirname(__FILE__));
include_once(str_replace('/inc/payumoney','',$educash_helper_path[0].'/pdf_library/invoice_functions.php'));

include_once plugin_dir_path(__FILE__) . "url_shortner.php";

$educash_helper_path = explode('/inc/',dirname(__FILE__));
include_once(str_replace('/inc','',$educash_helper_path[0].'/frontend/class-EduCash-Helper.php'));


$sms_code = explode('/inc/',dirname(__FILE__));
include_once(str_replace('/inc','',$sms_code[0].'/api/gupshup.api.php'));

global $wpdb;

//$user = get_user_by( 'id', 1 );
//$mobile_no = get_user_meta(1,'user_general_phone',true);
//echo $full_name."\n\n".$user->user_address_street_and_number."- ".$user->user_address_postal_code.", " .$user->user_address_city."\n\n".$clientName."\n\n".$mobile_no;

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
    $mobile_no = get_user_meta($user_id,'user_general_phone',true);
          if($status == "success"){

              $eduCashHelper = new EduCash_Helper();
              $current_count = $eduCashHelper->getEduCashForUser($userid) + $educash;
              $user_cash = array("user_educash"=>$current_count,"users_id"=>$userid);
              update_option("user_educash_count",$user_cash);
              $credentials = get_option("ghupshup_credentials");
              $full_name = $user->first_name." ".$user->last_name;
              $url = get_home_url(); /* replace it with url to send */


            $pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
            $pdf->AddPage();

            $pdf->right_blocks(40, 45, 22, "EduGorilla Community Pvt. Ltd.");
            $pdf->right_blocks(40, 55, 12, "Regn. No. U74999UP2016PTC088614");
            $pdf->Image("https://electronicsguide.000webhostapp.com/wp-content/uploads/2017/01/eg_logo.jpg",10,35,(53.898305)/2,30);

                             //$r = $wpdb->get_row("SELECT * FROM $table_name3 WHERE time = '$time' ");

            $pdf->right_blocks(80, 10, 30, "INVOICE");
            $pdf->right_blocks(145, 93, 12, "Date: ".date("d/m/Y"));
            $pdf->right_blocks(145, 100, 12, "Transaction id: ".$txnid);
            $pdf->right_blocks_bold(7, 93, 12, "Billed to: ");
            $pdf->right_blocks(100, 205, 18, "PAYMENT MADE: ");
            $pdf->right_blocks(7, 230, 18, "THANKS FOR YOUR BUSINESS");

            $pdf->right_blocks(160, 205, 18, "Rs. ".$amount."/-");


            $pdf->addCompanyAddress("Address: 4719/A, Sector 23A, Gurgaon-122002, India\n\nWebsite: https://edugorilla.com\n\nEmail: hello@edugorilla.com\n\nPhone no. +91 9410007819");
            $pdf->addClientAddress(ucwords("\n".$user->first_name.' '.$user->last_name."\n\n".
                                                    $user->user_address_street_and_number."- ".$user->user_address_postal_code.", " .
                                                    $user->user_address_city)."\n\n".$email."\n\n".$mobile_no);

             $cols=array( "Item" => 61,
                                "Rate"      => 43,
                                "Quantity"  => 43,
                                "Amount"    => 43,);
             $pdf->addCols( $cols);
                             $cols=array( "Item"      => "C",
                                          "Rate"      => "C",
                                          "Quantity"  => "C",
                                          "Amount"    => "C");
              $pdf->addLineFormat( $cols);
              $pdf->addLineFormat($cols);
              $y    = 165;
              $educash_rate = get_option("current_rate");
              $line = array( "Item"      => "EduCash",
                                            "Rate"      => $rate,
                                            "Quantity"  => $educash,
                                            "Amount"    => "Rs. ".$amount."/-");
              $size = $pdf->addLine( $y, $line );
              $y   += $size + 2;

              $file_name = sys_get_temp_dir();
              $file_name.= "/invoice.pdf";
              $pdf->Output($file_name , "F");
              $attachment = array($file_name);

              $email_setting_options = get_option('edugorilla_email_setting2');
              $email_subject = stripslashes($email_setting_options['subject']);
              $email_body = stripslashes($email_setting_options['body']);

              $email_body = str_replace("{ReceivedCount}", $educash, $email_body);
              $email_body = str_replace("{EduCashCount}", $current_count, $email_body);
              $email_body = str_replace("{EduCashUrl}",$url, $email_body);
              $email_body = str_replace("{Contact_Person}", $user->first_name, $email_body);
              $to = $email;
              $headers = array('Content-Type: text/html; charset=UTF-8');
              $value = wp_mail($to,$email_subject,$email_body,$headers,$attachment);

              $sms_setting_options2 = get_option('edugorilla_sms_setting2');
              $short_url = shorten_url($url);
              $edugorilla_sms_body2 = str_replace("{ReceivedCount}", $educash, $edugorilla_sms_body2);
              $edugorilla_sms_body2 = str_replace("{EduCashCount}", $current_count, $edugorilla_sms_body2);
              $edugorilla_sms_body2 = str_replace("{EduCashUrl}",$short_url, $edugorilla_sms_body2);
              $edugorilla_sms_body2 = str_replace("{Contact_Person}", $full_name, $edugorilla_sms_body2);

              $edugorilla_sms_body2 = stripslashes($sms_setting_options2['body']);

              $eduCashHelper->addEduCashToUser($userid, $educash, $status);
              $value = send_sms($credentials['user_id'],$credentials['password'],$mobile_no, $edugorilla_sms_body2);

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
