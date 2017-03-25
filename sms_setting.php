<?php
function edugorilla_sms_setting()
{
?>
    <div class="wrap">
        <h1>Promotional sms Template</h1>
        <div id="tabs">
          <ul>
              <li><a href="#tabs-lead-received">Promotional sms</a></li>
              <li><a href="#tabs-educash-added">EduCash Added</a></li>
              <li><a href="#tabs-educash-deducted">EduCash Deducted</a></li>
              <li><a href="#tabs-instant-sms">Instant sms</a></li>
              <li><a href="#tabs-daily-digest-sms">Daily Digest sms</a></li>
              <li><a href="#tabs-weekly-digest-sms">Weekly Digest sms</a></li>
              <li><a href="#tabs-monthly-digest-sms">Monthly Digest sms</a></li>
              <!--<li><a href="#tabs-4">Tab 4</a></li>
              <li><a href="#tabs-5">Tab 5</a></li>-->
          </ul>
            <div id="tabs-lead-received">
            <?php
                $sms_setting_form1 = $_POST['sms_setting_form1'];
                if ($sms_setting_form1 == "self") {
                    $errors1 = array();
                    $edugorilla_sms_body1 = $_POST['edugorilla_body1'];

                    if (empty($edugorilla_sms_body1)) $errors1['edugorilla_body1'] = "Empty";

                    if (empty($errors1)) {
                        $edugorilla_sms_setting1 = array('body' => stripslashes($edugorilla_sms_body1));

                        update_option("edugorilla_sms_setting1", $edugorilla_sms_setting1);
                        $success1 = "sms Settings Saved Successfully.";
                    	$sms_setting_options1 = get_option('edugorilla_sms_setting1');


                   		$edugorilla_sms_body1 = stripslashes($sms_setting_options1['body']);
                    }
                } else {
                    $sms_setting_options1 = get_option('edugorilla_sms_setting1');

                    $edugorilla_sms_body1 = stripslashes($sms_setting_options1['body']);

                }

                if ($success1) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success1; ?></p>
                    </div>
                    <?php
                }
            ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>sms body<sup><font color="red">*</font></sup></th>
                            <td>
                               <?php
									$content = $edugorilla_sms_body1;
									$editor_id = 'edugorilla_body1';

									wp_editor( $content, $editor_id );
								?>
                                <font color="red"><?php echo $errors1['edugorilla_body1']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="sms_setting_form1" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
          </div>
            <div id="tabs-educash-added">
            <?php

                $sms_setting_form2 = $_POST['sms_setting_form2'];
                if ($sms_setting_form2 == "self") {
                    $errors2 = array();
                    $edugorilla_sms_body2 = $_POST['edugorilla_body2'];

                    if (empty($edugorilla_sms_body2)) $errors2['edugorilla_body2'] = "Empty";

                    if (empty($errors2)) {
                        $edugorilla_sms_setting2 = array('body' => stripslashes($edugorilla_sms_body2));

                        update_option("edugorilla_sms_setting2", $edugorilla_sms_setting2);
                        $success2 = "sms Settings Saved Successfully.";

                    	$sms_setting_options2 = get_option('edugorilla_sms_setting2');
                    	$edugorilla_sms_body2 = stripslashes($sms_setting_options2['body']);
                    }
                } else {
                    $sms_setting_options2 = get_option('edugorilla_sms_setting2');
                    $edugorilla_sms_body2 = stripslashes($sms_setting_options2['body']);

                }

                if ($success2) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success2; ?></p>
                    </div>
                    <?php
                }
            ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>sms body<sup><font color="red">*</font></sup></th>
                            <td>
                                 <?php
									$content = $edugorilla_sms_body2;
									$editor_id = 'edugorilla_body2';

									wp_editor( $content, $editor_id );
								?>
                                <font color="red"><?php echo $errors2['edugorilla_body2']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="sms_setting_form2" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
          </div>
            <div id="tabs-educash-deducted">
            <?php
                $sms_setting_form3 = $_POST['sms_setting_form3'];
                if ($sms_setting_form3 == "self") {
                    $errors3 = array();
                    $edugorilla_sms_body3 = $_POST['edugorilla_body3'];

                    if (empty($edugorilla_sms_body3)) $errors3['edugorilla_body3'] = "Empty";

                    if (empty($errors3)) {
                        $edugorilla_sms_setting3 = array('body' => stripslashes($edugorilla_sms_body3));

                        update_option("edugorilla_sms_setting3", $edugorilla_sms_setting3);
                        $success3 = "sms Settings Saved Successfully.";
                    	$sms_setting_options3 = get_option('edugorilla_sms_setting3');
                    	$edugorilla_sms_body3 = stripslashes($sms_setting_options3['body']);
                    }
                } else {
                    $sms_setting_options3 = get_option('edugorilla_sms_setting3');
                    $edugorilla_sms_body3 = stripslashes($sms_setting_options3['body']);

                }

                if ($success3) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success3; ?></p>
                    </div>
                    <?php
                }
            ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>sms body<sup><font color="red">*</font></sup></th>
                            <td>
                                 <?php
									$content = $edugorilla_sms_body3;
									$editor_id = 'edugorilla_body3';
									wp_editor( $content, $editor_id );
								?>
                                <font color="red"><?php echo $errors3['edugorilla_body3']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="sms_setting_form3" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
          </div>
            <div id="tabs-instant-sms">
                <?php
                $sms_setting_form_instant = $_POST['sms_setting_form_instant'];
                if ($sms_setting_form_instant == "self") {
                    $errors1 = array();
                    $edugorilla_sms_body_instant = $_POST['edugorilla_body_instant'];

                    if (empty($edugorilla_sms_body_instant)) $errors1['edugorilla_body_instant'] = "Empty";

                    if (empty($errors1)) {
                        $edugorilla_sms_setting_instant = array('body' => stripslashes($edugorilla_sms_body_instant));

                        update_option("edugorilla_sms_setting_instant", $edugorilla_sms_setting_instant);
                        $success1 = "sms Settings Saved Successfully.";
                        $sms_setting_options_instant = get_option('edugorilla_sms_setting_instant');

                        $edugorilla_sms_body_instant = stripslashes($sms_setting_options_instant['body']);
                    }
                } else {
                    $sms_setting_options_instant = get_option('edugorilla_sms_setting_instant');

                    $edugorilla_sms_body_instant = stripslashes($sms_setting_options_instant['body']);

                }

                if ($success1) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success1; ?></p>
                    </div>
                    <?php
                }
                ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>sms body<sup><font color="red">*</font></sup></th>
                            <td>
                                <?php
                                $content = $edugorilla_sms_body_instant;
                                $editor_id = 'edugorilla_body_instant';

                                wp_editor($content, $editor_id);
                                ?>
                                <font color="red"><?php echo $errors1['edugorilla_body_instant']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="sms_setting_form_instant" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div id="tabs-daily-digest-sms">
                <?php
                $sms_setting_form_daily = $_POST['sms_setting_form_daily'];
                if ($sms_setting_form_daily == "self") {
                    $errors1 = array();
                    $edugorilla_sms_body_daily = $_POST['edugorilla_body_daily'];


                    if (empty($edugorilla_sms_body_daily)) $errors1['edugorilla_body_daily'] = "Empty";

                    if (empty($errors1)) {
                        $edugorilla_sms_setting_daily = array('body' => stripslashes($edugorilla_sms_body_daily));

                        update_option("edugorilla_sms_setting_daily", $edugorilla_sms_setting_daily);
                        $success1 = "sms Settings Saved Successfully.";
                        $sms_setting_options_daily = get_option('edugorilla_sms_setting_daily');

                        $edugorilla_sms_body_daily = stripslashes($sms_setting_options_daily['body']);
                    }
                } else {
                    $sms_setting_options_daily = get_option('edugorilla_sms_setting_daily');

                    $edugorilla_sms_body_daily = stripslashes($sms_setting_options_daily['body']);

                }

                if ($success1) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success1; ?></p>
                    </div>
                    <?php
                }
                ?>
                <form method="post">
                    <table class="form-table">
                        <tr>
                            <th>sms body<sup><font color="red">*</font></sup></th>
                            <td>
                                <?php
                                $content = $edugorilla_sms_body_daily;
                                $editor_id = 'edugorilla_body_daily';

                                wp_editor($content, $editor_id);
                                ?>
                                <font color="red"><?php echo $errors1['edugorilla_body_daily']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="sms_setting_form_daily" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
             <div id="tabs-weekly-digest-sms">
                <?php
                $sms_setting_form_weekly = $_POST['sms_setting_form_weekly'];
                if ($sms_setting_form_weekly == "self") {
                    $errors1 = array();

                    $edugorilla_sms_body_weekly = $_POST['edugorilla_body_weekly'];

                    if (empty($edugorilla_sms_body_weekly)) $errors1['edugorilla_body_weekly'] = "Empty";
                    if (empty($errors1)) {

                        update_option("edugorilla_sms_setting_weekly", $edugorilla_sms_setting_weekly);
                        $success1 = "sms Settings Saved Successfully.";
                        $sms_setting_options_weekly = get_option('edugorilla_sms_setting_weekly');

                        $edugorilla_sms_body_weekly = stripslashes($sms_setting_options_weekly['body']);
                    }
                } else {
                    $sms_setting_options_weekly = get_option('edugorilla_sms_setting_weekly');

                    $edugorilla_sms_body_weekly = stripslashes($sms_setting_options_weekly['body']);
                }
                if ($success1) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success1; ?></p>
                    </div>
                    <?php
                }
                ?>
                <form method="post">
                    <table class="form-table">

                        <tr>
                            <th>sms body<sup><font color="red">*</font></sup></th>
                            <td>
                                <?php
                                $content = $edugorilla_sms_body_weekly;
                                $editor_id = 'edugorilla_body_weekly';
                                wp_editor($content, $editor_id);
                                ?>
                                <font color="red"><?php echo $errors1['edugorilla_body_weekly']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="sms_setting_form_weekly" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

             <div id="tabs-monthly-digest-sms">
                <?php
                $sms_setting_form_monthly = $_POST['sms_setting_form_monthly'];
                if ($sms_setting_form_monthly == "self") {
                    $errors1 = array();

                    $edugorilla_sms_body_monthly = $_POST['edugorilla_body_monthly'];

                    if (empty($edugorilla_sms_body_monthly)) $errors1['edugorilla_body_monthly'] = "Empty";
                    if (empty($errors1)) {
                        $edugorilla_sms_setting_monthly = array('body' => stripslashes($edugorilla_sms_body_monthly));
                        update_option("sms_setting_form_monthly", $edugorilla_sms_setting_monthly);
                        $success1 = "sms Settings Saved Successfully.";
                        $sms_setting_options_monthly = get_option('sms_setting_form_monthly');

                        $edugorilla_sms_body_monthly = stripslashes($sms_setting_options_monthly['body']);
                    }
                } else {
                    $sms_setting_options_monthly = get_option('sms_setting_form_monthly');

                    $edugorilla_sms_body_monthly = stripslashes($sms_setting_options_monthly['body']);
                }
                if ($success1) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success1; ?></p>
                    </div>
                    <?php
                }
                ?>
                <form method="post">
                    <table class="form-table">

                        <tr>
                            <th>sms body<sup><font color="red">*</font></sup></th>
                            <td>
                                <?php
                                $content = $edugorilla_sms_body_monthly;
                                $editor_id = 'edugorilla_body_monthly';
                                wp_editor($content, $editor_id);
                                ?>
                                <font color="red"><?php echo $errors1['edugorilla_body_monthly']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="sms_setting_form_monthly" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

         <div id="tabs-4">
            <?php
                $sms_setting_form4 = $_POST['sms_setting_form4'];
                if ($sms_setting_form4 == "self") {
                    $errors4 = array();

                    $edugorilla_sms_body4 = $_POST['edugorilla_body4'];


                    if (empty($edugorilla_sms_body4)) $errors4['edugorilla_body4'] = "Empty";

                    if (empty($errors4)) {


                        update_option("edugorilla_sms_setting4", $edugorilla_sms_setting4);
                        $success4 = "sms Settings Saved Successfully.";
                    	$sms_setting_options4 = get_option('edugorilla_sms_setting4');

                    	$edugorilla_sms_body4 = stripslashes($sms_setting_options4['body']);
                    }
                } else {
                    $sms_setting_options4 = get_option('edugorilla_sms_setting4');

                    $edugorilla_sms_body4 = stripslashes($sms_setting_options4['body']);

                }

                if ($success4) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success4; ?></p>
                    </div>
                    <?php
                }
            ?>
                <form method="post">
                    <table class="form-table">

                        <tr>
                            <th>sms body<sup><font color="red">*</font></sup></th>
                            <td>
                               <?php
									$content = $edugorilla_sms_body4;
									$editor_id = 'edugorilla_body4';
									wp_editor( $content, $editor_id );
								?>
                                <font color="red"><?php echo $errors4['edugorilla_body4']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="sms_setting_form4" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
          </div>

        <div id="tabs-5">
            <?php
                $sms_setting_form5 = $_POST['sms_setting_form5'];
                if ($sms_setting_form5 == "self") {
                    $errors5 = array();

                    $edugorilla_sms_body5 = $_POST['edugorilla_body5'];


                    if (empty($edugorilla_sms_body5)) $errors5['edugorilla_body5'] = "Empty";

                    if (empty($errors5)) {
                        $edugorilla_sms_setting5 = array('body' => stripslashes($edugorilla_sms_body5));

                        update_option("edugorilla_sms_setting5", $edugorilla_sms_setting5);
                        $success5 = "sms Settings Saved Successfully.";

                    	 $sms_setting_options5 = get_option('edugorilla_sms_setting5');

                    	 $edugorilla_sms_body5 = stripslashes($sms_setting_options5['body']);
                    }
                } else {
                    $sms_setting_options5 = get_option('edugorilla_sms_setting5');

                    $edugorilla_sms_body5 = stripslashes($sms_setting_options5['body']);

                }

                if ($success5) {
                    ?>
                    <div class="updated notice">
                        <p><?php echo $success5; ?></p>
                    </div>
                    <?php
                }
            ?>
                <form method="post">
                    <table class="form-table">

                        <tr>
                            <th>sms body<sup><font color="red">*</font></sup></th>
                            <td>
                               <?php
									$content = $edugorilla_sms_body5;
									$editor_id = 'edugorilla_body5';
									wp_editor( $content, $editor_id );
								?>
                                <font color="red"><?php echo $errors5['edugorilla_body5']; ?></font>
                            </td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <input type="hidden" name="sms_setting_form5" value="self">
                                <input type="submit" class="button button-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
          </div>
        </div>

    </div>
    <?php
}

?>
