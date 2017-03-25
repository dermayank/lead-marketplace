<?php
    session_start();
    global $wpdb;

    $wploadPath = explode('/wp-content/', dirname(__FILE__));
    include_once(str_replace('wp-content/' , '', $wploadPath[0] . '/wp-load.php'));

    $sms_code = explode('/inc/',dirname(__FILE__));
    include_once(str_replace('/inc','',$sms_code[0].'/api/gupshup.api.php'));

    if(isset($_POST['amount'])&& isset($_POST['userid']) && isset($_POST['conversion_karmas']) && isset($_POST['email']) && isset($_SESSION['stop_reload']))
    {
        if(!empty($_POST['amount']) && !empty($_POST['userid']) && !empty($_POST['conversion_karmas']) && !empty($_POST['email']) && !empty($_SESSION['stop_reload']))
        {
            $user_id = $_POST['userid'];
            $conversion_karmas = $_POST['conversion_karmas'];
            $balance = mycred_get_users_cred($user_id);
            $educash = $_POST['amount'];
            $karmas = $educash*$conversion_karmas;
            $user = get_user_by( 'id', $user_id );
            $email = $_POST['email'];
            $mobile_no = get_user_meta($user_id,'user_general_phone',true);
            if($balance >= $karmas)
            {
               $eduCashHelper = new EduCash_Helper();
               $current_count = $eduCashHelper->getEduCashForUser($user_id) + $educash;
               $user_cash = array("user_educash"=>$current_count,"users_id"=>$user_id);
               update_option("user_educash_count",$user_cash);
               $smsapi = get_option("smsapi");

               $url = get_home_url();
               $url = $url."/manage-leads";

               mycred_subtract( 'Deduction',$current_user->id , $karmas, $karmas.' karmas are deducted from your account for the purchase of '.$educash.'educash', date( 'W' ) );
               $new_balance = mycred_get_users_cred($user_id);
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

               $sms_setting_options2 = get_option('edugorilla_sms_setting2');
               $edugorilla_sms_body2 = stripslashes($sms_setting_options2['body']);

               $status = "success";
               echo "<h2>Thank You. Your order status is ". $status .".</h2>";
               echo "<h2>Soon you will be allocated ".$educash." educash.</h2>";

               $eduCashHelper->addEduCashToUser($user_id, $educash, $status);
               $value = send_sms($smsapi['username'],$smsapi['password'],$mobile_no, $edugorilla_sms_body2);

            }
            else{
                echo("<h1>It looks like you don't have enough karmas to buy educash. Try another method of payment.</h1>");
                $redirecting_url = get_home_url();
                echo '<script>location.href="'.$redirecting_url.'";</script>';
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
