<?php
function client_preferences_page(){
	global $wpdb;
	$users_table = $wpdb->prefix.'users';
	$table_name = $wpdb->prefix . 'edugorilla_client_preferences';
	if($_POST['submit']){
		if(empty($_POST['client_email'])){
			$php_empty_error = "*This field cannot be blank";
		} else{
			$client_email = $_POST['client_email'];
			$check_client = $wpdb->get_var("SELECT COUNT(ID) from $users_table WHERE user_email = '$client_email' ");
            if($check_client == 0){
                $no_client_found = "*This client does not exist in our database";
            }
		}
	}
?>
<style>
#preference_form{
	display: none;
}
</style>

<script>
function form_not_empty(){
	var x = document.getElementById('client_email').value;
	if(x == ""){
		document.getElementById('js_empty_error').innerHTML = "*This field cannot be blank";
		return false;
	} else{
		return true;
	}
}
</script>

<div class = "wrap">
 <h1>Enter the Email-Id of the client whose preferences you want to know</h1>
  <form method = 'post' onsubmit = 'return form_not_empty()' action = "<?php echo $_SERVER['REQUEST_URI'];?>">
   <table class = "form-table">
				<tr>
					<th>Client's Email<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'text' id = 'client_email' name = 'client_email' value = "<?php echo $_POST['client_email']; ?>" placeholder = 'Type email here...' maxlength = '100'>
						<font color = "red"><span id = 'js_empty_error'></span><?php echo $php_empty_error.$no_client_found; ?></font>
					</td>
				</tr>
				<tr>
					<th>
						<input type = "hidden">
					</th>
					<td>
						<input type = 'submit' name = 'submit'>
					</td>
				</tr>
   </table>
  </form>
</div>

<br/><br/><br/>

