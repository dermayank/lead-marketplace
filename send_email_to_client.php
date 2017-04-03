<?php

function table_for_client()
{
	global $wpdb;
	$table_name6 = $wpdb->prefix.'edugorilla_client_preferences'; //client preferences
	$sql6 = "CREATE TABLE $table_name6 (
				                            id int(15) NOT NULL,				                     
											client_name varchar(200) NOT NULL,
											email_id varchar(200) NOT NULL,
											contact_no varchar(50) NOT NULL,
											preferences varchar(100) NOT NULL,
											location varchar(100) NOT NULL,
											category varchar(100) NOT NULL,
											unsubscribe_sms boolean DEFAULT 0,
											unsubscribe_email boolean DEFAULT 0,
											unlock_lead boolean DEFAULT 0,
											PRIMARY KEY id (id)
				  					    ) $charset_collate;";


	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	//Creating a table in cureent wordpress
	dbDelta($sql6);

}

//end pluginUninstall function
//Populate the Client Data

function send_mail_with_unlock($edugorilla_email_subject, $edugorilla_email_body, $lead_card)
{
	global $wpdb;
	$location_ids = $lead_card->getLocationList();
	$category = $lead_card->getCategoryList();
	$lead_id = $lead_card->getId();
	$categoryArray = explode(',', $category);
	$locationArray = explode(',', $location_ids);
	$table_name = $wpdb->prefix .'edugorilla_client_preferences';
	$client_email_addresses = $wpdb->get_results("SELECT * FROM $table_name");
	$headers = array('Content-Type: text/html; charset=UTF-8');
	foreach ($client_email_addresses as $cea) {
		$categoryCheck = 0;
		$locationCheck = 0;
		foreach ($categoryArray as $currentCategory) {
			if (preg_match('/' . $currentCategory . '/', $cea->category)) {
				$categoryCheck = 1;
			}
		}
		foreach ($locationArray as $currentLocation) {
			if (preg_match('/' . $currentLocation . '/', $cea->location)) {
				$locationCheck = 1;
			}
		}
		if (preg_match('/Instant_Notifications/', $cea->preferences) AND $categoryCheck == 1 AND $locationCheck == 1) {
			echo $cea->client_name;
			$eduLeadHelper = new EduLead_Helper();
			$query_status = $eduLeadHelper->set_card_unlock_status_to_db($cea->email_id, $lead_id, 1);
			if (str_starts_with($query_status, "Success")) {
				$lead_card->setUnlocked(true);
				add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
				$institute_emails_status = wp_mail($cea->email_id, $edugorilla_email_subject, ucwords($edugorilla_email_body), $headers);
				remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
			}
		}
	}
	if ($institute_emails_status) {
		# code...
		echo "Mail send";
	}
	return $institute_emails_status;
}

function str_starts_with($haystack, $needle)
{
	return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
}

//function to display client preferences form
function get_category_current_user($user_id, $client_data)
{
		global $wpdb;
		$categories_list = get_terms('listing_categories', array('hide_empty' => false));
		if($client_data){
		$count = 1;
		$more_category = "";
		foreach ($categories_list as $category_) {
			# code...
			if (preg_match('/'.$category_->term_id.'/', $client_data->category)) {
				# code...
				$category_name = "category".$count;
				$more_category = $more_category.'<br/><input list="categories_list" name="'.$category_name.'" size="30" value="'.$category_->name.'">';
				$count = $count+1;
				
			}
		}
}
$category_result = array();
array_push($category_result ,$more_category);
array_push($category_result, $count);
return $category_result;

}

function get_location_current_user($user_id , $client_data)
{
		global $wpdb;
		$location_list = get_terms('locations', array('hide_empty' => false));;
		if($client_data){
		$count2 = 1;
			$more_location = "";
		foreach ($location_list as $location_) {
			# code...
			if (preg_match('/'.$location_->term_id.'/', $client_data->location)) {
				# code...
				$location_name = "location" . $count2;
				$more_location = $more_location.'<br/><input list="location_list" name="'.$location_name.'" size="30" value="'.$location_->name.'">';
				$count2 = $count2+1;
				
			}
		}

}
$location_result = array();
array_push($location_result ,$more_location);
array_push($location_result, $count2);
return $location_result;
}

