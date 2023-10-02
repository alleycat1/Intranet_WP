<?php
wp_enqueue_script( 'jqxcore' );
wp_enqueue_script( 'jqxdatetimeinput' );
wp_enqueue_script( 'jqxcalendar' );
wp_enqueue_script( 'jqxtooltip' );
wp_enqueue_script( 'jqxbuttons' );
wp_enqueue_script( 'jqxmenu' );
wp_enqueue_script( 'globalize' );

wp_enqueue_script(WTA_NAME . '_wta_ajax',  WTA_PLUGIN_DIR . '/wta_ajax.js', array('jquery'), WTA_VAR, true);

// enqueue jQWidgets CSS files
wp_enqueue_style( 'jqx.base' );
wp_enqueue_style( 'jqx.orange' );


global $serverName;
global $connectionInfo;

$conn = sqlsrv_connect($serverName, $connectionInfo);

if($conn)
{
?>

<script>
function initializeWidgets() {
    jQuery("#jqxMenu").jqxMenu({ width: '200', mode: 'vertical'});
    jQuery("#jqxMenu").css('visibility', 'visible');
}
jQuery(document).ready(function ($) {
     initializeWidgets();
});
</script>
<table border=0>
     <tr>
          <td>
               <div id='jqxMenuWidget' style='width: 200px;'>
                    <div id='jqxMenu' style="visibility: hidden;">
                         <ul style="margin:0px">
                              <li>Weekly Trading Analysis</li>
                              <li>Paid out</li>
                              <li>Cash Counts</li>
                              <li>Account Details</li>
                              <li>Account Summary</li>
                              <li>Order History</li>
                              <li>My Documents</li>
                              <li>Test Menu
                              <ul>
                                   <li onclick="my_action_javascript()"><a href="#">Product Development</a></li>
                                   <li><a href="#">Delivery</a></li>
                                   <li><a href="#">Shop Online</a></li>
                                   <li><a href="#">Support</a></li>
                                   <li><a href="#">Training &amp; Consulting</a></li>
                              </ul>
                              </li>
                         </ul>
                    </div>
               </div>
          </td>
          <td>
               asdf
          </td>
     </tr>
</table>

<?php
}
else
     print_r(sqlsrv_errors());
?>

