<?php
$appear = '';

require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");
require_once __DIR__ . '/../database/class-DataBase-Helper.php';

class EduCash_Helper
{

    public function add_educash($clientEmail, $educash, $money, $comment, $firstname, $lastname, $street, $city, $postalcode, $state, $country)
	{
		global $wpdb;
        $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
        $users_table = $wpdb->prefix.users;

        $client_ID = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$clientEmail' ");
        $final_total = $this->get_educash($client_ID_result) + $educash;
        if($final_total>=0){
			$add_to_database = new DataBase_Helper();
			$add_to_database->addvaluetodatabase($client_ID, $educash, $money, $comment, $firstname, $lastname, $street, $city, $postalcode, $state, $country);
		}
	}

	public function get_educash($client_ID)
	{
		global $wpdb;
        $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
        $users_table = $wpdb->prefix.users;

	    $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE client_id = '$client_ID' ");
		return $total;
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
		$currentEduCashValue = $databaseHelper->get_educash_for_user($user_id);
		$newEduCashValue = $currentEduCashValue + $amount;
		$transaction_cost = $amount;
		if ($newEduCashValue > 0) {
			$insertion_status = $databaseHelper->add_educash_transaction($user_id, $transaction_cost, $transactionMessage);
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
