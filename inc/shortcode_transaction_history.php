<?php

  add_shortcode('transaction_history','transaction_history');
  function transaction_history($atts,$content = null)
  {
     if(is_user_logged_in()){
       global $wpdb;
       $url = plugins_url('',__FILE__);
       $url = str_replace('inc','frontend/css/lead-market-place-frontend.css',$url);
       function script1()
       {
       	 wp_enqueue_style('select2-css', $url);
       }
       add_action('admin_enqueue_scripts', 'script1', 2000);

       $current_educash = 0;

       $current_user_id = get_current_user_id();
       $table_name1 = $wpdb->prefix . 'edugorilla_lead_client_mapping';
       $sql = "SELECT * FROM $table_name1 WHERE client_id = $current_user_id order by date_time";
       $totalrows = $wpdb->get_results($sql);

       $table_name2 = $wpdb->prefix . 'edugorilla_lead_educash_transactions';
       $sql = "SELECT * FROM $table_name2 WHERE client_id = $current_user_id";
       $total_cash = $wpdb->get_results($sql);
       $i = 0;
       if(count($total_cash)>0)
       {
         foreach ($total_cash as $cash)
         {
            if($cash->transaction > 0){
              $date = $cash->time;
              $consumption[$i]['date']= $date;
              $consumption[$i]['spent'] = $cash->transaction;
              $consumption[$i]['val'] = 0;
              $i=$i+1;
              $current_educash = $current_educash + ($cash->transaction);}
         }
       }

       $current_educash = $current_educash - count($totalrows);
       if($current_educash<0)
          $current_educash = 0;

      ?>

        <link rel="stylesheet" href="<?php //echo $url; ?>">

  <div class = "timeline_class">
    <section class="intro_class">
    <div class="container_class">
      <h1 class="heading_class">Transaction History &darr;</h1>
      <h3 class="heading_class">(Your current educash is <?php echo $current_educash; ?> )</h3>
    </div>
  </section>

  <section class="timeline">
    <ul>
      <?php

        if(count($totalrows)>0){
          foreach($totalrows as $row){
            $date = $row->date_time;
            $new_time = explode(" ",$row->date_time);
            $get_date = $new_time[0];
            $get_time = $new_time[1];

            $consumption[$i]['date'] = $date;
            $consumption[$i]['spent'] = 1;
            $consumption[$i]['val'] = 1;
            $i=$i+1;
          }
        }

        function cmp($a, $b){
            return strnatcmp($a["date"], $b["date"]);
        }
        if(sizeof($consumption)>0){
        usort($consumption,"cmp");

        foreach ($consumption as $key => $value) {
          $new_format = explode(" ",$value['date']);
          $date = $new_format[0];
          $time = $new_format[1];
          if($value['val']==1){
      ?>
                <li>
                  <div>
                    <time><tl class="tl">You</tl><tl class="tl">spend</tl><tl class="tl">1</tl><tl class="tl">educash<tl class="tl">on</tl><tl class="tl"><?php echo $date;?></tl><tl class="tl">at</tl><tl class="tl"><?php echo $time;?></tl></time>
                  </div>
        <?php
        }
        else{
          ?>
                    <li>
                      <div>
                        <time><tl class="tl">You</tl><tl class="tl">were</tl><tl class="tl">allocated</tl><tl class="tl"><?php echo $value['spent'];?></tl><tl class="tl">educash<tl class="tl">on</tl><tl class="tl"><?php echo $date;?></tl><tl class="tl">at</tl><tl class="tl"><?php echo $time;?></tl></time>
                      </div>
            <?php
          }
        }
      }
    ?>
    </ul>
    </section>
    </div>
<?php }
    else{
        $redirecting_url = home_url("/login");
        echo '<script>location.href="'.$redirecting_url.'";</script>';
    }
  } ?>