function edugorilla_client(){
		$categories_list = get_terms('listing_categories', array('hide_empty' => false));
		$location_list = get_terms('locations', array('hide_empty' => false));
		$user_id = get_current_user_id();
		$category_count_value =1;
		$location_count_value =1;
		$notification = "";
		$in_val = "";
		$dd_val = "";
		$wd_val = "";
		$md_val = "";
		$unsub_email_val = "";
		$unsub_sms_val = "";
		$unlock_val = "";
		global $wpdb;
		$table_name = $wpdb->prefix . 'edugorilla_client_preferences';
		$current_user_data = $wpdb->get_row("SELECT * FROM $table_name  WHERE id = $user_id");
			$notificationString = $current_user_data->preferences;
			//$notificationArray = explode(",", $notificationString);
				if(preg_match('/Instant_Notifications/',$notificationString))
					$in_val = "checked";
				if(preg_match('/Daily_Digest/',$notificationString))
					$dd_val = "checked";
				if(preg_match('/Weekly_Digest/',$notificationString))
					$wd_val = "checked";
				if(preg_match('/Monthly_Digest/',$notificationString))
					$md_val = "checked";
				
        if($current_user_data->unlock_lead == 1)
		    $unlock_val = "checked"; 
        if($current_user_data->unsubscribe_email == 1)
		    $unsub_email_val = "checked";
        if($current_user_data->unsubscribe_sms == 1)
		    $unsub_sms_val = "checked";
                		
		$category_result = get_category_current_user($user_id , $current_user_data);
		$more_category = $category_result[0];
		$category_count_value = $category_result[1];
		$location_result = get_location_current_user($user_id , $current_user_data);
		$more_location = $location_result[0];
		$location_count_value = $location_result[1];
    
	if (isset($_POST['submit_client_pref'])) {
		$unlock_lead_ = $_POST['unlock_lead'];
		$notification_all = $_POST['notification'];
		if (!empty($notification_all)) {
			# code...
			$notification = "";
			foreach ($notification_all as $value) {
				# code...
				$notification = $value . ", " . $notification;
				if($value == "Instant_Notifications")
					$in_val = "checked";
				else if($value == "Daily_Digest")
					$dd_val = "checked";
				else if($value == "Weekly_Digest")
					$wd_val = "checked";
				else if($value == "Monthly_Digest")
					$md_val = "checked";
			}
		}
		$category_count = $_POST['category_count'];
		$location_count = $_POST['location_count'];
		$category_count_value = $category_count;
		$location_count_value = $location_count;
		$category = array();
		$location = array();
		$more_category = "";
		$more_location = "";
		for ($i = 0; $i < $category_count; $i++) {
			# code...
			$category_name = "category".$i;
			if ($i>0) {
				# code...
				$more_category = $more_category.'<br/><input list="categories_list" name="'.$category_name.'" size="30" value="'.$_POST[$category_name].'">';
			}else
				$category_select_val = $_POST[$category_name];
			array_push($category, $_POST[$category_name]);
		}
		
		for ($i = 0; $i < $location_count; $i++) {
			# code...
			$location_name = "location".$i;
			if ($i>0) {
				# code...
				$more_location = $more_location.'<br/><input list="location_list" name="'.$location_name.'" size="30" value="'.$_POST[$location_name].'">';
			}else
			$location_select_val = $_POST[$location_name];
			array_push($location, $_POST[$location_name]);
		}
		foreach ($category as $category_value) {
			$category_value =  str_replace("&","&amp;",$category_value);
			foreach ($categories_list as $cat_value) {
				//echo $categoryString;
				if(strcmp($cat_value->name , $category_value) == 0){
					$all_cat = $cat_value->term_id . "," . $all_cat;
				}
			}
		}
		foreach ($location as $location_value) {
			$location_value =  str_replace("&","&amp;",$location_value);
			foreach ($location_list as $loc_value) {
				if (strcmp($location_value , $loc_value->name) == 0) {
					# code...
					$all_loc = $loc_value->term_id . "," . $all_loc;
				}
			}
		}

        $not_email = $_POST['not_email'];
		$not_sms = $_POST['not_sms'];
 
		if($not_email == 1){
			$unsub_email_val = "checked";
		} else{
			$unsub_email_val = "";
			$not_email = 0;
		}
		 
		if($not_sms == 1){
			$unsub_sms_val = "checked";
		} else{
			$unsub_sms_val = "";
			$not_sms = 0;
		}
		if ($unlock_lead_ != 1) {
			$unlock_val = "";
			$unlock_lead_ = 0;
		}else
			$unlock_val = "checked";
		
		$user_id = get_current_user_id();
		echo $user_id;
		$user_detail = get_user_meta($user_id);
		$first_name = $user_detail['first_name'][0];
		$last_name = $user_detail['last_name'][0];
		$_client_name = $first_name . " " . $last_name;
		$client_email = $user_detail['user_general_email'][0];
		$client_contact = $user_detail['user_general_phone'][0];
 
		//Insert Data to table

			global $wpdb;
			$table_name = $wpdb->prefix . 'edugorilla_client_preferences';
			if ($wpdb->get_results("SELECT * FROM $table_name WHERE id = $user_id")) {
				$client_result = $wpdb->update($table_name,
					array(
						'preferences' => $notification,
						'location' => $all_loc,
                        'unsubscribe_email' => $not_email,
						'unsubscribe_sms' => $not_sms,
						'unlock_lead' => $unlock_lead_,
						'category' => $all_cat
					)
					,
					array('id' => $user_id)
					, $format = null, $where_format = null);
			} else {
				$client_result = $wpdb->insert(
					$wpdb->prefix . 'edugorilla_client_preferences',
					array(
						'id' => $user_id,
						'client_name' => $_client_name,
						'email_id' => $client_email,
						'contact_no' => $client_contact,
						'preferences' => $notification,
                        'unsubscribe_email' => $not_email,
						'unsubscribe_sms' => $not_sms,
						'location' => $all_loc,
						'unlock_lead' => $unlock_lead_,
						'category' => $all_cat
					)
				);
			}
			if ($client_result)
				$client_success = "Saved Successfully";
			else
				$client_success = "Please try again";
	}
	?>

	<script type="text/javascript">
		function add() {
			var ctrC = parseInt(document.getElementById("category_count").value);
			var ctrL = parseInt(document.getElementById("location_count").value);

			//Create an input type dynamically.
			var element_c = document.createElement("input");
			var element_l = document.createElement("input");
			var br1 = document.createElement("br");
			var br2 = document.createElement("br");

			var element_name_c = "category" + ctrC;
			element_c.setAttribute("list", "categories_list");
			element_c.setAttribute("size", 30);
			element_c.setAttribute("name", element_name_c);
			var foo1 = document.getElementById("get_category");
			foo1.insertBefore(br1, foo1.childNodes[0]);
			foo1.insertBefore(element_c, foo1.childNodes[0]);
			ctrC++;
			document.getElementById("category_count").value = ctrC;

			var element_name_l = "location" + ctrL;
			element_l.setAttribute("list", "location_list");
			element_l.setAttribute("size", 30);
			element_l.setAttribute("name", element_name_l);
			//Assign different attributes to the element.
			var foo2 = document.getElementById("get_location");
			foo2.insertBefore(br2, foo2.childNodes[0]);
			foo2.insertBefore(element_l, foo2.childNodes[0]);
			ctrL++;
			document.getElementById("location_count").value = ctrL;
		}
	</script>

	<!-- Client Form -->
	<form action = "" method = "post">
		<p><?php echo $client_success; ?></p>
		<table>
			<tr>
				<td rowspan = "4">Notification Preferences<sup><font color = "red">*</font></sup> :</td>
				<td colspan = "2"><input type = "checkbox" name = "notification[]" id = "notification"
				                       value = "Instant_Notifications" <?php echo $in_val ?>>Instant Notification
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "notification[]" value = "Daily_Digest" <?php echo $dd_val ?>>Daily
					Digest
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "notification[]" value = "Weekly_Digest" <?php echo $wd_val ?> >Weekly
					Digest
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "notification[]" value = "Monthly_Digest" <?php echo $md_val ?>>Monthly
					Digest
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "not_email"
				                       value = "1" <?php echo $unsub_email_val ?>>Unsubscribe
					Email
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "not_sms"
				                       value = "1" <?php echo $unsub_sms_val ?>>Unsubscribe
					SMS<br/>
				</td>
			</tr>
			<tr>
				<td rowspan = "2">Subscribe for following Categories :</td>
				<td>Location</td>
				<td>Category</td>
			</tr>
			<tr>
				<td>
					<?php $location = get_terms('locations', array('hide_empty' => false)); ?>
					<datalist id = "location_list">
						<?php foreach ($location as $value) { ?>
						<option value = "<?php echo $value->name; ?>">
							<?php } ?>
					</datalist>
					<div id = "get_location">
						<input list = "location_list" name = "location0" size = "30" value = "<?php echo $location_select_val?>">
						<?php echo $more_location ?>
					</div>
					<input type = "text" hidden name = "location_count" id = "location_count" value = "<?php echo $location_count_value ?>"></td>
				<td>
					<?php $categories = get_terms('listing_categories', array('hide_empty' => false)); ?>
					<datalist id = "categories_list">
						<?php foreach ($categories as $value) { ?>
						<option value = "<?php echo $value->name; ?>">
							<?php } ?>
					</datalist>
					<div id = "get_category">
						<input list = "categories_list" name = "category0" size = "30" value = "<?php
						 echo $category_select_val?>">
						<?php echo $more_category ?>
						<input type = "button" value = "  +  " onclick = "add()">
					</div>
					<input type = "text" hidden name = "category_count" id = "category_count" value = "<?php echo $category_count_value ?>">
					</td>
			</tr>
			<tr>
				<td>Automatically Unlock the Lead :</td>
				<td><input type = "checkbox" name = "unlock_lead" value = "1" <?php echo $unlock_val ?>></td>
			</tr>
			<tr><td><input type = "submit" name = "submit_client_pref"/></td></tr>
		</table>
	</form>
	<?php
}

