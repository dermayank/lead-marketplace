<?php
/**
 * Plugin Name: Lead Marketplace
 * Description: A platform to manage all the leads on Website.
 * Version: Alpha release
 * Author: EduGorilla Tech Team
 * Author URI: https://github.com/EduGorilla/lead-marketplace
 **/

function create_edugorilla_lead_table()
{
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name1 = $wpdb->prefix . 'edugorilla_lead_details'; //The table containing all the leads.
	$sql1 = "CREATE TABLE $table_name1 (
                                            id int(11) NOT NULL AUTO_INCREMENT,
                                            name varchar(200) NOT NULL,
                                            contact_no varchar(50) NOT NULL,
                                            email varchar(200) NOT NULL,
                                            query text(500) NOT NULL,
                                            is_promotional text(500) NOT NULL,
                                            listing_type text(500) NOT NULL,
                                            category_id text(500) NOT NULL,
                                            keyword text(500) NOT NULL,
                                            location_id varchar(200) NOT NULL,
                                            date_time varchar(200) NOT NULL,
                                            PRIMARY KEY id (id)
                                        ) $charset_collate;";


	$table_name2 = $wpdb->prefix . 'edugorilla_lead_contact_log'; //Logs when the leads were contacted.
	$sql2 = "CREATE TABLE $table_name2 (
                                            id int(11) NOT NULL AUTO_INCREMENT,
                                            contact_log_id int(11) NOT NULL,
                                            post_id int(11) NOT NULL,
                                            email_status text NOT NULL,
                                            sms_status text NOT NULL,
                                            date_time varchar(200) NOT NULL,
                                            PRIMARY KEY id (id)
                                        ) $charset_collate;";


	$table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions'; //Transaction history for EduCash.
    $sql3 = "CREATE TABLE $table_name3 (
                                            id mediumint(9) NOT NULL AUTO_INCREMENT,
                                            admin_id int(9) NOT NULL,
                                            client_id int(9) NOT NULL,
                                            transaction int(9) DEFAULT 0 NOT NULL,
                                            amount int(9) DEFAULT 0 NOT NULL,
                                            time datetime NOT NULL,
                                            comments varchar(500) DEFAULT 'No comment' NOT NULL,
                                            PRIMARY KEY  (id)
                                        ) $charset_collate;";

	$table_name4 = $wpdb->prefix . 'edugorilla_lead_client_mapping'; //Mapping between client id and lead id
		$sql4 = "CREATE TABLE $table_name4 (
				                            id int(15) NOT NULL AUTO_INCREMENT PRIMARY KEY,
											client_id int(15) NOT NULL,
											lead_id int(15) NOT NULL,
											is_unlocked boolean DEFAULT 0 NOT NULL,
                                            is_hidden boolean DEFAULT 0 NOT NULL,
						                    date_time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
									        operation SMALLINT(1) NOT NULL DEFAULT '1'
				  					    ) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	//Creating a table in cureent wordpress
	dbDelta($sql1);
	dbDelta($sql2);
	dbDelta($sql3);
	dbDelta($sql4);
	dbDelta($sql5);

}

register_activation_hook(__FILE__, 'create_edugorilla_lead_table');

register_activation_hook(__FILE__,'my_email_activation');

register_activation_hook(__FILE__, 'table_for_client');

register_deactivation_hook(__FILE__, 'my_deactivation');

function edugorilla_lead_plugin_uninstall()
{
	global $wpdb;
	$table_name1 = $wpdb->prefix . 'edugorilla_lead_details';
	$table_name2 = $wpdb->prefix . 'edugorilla_lead_contact_log';
	$table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
	$table_name4 = $wpdb->prefix . 'edugorilla_lead_client_mapping';
	$table_name6 = $wpdb->prefix . 'edugorilla_client_preferences';
	$wpdb->query("DROP TABLE IF EXISTS $table_name1");
	$wpdb->query("DROP TABLE IF EXISTS $table_name2");
	$wpdb->query("DROP TABLE IF EXISTS $table_name3");
	$wpdb->query("DROP TABLE IF EXISTS $table_name4");
	$wpdb->query("DROP TABLE IF EXISTS $table_name6");
}//end pluginUninstall function

//hook into WordPress when its being deactivated, uncommenting the following line will cause data loss
//register_deactivation_hook(__FILE__, 'edugorilla_lead_plugin_uninstall');

add_action("admin_menu", "create_edugorilla_menus");

