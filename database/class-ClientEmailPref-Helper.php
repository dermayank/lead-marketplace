<?php

class ClientEmailPref_Helper
{
	public function removeUnsubscribedEmails($filterEmailIds)
	{
		global $wpdb;
		$resultEmailIds = array();
		$table_name = $wpdb->prefix . 'edugorilla_client_preferences';
		$client_email_addresses = $wpdb->get_results("SELECT * FROM $table_name");
		foreach ($client_email_addresses as $cea) {
			if (in_array($cea->email_id, $filterEmailIds)) {
				if ($cea->unsubscribe_email != 0) {
					$filterEmailIds = array_diff($filterEmailIds, array($cea->email_id));
				}
			}
		}
		return $filterEmailIds;
	}
}


?>