<?php
       if($_POST['submit'] && !(empty($_POST['client_email'])) && $check_client > 0){
         $value = $wpdb->get_row("SELECT * FROM $table_name WHERE email_id = '$client_email' ");
		 
		 $not_email = $value->unsubscribe_email;
		 $not_sms = $value->unsubscribe_sms;
		 $is_lead_unlocked = $value->unlock_lead;
		 
		 if($not_email == 1){
			 $unsub_email_val = "checked";
		 } else{
			 $unsub_email_val = "";
		 }
		 
		 if($not_sms == 1){
			 $unsub_sms_val = "checked";
		 } else{
			 $unsub_sms_val = "";
		 }
		 
		 if($is_lead_unlocked == 1){
			 $unlock_val = "checked";
		 } else{
			 $unlock_val = "";
		 }	 
		$categories_list = get_terms('listing_categories', array('hide_empty' => false));
		$location_list = get_terms('locations', array('hide_empty' => false));
		$user_id = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$client_email' ");
		$category_count_value =1;
		$location_count_value =1;
		$notification = "";
		$in_val = "";
		$dd_val = "";
		$wd_val = "";
		$md_val = "";
		$current_user_data = $wpdb->get_row("SELECT * FROM $table_name WHERE id = '$user_id' ");
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
	
		$category_result = get_category_current_user($user_id , $current_user_data);
		$more_category = $category_result[0];
		$category_count_value = $category_result[1];
		$location_result = get_location_current_user($user_id , $current_user_data);
		$more_location = $location_result[0];
		$location_count_value = $location_result[1];
	   }
	if (isset($_POST['submit_client_pref'])) {
		$categories_list = get_terms('listing_categories', array('hide_empty' => false));
		$location_list = get_terms('locations', array('hide_empty' => false));
		$unlock_lead_ = $_POST['unlock_lead'];
		$client_email2 = $_POST['client_email2'];
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
			# code...
			$unlock_val = "";
			$unlock_lead_ = 0;
		}else
			$unlock_val = "checked";

		$user_id = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$client_email2' ");
		$user_detail = get_user_meta($user_id);
		$first_name = $user_detail['first_name'][0];
		$last_name = $user_detail['last_name'][0];
		$_client_name = $first_name . " " . $last_name;
		$client_email = $user_detail['user_general_email'][0];
		$client_contact = $user_detail['user_general_phone'][0];

			if ($wpdb->get_results("SELECT * FROM $table_name WHERE id = $user_id")) {
				$client_result = $wpdb->update($table_name,
					array(
						'preferences' => $notification,
						'location' => $all_loc,
						'unlock_lead' => $unlock_lead_,
						'unsubscribe_email' => $not_email,
						'unsubscribe_sms' => $not_sms,
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
				$client_success = "<font color = 'green'>Saved Successfully</font>";
			else
				$client_success = "<font color = 'red'>Please try again</font>";
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

<div id = "preference_form">
<h2>Client Preferences</h2>
	<form action="" method="post">
		<p><?php echo $client_success; ?></p>
		<table>
		    <tr>
			    <td>Client's Email</td>
			    <td><input type = "text" name = "client_email2" value = "<?php echo $client_email.$client_email2; ?>" readonly></td>
			</tr>
			<tr>
				<td rowspan="4">Notification Preferences<sup><font color="red">*</font></sup> :</td>
				<td colspan="2"><input type="checkbox" name="notification[]" id="notification"
				                       value="Instant_Notifications" <?php echo $in_val ?>>Instant Notification
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="checkbox" id="notification" name="notification[]" value="Daily_Digest" <?php echo $dd_val ?>>Daily
					Digest
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="checkbox" id="notification" name="notification[]" value="Weekly_Digest" <?php echo $wd_val ?> >Weekly
					Digest
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="checkbox" id="notification" name="notification[]" value="Monthly_Digest" <?php echo $md_val ?>>Monthly
					Digest
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="checkbox" id="notification" name="not_email"
				                       value="1" <?php echo $unsub_email_val ?>>Unsubscribe
					Email
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="checkbox" id="notification" name="not_sms"
				                       value="1" <?php echo $unsub_sms_val ?>>Unsubscribe
					SMS<br/>
				</td>
			</tr>
			<tr>
				<td rowspan="2">Subscribe for following Categories :</td>
				<td>Location</td>
				<td>Category</td>
			</tr>
			<tr>
				<td>
					<?php $location = get_terms('locations', array('hide_empty' => false)); ?>
					<datalist id="location_list">
						<?php foreach ($location as $value) { ?>
						<option value="<?php echo $value->name; ?>">
							<?php } ?>
					</datalist>
					<div id="get_location">
						<input list="location_list" name="location0" size="30" value="<?php echo $location_select_val?>">
						<?php echo $more_location ?>
					</div>
					<input type="text" hidden name="location_count" id="location_count" value="<?php echo $location_count_value ?>">
					<font color="red"><?php echo $c_errors['location']; ?></font></td>
				<td>
					<?php $categories = get_terms('listing_categories', array('hide_empty' => false)); ?>
					<datalist id="categories_list">
						<?php foreach ($categories as $value) { ?>
						<option value="<?php echo $value->name; ?>">
							<?php } ?>
					</datalist>
					<div id="get_category">
						<input list="categories_list" name="category0" size="30" value="<?php
						 echo $category_select_val?>">
						<?php echo $more_category ?>
						<input type="button" value="  +  " onclick="add()">
					</div>
					<input type="text" hidden name="category_count" id="category_count" value="<?php echo $category_count_value ?>">
					<font color="red"><?php echo $c_errors['category']; ?></font></td>
			</tr>
			<tr>
				<td>Automatically Unlock the Lead :</td>
				<td><input type="checkbox" name="unlock_lead" value="1" <?php echo $unlock_val ?>></td>
			</tr>
			<tr><td><input type="submit" name="submit_client_pref"/></td></tr>
		</table>
	</form>
</div>
<?php
if($_POST['submit'] && !(empty($_POST['client_email'])) && $check_client > 0){
	echo "<script>document.getElementById('preference_form').style.display = 'block';</script>";
}
if ($_POST['submit_client_pref']) {
	echo "<script>document.getElementById('preference_form').style.display = 'block';</script>";
}
}
?>