function create_edugorilla_menus()
{
	add_object_page(
		'Lead Marketplace',
		'Lead Marketplace',
		'read',
		'edugorilla',
		'edugorilla'
	);

	add_submenu_page(
		'edugorilla',
		'Lead Marketplace | Lead capture form',
		'Lead capture form',
		'read',
		'edugorilla',
		'edugorilla'
	);

	add_submenu_page(
		'edugorilla',
		'Lead Marketplace | Listing',
		'Sent Leads',
		'read',
		'Listing',
		'form_list'
	);

	add_submenu_page(
		'edugorilla',
		'Lead Marketplace | OTP',
		'OTP',
		'read',
		'edugorilla-otp',
		'edugorilla_otp'
	);

	add_submenu_page(
		'edugorilla',
		'Lead Marketplace | Template of Email',
		'Template of Email',
		'read',
		'edugorilla-email-setting',
		'edugorilla_email_setting'
	);

	add_submenu_page(
		'edugorilla',
		'Lead Marketplace | Template of SMS',
		'Template of SMS',
		'read',
		'edugorilla-sms-setting',
		'edugorilla_sms_setting'
	);

	add_submenu_page(
		'',
		'Lead Marketplace | Edit Lead',
		'Promotion Sent Edit',
		'read',
		'edugorilla-edit-lead',
		'edugorilla_lead_edit'
	);

	add_submenu_page(
		'',
		'Lead Marketplace | Delete Lead',
		'View Lead',
		'read',
		'edugorilla-delete-lead',
		'edugorilla_lead_delete'
	);

	add_submenu_page(
		'edugorilla',
		'Lead Marketplace | Allocate EduCash',
		'Allocate EduCash',
		'read',
		'allocate_educash_form_page',
		'allocate_educash_form_page'
	);

    add_submenu_page(
		'edugorilla',
		'Lead Marketplace | Transaction History',
		'Transaction History',
		'read',
		'transaction_history_form_page',
		'transaction_history_form_page'
	);

	add_submenu_page(
		'edugorilla',
		'Lead Marketplace | Conversion Tables',
		'Conversion Tables',
		'read',
		'conversion-tables',
		'conversion_tables'
	);

    add_submenu_page(
		'edugorilla',
		'Lead Marketplace | Third Party Settings',
		'Third Party Settings',
		'manage_options',
		'edugorilla-settings',
		'edugorilla_settings'
	);
}

include_once plugin_dir_path(__FILE__) . "view.php";
include_once plugin_dir_path(__FILE__) . "edit.php";
include_once plugin_dir_path(__FILE__) . "otp.php";
include_once plugin_dir_path(__FILE__) . "sms_setting.php";
include_once plugin_dir_path(__FILE__) . "educash_allotment_and_history.php";
include_once plugin_dir_path(__FILE__) . 'frontend/class-Lead-Card.php'; /*Cards used for displaying leads */
include_once plugin_dir_path(__FILE__) . 'frontend/class-Custom-Lead-API.php'; /*API to be used for displaying leads */
include_once plugin_dir_path(__FILE__) . 'frontend/class-EduCash-Helper.php'; /*Utility class used for dealing with EduCash */
include_once plugin_dir_path(__FILE__) . 'database/class-DataBase-Helper.php'; /*Utility class used for dealing with Database */
include_once plugin_dir_path(__FILE__) . "send_email_to_client.php";

include_once plugin_dir_path(__FILE__) . "edugorilla_pages.php";