add_shortcode('client_preference_form', 'edugorilla_client');


add_action('mail_send_daily', 'do_this_daily');
add_action('mail_send_weekly', 'do_this_weekly');
add_action('mail_send_monthly', 'do_this_monthly');

function my_email_activation()
{
	$time_day = date("y-m-d") . " 17:00:00";
	$daily_time = strtotime($time_day);

	$startdate = strtotime("Friday");
	$week_time = date("y", $startdate) . '-' . date("m", $startdate) . '-' . date("d", $startdate) . " 12:00:00";
	$weekly_time = strtotime($week_time);

	wp_schedule_event($daily_time, 'daily', 'mail_send_daily');
	wp_schedule_event($weekly_time, 'weekly', 'mail_send_weekly');
	wp_schedule_event($weekly_time, 'monthly', 'mail_send_monthly');
}


function do_this_weekly()
{
	//do something weekly 
	// send mail every week at 12PM on Friday
	$edugorilla_email = get_option('email_setting_form_weekly');
	$edugorilla_email_body = stripslashes($edugorilla_email['body']);
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'edugorilla_lead_details';
	$table_name2 = $wpdb->prefix . 'edugorilla_client_preferences';
	$lead_details = $wpdb->get_results("SELECT * FROM $table_name1");
	$client_email_addresses = $wpdb->get_results("SELECT * FROM $table_name2");


	foreach ($client_email_addresses as $client) {
		# code...
		if(preg_match('/Weekly_Digest/',$client->preferences)) {
			$category_location_lead_count = 0;
			$category_val = null;
			$location_val = null;
			foreach ($lead_details as $lead_detail) {
				# code...
				if((preg_match('/'.$lead_detail->category_id.'/',$client->category) OR empty($client->category)) AND (preg_match('/'.$lead_detail->location_id.'/',$client->location) OR  empty($client->location))){
					# code...
					if ($category_val == null || $location_val == null){
						# code...
						$categories_all = get_terms('listing_categories', array('hide_empty' => false));
						$location_all = get_terms('locations', array('hide_empty' => false));
						foreach ($categories_all as $value) {
							# code...
							if($lead_detail->category_id == $value->term_id)
								$category_val = $value->name;
						}

						foreach ($location_all as $value2) {
							# code...
							if($lead_detail->location_id == $value2->term_id)
								$location_val = $value2->name;
						}
					}
					$category_location_lead_count = $category_location_lead_count+1;
				}
			}
			$edugorilla_email_subject = str_replace("{category}", $category_val,
				$edugorilla_email['subject']);
			$email_template_datas = array("{Contact_Person}" => $client->client_name, "{category}" => $category_val,"{location}" => $location_val, "{category_location_lead_count}" => $category_location_lead_count);
			foreach ($email_template_datas as $var => $email_template_data) {
				$edugorilla_email_body = str_replace($var, $email_template_data, $edugorilla_email_body);
			}

			$headers = "";
			add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
			$institute_emails_status = wp_mail($client->email_id, $edugorilla_email_subject, ucwords($edugorilla_email_body), $headers);
			remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
		}
	}
}

