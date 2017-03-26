<?php
function client_preferences_page(){

	global $wpdb;
	$users_table = $wpdb->prefix.'users';

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
					<th>Client Email<sup><font color="red">*</font></sup></th>
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

<div id = 'preference_form'>
<h2>Client Preferences</h2>
    <form action = "<?php echo $_SERVER['REQUEST_URI'];?>" method = "post">
		<p><?php echo $client_success; ?></p>
		<table>
			<tr>
				<td rowspan = "4">Notification Preferences<sup><font color = "red">*</font></sup> :</td>
				<td colspan = "2"><input type = "checkbox" name = "notification[]" id = "notification"
				                       value = "Instant_Notifications">Instant Notification
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "notification[]" value = "Daily_Digest">Daily
					Digest
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "notification[]" value = "Weekly_Digest">Weekly
					Digest
				</td>
			</tr>
			<tr>
				<td colspan = "2"><input type = "checkbox" id = "notification" name = "notification[]" value = "Monthly_Digest">Monthly
					Digest<br/>
					<font color = "red"><?php echo $c_errors['notification']; ?></font>
				</td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
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
						<input list = "location_list" name = "location0" size = "30">
					</div>
					<input type = "text" hidden name = "location_count" id = "location_count" value = "1">
					<font color = "red"><?php echo $c_errors['location']; ?></font></td>
				<td>
					<?php $categories = get_terms('listing_categories', array('hide_empty' => false)); ?>
					<datalist id = "categories_list">
						<?php foreach ($categories as $value) { ?>
						<option value = "<?php echo $value->name; ?>">
							<?php } ?>
					</datalist>
					<div id = "get_category">
						<input list = "categories_list" name = "category0" size = "30"><input type = "button" value = "  +  "
						                                                                onclick = "add()">
					</div>
					<input type = "text" hidden name = "category_count" id = "category_count" value = "1">
					<font color = "red"><?php echo $c_errors['category']; ?></font></td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
			</tr>
			<tr>
			    <td><input type = 'hidden'></td>
			    <td><input type = 'hidden'></td>
			</tr>
			<tr>
				<td>Automatically Unlock the Lead :</td>
				<td><input type = "checkbox" name = "unlock_lead" value = "1" checked = ""></td>
			</tr>
			<tr><td><input type = "submit" name = "submit_client_pref"/></td></tr>
		</table>
	</form>
</div>
<?php
if($_POST['submit'] && !(empty($_POST['client_email'])) && $check_client > 0){
	echo "<script>document.getElementById('preference_form').style.display = 'block';</script>";
}
}
?>