function edugorilla()
{
	$caller = $_POST['caller'];

	if ($caller == "self") {
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
			$result1 = $wpdb->insert(
				$wpdb->prefix . 'edugorilla_lead_details',
				array(
					'name' => $name,
					'contact_no' => $contact_no,
					'email' => $email,
					'query' => $query,
					'is_promotional' => $is_promotional_lead,
					'listing_type' => $listing_type,
					'keyword' => $keyword,
					'category_id' => $category,
					'location_id' => $location_id,
					'date_time' => current_time('mysql')
				)
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

			foreach ($json_results as $json_result) {
				if ($is_promotional_lead == "yes") {
					$edugorilla_email_subject = str_replace("{category}", $json_result->contact_category, $edugorilla_email['subject']);
					$email_template_datas = array("{Contact_Person}" => $json_result->contact_person, "{category}" => $json_result->contact_category, "{location}" => $json_result->contact_location, "{listing_URL}" => $json_result->listing_url, "{name}" => $name, "{contact no}" => $contact_no, "{email address}" => $email, "{query}" => $query);

					foreach ($email_template_datas as $var => $email_template_data) {
						$edugorilla_email_body = str_replace($var, $email_template_data, $edugorilla_email_body);
					}

					$institute_send_emails_status = send_mail($edugorilla_email_subject, $edugorilla_email_body);

					$institute_emails = explode(",", $json_result->emails);
					foreach ($institute_emails as $institute_email) {
						add_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');

						if (!empty($institute_email))
							$institute_emails_status[$institute_email] = wp_mail($institute_email, $edugorilla_email_subject, ucwords($edugorilla_email_body));

						remove_filter('wp_mail_content_type', 'edugorilla_html_mail_content_type');

					}

					$institute_phones = explode(",", $json_result->phones);
					include_once plugin_dir_path(__FILE__) . "api/gupshup-api.php";
					foreach ($institute_phones as $institute_phone) {
						$smsapi = get_option("smsapi");
						$msg = str_replace("{Contact_Person}", $json_result->contact_person, $smsapi['message']);
						$institute_sms_status[$institute_phone] = send_sms($smsapi['username'],$smsapi['password'],$institute_phone,$msg);
					}

					$contact_log_id = $wpdb->insert_id;

					$result2 = $wpdb->insert(
						$wpdb->prefix . 'edugorilla_lead_contact_log',
						array(
							'contact_log_id' => $contact_log_id,
							'post_id' => $json_result->post_id,
							'email_status' => json_encode($institute_emails_status),
							'sms_status' => json_encode($institute_sms_status),
							'date_time' => current_time('mysql')
						)
					);

				}
			}

			if ($result1) {
				$success = "Saved Successfully.";
			} elseif ($result2) $success = "Saved and Message Send Successfully.";
			else $success = $result1;

			//  foreach($_REQUEST as $var=>$val)$$var="";
		}
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

								<option value="<?php echo $var; ?>">
									<?php
									$d = get_term_by('id', $var, 'listing_categories');
									echo $d->name;
									?>
								</option>

								<?php
								foreach ($vals as $index => $val) {
									?>

									<option value="<?php echo $index; ?>">
										<?php echo $val; ?>
									</option>
									<?php
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

								<option value="<?php echo $var; ?>">
									<?php
									$d = get_term_by('id', $var, 'locations');
									echo $d->name;
									?>
								</option>

								<?php
								foreach ($vals as $index => $val) {
									?>

									<option value="<?php echo $index; ?>">
										<?php echo "->" . $val; ?>
									</option>
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
						<input type="hidden" name="caller" value="self">
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


function script()
{
	wp_enqueue_style('select2-css', plugins_url('/libs/select2/select2.css', __FILE__));
	wp_enqueue_style('modal-css', plugins_url('/css/jquery.modal.css', __FILE__));
	wp_enqueue_style('jquery-ui-styles', "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css");

	wp_enqueue_script(
		'select2-script',                         // Handle
		plugins_url('/libs/select2/select2.js', __FILE__),  // Path to file
		array('jquery')                             // Dependancies
	);

	wp_enqueue_script(
		'modal-script',                         // Handle
		plugins_url('/js/jquery.modal.js', __FILE__),  // Path to file
		array('jquery')                             // Dependancies
	);
	wp_enqueue_script(
		'script',                         // Handle
		plugins_url('/js/script.js', __FILE__),  // Path to file
		array('jquery', 'jquery-ui-datepicker', 'jquery-ui-tabs') // Dependancies
	);

}

add_action('admin_enqueue_scripts', 'script', 2000);


function edugorilla_show_location()
{
	$term = strtolower($_REQUEST['term']);
	$ptype = strtolower($_REQUEST['ptype']);
	$address = $_REQUEST['address'];
	$category = $_REQUEST['category'];

	$args = array();
	$args['posts_per_page'] = -1;
	$args['post_status'] = 'publish';
	if (!empty($ptype)) $args['post_type'] = $ptype;
	if (!empty($term)) $args['s'] = $term;

	if (!empty($category) && !empty($address)) $args['tax_query']['relation'] = 'AND';

	if (!empty($category)) {
		//$address = "%".$address."%";
		$args['tax_query'][0] = array(
			'taxonomy' => 'listing_categories',
			'field' => 'id',
			'terms' => $category
		);
	}

	if (!empty($address)) {
		//$address = "%".$address."%";
		$args['tax_query'][1] = array(
			'taxonomy' => 'locations',
			'field' => 'id',
			'terms' => $address
		);
	}

	$eduction_posts = array();
	$the_query = new WP_Query($args);
	if ($the_query->have_posts()) {
		while ($the_query->have_posts()) {
			$the_query->the_post();
			$emails = array();
			$phones = array();
			$eduction_post = array();
			$eduction_post['post_id'] = get_the_ID();
			$eduction_post['title'] = get_the_title();
			$eduction_post['listing_url'] = get_permalink($the_query->ID);

			if (get_post_meta(get_the_ID(), 'listing_address', true)) $eduction_post['address'] = get_post_meta(get_the_ID(), 'listing_address', true);
			else  $eduction_post['address'] = "Unavailable";

			if (get_post_meta(get_the_ID(), 'listing_verified', true)) $eduction_post['flag'] = "verified";
			else  $eduction_post['flag'] = "Unverified";

			if (get_post_meta(get_the_ID(), 'listing_person', true)) $eduction_post['contact_person'] = get_post_meta(get_the_ID(), 'listing_person', true);
			else  $eduction_post['contact_person'] = "Guest";

			if (get_post_meta(get_the_ID(), 'listing_locations', true)) {
				$location_temp = get_post_meta(get_the_ID(), 'listing_locations', true);
				$eduction_post['contact_location'] = str_replace("-", " ", $location_temp[1]) . ", " . str_replace("-", " ", $location_temp[0]);
			} else  $eduction_post['contact_location'] = "N/A";

			if (get_post_meta(get_the_ID(), 'listing_listing_category', true)) {
				$category_temp = get_post_meta(get_the_ID(), 'listing_listing_category', true);

				$eduction_post['contact_category'] = str_replace("-", " ", $category_temp[0]) . ", " . str_replace("-", " ", $category_temp[1]);
			} else  $eduction_post['contact_category'] = "N/A";

			//check whether email values ara available or not.
			if (get_post_meta(get_the_ID(), 'listing_email', true)) $emails[] = get_post_meta(get_the_ID(), 'listing_email', true);
			if (get_post_meta(get_the_ID(), 'listing_alternate_email', true)) $emails[] = get_post_meta(get_the_ID(), 'listing_alternate_email', true);

			$eduction_post['emails'] = implode(", ", $emails);

			//check whether phone numbers are available or not.
			if (get_post_meta(get_the_ID(), 'listing_phone', true)) $phones[] = get_post_meta(get_the_ID(), 'listing_phone', true);
			if (get_post_meta(get_the_ID(), 'listing_phone2', true)) $phones[] = get_post_meta(get_the_ID(), 'listing_phone2', true);
			if (get_post_meta(get_the_ID(), 'listing_phone3', true)) $phones[] = get_post_meta(get_the_ID(), 'listing_phone3', true);
			if (get_post_meta(get_the_ID(), 'listing_phone4', true)) $phones[] = get_post_meta(get_the_ID(), 'listing_phone4', true);
			if (get_post_meta(get_the_ID(), 'listing_phone5', true)) $phones[] = get_post_meta(get_the_ID(), 'listing_phone5', true);

			$eduction_post['phones'] = implode(", ", $phones);

			$eduction_post['lat'] = get_post_meta(get_the_ID(), 'listing_map_location_latitude', true);
			$eduction_post['long'] = get_post_meta(get_the_ID(), 'listing_map_location_longitude', true);

			$eduction_posts[] = $eduction_post;
		}
	}
	wp_reset_query();

	//$response = json_encode(array_values($eduction_posts));
	$response = json_encode($eduction_posts);
	echo $response;
	exit();

}

add_action('wp_ajax_edugorilla_show_location', 'edugorilla_show_location');
add_action('wp_ajax_nopriv_edugorilla_show_location', 'edugorilla_show_location');


function edugorilla_html_mail_content_type()
{
	return 'text/html';
}

function vc_remove_wp_ver_css_js($src)
{
	if (strpos($src, 'ver=' . get_bloginfo('version')))
		$src = remove_query_arg('ver', $src);
	return $src;
}

add_filter('style_loader_src', 'vc_remove_wp_ver_css_js', 9999);
add_filter('script_loader_src', 'vc_remove_wp_ver_css_js', 9999);


include_once plugin_dir_path(__FILE__) . "email_setting.php";

include_once plugin_dir_path(__FILE__) . "list.php";

include_once plugin_dir_path(__FILE__) . "inc/shortcode_transaction_history.php";

include_once plugin_dir_path(__FILE__) . "inc/shortcode_educash_payment.php";

function edugorilla_shortcode_require()
{
	// for bootstrap 4.0 to work
	wp_enqueue_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css');
	wp_enqueue_script('ajaxlib1', 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js');
	wp_enqueue_script('ajaxlib2', 'https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js');
	wp_enqueue_script('bootjs', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js');
	wp_enqueue_script('angularJs', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.js');
	wp_enqueue_script('angularAnimate', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular-animate.js');

	wp_enqueue_style('custom_css', plugins_url('/frontend/css/lead-market-place-frontend.css', __FILE__), array(), rand(111, 9999), 'all');
	wp_enqueue_style('custom_css', plugins_url('/frontend/css/lead-portal-animations.css', __FILE__), array(), rand(111, 9999), 'all');

	wp_enqueue_script(
		'angular-leads-script',                         // Handle
		plugins_url('/frontend/js/lead-portal.js', __FILE__),  // Path to file
		array('angularJs')                             // Dependancies
	);
}


add_action('wp_enqueue_scripts', 'edugorilla_shortcode_require');
?>