function do_this_daily()
{
	//do something every day
	// send mail every day at 5PM
	$edugorilla_email = get_option('email_setting_form_daily');
	$edugorilla_email_body = stripslashes($edugorilla_email['body']);
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'edugorilla_lead_details';
	$table_name2 = $wpdb->prefix . 'edugorilla_client_preferences';
	$lead_details = $wpdb->get_results("SELECT * FROM $table_name1");
	$client_email_addresses = $wpdb->get_results("SELECT * FROM $table_name2");


	foreach ($client_email_addresses as $client) {
		# code...
		if(preg_match('/Daily_Digest/',$client->preferences)) {
			$category_location_lead_count = 0;
			$category_val = null;
			$location_val = null;
			foreach ($lead_details as $lead_detail) {
				# code...
				if((preg_match('/'.$lead_detail->category_id.'/',$client->category) OR empty($client->category)) AND (preg_match('/'.$lead_detail->location_id.'/',$client->location) OR  empty($client->location))){
					# code...
					if ($category_val == null || $location_val == null){
						# code...
						$categories_all = get_terms('listing_categories', array('hide_empty' => false));
						$location_all = get_terms('locations', array('hide_empty' => false));
						foreach ($categories_all as $value) {
							# code...
							if($lead_detail->category_id == $value->term_id)
								$category_val = $value->name;
						}

						foreach ($location_all as $value2) {
							# code...
							if($lead_detail->location_id == $value2->term_id)
								$location_val = $value2->name;
						}
					}
					$category_location_lead_count = $category_location_lead_count+1;
				}
			}
			$edugorilla_email_subject = str_replace("{category}", $category_val,
				$edugorilla_email['subject']);
			$email_template_datas = array("{Contact_Person}" => $client->client_name, "{category}" => $category_val,"{location}" => $location_val, "{category_location_lead_count}" => $category_location_lead_count);
			foreach ($email_template_datas as $var => $email_template_data) {
				$edugorilla_email_body = str_replace($var, $email_template_data, $edugorilla_email_body);
			}

			$headers = "";
			add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
			$institute_emails_status = wp_mail($client->email_id, $edugorilla_email_subject, ucwords($edugorilla_email_body), $headers);
			remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
		}
	}

}

