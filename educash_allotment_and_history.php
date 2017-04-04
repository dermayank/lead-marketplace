<?php

require_once __DIR__ . '/frontend/class-EduCash-Helper.php';

function allocate_educash_form_page()
{
    global $wpdb;
    $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
    $users_table = $wpdb->prefix.'users';

//Checking if the admin has filled adequate information to submit the form to allot educash and inserting the legal values in table

		if ($_POST['submit']) {
			if (empty($_POST['clientName'])) {
            echo '<script>alert("The field of client email cannot be blank");</script>';
            }
			else {
			if (empty($_POST['educash'])) {
            echo '<script>alert("The field of educash cannot be blank");</script>';
            }
			else {
            $clientName	= $_POST['clientName'];
            $check_client = $wpdb->get_var("SELECT COUNT(ID) from $users_table WHERE user_email = '$clientName' ");
            if($check_client == 0){
                echo '<script>alert("This client does not exist in our database");</script>';
            }
			else{
				$educash_added = $_POST['educash'];
				$adminName = wp_get_current_user();
				$time = current_time('mysql');
				$money = $_POST['money'];
				$adminComment = $_POST['adminComment'];
				
				$firstname = $_POST['client_firstname'];
				$lastname = $_POST['client_lastname'];
				$street = $_POST['client_street'];
				$city = $_POST['client_city'];
				$postalcode = $_POST['client_postalcode'];
				$phone_number = $_POST['client_phone_number'];
				$country = $_POST['client_country'];
				
				$initiate_transaction = new EduCash_Helper();
				$make_transaction = $initiate_transaction->add_educash($clientName, $educash_added, $money, $adminComment, $firstname, $lastname, $street, $city, $postalcode, $phone_number, $country);
           }
        }
		}
		}

		if ($_POST['SUBMIT']) {
        if (empty($_POST['clientName1'])) {
            $clientnamerr = "<span  style='color:red;'>* This field cannot be blank</span>";
        } else {
			if (empty($_POST['educash1'])) {
            $educasherr = "<span style='color:red;'>* This field cannot be blank</span>";
          }
		    else{
            $clientName = $_POST['clientName1'];
            $check_client = $wpdb->get_var("SELECT COUNT(ID) from $users_table WHERE user_email = '$clientName' ");
             if($check_client == 0){
                $invalid_client = "<span style='color:red'>This client does not exist in our database</span>";
             }
             else {
			 $educash_added = $_POST['educash1'];
			 $client_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$clientName' ");
			 $check_transaction = new EduCash_Helper();
			 $total = $check_transaction->get_educash($client_ID_result);
             $final_total = $total + $educash_added;
              if($final_total >= 0){
		       $all_meta_for_user = get_user_meta( $client_ID_result );
	           $client_firstname = $all_meta_for_user['user_general_first_name'][0];
	           $client_lastname = $all_meta_for_user['user_general_last_name'][0];
	           $client_street = $all_meta_for_user['user_address_street_and_number'][0];
	           $client_city = $all_meta_for_user['user_address_city'][0];
	           $client_postal_code = $all_meta_for_user['user_address_postal_code'][0];
	           $client_phone_number = $all_meta_for_user['user_general_phone'][0];
	           $client_country = $all_meta_for_user['user_address_country'][0];
               }
		}
		}
		}
		}

//Form to allocate educash
?>
<style>
.modalbg {
    display: none;
	position:fixed;
	top:0;
	right:0;
    z-index: 1;
    padding-top: 5%;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-contentbg {
    background-color: #fefefe;
	position:relative;
	margin:auto;
    padding: 0;
    border: 1px solid #888;
    width: 55%;
	height:80%;
	overflow:auto;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

@-webkit-keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

@keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

.closebg {
    color: white;
    float: left;
    font-size: 28px;
    font-weight: bold;
}

.closebg:hover,
.closebg:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal-headerbg {
	width:100%;
    padding: 2px 16px;
    background-color: #5cb85c;
    color: white;
}

.modal-bodybg {padding: 2px 16px;}
</style>
<script>
    function validate_allotment_form() {
    var x = document.getElementById("clientName11").value;
    var y = document.getElementById("educash11").value;
    if (x == "" && (y == "" || y == 0)) {
        document.getElementById('errmsg1').innerHTML = "* This field cannot be blank";
        document.getElementById('errmsg2').innerHTML = "* This field cannot be blank or 0";
        return false;
    }
    if (x == "") {
        document.getElementById('errmsg1').innerHTML = "* This field cannot be blank";
        return false;
    }
    if (y == "" || y == 0) {
        document.getElementById('errmsg2').innerHTML = "* This field cannot be blank or 0";
        return false;
    }
	if(x != "" && y != 0) {
	    return true;
	}
}
    function validate_final_allotment_form(){
    var x = document.getElementById("clientName22").value;
    var y = document.getElementById("educash22").value;
    var z = document.getElementById("money22").value;
	
    if (x == "" && (y == "" || y == 0)) {
        document.getElementById('errmsgf1').innerHTML = "* This field cannot be blank";
        document.getElementById('errmsgf2').innerHTML = "* This field cannot be blank or 0";
        return false;
    }
    if (x == "") {
        document.getElementById('errmsgf1').innerHTML = "* This field cannot be blank";
        return false;
    }
    if (y == "" || y == 0) {
        document.getElementById('errmsgf2').innerHTML = "* This field cannot be blank or 0";
        return false;
    }
    if (z < 0) {
        document.getElementById('errmsgf3').innerHTML = "* This field cannot be negative";
		return false;
    }
	for( i = 0; i < 7; i++ ){
		if(document.getElementsByClassName('compulsory_popup_field')[i].value == ""){
			document.getElementsByClassName('compulsory_popup_field_error')[i].innerHTML = "* This field cannot be blank";
			return false;
		}
	}
	if(x != "" && y != 0 && z >= 0){
		return confirm("Are you sure you want to submit this entry ?");
		}
}
</script>
<div id = 'myModalbg' class = "modalbg">
    <div class = "modal-contentbg">
    <div class = "modal-headerbg">
      <span class = "closebg">&times;</span>
      <center><h2>You are about to make the follwing entry:</h2></center>
    </div>
    <div class = "modal-bodybg">
<div class = "wrap">
<form name = "myForm" method = 'post' onsubmit = "return validate_final_allotment_form()" action = "<?php echo $_SERVER['REQUEST_URI'];?>">
<table class = "form-table">

				<tr>
					<th>Street and number<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'text' name = 'client_street' class = 'compulsory_popup_field' value = "<?php echo $client_street; ?>" maxlength = '100'>
						<span style = 'color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
					<td></td>
					<th>Client Firstname<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'text' name = 'client_firstname' class = 'compulsory_popup_field' value = "<?php echo $client_firstname;?>" maxlength = '100'>
						<span style = 'color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
				</tr>
				<tr>
					<th>Postal code<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'text'  name = 'client_postalcode' class = 'compulsory_popup_field' value = "<?php echo $client_postal_code; ?>" maxlength = '100'>
						<span style = 'color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
					<td></td>
					<th>Client Lastname<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'text' name = 'client_lastname' class = 'compulsory_popup_field' value = "<?php echo $client_lastname; ?>" maxlength = '100'>
						<span style = 'color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
				</tr>
				<tr>
					<th>City<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'text' name = 'client_city' class = 'compulsory_popup_field' value = "<?php echo $client_city; ?>" maxlength = '100'>
						<span style = 'color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
					<td></td>
					<th>Client Email<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'text' id = 'clientName22' class = 'popup_input_field' name = 'clientName' value = "<?php echo $_POST['clientName1']; ?>" maxlength = '100'>
						<span style = 'color:red;' id = 'errmsgf1'></span>
					</td>
				</tr>
				<tr>
					<th>Phone No.<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'number' name = 'client_phone_number' class = 'compulsory_popup_field' value = "<?php echo $client_phone_number; ?>" maxlength = '100'>
						<span style = 'color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
					<td></td>
					<th>EduCash (Enter EduCash to be allotted)<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'number' id = 'educash22' class = 'popup_input_field' name = 'educash' min = '-100000000' value = "<?php echo $_POST['educash1']; ?>" max = '100000000'>
						<span style = 'color:red;' id = 'errmsgf2'></span>
					</td>
				</tr>
				<tr>
					<th>Country<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'text' name = 'client_country' class = 'compulsory_popup_field' value = "<?php echo $client_country; ?>" maxlength = '100'>
						<span style = 'color:red;' class = 'compulsory_popup_field_error'></span>
					</td>
					<td></td>
					<th>Amount (Amount paid by client)</th>
					<td>
						<input type = 'number' id = 'money22' class = 'popup_input_field' name = 'money' min = '0' value = "<?php if($_POST['educash1'] >= 0){$educash_rate = get_option("current_rate"); echo $educash_rate['rate']*$_POST['educash1'];}
                                               else{echo "0";} ?>" max = '100000000'>
						<span style = 'color:red;' id = 'errmsgf3'></span>
					</td>
				</tr>
</table><br/>
<center><b>Comments (optional)</b><br/><textarea rows = '4' cols = '60' id = 'adminComment22' class = 'popup_input_field' name = 'adminComment' maxlength = '500'><?php echo $_POST['adminComment1']; ?></textarea><br/><br/>
						<input type = 'submit' name = 'submit'><br/><br/></center>

			
</form>
</div>
    </div>
    </div>
</div>
        <div class = "wrap">
		<h1>Use this form to allocate educash to a client</h1>
		
		<form method = 'post' onsubmit = "return validate_allotment_form()" action = "<?php echo $_SERVER['REQUEST_URI'];?>">
			<table class = "form-table">
				<tr>
					<th>Client Email<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'text' id = 'clientName11' name = 'clientName1' value = "<?php echo $_POST['clientName1']; ?>" placeholder = 'Type email here...' maxlength = '100'>
						<span style = 'color:red;' id = 'errmsg1'></span>
						<span><?php echo $clientnamerr; echo $invalid_client;?></span>
					</td>
				</tr>
				<tr>
					<th>EduCash (Enter EduCash to be allotted)<sup><font color="red">*</font></sup></th>
					<td>
						<input type = 'number' id = 'educash11' name = 'educash1' min = '-100000000' value = "<?php echo $_POST['educash1']; ?>" max = '100000000'>
						<span><?php echo $educasherr;?> </span>
                        <span style = 'color:red;' id = 'errmsg2'></span>
					</td>
				</tr>
				<tr>
					<th>Comments (optional)</th>
					<td>
                        <textarea rows = '4' cols = '60' id = 'adminComment11' name = 'adminComment1' maxlength = '500'><?php echo $_POST['adminComment1']; ?></textarea>
					</td>
				</tr>
				<tr>
					<th>
						<input type = "hidden">
					</th>
					<td>
						<input type = 'submit' name = 'SUBMIT'>
					</td>
				</tr>
			</table>
            </form>
			</div>

<?php
      if ($_POST['SUBMIT']) {
        if ((!empty($_POST['clientName1'])) && (!empty($_POST['educash1'])) && (!($check_client == 0)) && $final_total >= 0) {
			echo "<script>function display_dialogue(){var modal = document.getElementById('myModalbg');
		 modal.style.display = 'block';
         var spanbg = document.getElementsByClassName('closebg')[0];
         spanbg.onclick = function() {
         modal.style.display = 'none';
        }
        window.onclick = function(event) {
        if (event.target == modal) {
        modal.style.display = 'none';
        }
			}}
	    display_dialogue();</script>";
		};
		
		if($final_total < 0){
			   echo "<center><span style='color:red;'>The total balance that the client ".$_POST['clientName1']." has
                 is ".$total.". Your entry will leave this client with negative amount of educash which is not allowed.</span></center>";
		    }
	  }

//Displaying the transaction made just now if the values are legal and sending a mail to respective client otherwise displaying error message

    if ($_POST['submit'] && (!empty($_POST['clientName'])) && (!empty($_POST['educash'])) && (!($check_client == 0))) {
        if($make_transaction == true){

//Creating invoice

        require('pdf_library/invoice_functions.php');
        $pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
        $pdf->AddPage();

		$pdf->right_blocks(40, 45, 22, "EduGorilla Community Pvt. Ltd.");
		$pdf->right_blocks(40, 55, 12, "Regn. No. U74999UP2016PTC088614");
		$pdf->Image(plugin_dir_path(__FILE__) . "pdf_library/eg_logo.JPG",10,35,26.9491525,30);
		
		$r = $wpdb->get_row("SELECT * FROM $table_name3 WHERE time = '$time' ");
		
		$pdf->right_blocks(80, 10, 30, "INVOICE");
		$pdf->right_blocks(145, 93, 12, "Date: ".date("d/m/Y"));
		$pdf->right_blocks(145, 100, 12, "Transaction id: ".$r->id);
		$pdf->right_blocks_bold(7, 93, 12, "Billed to: ");
		$pdf->right_blocks(100, 205, 18, "PAYMENT MADE: ");
		$pdf->right_blocks(7, 230, 18, "THANKS FOR YOUR BUSINESS");

		$pdf->right_blocks(160, 205, 18, "Rs. ".$money."/-");
		
		
		$pdf->addCompanyAddress("Address: 4719/A, Sector 23A, Gurgaon-122002, India\n\nWebsite: https://edugorilla.com\n\nEmail: hello@edugorilla.com\n\nPhone no. +91 9410007819");
        $pdf->addClientAddress(ucwords("\n".$firstname.' '.$lastname."\n\n".
		                       $street.", ".
                               $city.' - '.$postalcode.", " .
                               $country)."\n\n".$clientName."\n\n".$phone_number);

		$cols=array( "Item"      => 61,
                     "Rate"      => 43,
                     "Quantity"  => 43,
                     "Amount"    => 43,);
        $pdf->addCols( $cols);
        $cols=array( "Item"      => "C",
                     "Rate"      => "C",
                     "Quantity"  => "C",
                     "Amount"    => "C");
        $pdf->addLineFormat( $cols);
        $pdf->addLineFormat($cols);
        $y    = 165;
		$educash_rate = get_option("current_rate");
        $line = array( "Item"      => "EduCash",
                       "Rate"      => $educash_rate['rate'],
                       "Quantity"  => $educash_added,
                       "Amount"    => "Rs. ".$money."/-");
        $size = $pdf->addLine( $y, $line );
        $y   += $size + 2;
							   
		$file_name = sys_get_temp_dir();
		$file_name.= "/invoice.pdf";
		$pdf->Output($file_name , "F");
		$attachment = array($file_name);

		$client_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$clientName' ");
	    $total_educash = new EduCash_Helper();
		$total = $total_educash->get_educash($client_ID_result);

		$send_email_for_transaction = new EduCash_Helper();
		$send_email_for_transaction->send_email($firstname, $lastname, $total, $clientName, $educash_added, $attachment);
		
		$display_transaction = new EduCash_Helper();
		$display_transaction->display_current_transaction($time, $clientName);

       if($educash_added > 0){
			$table_name6 = $wpdb->prefix.'edugorilla_client_preferences';
			$check_client_notifications = $wpdb->get_var("SELECT COUNT(id) from $table_name6 WHERE email_id = '$clientName' ");

			if($check_client_notifications == 0){
				$user_id = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$clientName' ");
				$_client_name = $firstname . " " . $lastname;
				$notification = "Monthly_Digest, Weekly_Digest, Daily_Digest, Instant_Notifications, ";
				$wpdb->insert(
					$table_name6,
					array(
						'id' => $user_id,
						'client_name' => $_client_name,
						'email_id' => $clientName,
						'contact_no' => $phone_number,
						'preferences' => $notification,
                        'unsubscribe_email' => 0,
						'unsubscribe_sms' => 0,
						'unlock_lead' => 0
					)
				);
			}

		}

   } else{
	   $client_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$clientName' ");
	   $check_transaction = new EduCash_Helper();
	   $total = $check_transaction->get_educash($client_ID_result);

	   echo "<center><span style='color:red;'>The total balance that the client ".$_POST['clientName']." has
                 is ".$total.". Your entry will leave this client with negative amount of educash which is not allowed.</span></center>";
   }
}
}

function transaction_history_form_page()
{
    global $wpdb;
    $table_name3 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
    $users_table = $wpdb->prefix.users;

//Checking if the admin has filled atleast one field to submit the form to see history

    if ($_POST['Submit']) {
        if (empty($_POST['admin_Name']) && empty($_POST['client_Name']) && empty($_POST['date']) && empty($_POST['date2'])) {
            $all_four_error = "<span style='color:red;'> * All four fields cannot be blank</span>";
        }
    }

//Form to see history of educash transactions
?>
    <div class = "wrap">
    <h1>Use this form to know the history of educash transactions</h1>
    <p style = 'color:green;'>Fill atleast one field<p>
    <form method = 'post' action = "<?php echo $_SERVER['REQUEST_URI'];?>">
             <table class = "form-table">
				<tr>
					<th>Admin Email</th>
					<td>
						<input type = 'text' name = 'admin_Name' placeholder = 'Type admin email here...' maxlength = '100'>
					</td>
				</tr>
				<tr>
					<th>Client Email</th>
					<td>
						<input type = 'text' name = 'client_Name' placeholder = 'Type client email here...' max = '100'>
					</td>
				</tr>
				<tr>
					<th>Date From: </th>
					<td>
						<input type = 'date' name = 'date' min = '1990-12-31' max = '2050-12-31'>
					</td>
				</tr>
				<tr>
					<th>Date To: </th>
					<td>
						<input type = 'date' name = 'date2' min = '1990-12-31' max = '2050-12-31'>
					</td>
				</tr>
				<tr>
					<th>
						<input type = "hidden">
					</th>
					<td>
						<input type = 'submit' name = 'Submit'>
					</td>
				</tr>
				<tr>
					<th>
						<input type = "hidden">
					</th>
					<td>
						<?php echo $all_four_error;?>
					</td>
				</tr>
			 </table>
    </form>
<?php
//Displaying the history of required fields

       $admin_Name = $_POST['admin_Name'];
       $client_Name = $_POST['client_Name'];
       $admin_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$admin_Name' ");
       $client_ID_result = $wpdb->get_var("SELECT ID FROM $users_table WHERE user_email = '$client_Name' ");
       $date = $_POST['date'];
       $date2 = $_POST['date2'];
    if (($_POST['Submit']))
          if((!empty($_POST['admin_Name']) || !empty($_POST['client_Name'])) && empty($_POST['date']) && empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1)");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                           IF('$client_Name' != '', client_id = '$client_ID_result', 1=1)");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1)");
            echo "<span style='color:green;'>Total EduCash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions is:</p>";
            echo "<center><table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((!empty($_POST['admin_Name']) || !empty($_POST['client_Name'])) && !empty($_POST['date']) && empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                           IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            echo "<span style='color:green;'>Total EduCash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions is:</p>";
            echo "<center><table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((!empty($_POST['admin_Name']) || !empty($_POST['client_Name'])) && empty($_POST['date']) && !empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                           IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE IF('$admin_Name' != '', admin_id = '$admin_ID_result', 1=1) AND
                                            IF('$client_Name' != '', client_id = '$client_ID_result', 1=1) AND DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            echo "<span style='color:green;'>Total EduCash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions is:</p>";
            echo "<center><table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((empty($_POST['admin_Name']) && empty($_POST['client_Name'])) && !empty($_POST['date']) && empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '2050-12-31' ");
            echo "<span style='color:green;'>Total EduCash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions done from ".$_POST['date']." is:</p>";
            echo "<center><table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((empty($_POST['admin_Name']) && empty($_POST['client_Name'])) && empty($_POST['date']) && !empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE DATE(time) BETWEEN 'TRUE' AND '$date2' ");
            echo "<span style='color:green;'>Total EduCash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions done till ".$_POST['date2']." is:</p>";
            echo "<center><table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
    if (($_POST['Submit']))
          if((empty($_POST['admin_Name']) && empty($_POST['client_Name'])) && !empty($_POST['date']) && !empty($_POST['date2'])) {
            $check_result = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name3  WHERE DATE(time) BETWEEN '$date' AND '$date2' ");
            if($check_result == 0){
            echo "<center><span style='color:red;'>No records found</span></center>";
            }
            else{
            $results = $wpdb->get_results("SELECT * FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '$date2' ");
            $total = $wpdb->get_var("SELECT sum(transaction) FROM $table_name3 WHERE DATE(time) BETWEEN '$date' AND '$date2' ");
            echo "<span style='color:green;'>Total EduCash transactions are <b>" . $total . "</b></span>";
            echo "<p>The history of transactions done from ".$_POST['date']." to ".$_POST['date2']." is:</p>";
            echo "<center><table class='widefat fixed' cellspacing='0'><tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            foreach ($results as $r) {
                $Admin_Id = $r->admin_id;
                $Client_Id = $r->client_id;
                $admin_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Admin_Id' ");
                $client_email_result = $wpdb->get_var("SELECT user_email FROM $users_table WHERE ID = '$Client_Id' ");
                echo "<tr><td>" . $r->id . "</td><td>" . $admin_email_result . "</td><td>" . $client_email_result . "</td><td>" . $r->transaction . "</td><td>".$r->amount."</td><td>" . $r->time . "</td><td>" . $r->comments . "</td></tr>";
            }
            echo "<tr><th>Id</th><th>Admin Email</th><th>Client Email</th><th>EduCash transaction</th><th>Amount</th><th>Time</th><th>Comments</th></tr>";
            echo "</table></center><br/>";
            }
      }
}
?>
