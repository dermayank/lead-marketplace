<?php
  session_start();
  add_shortcode('educash_payment','educash_payment');
  function educash_payment($atts,$content = null)
  {
      if(is_user_logged_in())
      {
        global $current_user;
        get_currentuserinfo();
        $out = get_option("current_rate");
        $out1 = get_option("karma_rate");
        $conversion=0;
        $conversion=$out['rate'];
        $conversion_karmas = 0;
        $conversion_karmas = $out1['rate'];
        $_SESSION['stop_reload'] =1;
        $angpath = plugin_dir_url(__FILE__) ;
        $angpath = str_replace('inc/','frontend/js/lead-portal.js',$angpath);

        function wptuts_scripts_basic()
        {
        wp_register_script( 'custom-script', $angpath );
        wp_enqueue_script( 'custom-script' );
        }
        add_action( 'wp_enqueue_scripts', 'wptuts_scripts_basic' );

        $credentials = get_option("payumoney_parameters");
        $background = plugins_url('payumoney/background.png',__FILE__);
        $payumoney_page = plugins_url('payumoney/PayUMoney_form.php',__FILE__);
        $redirect_url = plugins_url('payumoney/success.php',__FILE__);
        $payumoney_logo = plugins_url('payumoney/PayUMoney_logo.png',__FILE__);
        $karmapay = plugins_url('/karmapay.php',__FILE__);
        ?>

        <form name="tranaction_form" method="post" action="">
          <div class="pay_card"  style="background-image: url("<?php echo $background;?>");">
            <div class ="pay_card2">
              <input id="rspay" value="<?php echo $conversion; ?>" type="hidden"/>
              <input id="karmapay" value="<?php echo $conversion_karmas;?>"  type="hidden"/>
              <input name="userid" id="userid" value="<?php echo $current_user->id; ?>" type="hidden" />
              <input name="allocation_page" id="allocation_page" value="<?php echo $allocation_page; ?>" type="hidden" />
              <input name="email" id="email" value="<?php echo $current_user->user_email; ?>" type="hidden" />
              <input name="firstname" id="firstname" value="<?php echo $current_user->user_firstname; ?>" type="hidden" />
              <input name="lastname" id="lastname" value="<?php echo $current_user->user_lastname; ?>" type="hidden" />
              <input name="txnid" id="txnid" value="<?php echo $credentials['password'];  ?>" type="hidden"/>
              <input name="rate" id="rate" value="<?php echo $conversion; ?>" type="hidden"/>
              <input name="saltid" id="saltid" value="<?php echo $credentials['user_id']; ?>" type="hidden"/>
              <input name="furl" id="furl" value="<?php echo $redirect_url; ?>" type="hidden"/>

              <b><span class="pay_heading1">Enter number of educash you want</span></b></br>
              <div class="pay_inputbox1"><input type="number" name="amount" placeholder="0" style="font-size: 20pt; text-align: center; font-weight: bold;" maxlength="10" onkeyup="showHint(this.value)" required/></div>
              <div class="pay_output_amount"><b>Total = <span id="rspayment">0</span> Rs. </b></div>
              <b><h3><p class="conversion">*(1 educash is equal to <?php echo $conversion; ?> Rs)</p></h3></b></br>
              <b><span class="pay_heading1">Click here to pay using PayUMoney </span></b></br></br>
              <button onClick=this.form.action="<?php echo $payumoney_page;?>" class="pay_button1"><img src="<?php echo $payumoney_logo;?>"></img></button>
          </div>
       </div>
       <div class="pay_card" >
         <div class ="pay_card2">

             <input name="conversion_karmas" id="conversion_karmas" value="<?php echo $conversion_karmas; ?>" type="hidden"/>
             <div class="pay_output_amount"><b>Total =  <span id="karmapayment">0</span> karmas. </b></div>
             <b><h3><p class="conversion">*(1 educash is equal to <?php echo $conversion_karmas; ?> karmas)</p></h3></b></br>
             <b><span class="pay_heading1">Click here to buy educash using Karmas </span></b></br></br>
             <button onClick=this.form.action="<?php echo $karmapay;?>" class="pay_button1" style="color:Grey">Karma</button>
          </div>
        </div>
     </form>

      <?php
      }
      else{
        echo "please login to view this page";
      }
  }
?>
