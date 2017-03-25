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
											unlock_lead boolean DEFAULT 0,
											PRIMARY KEY id (id)
				  					    ) $charset_collate;";


	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	//Creating a table in cureent wordpress
	dbDelta($sql6);

}

//end pluginUninstall function

function send_mail($edugorilla_email_subject, $edugorilla_email_body, $lead_card)
{
	global $wpdb;
	$location_id = $lead_card->getLocationId();
	$category = $lead_card->getCategoryId();
	$lead_id = $lead_card->getId();
	$cat = array();
	$cat = explode(',', $category);
	$table_name = $wpdb->prefix .'edugorilla_client_preferences';
	$client_email_addresses = $wpdb->get_results("SELECT * FROM $table_name where unlock_lead = 1");
	$headers = array('Content-Type: text/html; charset=UTF-8');
	foreach ($client_email_addresses as $cea) {
		$check = 0;
		foreach ($cat as $val) {
		# code...
		if (preg_match('/'.$val.'/', $cea->category)) {
			# code...
			$check = 1;
			}
		}
		if (preg_match('/Instant_Notifications/', $cea->preferences) AND $check == 1 AND preg_match('/' . $location_id . '/', $cea->location)) {
			echo $cea->client_name;
			$lead_card->setUnlocked(true);
			$eduLeadHelper = new EduLead_Helper();
			$eduLeadHelper->set_card_unlock_status_to_db($cea->email_id, $lead_id, 1);
			add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
			$institute_emails_status = wp_mail($cea->email_id , $edugorilla_email_subject , ucwords($edugorilla_email_body),$headers);
			remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
		}
	}

	if ($institute_emails_status) {
		# code...
		echo "Mail send";
	}
	return $institute_emails_status;
}

