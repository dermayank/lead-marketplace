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

	if ($_POST['submit_client_pref']) {
		
		# code...
		$unlock_lead_ = $_POST['unlock_lead'];
		$notification_all = $_POST['notification'];
		if (!empty($notification_all)) {
			# code...
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
				else if ($value == "Unsubscribe_Email")
 					$unsub_email_val = "checked";
 				else if ($value == "Unsubscribe_SMS")
         			$unsub_sms_val = "checked";
			}
		}
		$category_count = $_POST['category_count'];
		$location_count = $_POST['location_count'];
		$category = array();
		$location = array();
		$more_category_count = $category_count;
		for ($i = 0; $i < $category_count; $i++) {
			# code...
			$category_name = "category".$i;
			if ($more_category_count>1) {
				# code...
				$more_category = $more_category.'<br/><input list="location_list" name="'.$category_name.'" size="30" value="'.$_POST[$category_name].'">';
				$more_category_count = $more_category_count-1;
			}else
				$category_select_val = $_POST[$category_name];
			array_push($category, $_POST[$category_name]);
		}
		$more_location_count = $location_count;
		for ($i = 0; $i < $location_count; $i++) {
			# code...
			$location_name = "location".$i;
			if ($more_location_count>1) {
				# code...
				$more_location = $more_location.'<br/><input list="location_list" name="'.$location_name.'" size="30" value="'.$_POST[$location_name].'">';
				$more_location_count = $more_location_count-1;
			}else
			$location_select_val = $_POST[$location_name];
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
			$unlock_val = "";
			$unlock_lead_ = 0;
		}else
			$unlock_val = "checked";
		/** Error Checking **/
		$c_errors = array();
		if (empty($location)) $c_errors['location'] = "Empty";
		
		if (empty($category)) $c_errors['category'] = "Empty";

		$client_email2 = $_POST['client_email2'];
		$user_id = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$client_email2' ");
		$user_detail = get_user_meta($user_id);
		$first_name = $user_detail['first_name'][0];
		$last_name = $user_detail['last_name'][0];
		$_client_name = $first_name . " " . $last_name;
		$client_email = $user_detail['user_general_email'][0];
		$client_contact = $user_detail['user_general_phone'][0];



		//Insert Data to table
		if(empty($errors)){
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
				$client_success = "<font color = 'green'>Saved Successfully</font>";
			else
				$client_success = "<font color = 'red'>Please try again</font>";
		
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

<div id = 'preference_form'>
<h2>Client Preferences</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
		<p><?php echo $client_success; ?></p>
		<table>
		    <tr>
			<td>Client's Email</td>
			<td><input type = 'text' name = 'client_email2' value = '<?php echo $client_email;?>' readonly></td>
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
 				<td colspan="2"><input type="checkbox" id="notification" name="notification[]"
 				                       value="Unsubscribe_Email" <?php echo $unsub_email_val ?>>Unsubscribe
 					Email
 				</td>
 			</tr>
 			<tr>
 				<td colspan="2"><input type="checkbox" id="notification" name="notification[]"
 				                       value="Unsubscribe_SMS" <?php echo $unsub_sms_val ?>>Unsubscribe
 					SMS<br/>
					<font color="red"><?php echo $c_errors['notification']; ?></font>
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
					<input type="text" hidden name="location_count" id="location_count" value="1">
					<font color="red"><?php echo $c_errors['location']; ?></font></td>
				<td>
					<?php $categories = get_terms('listing_categories', array('hide_empty' => false)); ?>
					<datalist id="categories_list">
						<?php foreach ($categories as $value) { ?>
						<option value="<?php echo $value->name; ?>">
							<?php } ?>
					</datalist>
					<div id="get_category">
						<input list="categories_list" name="category0" size="30" value="<?php echo $category_select_val?>">
						<?php echo $more_category ?>
						<input type="button" value="  +  " onclick="add()">
					</div>
					<input type="text" hidden name="category_count" id="category_count" value="1">
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
