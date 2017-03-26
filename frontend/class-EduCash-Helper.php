<?php
$appear = '';

require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");
require_once __DIR__ . '/../database/class-DataBase-Helper.php';

class EduCash_Helper
{
    public function add_educash($clientEmail, $educash, $money, $comment, $firstname, $lastname, $street, $city, $postalcode, $phone_number, $country)
	{
		global $wpdb;
        $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
        $users_table = $wpdb->prefix.users;

        $client_ID = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$clientEmail' ");
		$current_educash = $this->get_educash($client_ID);
        $final_total = $current_educash + $educash;
        if($final_total >= 0){
			$add_to_database = new DataBase_Helper();
			$add_to_database->addvaluetodatabase($client_ID, $educash, $money, $comment, $firstname, $lastname, $street, $city, $postalcode, $phone_number, $country);
            $transaction_done = true;
		}
		else{
			$transaction_done = false;
		}

		return $transaction_done;
	}

	public function get_educash($client_ID)
	{
		global $wpdb;
        $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
        $users_table = $wpdb->prefix.'users';

        $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE client_id = '$client_ID' ");
		return $total;
	}
	
	public function send_email($firstname, $lastname, $total, $clientName, $educash_added, $attachment)
	{
		$edugorilla_email_datas = get_option('edugorilla_email_setting2');
        $edugorilla_email_datas2 = get_option('edugorilla_email_setting3');
        $arr1 = array("{Contact_Person}", "{ReceivedCount}", "{EduCashCount}", "{EduCashUrl}");
        $to = $clientName;
        if($educash_added > 0){
        $positive_email_subject = $edugorilla_email_datas['subject'];
        $subject =  $positive_email_subject;
        $arr2 = array($firstname." ".$lastname, $educash_added, $total, "https://edugorilla.com/");
        $positive_email_body = str_replace($arr1, $arr2, $edugorilla_email_datas['body']);
        $message =  $positive_email_body;
		
		wp_mail( $to, $subject, $message, "Content-type: text/html; charset=iso-8859-1", $attachment);
		
		}
        else{
        $negative_email_subject = $edugorilla_email_datas2['subject'];
        $subject =  $negative_email_subject;
        $negative_educash = $educash_added*(-1);
        $arr3 = array($firstname." ".$lastname, $negative_educash, $total, "https://edugorilla.com/");
        $negative_email_body = str_replace($arr1, $arr3, $edugorilla_email_datas2['body']);
        $message =  $negative_email_body;
		
		 wp_mail($to, $subject, $message, "Content-type: text/html; charset=iso-8859-1");
        }
	}

	public function display_current_transaction($time, $clientName)
	{
		global $wpdb;
        $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
		$adminName = wp_get_current_user();
		
		$r = $wpdb->get_row("SELECT * FROM $table_name3 WHERE time = '$time' ");
        echo "<center></p>You have made the following entry just now:</p>";
        echo "<table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
        echo "<tr><td>" . $r->id . "</td><td>" . $adminName->user_email . "</td><td>" . $clientName . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
        echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>Educash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
        echo "</table></center><br/><br/>";
	}

	public function removeEduCashFromCurrentUser($amount)
	{
		$userId = wp_get_current_user()->ID;
		return $this->removeEduCashFromUser($userId, $amount);
	}

	public function getEduCashForCurrentUser()
	{
		$userId = wp_get_current_user()->ID;
		return $this->getEduCashForUser($userId);
	}

	public function addEduCashToUser($userId, $amount, $transactionMessage)
	{
        $databaseHelper = new DataBase_Helper();
		$currentEduCashValue = $databaseHelper->get_educash_for_user($userId);
		$newEduCashValue = $currentEduCashValue + $amount;
		$transaction_cost = $amount;
		if ($newEduCashValue > 0) {
			$insertion_status = $databaseHelper->add_educash_transaction($userId, $transaction_cost, $transactionMessage);
			return "Success : $insertion_status";
		}
		return "Insufficient Funds : $newEduCashValue";
	}

	public function removeEduCashFromUser($user_id, $amount)
	{
		$databaseHelper = new DataBase_Helper();
		$currentEduCashValue = $databaseHelper->get_educash_for_user($user_id);
		$newEduCashValue = $currentEduCashValue - $amount;
		$transaction_cost = -$amount;
		if ($newEduCashValue > 0) {
			$insertion_status = $databaseHelper->add_educash_transaction($user_id, $transaction_cost, "Unlocked a lead");
			return "Success : $insertion_status";
		}
		return "Insufficient Funds : $newEduCashValue";
	}

	public function getEduCashForUser($userId)
	{
	    $current_user = $userId;
  	    global $wpdb;
  	    $current_educash = 0;
		$out = get_option("user_educash_count");
	    if($out['users_id']!= $userId){
		  //echo "calledthis";
	  	  $current_user_id = $userId;

	  	  $table_name2 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
	  	  $sql = "SELECT * FROM $table_name2 WHERE client_id = $current_user_id";
	  	  $total_cash = $wpdb->get_results($sql);
	  	  $i = 0;
	  	  if(count($total_cash)>0)
	  	  {
	  		foreach ($total_cash as $cash)
	  		{
	  			 $date = $cash->time;
	  			 $consumption[$i]['date']= $date;
	  			 $consumption[$i]['spent'] = $cash->transaction;
	  			 $consumption[$i]['val'] = 0;
	  			 $i=$i+1;
			    $current_educash = $current_educash + ($cash->transaction);
	  	  	}
	  	  }

	  	  if($current_educash<0)
	  		 $current_educash = 0;

		  $user_cash = array("user_educash"=>$current_educash,"users_id"=>$current_user_id);
	  	  update_option("user_educash_count",$user_cash);
	   }
	   $out = get_option("user_educash_count");
	   return $out['user_educash'];
	}

}

?>
