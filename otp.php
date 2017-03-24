<?php
function edugorilla_otp()
{
    $otp_form = $_POST['otp_form'];
    if ($otp_form == "self") {
        $edugorilla_mno = $_POST['edugorilla_mno'];
        if (!preg_match("/([0-9]{10}+)/", $edugorilla_mno)) $error = "INVALID";

        if (empty($error)) {
        	include_once plugin_dir_path(__FILE__) . "api/gupshup.api.php";
        	$otp = rand(1000,9999);
        	$msg = "Your OTP is".$otp.".";
        	$credentials = get_option("smsapi");
        	$response = send_sms($credentials['username'],$credentials['password'],$edugorilla_mno,$msg);

        	$response = trim($response);

        	if($response != "error") $success = "<div class='notice notice-success is-dismissible'><p>OTP $otp has been sent successfully. </p></div>";
        	else $success = "<div class='notice notice-error is-dismissible'><p>Something went wrong</p></div>";
        }
    }
    ?>
    <div class="wrap">
        <h1>EduGorilla OTP</h1>
        <?php
        if ($success) {
             echo $success;
    	?>

            <?php
        }
        ?>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th>Mobile No.<sup><font color="red">*</font></sup></th>
                    <td>
                        <input name="edugorilla_mno" value="<?php echo $edugorilla_mno; ?>"
                               placeholder="Type mobile no. here...">
                        <font color="red"><?php echo $error; ?></font>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="hidden" name="otp_form" value="self">
                        <input type="submit" class="button button-primary" value="Send OTP">
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php
}

function edugorilla_settings()
{ ?>
  <div id="tabs">

    <ul>
      <li><a href="#tabs-1">Gupshup Credentials</a></li>
      <li><a href="#tabs-2">PayUMoney Credentials</a></li>
    </ul>

    <div id="tabs-1">
        <?php
        $ghupshup_credentials_form = $_POST['ghupshup_credentials_form'];
        if($ghupshup_credentials_form == "self")
        {
            $ghupshup_user_id = $_POST['ghupshup_user_id'];
            $ghupshup_pwd = $_POST['ghupshup_pwd'];

            $errors = array();

            if(empty($ghupshup_user_id)) $errors['ghupshup_user_id'] = "Empty";
            if(empty($ghupshup_pwd)) $errors['ghupshup_pwd'] = "Empty";

            if(empty($errors))
            {
                $credentials = array("user_id"=>$ghupshup_user_id, "password" => $ghupshup_pwd);
                update_option("ghupshup_credentials",$credentials);
                $success = "Saved Successfully";
            }
        }else
        {
            $credentials = get_option("ghupshup_credentials");
            $ghupshup_user_id = $credentials['user_id'];
            $ghupshup_pwd = $credentials['password'];
        }
        ?>
        <div class="wrap">
            <h1>Ghupshup Credentials</h1>
            <form method="post">
                <table>
                    <tr>
                        <th>User ID</th>
                        <td>
                            <input name="ghupshup_user_id" value="<?php echo $ghupshup_user_id; ?>">
                            <font color="red"><?php echo $errors['ghupshup_user_id']; ?></font>
                        </td>
                    </tr>
                    <tr>
                        <th>Password</th>
                        <td>
                            <input name="ghupshup_pwd" value="<?php echo $ghupshup_pwd; ?>">
                            <font color="red"><?php echo $errors['ghupshup_pwd']; ?></font>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="hidden" name="ghupshup_credentials_form" value="self"></td>
                        <td><input type="submit" class="button button-primary" value="Save"></td>
                    </tr>
                </table>
            </form>
        </div>
      </div>
    <div id="tabs-2">
      <div class="wrap">
          <h1>PayUMoney Credentials</h1></br>
          <?php
          $out = get_option("payumoney_parameters");
         ?>
          <form method="post" action="">
              <table>
                  <tr>
                      <th>Salt Id</th>
                      <td>
                          <input type="text" name="salt" value="<?php echo $out['user_id'] ?>"/></br>
                      </td>
                  </tr>
                  <tr>
                      <th>Merchant Id</th>
                      <td>
                          <input type="text" name="mcid" value="<?php echo $out['password'];?>"/></br></br>
                      </td>
                  </tr>
                  <tr>
                      <td><input type="submit" class="button button-primary" value="Save"></td>
                  </tr>
              </table>
          </form>
          </div></br></br>
      </div>
      <?php
      if (isset($_POST['salt']) && isset($_POST['mcid']) )
      {
        $salt = $_POST['salt'];
        $txnid = $_POST['mcid'];
        if(!empty($salt) && !empty($txnid)){

          $credentials1 = array("user_id"=>$salt, "password" =>$txnid);
          update_option("payumoney_parameters",$credentials1);
          $success = "Saved Successfully";

          echo"<h2>Your salt and merchant id are successfully recieved. Now you can go ahead and continue with transactions</h2>";

        }
        else{
          echo "<h2>Please fill salt and test key properly </h2><br><br>";
        }
      }
}


