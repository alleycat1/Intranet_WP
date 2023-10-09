<style type="text/css">
     .paid_out:hover {
          color: #FF00FF;
     }
     .paid_out {
          cursor: pointer;
     }
</style>
<?php
wp_enqueue_script( 'jqxcore' );
wp_enqueue_script( 'jqxdatetimeinput' );
wp_enqueue_script( 'jqxcalendar' );
wp_enqueue_script( 'jqxtooltip' );
wp_enqueue_script( 'jqxbuttons' );
wp_enqueue_script( 'jqxmenu' );
wp_enqueue_script( 'jqxdata' );
wp_enqueue_script( 'jqxscrollbar' );
wp_enqueue_script( 'jqxgrid' );
wp_enqueue_script( 'jqxgrid.edit' );
wp_enqueue_script( 'jqxgrid.columnsresize', );
wp_enqueue_script( 'jqxgrid.sort' );
wp_enqueue_script( 'jqxgrid.selection' );
wp_enqueue_script( 'jqxlistbox' );
wp_enqueue_script( 'jqxdropdownlist' );
wp_enqueue_script( 'jqxcheckbox' );
wp_enqueue_script( 'jqxnumberinput' );
wp_enqueue_script( 'jqxsplitter' );
wp_enqueue_script( 'jqxdata.export' );
wp_enqueue_script( 'jqxgrid.export' );
wp_enqueue_script( 'jqxcombobox' );
wp_enqueue_script( 'jqxpopover' );

// enqueue jQWidgets CSS files
wp_enqueue_style( 'jqx.base' );
wp_enqueue_style( 'jqx.orange' );

wp_enqueue_script(WTA_NAME . '_wta_paid_out',  WTA_PLUGIN_DIR . '/wta_paid_out.js', array('jquery'), WTA_VAR, true);
wp_enqueue_script(WTA_NAME . '_wta_input',  WTA_PLUGIN_DIR . '/wta_input.js', array('jquery'), WTA_VAR, true);
wp_enqueue_script(WTA_NAME . '_wta_summary',  WTA_PLUGIN_DIR . '/wta_summary.js', array('jquery'), WTA_VAR, true);

global $serverName;
global $connectionInfo;

$conn = sqlsrv_connect($serverName, $connectionInfo);

function getOutLetData($conn, &$outlets)
{
     $sql = "SELECT ID, OutletCode, OutletName FROM Outlets WHERE Deleted <> 1";
     $stmt = sqlsrv_query($conn, $sql);
     if ($stmt === false) {
          return;
     }
     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $outlets[$row['OutletCode']] = ['id'=>$row['ID'], 'name'=>$row['OutletName']];
     }
}

function getTerminalData($conn, &$terminals)
{
     $sql = "SELECT ID, OutletID, TerminalID, Description FROM EPOSTerminals WHERE OutletID NOT IN (SELECT ID FROM Outlets WHERE Deleted=1)";
     $stmt = sqlsrv_query($conn, $sql);
     if ($stmt === false) {
          return;
     }
     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          if(!isset($terminals[$row['OutletID']]))
               $terminals[$row['OutletID']] = array();
          $terminals[$row['OutletID']][$row['TerminalID']] = $row['Description'];
     }
}

$outlets = array();
$terminals = array();
getOutLetData($conn, $outlets);
getTerminalData($conn, $terminals);

$user_outlets = array();
$user = wp_get_current_user();
if(in_array("administrator", $user->roles))
{
     foreach($outlets as $code => $data)
          $user_outlets[$code] = $data;
}
else
{
     foreach($user->roles as $role => $role_data)
     {
          foreach($outlets as $code => $outlet)
               if(strpos($role, $code) === 0)
               {
                    $user_outlets[$code] = $outlet;
                    break;
               }
     }
}
if(count($user_outlets) == 0)
{
     echo "Please sign up first!";
}