function do_this_monthly()
{
	//do something every month
	// send mail every month at 12PM on Friday
	$edugorilla_email = get_option('email_setting_form_monthly');
	$edugorilla_email_body = stripslashes($edugorilla_email['body']);
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'edugorilla_lead_details';
	$table_name2 = $wpdb->prefix . 'edugorilla_client_preferences';
	$lead_details = $wpdb->get_results("SELECT * FROM $table_name1");
	$client_email_addresses = $wpdb->get_results("SELECT * FROM $table_name2");


	foreach ($client_email_addresses as $client) {
		# code...
		if(preg_match('/Monthly_Digest/',$client->preferences)) {
			$category_location_lead_count = 0;
			$category_val = null;
			$location_val = null;
			foreach ($lead_details as $lead_detail) {
				# code...
				if((preg_match('/'.$lead_detail->category_id.'/',$client->category) OR empty($client->category)) AND (preg_match('/'.$lead_detail->location_id.'/',$client->location) OR  empty($client->location))){
					# code...
					if ($category_val == null || $location_val == null){
						# code...
						$categories_all = get_terms('listing_categories', array('hide_empty' => false));
						$location_all = get_terms('locations', array('hide_empty' => false));
						foreach ($categories_all as $value) {
							# code...
							if($lead_detail->category_id == $value->term_id)
								$category_val = $value->name;
						}

						foreach ($location_all as $value2) {
							# code...
							if($lead_detail->location_id == $value2->term_id)
								$location_val = $value2->name;
						}
					}
					$category_location_lead_count = $category_location_lead_count+1;
				}
			}
			$edugorilla_email_subject = str_replace("{category}", $category_val,
				$edugorilla_email['subject']);
			$email_template_datas = array("{Contact_Person}" => $client->client_name, "{category}" => $category_val,"{location}" => $location_val, "{category_location_lead_count}" => $category_location_lead_count);
			foreach ($email_template_datas as $var => $email_template_data) {
				$edugorilla_email_body = str_replace($var, $email_template_data, $edugorilla_email_body);
			}

			$headers = "";
			add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
			$institute_emails_status = wp_mail($client->email_id, $edugorilla_email_subject, ucwords($edugorilla_email_body), $headers);
			remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
		}
	}

}


function my_deactivation()
{
	wp_clear_scheduled_hook('mail_send_daily');
	wp_clear_scheduled_hook('mail_send_weekly');
	wp_clear_scheduled_hook('mail_send_monthly');
}

//custom cron intervals for weekly and monthly
add_filter('cron_schedules', 'monthly_add_weekly_cron_schedule');
function monthly_add_weekly_cron_schedule($schedules)
{
	$schedules['weekly'] = array(
		'interval' => 604800, // 1 week in seconds
		'display' => __('Once Weekly'),
	);

	$schedules['monthly'] = array(
		'interval' => 2592000, // 1 month in seconds
		'display' => __('Once Monthly'),
	);

	return $schedules;
}


?>