function conversion_tables(){?>
    <div class="wrap">
     <h1>Conversion Rate - Rs to educash</h1><br>
        <form method="post" action="">
            <table>
                <tr>
                <th>New Rate</th>
                    <td>
                        <input type="number" name="rate"/> rs</br></br>
                    </td>
                </tr>
                <tr>
                    <td><input type="submit" class="button button-primary" value="Save"></td>
                </tr>
            </table>
        </form>
    </div>

       <?php
      if (isset($_POST['rate']))
      {
        if(!empty($_POST['rate']))
          {
            $credentials2 = array("rate"=>$_POST['rate']);
            update_option("current_rate",$credentials2);
            $success = "Saved Successfully";
          }
      }
      ?>
      <table>
        <tr>
          <th>Current Rate = </th>
            <td>1 educash for <?php
             $out = get_option("current_rate");
            echo $out['rate']; ?> Rs</td>
        </tr>
    </table><br></br>

    <div class="wrap">
      <h1>Conversion Rate - Karma to educash</h1><br>
        <form method="post" action="">
            <table>
                <tr>
                    <th>New Rate</th>
                    <td>
                        <input type="number" name="karma"/> karmas.</br></br>
                    </td>
                </tr>
                <tr>
                    <td><input type="submit" class="button button-primary" value="Save"></td>
                </tr>
            </table>
        </form>
    </div>

      <?php
      if (isset($_POST['karma']))
      {
        if(!empty($_POST['karma']))
          {
            $credentials3 = array("rate"=>$_POST['karma']);
            update_option("karma_rate",$credentials3);
          }
      } ?>

      <table>
        <tr>
          <th>Current Rate = </th>
            <td>1 educash for <?php
             $out = get_option("karma_rate");
            echo $out['rate']; ?> karmas</td>
        </tr>
    </table><br></br>


    <div class="wrap">
    <h1>Conversion Rate - Educash to leads</h1><br>
      <form method="post" action="">
          <table>
              <tr>
                  <th>New Rate</th>
                  <td>
                      <input type="number" name="educash_to_lead"/> educash.</br></br>
                  </td>
              </tr>
              <tr>
                  <td><input type="submit" class="button button-primary" value="Save"></td>
              </tr>
          </table>
      </form>
    </div>

    <?php
    if (isset($_POST['educash_to_lead']))
    {
      if(!empty($_POST['educash_to_lead']))
        {
          $credentials3 = array("rate"=>$_POST['educash_to_lead']);
          update_option("educashtolead_rate",$credentials3);
        }
    } ?>

    <table>
      <tr>
        <th>Current Rate = </th>
          <td>1 lead for <?php
           $out = get_option("educashtolead_rate");
          echo $out['rate']; ?> educash</td>
      </tr>
    </table>
<?php
  }
?>
