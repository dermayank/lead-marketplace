<?php
    session_start();
    $wploadPath = explode('/wp-content/', dirname(__FILE__));
    include_once(str_replace('wp-content/' , '', $wploadPath[0] . '/wp-load.php'));
    if(isset($_POST['amount'])&& isset($_POST['userid']) && isset($_POST['conversion_karmas']) && isset($_POST['email']) && isset($_SESSION['stop_reload']))
    {
        if(!empty($_POST['amount']) && !empty($_POST['userid']) && !empty($_POST['conversion_karmas']) && !empty($_POST['email']) && !empty($_SESSION['stop_reload']))
        {
            $user_id = $_POST['userid'];
            $conversion_karmas = $_POST['conversion_karmas'];
            $balance = mycred_get_users_cred($user_id);
            $educash = $_POST['amount'];
            $karmas = $educash*$conversion_karmas;

            $email = $_POST['email'];

            if($balance >= $karmas)
            {
               mycred_subtract( 'Deduction',$current_user->id , $karmas, $karmas.' karmas are deducted from your account for the purchase of '.$educash.'educash', date( 'W' ) );
               $new_balance = mycred_get_users_cred($user_id);
               $email_setting_options = get_option('edugorilla_email_setting2');
               $email_subject = stripslashes($email_setting_options['subject']);
               $email_body = stripslashes($email_setting_options['body']);
               $email_body = str_replace("{educash}", $educash, $email_body);
               $email_body = str_replace("{balance}", $balance, $email_body);
               $email_body = str_replace("{new balance}", $new_balance, $email_body);
               $to = $email;
               $headers = array('Content-Type: text/html; charset=UTF-8');
               $value = wp_mail($to,$email_subject,$email_body,$headers);
               $status = "success";
               echo "<h3>Thank You. Your order status is ". $status .".</h3>";
               echo "<h4>Soon you will be allocated expected educash.</h4>";

               $eduCashHelper = new EduCash_Helper();
               $eduCashHelper->addEduCashToUser($user_id, $educash, $status);
            }
            else{
                include_once str_replace('inc/','frontend/custom_dialog.php',plugin_dir_path( __FILE__ ));
                custom_dialog("It looks like you don't have enough karmas to buy educash. Try another method of payment.");
            }
        }
    }
    else{
        echo "<b>Sorry, you are not allowed to view this page.</b>";
    }
    session_destroy();
 ?>
