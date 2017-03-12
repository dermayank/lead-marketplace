<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('css/lead-market-place-frontend.css', __FILE__); ?>">
<?php
$wploadPath = explode('/wp-content/', dirname(__FILE__));
include_once(str_replace('wp-content/' , '', $wploadPath[0] . '/wp-load.php'));
$css_path = plugins_url('css/lead-market-place-frontend.css', __FILE__);

function custom_dialog_style()
{
    wp_enqueue_style('custom_css_style',$css_path);
}
add_action('wp_enqueue_style', 'custom_dialog_style');

function custom_dialog($msg){ ?>
<body onload= "Load();">
<input type="hidden" id="custom_myBtn"/>

<!-- The Modal -->
<div id="custom_myModal" class="custom_modal">

  <!-- Modal content -->
  <div class="custom_modal-content">
    <span class="custom_close">&times;</span>
    <div class="pad"><p><?php echo $msg; ?></p></div>
  </div>

</div>
</body>
<script src="<?php echo plugins_url('js/lead-portal.js', __FILE__); ?>"></script>
<?php
}
?>