function send_mail_pro($edugorilla_email_subject , $edugorilla_email_body, $lead_card){
	global $wpdb;
	$e_subject = $edugorilla_email_subject;
	$e_body = $edugorilla_email_body;
	$location_id = $lead_card->getLocationId();
	$category = $lead_card->getCategoryId();
	$cat = array();
	$cat = explode(',', $category);
	$table_name = $wpdb->prefix .'edugorilla_client_preferences';
	$client_email_addresses = $wpdb->get_results( "SELECT * FROM $table_name");
	$headers = array('Content-Type: text/html; charset=UTF-8');
	foreach ($client_email_addresses as $cea) {
		$check = 0;
		foreach ($cat as $val) {
		# code...
		if (preg_match('/'.$val.'/', $cea->category)) {
			# code...
			$check = 1;
			}
		}
		if (preg_match('/Instant_Notifications/',$cea->preferences) AND $check == 1 AND preg_match('/'.$location_id.'/', $cea->location)) {
			add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
			$institute_emails_status = wp_mail($cea->email_id , $e_subject , ucwords($e_body),$headers);
			remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type'); 
		}
	}
	if ($institute_emails_status) {
		# code...
		echo "mail send";
	}
	return $institute_emails_status;
}
//function to display client preferences form
function edugorilla_client(){

	if (isset($_POST['submit_client_pref'])) {
		# code...
		$unlock_lead_ = $_POST['unlock_lead'];
		$notification_all = $_POST['notification'];

		if (!empty($notification_all)) {
			# code...
			foreach ($notification_all as $value) {
				# code...
				$notification = $value . ", " . $notification;
			}
		}
		$category_count = $_POST['category_count'];
		$location_count = $_POST['location_count'];

		$category = array();
		$location = array();
		for ($i = 0; $i < $category_count; $i++) {
			# code...
			$category_name = "category".$i;
			array_push($category, $_POST[$category_name]);
		}

		for ($i = 0; $i < $location_count; $i++) {
			# code...
			$location_name = "location".$i;
			array_push($location, $_POST[$location_name]);
		}

		$categories_list = get_terms('listing_categories', array('hide_empty' => false));
		foreach ($categories_list as $cat_value) {
			# code...
			foreach ($category as $category_value) {
				# code...
				if ($category_value == $cat_value->name) {
					# code...
					$all_cat = $cat_value->term_id . "," . $all_cat;
				}
			}
		}

		$location_list = get_terms('locations', array('hide_empty' => false));
		foreach ($location_list as $loc_value) {
			# code...
			foreach ($location as $location_value) {
				# code...
				if ($location_value == $loc_value->name) {
					# code...
					$all_loc = $loc_value->term_id . "," . $all_loc;
				}
			}
		}

		if ($unlock_lead_ != 1) {
			# code...
			$unlock_lead_ = 0;
		}

		/** Error Checking **/
		$c_errors = array();


		if (empty($location)) $c_errors['location'] = "Empty";
		//elseif (!preg_match("/([A-Za-z]+)/", $location)) $c_errors['location'] = "Invalid Name";

		if (empty($category)) $c_errors['category'] = "Empty";
		//elseif (!preg_match("/([A-Za-z]+)/", $category)) $c_errors['category'] = "Invalid Name";

		$user_id = get_current_user_id();
		$user_detail = get_user_meta($user_id);
		$first_name = $user_detail['first_name'][0];
		$last_name = $user_detail['last_name'][0];
		$_client_name = $first_name . " " . $last_name;
		$client_email = $user_detail['user_general_email'][0];
		$client_contact = $user_detail['user_general_phone'][0];

		//Insert Data to table
		if(empty($errors)){

			global $wpdb;
			$table_name = $wpdb->prefix . 'edugorilla_client_preferences';
			if ($wpdb->get_results("SELECT * FROM $table_name WHERE id = $user_id")) {
				$client_result = $wpdb->update($table_name,
					array(
						'preferences' => $notification,
						'location' => $all_loc,
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
	}

	?>

	<script type="text/javascript">

		var ctrC = 1;
		var ctrL = 1;

		function add() {

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
	<form action="" method="post">
		<p><?php echo $client_success; ?></p>
		<table>
			<tr>
				<td rowspan="4">Notification Preferences<sup><font color="red">*</font></sup> :</td>
				<td colspan="2"><input type="checkbox" name="notification[]" id="notification"
				                       value="Instant_Notifications">Instant Notification
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="checkbox" id="notification" name="notification[]" value="Daily_Digest">Daily
					Digest
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="checkbox" id="notification" name="notification[]" value="Weekly_Digest">Weekly
					Digest
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="checkbox" id="notification" name="notification[]" value="Monthly_Digest">Monthly
					Digest<br/>
					<font color="red"><?php echo $c_errors['notification']; ?></font>
				</td>
			</tr>
			<tr>
				<td rowspan="2">Subscribe for following Categories :</td>
				<td>Location</td>
				<td>Category</td>
			</tr>
			<!--<div class="ui-widget">
			  <input id="tags_loc" name="location" size="50">
			</div>-->
			<tr>
				<td>
					<?php $location = get_terms('locations', array('hide_empty' => false)); ?>
					<datalist id="location_list">
						<?php foreach ($location as $value) { ?>
						<option value="<?php echo $value->name; ?>">
							<?php } ?>
					</datalist>
					<div id="get_location">
						<input list="location_list" name="location0" size="30">
					</div>
					<input type="text" hidden name="location_count" id="location_count" value="1">
					<font color="red"><?php echo $c_errors['location']; ?></font></td>
				<!--<div class="ui-widget">
				  <input id="tags" name="category" size="50">
				</div>-->
				<td>
					<?php $categories = get_terms('listing_categories', array('hide_empty' => false)); ?>
					<datalist id="categories_list">
						<?php foreach ($categories as $value) { ?>
						<option value="<?php echo $value->name; ?>">
							<?php } ?>
					</datalist>
					<div id="get_category">
						<input list="categories_list" name="category0" size="30"><input type="button" value="  +  "
						                                                                onclick="add()">
					</div>
					<input type="text" hidden name="category_count" id="category_count" value="1">
					<font color="red"><?php echo $c_errors['category']; ?></font></td>
			</tr>
			<tr>
				<td>Automatically Unlock the Lead :</td>
				<td><input type="checkbox" name="unlock_lead" value="1" checked=""></td>
			</tr>
			<tr><td><input type="submit" name="submit_client_pref"/></td></tr>
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
	$edugorilla_email = get_option('edugorilla_email_setting1');
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