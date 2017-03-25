<?php
$appear = '';

require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");
require_once __DIR__ . '/../database/class-DataBase-Helper.php';

class EduLead_Helper
{

	public function set_card_unlock_status_to_db($client_id, $lead_id, $unlock_status)
	{
		global $wpdb;
		$out = get_option("educashtolead_rate");
		$lead_table = $wpdb->prefix . 'edugorilla_lead_client_mapping';
		$result_status_string = "";
		if ($unlock_status == '1') {
			$eduCashHelper = new EduCash_Helper();
			$eduCashCostForLead = $out['rate'];  // fetched from meta table
			$query_status = $eduCashHelper->removeEduCashFromUser($client_id, $eduCashCostForLead);
			if (!$this->str_starts_with($query_status, "Success")) {
				return new WP_Error('EduCashError', $query_status . " for $client_id");
			}
			$result_status_string = $query_status;
		} else {
			$result_status_string = "Locking the card for status $unlock_status.";
		}
		$update_query = "UPDATE $lead_table SET is_unlocked = '$unlock_status' WHERE $lead_table.lead_id = $lead_id AND $lead_table.client_id = $client_id";
		$wpdb->get_results($update_query);

		$data_object = array();
		$data_object[] = "Successfully updated unlock_status to the database : $result_status_string";
		$response = new WP_REST_Response($data_object);
		return $response;
	}

	/**
	 * Get details of all the leads table from database
	 *
	 */
	public function get_lead_details_from_db($client_id)
	{
		global $wpdb;
		//$card1 = new Lead_Card('Rohit', 'Lucknow', 'CEO', 'Nirvana');
		//$card2 = new Lead_Card('Anantharam', 'Chennai', 'CTO', 'Relationship');
		$cards_object = array();
		$lead_detail_table = $wpdb->prefix . 'edugorilla_lead_details';
		$lead_table = $wpdb->prefix . 'edugorilla_lead_client_mapping';


		$detail_query = "select * from $lead_detail_table";
		$leads_details = $wpdb->get_results($detail_query, 'ARRAY_A');
		foreach ($leads_details as $leads_detail) {
			$lead_id = $leads_detail['id'];
			$lead_name = $leads_detail['name'];
			$lead_email = $leads_detail['contact_no'];
			$lead_contact_no = $leads_detail['email'];
			$lead_query = $leads_detail['query'];
			$lead_category = $leads_detail['category_id'];
			$lead_location = $leads_detail['location_id'];
			$lead_date_time = $leads_detail['date_time'];
			$lead_is_promotional = $leads_detail['is_promotional'];
			$mapping_query = "select * from $lead_table WHERE lead_id=$lead_id";
			$leads_mapping_details = $wpdb->get_results($mapping_query, 'ARRAY_A');
			$lead_is_unlocked = "unknown";
			$lead_is_hidden = "unknown";
			foreach ($leads_mapping_details as $leads_mapping_detail) {
				$lead_is_unlocked = $leads_mapping_detail['is_unlocked'];
				$lead_is_hidden = $leads_mapping_detail['is_hidden'];
			}
			if ($lead_is_unlocked == "unknown" || $lead_is_hidden == "unknown") {
				//Seems like a new client, so creating this new row.
				$card_unlock_status = 0;
				if ($lead_is_promotional == "yes") {
					$card_unlock_status = 1;
				}
				$result1 = $wpdb->insert(
					$lead_table,
					array(
						'client_id' => $client_id,
						'lead_id' => $lead_id,
						'is_unlocked' => $card_unlock_status
					)
				);

				$lead_is_unlocked = $card_unlock_status;
				$lead_is_hidden = 0;
			}
			$db_card = new Lead_Card($lead_id, $lead_name, $lead_email, $lead_contact_no, $lead_query, $lead_category, $lead_location, $lead_date_time, $lead_is_unlocked, $lead_is_hidden);
			$cards_object[] = $db_card;
		}

		return $cards_object;
	}

	public function set_card_hidden_status_to_db($client_id, $lead_id, $hidden_status)
	{
		global $wpdb;
		$lead_table = $wpdb->prefix . 'edugorilla_lead_client_mapping';
		$update_query = "UPDATE $lead_table SET is_hidden = '$hidden_status' WHERE $lead_table.lead_id = $lead_id AND $lead_table.client_id = $client_id";
		$hidden_status_update_result = $wpdb->get_results($update_query);
		return $hidden_status_update_result;
	}

	function str_starts_with($haystack, $needle)
	{
		return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
	}

	function str_ends_with($haystack, $needle)
	{
		return substr_compare($haystack, $needle, -strlen($needle)) === 0;
	}

}

?>
