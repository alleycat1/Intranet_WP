<style type="text/css">
     .paid_out:hover {
          color: #FF00FF;
     }
     .paid_out {
          cursor: pointer;
     }
     .my_td{
          padding-top:5px;
          padding-bottom:5px;
          border:0px;
     }
</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
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
wp_enqueue_script( 'jqxwindow' );

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

function getPaidOutTypeData($conn, &$paidOutTypes)
{
     $sql = "SELECT ID, Description FROM SettingsPayoutTypes";
     $stmt = sqlsrv_query($conn, $sql);
     if ($stmt === false) {
          return;
     }
     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $paidOutTypes[$row['ID']] = $row['Description'];
     }
}

function getSupplierData($conn, &$suppliers)
{
     $sql = "SELECT ID, SupplierName FROM Suppliers";
     $stmt = sqlsrv_query($conn, $sql);
     if ($stmt === false) {
          return;
     }
     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $suppliers[$row['ID']] = $row['SupplierName'];
     }
}

$outlets = array();
getOutLetData($conn, $outlets);

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
     $terminals = array();
     getTerminalData($conn, $terminals);

     $paidOutTypes = array();
     getPaidOutTypeData($conn, $paidOutTypes);

     $suppliers = array();
     getSupplierData($conn, $suppliers);

     sqlsrv_close($conn);

     echo "<script>";
     echo "var user_id = $user->id;";
     $outlet_obj = array();
     foreach($user_outlets as $code => $outlet)
     {
          $outlet_obj[count($outlet_obj)] = ['label'=>($code." - ".$outlet['name']), 'value'=>$outlet['id']];
     }
     echo "var outlets=" . json_encode($outlet_obj) . ";";

     $paidOutType_obj = array();
     foreach($paidOutTypes as $id => $type)
     {
          $paidOutType_obj[count($paidOutType_obj)] = ['label'=>$type, 'value'=>$id];
     }
     echo "var paidOutTypes=" . json_encode($paidOutType_obj) . ";";
     echo "var paidOutTypes_org=" . json_encode($paidOutTypes) . ";";

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
     echo "var terms = Array();";
     echo "var tabId=0;";
     echo "for(var i in terminals[$first_id]) terms[i] = terminals[$first_id][i];";

     $supplier_obj = array();
     foreach($suppliers as $id => $name)
     {
          $supplier_obj[count($supplier_obj)] = ['label'=>$name, 'value'=>$id];
     }
     echo "var suppliers=" . json_encode($supplier_obj) . ";";
     echo "var suppliers_org=" . json_encode($suppliers) . ";";

     echo "</script>";
?>

<script>
jQuery(document).ready(function ($) {
     initializePaidoutWidgets();
     initializeInputWidgets();
     initializeSummaryWidgets();
});

function calcPaidOutTotal()
{
     ex_vat =  parseFloat(document.getElementById("ex_vat").value);
     vat_amount =  parseFloat(document.getElementById("vat_amount").value);
     ex_vat = isNaN(ex_vat) ? 0 : ex_vat;
     vat_amount = isNaN(vat_amount) ? 0 : vat_amount;
     total = ex_vat + vat_amount;
     document.getElementById("paidout_total_amount").value = total;
}
</script>
<table border=0 hight=500>
     <tr>
          <td style="border:0px;">
               <div id='jqxMenuWidget' style='width: 1250px;'>
                    <div id='jqxMenu' style="visibility: hidden;">
                         <ul style="margin:0px">
                              <li id="btnWTA">Weekly Trading Analysis</li>
                              <li id="btnSummary">Summary</li>
                              <li id="btnPaidOuts">Paid Outs</li>
                              <li id="btnOtherIncome">Other Income</li>
                              <li id="btnCashCounts">Cash Counts</li>
                              <li id="btnBankingAdjustments">Banking & Adjustments</li>
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
     <div>ADD PAID OUT DATA</div>
     <div style="overflow: hidden;">
          <table style="width:1080px; border-bottom:0px; height:30px; margin:0px">
               <tr>
                    <td colspan=2 style="border-bottom:0px;">
                         <div id="paid_grid" style="width:1080px; height:250px" ></div>
                              <div style="font-size: 12px; font-family: Verdana, Geneva, 'DejaVu Sans', sans-serif;">
                              </div>
                         </div>
                    </td>
               </tr>
               <tr style="height:30px; margin:0px">
                    <td border=0 width=50% style="border-bottom:0px; height:30px; margin:0px">
                         <button style="padding:4px 16px;" id="btn_add">&nbsp;+&nbsp;</button> 
                    </td>
                    <td border=0 width=50% style="border-bottom:0px; text-align:right; height:30px; margin:0px">
                         <button style="padding:4px 16px;" id="btn_close">CLOSE</button> 
                    </td>
               </tr>
          </table>
     </div>
</div>

<div id="popupEdit" hidden>
     <div>ADD PAID OUT DATA</div>
     <div style="overflow: hidden;">
          <table width=100%>
               <tr>
                    <td class="my_td" align="right">PAYOUT TYPE:</td>
                    <td class="my_td" align="left"><div style='float: left; margin-top: 10px;' id='jqxPaidOutType'></div></td>
               </tr>
               <tr>
                    <td class="my_td" align="right">SUPPLIER:</td>
                    <td class="my_td" align="left"><div style='float: left;' id='jqxSupplier'></div></td>
               </tr>
               <tr>
                    <td class="my_td" align="right">EX VAT(£):</td>
                    <td class="my_td" align="left"><input id="ex_vat" style="height:30px" onchange="javascript:calcPaidOutTotal()" required/></td>
               </tr>
               <tr>
                    <td class="my_td" align="right">VAT AMOUNT(£):</td>
                    <td class="my_td" align="left"><input id="vat_amount" style="height:30px" onchange="javascript:calcPaidOutTotal()" required/></td>
               </tr>
               <tr>
                    <td class="my_td" align="right">TOTAL(£):</td>
                    <td class="my_td" align="left"><input id="paidout_total_amount" style="height:30px" readonly/></td>
               </tr>
               <tr>
                    <td class="my_td" align="right">REFERENCE:</td>
                    <td class="my_td" align="left"><input id="reference" style="height:30px" /></td>
               </tr>
               <tr>
                    <td class="my_td" align="right">DESCRIPTION:</td>
                    <td class="my_td" align="left" ><input id="description" style="height:30px" /></td>
               </tr>
               <tr>
                    <td class="my_td" align="left"></td>
                    <td class="my_td" align="right">
                         <input id="Cancel" type="button" value="Cancel" />
                         <input type="button" id="Save" value="  Save  " />
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

