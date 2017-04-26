<?php

function edugorilla_lead_edit(){
    	$lead_edit_form = $_POST['lead_edit_form'];
		$iid = $_REQUEST['iid'];
	if ($lead_edit_form == "self") {
		/** Get Data From Form **/
		$name = $_POST['name'];
		$contact_no = $_POST['contact_no'];
		$keyword = $_POST['keyword'];
		$email = $_POST['email'];
		$is_promotional_lead = $_POST['is_promotional_lead'];
		$listing_type = $_POST['listing_type'];
		$query = $_POST['query'];
		$category_id = $_POST['category_id'];
		$location_id = $_POST['location'];
		$edugorilla_institute_datas = $_POST['edugorilla_institute_datas'];
		$is_promotional_lead = $_POST['is_promotional_lead'];

		/** Error Checking **/
		$errors = array();
		if (empty($name)) $errors['name'] = "Empty";
		elseif (!preg_match("/([A-Za-z]+)/", $name)) $errors['name'] = "Invalid";

		if (empty($contact_no)) $errors['contact_no'] = "Empty";
		elseif (!preg_match("/([0-9]{10}+)/", $contact_no)) $errors['contact_no'] = "Invalid";

		if (empty($email)) $errors['email'] = "Empty";
		elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) $errors['email'] = "Invalid";

		if (empty($query)) $errors['query'] = "Empty";


		if (empty($errors)) {
			$institute_emails_status = array();
			$institute_sms_status = array();

			if (!empty($category_id)) $category = implode(",", $category_id);
			else $category = "-1";

			if (empty($location_id)) $location_id = "-1";

			$json_results = json_decode(stripslashes($edugorilla_institute_datas));

			$edugorilla_email = get_option('edugorilla_email_setting1');

			$edugorilla_email_body = stripslashes($edugorilla_email['body']);


			global $wpdb;
			$result1 = $wpdb->update(
				$wpdb->prefix . 'edugorilla_lead_details',
				array(
					'name' => $name,
					'contact_no' => $contact_no,
					'email' => $email,
					'query' => $query,
					'is_promotional' => $is_promotional_lead,
					'listing_type' => $listing_type,
					'category_id' => $category,
					'location_id' => $location_id,
					'date_time' => current_time('mysql')
				),
				array( 'id' => $iid, )
			);
			$user_login = str_replace(" ", "_", $name);

			$uid = email_exists($email);
			if ($uid) {
				wp_update_user(array('ID' => $uid, 'user_email' => $email));
				update_user_meta($uid, 'user_general_phone', $contact_no);
				update_user_meta($uid, 'user_general_email', $email);
			} else {
				$userdata = array(
					'user_login' => $user_login,
					'user_pass' => $contact_no,
					'first_name' => $name,
					'user_email' => $email,
					'user_pass' => $contact_no
				);
				$user_id = wp_insert_user($userdata);

				if (!is_wp_error($user_id)) {
					add_user_meta($user_id, 'user_general_first_name', $name);
					add_user_meta($user_id, 'user_general_phone', $contact_no);
					add_user_meta($user_id, 'user_general_email', $email);
				}
			}

			foreach ( $json_results as $json_result ) {
				$edugorilla_email_subject = str_replace( "{category}", $json_result->contact_category, $edugorilla_email['subject'] );
				$email_template_datas     = array(
					"{Contact_Person}" => $json_result->contact_person,
					"{category}"       => $json_result->contact_category,
					"{location}"       => $json_result->contact_location,
					"{listing_URL}"    => $json_result->listing_url,
					"{name}"           => $name,
					"{contact no}"     => $contact_no,
					"{email address}"  => $email,
					"{query}"          => $query
				);

				foreach ( $email_template_datas as $var => $email_template_data ) {
					$edugorilla_email_body = str_replace( $var, $email_template_data, $edugorilla_email_body );
				}
				$institute_emails    = explode( ",", $json_result->emails );
				$institute_phones    = explode( ",", $json_result->phones );
				$contact_log_id      = $wpdb->insert_id;
				$contact_person_name = $json_result->contact_person;
				$log_post_id         = $json_result->post_id;
				if ( $is_promotional_lead == "yes" ) {
					$result2 = send_mail_without_unlock( $edugorilla_email_subject, $edugorilla_email_body, $institute_emails, $institute_phones, $contact_log_id, $contact_person_name, $log_post_id );

				} else {
					$result2 = send_mail_without_unlock( "LockedEmail : " . $edugorilla_email_subject, $edugorilla_email_body, $institute_emails, $institute_phones, $contact_log_id, $contact_person_name, $log_post_id );
				}
			}

			if ($result1) {
				$success = "Updated Successfully.";
			} elseif ($result2) $success = "Updated and Message Send Successfully.";
			else $success = $result1;

			//  foreach($_REQUEST as $var=>$val)$$var="";
		}
	}
	else
	{
		global $wpdb;
		$q = "select * from edugorilla_lead_details where id=$iid";
		$lead_details = $wpdb->get_results($q, ARRAY_A);

		foreach($lead_details as $lead_detail);

		$name = $lead_detail['name'];
		$contact_no = $lead_detail['contact_no'];
		$email = $lead_detail['email'];
		$query = $lead_detail['query'];
		$is_promotional_lead = $lead_detail['is_promotional'];
		$listing_type = $lead_detail['listing_type'];
		$category_ids = explode(",",$lead_detail['category_id']);
		$keyword = $lead_detail['keyword'];
		$location = $lead_detail['location_id'];

	}
	?>
	<style>

		#map {
			width: 60%;
			height: 500px;
			border: double;
		}

		.controls {
			margin-top: 10px;
			border: 1px solid transparent;
			border-radius: 2px 0 0 2px;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			height: 32px;
			outline: none;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
		}

		#pac-input {
			background-color: #fff;
			font-family: Roboto;
			font-size: 15px;
			font-weight: 300;
			margin-left: 12px;
			padding: 0 11px 0 13px;
			text-overflow: ellipsis;
			width: 300px;
		}

		#pac-input:focus {
			border-color: #4d90fe;
		}
	</style>


	<div class="wrap">
		<h1>EduGorilla Leads</h1>
		<?php
		if ($success) {
			?>
			<div class="updated notice">
				<p><?php echo $success; ?></p>
			</div>
			<?php
		}
		?>
		<form name=details method="post">
			<table class="form-table">
				<tr>
					<th>Name<sup><font color="red">*</font></sup></th>
					<td>
						<input id="edu_name" name="name" value="<?php echo $name; ?>" placeholder="Type name here...">
						<font color="red"><?php echo $errors['name']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Contact No.<sup><font color="red">*</font></sup></th>
					<td>
						<input id="edu_contact_no" name="contact_no" value="<?php echo $contact_no; ?>"
						       placeholder="Type contact number here">
						<font color="red"><?php echo $errors['contact_no']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Email<sup><font color="red">*</font></sup></th>
					<td>
						<input id="edu_email" name="email" value="<?php echo $email; ?>" placeholder="Type email here">
						<font color="red"><?php echo $errors['email']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Query<sup><font color="red">*</font></sup></th>
					<td>
                        <textarea id="edu_query" name="query" rows="4" cols="65"
                                  placeholder="Type your query here"><?php echo $query; ?></textarea>
						<font color="red"><?php echo $errors['query']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Is it a promotional lead?</th>
					<td>
						<input name="is_promotional_lead" id="is_promotional_lead" type="checkbox"
						       value="yes" <?php if ($is_promotional_lead == "yes") echo "checked"; ?>>
					</td>
				</tr>
				<tr>
					<th>Listing Type<sup><font color="red">*</font></sup></th>
					<td>
						<select name="listing_type" id="edugorilla_listing_type">
							<option value="">Select</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Category</th>
					<td>
						<select disabled name="category_id[]" multiple id="edugorilla_category"
						        class="js-example-basic-single">
							<?php
							$temparray = array();
							$categories = get_terms('listing_categories', array('hide_empty' => false));

							foreach ($categories as $category) {
								$temparray[$category->parent][$category->term_id] = $category->name;
							}

							foreach ($temparray as $var => $vals) {
								?>
						<?php if(in_array($var,$category_ids)){ ?>
								<option value="<?php echo $var; ?>" selected>
									<?php
									$d = get_term_by('id', $var, 'listing_categories');
									echo $d->name;
									?>
								</option>
						<?php }else{ ?>
								<option value="<?php echo $var; ?>">
									<?php
									$d = get_term_by('id', $var, 'listing_categories');
									echo $d->name;
									?>
								</option>
						<?php } ?>
								<?php
								foreach ($vals as $index => $val) {
									?>
						<?php if(in_array($index,$category_ids)){ ?>
								<option value="<?php echo $index; ?>" selected>
										<?php echo $val; ?>
									</option>
						<?php }else{ ?>
									<option value="<?php echo $index; ?>">
										<?php echo $val; ?>
									</option>
									<?php
									}
								}
								?>

								<?php
							}
							?>
						</select>
						<font color="red"><?php echo $errors['category_id']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Keyword</th>
					<td>
						<input name="keyword" id="edugorilla_keyword" disabled value="<?php echo $keyword; ?>"
						       placeholder="Type keyword here">
						<font color="red"><?php echo $errors['keyword']; ?></font>
					</td>
				</tr>
				<tr>
					<th>Location</th>
					<td>
						<select disabled name="location" id="edugorilla_location" class="js-example-basic-single">
							<option value="">Select</option>
							<?php
							$templocationarray = array();
							$edugorilla_locations = get_terms('locations', array('hide_empty' => false));

							foreach ($edugorilla_locations as $edugorilla_location) {
								$templocationarray[$edugorilla_location->parent][$edugorilla_location->term_id] = $edugorilla_location->name;
							}

							foreach ($templocationarray as $var => $vals) {

								?>
						<?php if($location == $var){ ?>
								<option value="<?php echo $var; ?>" selected>
									<?php
									$d = get_term_by('id', $var, 'locations');
									echo $d->name;
									?>
								</option>
						<?php }else{ ?>
								<option value="<?php echo $var; ?>">
									<?php
									$d = get_term_by('id', $var, 'locations');
									echo $d->name;
									?>
								</option>
						<?php } ?>
								<?php
								foreach ($vals as $index => $val) {
									?>
							<?php if($location == $index){ ?>
									<option value="<?php echo $index; ?>" selected>
										<?php echo "->" . $val; ?>
									</option>
							<?php }else{ ?>
									<option value="<?php echo $index; ?>">
										<?php echo "->" . $val; ?>
									</option>
							<?php } ?>
									<?php
								}
								?>

								<?php
							}
							?>
						</select><br><br>
						<input type="button" class="button button-secondary" id="edugorilla_filter"
						       value="Filter"><br><br>

						<div id="map"></div>
					</td>
				</tr>
				<tr>
					<th>
						<input type="hidden" id="edugorilla_institute_datas" name="edugorilla_institute_datas">
						<input type="hidden" name="lead_edit_form" value="self">
					</th>
					<td>

						<a id="save_details_button" disabled href="#confirmation" class="button button-primary">Save
							Details</a>
					</td>
				</tr>
			</table>
		</form>
	</div>

	<!-------Modal------>
	<div id="confirmation" style="display:none;">

	</div>
	<!---/Modal-------->
	<script>

		function initMap() {
			var points = {lat: 0, lng: 0};
			var map = new google.maps.Map(document.getElementById('map'), {
				zoom: 1,
				center: points
			});

			var infowindow = new google.maps.InfoWindow();

		}
		initMap();
	</script>
<?php
}
?>
