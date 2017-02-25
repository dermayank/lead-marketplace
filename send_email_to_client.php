<?php 
	
	function table_for_client(){
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
											PRIMARY KEY id (id)
				  					    ) $charset_collate;";	


	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	//Creating a table in cureent wordpress
	dbDelta($sql6);
	  					    
	}

//end pluginUninstall function

function send_mail($edugorilla_email_subject , $edugorilla_email_body){
	global $wpdb;
	$table_name = $wpdb->prefix .'edugorilla_client_preferences';
	$client_email_addresses = $wpdb->get_results( "SELECT * FROM $table_name" );
	$headers = array('Content-Type: text/html; charset=UTF-8');
	foreach ($client_email_addresses as $cea) {
		if (preg_match('/Instant_Notifications/',$cea->preferences)) {
			add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');
			$institute_emails_status = wp_mail($cea->email_id , $edugorilla_email_subject , ucwords($edugorilla_email_body),$headers);
			remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type'); 
		}
	}

	return $institute_emails_status;
}

//function to display client preferences form
function edugorilla_client(){

	if (isset($_POST['submit_client_pref'])) {
		# code...
		$notification_all = $_POST['notification'];
		foreach ($notification_all as $value) {
			# code...
			$notification = $value.", ".$notification;
		}
		$_location = $_POST['location'];
		$_category = $_POST['category'];

		foreach ($_category as $cat) {
			# code...
			$all_cat =  $cat.",".$all_cat;
		}


		foreach ($_location as $loc) {
			# code...
			$all_loc = $loc.",".$all_loc;
		}

		/** Error Checking **/
		$c_errors = array();


		if (empty($location)) $c_errors['location'] = "Empty";
		elseif (!preg_match("/([A-Za-z]+)/", $location)) $c_errors['location'] = "Invalid Name";

		if (empty($category)) $c_errors['category'] = "Empty";
		elseif (!preg_match("/([A-Za-z]+)/", $category)) $c_errors['category'] = "Invalid Name";

		$user_id = get_current_user_id(); 
     	$user_detail = get_user_meta($user_id); 
     	$first_name = $user_detail['first_name'][0];
     	$last_name = $user_detail['last_name'][0];
     	$_client_name = $first_name." ".$last_name;
     	$client_email = $user_detail['user_general_email'][0];
     	$client_contact = $user_detail['user_general_phone'][0];

		//Insert Data to table
		if(empty($errors)){

		global $wpdb;
		$table_name = $wpdb->prefix .'edugorilla_client_preferences';
		if($wpdb->get_results( "SELECT * FROM $table_name WHERE id = $user_id")){
		$client_result = $wpdb->update( $table_name, 
				array(
					'preferences' => $notification,
					'location' => $all_loc,
					'category' => $all_cat
					)
				, 
				array('id' =>$user_id)
				, $format = null, $where_format = null );
		}else{
		$client_result = $wpdb->insert(
				$wpdb->prefix.'edugorilla_client_preferences',
				array(
					'id' => $user_id,
					'client_name' => $_client_name,
					'email_id' => $client_email,
					'contact_no' => $client_contact,
					'preferences' => $notification,
					'location' => $all_loc,
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
	<!-- Client Form -->
	<form action="" method="post">
		<p><?php echo $client_success; ?></p>
		<table>
			<tr><td rowspan="4">Notification Preferences<sup><font color="red">*</font></sup> : </td><td><input type="checkbox" name="notification[]" id="notification" value="Instant_Notifications">Instant Notification</td></tr>
			<tr><td><input type="checkbox" id="notification" name="notification[]" value="Daily_Digest">Daily Digest</td></tr>
			<tr><td><input type="checkbox" id="notification" name="notification[]" value="Weekly_Digest">Weekly Digest</td></tr>
			<tr><td><input type="checkbox" id="notification" name="notification[]" value="Monthly_Digest">Monthly Digest<br/>
				<font color="red"><?php echo $c_errors['notification']; ?></font>
			</td></tr>
			<tr><td>Location/State</td><td>
				<?php $location = get_terms('locations', array('hide_empty' => false));
					foreach ($location as $value) {
			?>
				<input type="checkbox" value="<?php echo $value->term_id; ?>" name="location[]" id="location"><?php echo $value->name; ?>/	
			<?php	}
				 ?>
				<font color="red"><?php echo $c_errors['location']; ?></font></td></tr>
			<tr><td>Category</td><td>
				<?php $categories = get_terms('listing_categories', array('hide_empty' => false));
					foreach ($categories as $value) {
			?>
				<input type="checkbox" value="<?php echo $value->term_id; ?>" name="category[]" id="category"><?php echo $value->name; ?>/	
			<?php	}
				 ?>
			<font color="red"><?php echo $c_errors['category']; ?></font></td></tr>
			<tr><td><input type="submit" name="submit_client_pref"/></td></tr>
		</table>
	</form>
<?php
}
	add_shortcode('client_preference_form','edugorilla_client');

	

	add_action('mail_send_daily','do_this_daily');
	add_action('mail_send_weekly','do_this_weekly');
	add_action('mail_send_monthly','do_this_monthly');

	function my_email_activation(){
		$time_day =  date("y-m-d")." 17:00:00";
		$daily_time=strtotime($time_day);

		$startdate = strtotime("Friday");
		$week_time =  date("y" ,$startdate).'-'.date("m" ,$startdate).'-'.date("d",$startdate)." 12:00:00";
		$weekly_time = strtotime($week_time);

		wp_schedule_event($daily_time,'daily','mail_send_daily');
		wp_schedule_event($weekly_time,'weekly','mail_send_weekly');
		wp_schedule_event($weekly_time,'monthly','mail_send_monthly');			
	}


	function do_this_weekly() {
	//do something weekly 
	// send mail every week at 12PM on Friday
		$edugorilla_email = get_option('email_setting_form_weekly');
		$edugorilla_email_body = stripslashes($edugorilla_email['body']);
		global $wpdb;
		$table_name1 = $wpdb->prefix .'edugorilla_lead_details';
		$table_name2 = $wpdb->prefix .'edugorilla_client_preferences';
		$lead_details = $wpdb->get_results( "SELECT * FROM $table_name1");
		$client_email_addresses = $wpdb->get_results( "SELECT * FROM $table_name2");	
		

		foreach ($client_email_addresses as $client) {
			# code...
		if(preg_match('/Weekly_Digest/',$client->preferences)) {
			$category_location_lead_count = 0;
			$category_val = null;
			$location_val = null;
			foreach ($lead_details as $lead_detail) {
				# code...
				if(preg_match('/'.$lead_detail->category_id.'/',$client->category) AND preg_match('/'.$lead_detail->location_id.'/',$client->location)){
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
				$institute_emails_status = wp_mail($client->email_id , $edugorilla_email_subject , ucwords($edugorilla_email_body),$headers);
				remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type'); 
			}
		}
	}

	function do_this_daily() {
		//do something every day
		// send mail every day at 5PM	
		$edugorilla_email = get_option('edugorilla_email_setting1');
		$edugorilla_email_body = stripslashes($edugorilla_email['body']);
		global $wpdb;
		$table_name1 = $wpdb->prefix .'edugorilla_lead_details';
		$table_name2 = $wpdb->prefix .'edugorilla_client_preferences';
		$lead_details = $wpdb->get_results( "SELECT * FROM $table_name1");
		$client_email_addresses = $wpdb->get_results( "SELECT * FROM $table_name2");	
		

		foreach ($client_email_addresses as $client) {
			# code...
		if(preg_match('/Daily_Digest/',$client->preferences)) {
			$category_location_lead_count = 0;
			$category_val = null;
			$location_val = null;
			foreach ($lead_details as $lead_detail) {
				# code...
				if(preg_match('/'.$lead_detail->category_id.'/',$client->category) AND preg_match('/'.$lead_detail->location_id.'/',$client->location)){
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
				$institute_emails_status = wp_mail($client->email_id , $edugorilla_email_subject , ucwords($edugorilla_email_body),$headers);
				remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type'); 
			}
		}

	}

	function do_this_monthly() {
		//do something every month
		// send mail every month at 12PM on Friday
		$edugorilla_email = get_option('email_setting_form_monthly');
		$edugorilla_email_body = stripslashes($edugorilla_email['body']);
		global $wpdb;
		$table_name1 = $wpdb->prefix .'edugorilla_lead_details';
		$table_name2 = $wpdb->prefix .'edugorilla_client_preferences';
		$lead_details = $wpdb->get_results( "SELECT * FROM $table_name1");
		$client_email_addresses = $wpdb->get_results( "SELECT * FROM $table_name2");	
		

		foreach ($client_email_addresses as $client) {
			# code...
		if(preg_match('/Monthly_Digest/',$client->preferences)) {
			$category_location_lead_count = 0;
			$category_val = null;
			$location_val = null;
			foreach ($lead_details as $lead_detail) {
				# code...
				if(preg_match('/'.$lead_detail->category_id.'/',$client->category) AND preg_match('/'.$lead_detail->location_id.'/',$client->location)){
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
				$institute_emails_status = wp_mail($client->email_id , $edugorilla_email_subject , ucwords($edugorilla_email_body),$headers);
				remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type'); 
			}
		}

	}


	function my_deactivation() {
		wp_clear_scheduled_hook('mail_send_daily');
		wp_clear_scheduled_hook('mail_send_weekly');
		wp_clear_scheduled_hook('mail_send_monthly');
	}

	//custom cron intervals for weekly and monthly
	add_filter( 'cron_schedules', 'monthly_add_weekly_cron_schedule' );
	function monthly_add_weekly_cron_schedule( $schedules ) {
    $schedules['weekly'] = array(
        'interval' => 604800, // 1 week in seconds
        'display'  => __( 'Once Weekly' ),
    );
 
 	 $schedules['monthly'] = array(
        'interval' => 2592000, // 1 month in seconds
        'display'  => __( 'Once Monthly' ),
    );

    return $schedules;
	}


?>