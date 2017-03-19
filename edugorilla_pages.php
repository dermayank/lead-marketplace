<?php

	function show_all_other_shortcodes(){ ?>

		<input type="hidden" id="custom_myBtn"/>

		<!-- The Modal -->
		<div id="custom_myModal" class="custom_modal">

		  <!-- Modal content -->
		  <div class="custom_modal-content">
		    <span class="custom_close" id="close_now" ></span>
		    <div class="pad"><p id="custom_msg"></p></div>
		  </div>

		</div>
	   <div id="wrap">
          <ul class="tab">
	          <li><a href="#" class="tablinks active" onclick="select_page('edugorilla_leads_sh')">Leads</a></li>
	          <li><a href="#" class="tablinks" onclick="select_page('educash_payment_sh')">Buy</a></li>
              <li><a href="#" class="tablinks" onclick="select_page('transaction_history_sh')">Transaction History</a></li>
              <li><a href="#" class="tablinks" onclick="select_page('client_preference_form_sh')">Manage Preferences</a></li>
          </ul>
      </div>

			<div id="#edugorilla_content">
			<div id="edugorilla_leads_sh" class="tabcontent">
	  			 <?php echo do_shortcode('[edugorilla_leads]');  ?>
	  		 </div>
	        <div id="educash_payment_sh" class="tabcontent" style="display: none;">
	          	<?php echo do_shortcode('[educash_payment]');  ?>
	        </div>
            <div id="transaction_history_sh" class="tabcontent" style="display: none;">
	          	<?php echo do_shortcode('[transaction_history]');  ?>
	 	    </div>
	        <div id="client_preference_form_sh" class="tabcontent" style="display: none;">
	          	<?php echo do_shortcode('[client_preference_form]');  ?>
	        </div>

        </div>

<style type="text/css">

ul.tab {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
}

ul.tab li {float: left;}

ul.tab li a {
    display: inline-block;
    color: black;
    text-align: center;
    text-decoration: none;
    transition: 0.3s;
    font-size: 17px;
    padding:22px;
    background: #fff;
    border:1px solid #ccc;
    border-bottom: 0px;
    margin-left: 5px;
}

ul.tab li a:hover {background-color: #ddd;}

ul.tab li a:focus, .active {background-color: #ccc;}

.tabcontent {
    padding: 6px 12px;
    border: 1px solid #ccc;
}

 </style>

<script type="text/javascript">
	var modal = document.getElementById('custom_myModal');

	var span = document.getElementsByClassName("custom_close")[0];

	span.onclick = function() {
	    modal.style.display = "none";
	}

	window.onclick = function(event) {
	    if (event.target == modal) {
	        modal.style.display = "none";
	    }
	}

	function Load(msg)
	{
	    modal.style.display = "block";
	    document.getElementById('custom_msg').innerHTML = msg;
	    document.getElementById('close_now').innerHTML = "&times;";
	}

	function select_page(edugorilla_page) {

    var i, tabcontent, tablinks;

    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    document.getElementById(edugorilla_page).style.display = "block";
    event.currentTarget.className += " active";
	}
</script>

<?php

	}

	add_shortcode('edugorilla_pages_ui','show_all_other_shortcodes');

?>
