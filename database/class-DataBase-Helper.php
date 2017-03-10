<?php

class DataBase_Helper
{
	private $eduCashMetaDataKey = 'edu_cash_amount';

    public function addvaluetodatabase($client_ID, $educash, $money, $comment, $firstname, $lastname, $street, $city, $postalcode, $state, $country)
	{
		global $wpdb;
        $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
        $users_table = $wpdb->prefix.users;
		
        $adminName = wp_get_current_user();
		$time = current_time('mysql');
		
		$wpdb->insert($table_name3, array(
                'time' => $time,
                'admin_id' => $adminName->ID,
                'client_id' => $client_ID,
                'transaction' => $educash,
                'amount' => $money,
                'comments' => $comment
            ));
		update_user_meta($client_ID, 'user_general_first_name', $firstname);
		update_user_meta($client_ID, 'user_general_last_name', $lastname);
		update_user_meta($client_ID, 'user_address_street_and_number', $street);
		update_user_meta($client_ID, 'user_address_city', $city);
		update_user_meta($client_ID, 'user_address_postal_code', $postalcode);
		update_user_meta($client_ID, 'user_address_county', $state);
		update_user_meta($client_ID, 'user_address_country', $country);
	}

	public function add_educash_transaction($client_id, $educash, $adminComment)
	{
		global $wpdb;
		$transaction_table = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
		$time = current_time('mysql');
		$insert_status = $wpdb->insert($transaction_table, array(
			'time' => $time,
			'admin_id' => -1,
			'client_id' => $client_id,
			'transaction' => $educash,
			'comments' => $adminComment
		));
		return $insert_status;
	}

	private function metaEduCashFromUser($user_id, $amount)
	{
		$eduCashValue = get_user_meta($user_id, $key = $this->eduCashMetaDataKey, $single = true);
		if (!$eduCashValue) {
			//Initially everyone gets 10 eduCash
			$eduCashValue = 10;
			// Optional, default is false. Whether the same key should not be added.
			$unique = true;
			$meta_id = add_user_meta($user_id, $this->eduCashMetaDataKey, $eduCashValue, $unique);
			if (false == $meta_id) {
				return "Unable to Create EduCash Meta Tag";
			}
		}
		$newEduCashValue = $eduCashValue - $amount;
		if ($newEduCashValue < 0) {
			return "Not Enough Funds";
		}
		$databaseHelper = new DataBase_Helper();
		$databaseHelper->add_educash_transaction($user_id, $newEduCashValue, "Unlocked a lead");
		$update_status = update_user_meta($user_id, $this->eduCashMetaDataKey, $newEduCashValue, $eduCashValue);
		if (true != $update_status) {
			return "Unable to update EduCash Meta Tag";
		}
		return "Success";
	}

	public function get_educash_for_user($current_user_id)
	{
		global $wpdb;
		$current_educash = 0;
		$transaction_table = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
		$sql = "SELECT * FROM $transaction_table WHERE client_id = $current_user_id";
		$total_cash = $wpdb->get_results($sql);
		$i = 0;
		if (count($total_cash) > 0) {
			foreach ($total_cash as $cash) {
				if ($cash->transaction > 0) {
					$date = $cash->time;
					$consumption[$i]['date'] = $date;
					$consumption[$i]['spent'] = $cash->transaction;
					$consumption[$i]['val'] = 0;
					$i = $i + 1;
					$current_educash = $current_educash + ($cash->transaction);
				}
			}
		}
		if ($current_educash < 0)
			$current_educash = 0;
		return $current_educash;
	}

	private function modify_educash_from_user($user_id, $amount)
	{

	}
}


?>
