<?php
$appear = '';

require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");

class EduCash_Helper
{

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

  	  $current_user_id = $userId;
  	  $table_name1 = $wpdb->prefix . 'edugorilla_lead_client_mapping';
  	  $sql = "SELECT * FROM $table_name1 WHERE client_id = $current_user_id order by date_time";
  	  $totalrows = $wpdb->get_results($sql);

  	  $table_name2 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
  	  $sql = "SELECT * FROM $table_name2 WHERE client_id = $current_user_id";
  	  $total_cash = $wpdb->get_results($sql);
  	  $i = 0;
  	  if(count($total_cash)>0)
  	  {
  		foreach ($total_cash as $cash)
  		{
  		   if($cash->transaction > 0){
  			 $date = $cash->time;
  			 $consumption[$i]['date']= $date;
  			 $consumption[$i]['spent'] = $cash->transaction;
  			 $consumption[$i]['val'] = 0;
  			 $i=$i+1;
  			 $current_educash = $current_educash + ($cash->transaction);}
  		}
  	  }

  	  $current_educash = $current_educash - count($totalrows);
  	  if($current_educash<0)
  		 $current_educash = 0;

	  $user_cash = array("user_educash"=>$current_educash);
  	  update_option("user_educash_count",$user_cash);
	  $out = get_option("user_educash_count");
	  return $out['user_educash'];
	}

}

?>