if($conn && count($user_outlets) > 0)
{
     sqlsrv_close($conn);

     echo "<script>";
     echo "var user_id = $user->id;";
     echo "var terms = Array();";
     $outlet_obj = array();
     foreach($user_outlets as $code => $outlet)
     {
          $outlet_obj[count($outlet_obj)] = ['label'=>($code." - ".$outlet['name']), 'value'=>$outlet['id']];
     }
     echo "var outlets=" . json_encode($outlet_obj) . ";";

     $terminal_obj = array();
     $first_id = "";
     foreach($user_outlets as $code => $outlet)
     {
          $id =  $outlet['id'] + 0;
          foreach($terminals[$id] as $termId => $term_desc)
          {
               if(!isset($terminal_obj[$id]))
                    $terminal_obj[$id] = array();
               if($first_id == "")
                    $first_id = $id;
               $terminal_obj[$id][count($terminal_obj[$id])] = ['label'=>$term_desc, 'value'=>$termId];
          }
     }
     echo "var terminals=" . json_encode($terminal_obj) . ";";
     echo "var terms;";
     echo "var tabId=0;";
     echo "for(var i in terminals[$first_id]) terms[i] = terminals[$first_id][i];";
     echo "</script>";
?>

<script>
jQuery(document).ready(function ($) {
     initializePaidoutWidgets();
     initializeInputWidgets();
     initializeSummaryWidgets();
});

</script>
<table border=0 hight=500>
     <tr>
          <td style="border:0px;">
               <div id='jqxMenuWidget' style='width: 1250px;'>
                    <div id='jqxMenu' style="visibility: hidden;">
                         <ul style="margin:0px">
                              <li id="btnWTA">Weekly Trading Analysis</li>
                              <li id="btnSummary">Summary</li>
                              <li id="btnCashCounts">Cash Counts</li>
                              <li id="current_cash" style="margin-left:100px;border:0px;">Current Cash on Site: £10.00<li>
                         </ul>
                    </div>
               </div>
               <div style="height:50px">
                    <div style='float: left; margin-top: 10px;' id='jqxOutlet'></div>
                    <div style='float: left; margin-top: 10px; margin-left: 100px;' id='jqxCalendar'></div>
                    <div style='float: left; margin-top: 10px; margin-left: 100px;' id='jqxTerm'></div>
               </div>
               <div id="tabWTA">
                    <div style="border: none;" id='jqxGrid'>
                         <div id="wta_grid" style="width:1250px"></div>
                         <div style="font-size: 12px; font-family: Verdana, Geneva, 'DejaVu Sans', sans-serif; margin-top: 30px;">
                              <div id="cellbegineditevent"></div>
                              <div style="margin-top: 10px;" id="cellendeditevent"></div>
                         </div>
                         <div style="width:1200px">&nbsp;</div>
                    </div>
               </div>
               <div id="tabSummary">
                    <div style="border: none;" id='jqxGrid1'>
                         <div id="summary_grid" style="width:1250px"></div>
                         <div style="font-size: 12px; font-family: Verdana, Geneva, 'DejaVu Sans', sans-serif; margin-top: 30px;">
                              <div id="cellbegineditevent1"></div>
                              <div style="margin-top: 10px;" id="cellendeditevent1"></div>
                         </div>
                         <div style="width:1200px">&nbsp;</div>
                    </div>
               </div>
               <div id="tabCashCount">
               </div>
          </td>
     </tr>
</table>

<div id="popup_paid" hidden>
     <div id="jqxGrid2">
     <table border=0 hight=500>
          <tr>
               <td style="border:0px;">
                    <div style="height:40px">
                         <div style='float: left; margin-top:10px; height:30px' id='ZRef_label'>ZRef:<span>1</span></div>
                    </div>
               </td>
          </tr>
          <tr>
               <td>
                    <div id="paid_grid" style="width:1000px"></div>
                         <div style="font-size: 12px; font-family: Verdana, Geneva, 'DejaVu Sans', sans-serif; margin-top: 30px;">
                              <div id="cellbegineditevent2"></div>
                              <div style="margin-top: 10px;" id="cellendeditevent2"></div>
                         </div>
                         <div style="width:1000px">&nbsp;</div>
                    </div>
               </td>
          </tr>
     </table>
     </div>
</div>
<?php
     wp_enqueue_script(WTA_NAME . '_wta_ajax',  WTA_PLUGIN_DIR . '/wta_ajax.js', array('jquery'), WTA_VAR, true);
}
else if(!$conn)
     print_r(sqlsrv_errors());
?>

