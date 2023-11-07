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
wp_enqueue_script( 'jqxinput' );

// enqueue jQWidgets CSS files
wp_enqueue_style( 'jqx.base' );
wp_enqueue_style( 'jqx.orange' );

wp_enqueue_script(WTA_NAME . '_wta_paid_out',  WTA_PLUGIN_DIR . '/wta_paid_out.js', array('jquery'), WTA_VAR, true);
wp_enqueue_script(WTA_NAME . '_wta_paid_out_view',  WTA_PLUGIN_DIR . '/wta_paid_out_view.js', array('jquery'), WTA_VAR, true);
wp_enqueue_script(WTA_NAME . '_wta_other_income',  WTA_PLUGIN_DIR . '/wta_other_income.js', array('jquery'), WTA_VAR, true);
wp_enqueue_script(WTA_NAME . '_wta_cash_counts',  WTA_PLUGIN_DIR . '/wta_cash_counts.js', array('jquery'), WTA_VAR, true);
wp_enqueue_script(WTA_NAME . '_wta_banking',  WTA_PLUGIN_DIR . '/wta_banking.js', array('jquery'), WTA_VAR, true);
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

function getIncomeInfo($conn, &$incomes)
{
     $sql = "SELECT OutletID, IncomeID, Description FROM OutletsIncome t LEFT JOIN SettingsIncomeStreams s ON s.ID = t.IncomeID WHERE OutletID NOT IN (SELECT ID FROM Outlets WHERE Deleted=1) ORDER BY OutLetID";
     $stmt = sqlsrv_query($conn, $sql);
     if ($stmt === false) {
          return;
     }
     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          if(!isset($incomes[$row['OutletID']]))
               $incomes[$row['OutletID']] = array();
          $incomes[$row['OutletID']][$row['IncomeID']] = $row['Description'];
     }
}

function getLocationInfo($conn, &$locations)
{
     $sql = "SELECT OutletID, t.ID, Description FROM OutletsCashLocations t LEFT JOIN Outlets s ON s.ID = t.OutletID WHERE OutletID NOT IN (SELECT ID FROM Outlets WHERE Deleted=1) ORDER BY OutLetID";
     $stmt = sqlsrv_query($conn, $sql);
     if ($stmt === false) {
          return;
     }
     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          if(!isset($locations[$row['OutletID']]))
               $locations[$row['OutletID']] = array();
          $locations[$row['OutletID']][$row['ID']] = $row['Description'];
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

function getBankingTypeData($conn, &$bankingTypes)
{
     $sql = "SELECT ID, Description FROM SettingsAdjustmentTypes";
     $stmt = sqlsrv_query($conn, $sql);
     if ($stmt === false) {
          return;
     }
     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $bankingTypes[$row['ID']] = $row['Description'];
     }
}

/*
function getSupplierData($conn, &$suppliers)
{
     $sql = "SELECT OutletID, SupplierID, SupplierName FROM OutletsSuppliers LEFT JOIN Suppliers ON Suppliers.ID=SupplierID WHERE OutletID NOT IN (SELECT ID FROM Outlets WHERE Deleted=1)";
     $stmt = sqlsrv_query($conn, $sql);
     if ($stmt === false) {
          return;
     }
     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          if(!isset($suppliers[$row['OutletID']]))
               $suppliers[$row['OutletID']] = array();
          $suppliers[$row['OutletID']][$row['SupplierID']] = $row['SupplierName'];
     }
}
*/

$outlets = array();
getOutLetData($conn, $outlets);

$user_outlets = array();
$user = wp_get_current_user();
$is_admin = false;
if(in_array("administrator", $user->roles))
{
     $is_admin = true;
     foreach($outlets as $code => $data)
          $user_outlets[$code] = $data;
}
else
{
     foreach($user->roles as $role => $role_data)
     {
          foreach($outlets as $code => $outlet)
               if(strpos($role_data, $code) === 0)
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

     $bankingTypes = array();
     getBankingTypeData($conn, $bankingTypes);

     //$suppliers = array();
     //getSupplierData($conn, $suppliers);

     $incomes = array();
     getIncomeInfo($conn, $incomes);

     $locations = array();
     getLocationInfo($conn, $locations);

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

     $bankingType_obj = array();
     foreach($bankingTypes as $id => $type)
     {
          $bankingType_obj[count($bankingType_obj)] = ['label'=>$type, 'value'=>$id];
     }
     echo "var bankingTypes=" . json_encode($bankingType_obj) . ";";
     if($is_admin)
          echo "var adm=1;";
     else
          echo "var adm=0;";

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

     $income_obj = array();
     $first_id = "";
     foreach($user_outlets as $code => $outlet)
     {
          $id =  $outlet['id'] + 0;
          foreach($incomes[$id] as $incomeId => $income_desc)
          {
               if(!isset($income_obj[$id]))
                    $income_obj[$id] = array();
               if($first_id == "")
                    $first_id = $id;
               $income_obj[$id][count($income_obj[$id])] = ['label'=>$income_desc, 'value'=>$incomeId];
          }
     }
     echo "var incomeInfo=" . json_encode($income_obj) . ";";
     echo "var incomes = Array();";
     echo "for(var i in incomeInfo[$first_id]) incomes[i] = incomeInfo[$first_id][i];";

     $location_obj = array();
     $first_id = "";
     foreach($user_outlets as $code => $outlet)
     {
          $id =  $outlet['id'] + 0;
          foreach($locations[$id] as $locationId => $location_desc)
          {
               if(!isset($location_obj[$id]))
                    $location_obj[$id] = array();
               if($first_id == "")
                    $first_id = $id;
               $location_obj[$id][count($location_obj[$id])] = ['label'=>$location_desc, 'value'=>$locationId];
          }
     }
     echo "var locationInfo=" . json_encode($location_obj) . ";";
     echo "var locations = Array();";
     echo "for(var i in locationInfo[$first_id]) locations[i] = locationInfo[$first_id][i];";

     /*
     $supplier_obj = array();
     $first_id = "";
     foreach($user_outlets as $code => $outlet)
     {
          $id =  $outlet['id'] + 0;
          foreach($suppliers[$id] as $supplierId => $suppler_desc)
          {
               if(!isset($supplier_obj[$id]))
                    $supplier_obj[$id] = array();
               if($first_id == "")
                    $first_id = $id;
               $supplier_obj[$id][count($supplier_obj[$id])] = ['label'=>$suppler_desc, 'value'=>$supplierId];
          }
     }
     echo "var supplierInfo=" . json_encode($supplier_obj) . ";";
     echo "var suppliers = Array();";
     echo "for(var i in supplierInfo[$first_id]) suppliers[i] = supplierInfo[$first_id][i];";
     */

     echo "</script>";
?>

<script>
jQuery(document).ready(function ($) {
     initializeInputWidgets();
     initializePaidoutWidgets();
     initializeIncomeWidgets();
     initializeCashCountsWidgets();
     initializeSummaryWidgets();
     initializePaidoutViewWidgets();
     initializeBankingWidgets();
     setInterval(getCashOnSite, 10*1000);
     getCashOnSite();
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
          <td style="border:0px; width:1250px">
               <div id='jqxMenuWidget' style='width: 1250px;'>
                    <span style="border:0px; width:1250px; text-align:center"><H1>Weekly Trading Analysis</H1></span>
                    <div id='jqxMenu' style="visibility: hidden;">
                         <ul style="margin:0px">
                              <li id="btnWTA">Weekly Trading Analysis</li>
                              <li id="btnSummary">Summary</li>
                              <li id="btnPaidOuts">Paid Outs</li>
                              <li id="btnOtherIncome">Other Income</li>
                              <li id="btnCashCounts">Cash Counts</li>
                              <li id="btnBankingAdjustments">Banking & Adjustments</li>
                              <li id="current_values" style="margin-left:100px;border:0px; margin-top:0px; padding-top:5px">
                                   <span>
                                   <table border=0 style="width:200px; height:40px">
                                        <tr><td style='padding:0px; text-align:right'>Current Cash on Site:&nbsp;</td><td style='padding:0px; text-align:right'><span id="current_cash">£ 0.00</span></td></tr>
                                        <tr><td style='padding:0px; text-align:right; border:0px'>Expected Banking:&nbsp;</td><td style='padding:0px; border:0px; text-align:right'><span id="expected_banking">£ 0.00</span></td></tr>
                                   </table>
                                   </span>
                              <li>
                         </ul>
                    </div>
               </div>
               <div style="height:50px">
                    <div style='float: left; margin-top: 10px;' id='jqxOutlet'></div>
                    <div style='float: left; margin-top: 10px; margin-left: 100px;' id='jqxCalendar'></div>
                    <div style='float: left; margin-top: 10px; margin-left: 100px;' id='jqxTerm'></div>
                    <div style='float: left; margin-top: 10px; margin-left: 100px;' id='jqxIncomeType'></div>
                    <div style='float: left; margin-top: 10px; margin-left: 100px;' id='jqxCashSubmitTime'></div>
                    <div style='float: left; margin-top: 10px; margin-left: 100px;' id='jqxBankingType'></div>
               </div>
               <div id="tabWTA">
                    <div style="border: none;" id='jqxGrid'>
                         <div id="wta_grid" style="width:1250px"></div>
                    </div>
               </div>
               <div id="tabSummary" hidden>
                    <div style="border: none;" id='jqxGrid1'>
                         <div id="summary_grid" style="width:1250px"></div>
                    </div>
               </div>
               <div id="tabPaidOuts" hidden>
                    <div style="border: none;" id='jqxGrid2'>
                         <div id="paidout_grid1" style="width:1250px"></div><br>
                         <div id="paidout_grid2" style="width:1250px"></div>
                    </div>
               </div>
               <div id="tabOtherIncome" hidden>
                    <div style="border: none;" id='jqxGrid3'>
                         <div id="income_grid" style="width:1250px"></div>
                         <button style="padding:4px 16px;" id="income_add">&nbsp;+&nbsp;</button> 
                    </div>
               </div>
               <div id="tabCashCounts" hidden>
                    <div style="border: none;" id='jqxGrid4'>
                         <div id="cash_counts_grid" style="width:1250px"></div>
                         <button style="padding:4px 16px;" id="cash_counts_submit">&nbsp;SUBMIT&nbsp;</button> 
                         &nbsp;&nbsp;&nbsp;
                         <button style="padding:4px 16px;" id="cash_counts_refresh">&nbsp;REFRESH&nbsp;</button> 
                    </div>
               </div>
               <div id="tabBanking" hidden>
                    <div id="banking_grid" style="width:1250px"></div>
                    <button style="padding:4px 16px;" id="banking_add">TRANSFER TO BANK</button> 
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

<div id="popupPaidoutInputEdit" hidden>
     <div>ADD PAID OUT DATA</div>
     <div style="overflow: hidden;">
          <table width=100%>
               <tr>
                    <td class="my_td" align="right">PAYOUT TYPE:</td>
                    <td class="my_td" align="left"><div style='float: left; margin-top: 10px;' id='jqxPaidOutType'></div></td>
               </tr>
               <!--
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
               -->
               <tr>
                    <td class="my_td" align="right">TOTAL(£):</td>
                    <td class="my_td" align="left"><input id="paidout_total_amount" style="height:30px"/></td>
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
                         <input id="PaidoutCancel" type="button" value="Cancel" />
                         <input type="button" id="PaidoutSave" value="  Save  " />
                    </td>
               </tr>
          </table>
     </div>
</div>

<div id="popupIncomeEdit" hidden>
     <div>ADD INCOME DATA</div>
     <div style="overflow: hidden;">
          <table width=100%>
               <tr>
                    <td class="my_td" align="right">DATE:</td>
                    <td class="my_td" align="left"><div style='float: left; margin-top: 10px;' id='jqxIncomeDate'></div></td>
               </tr>
               <tr>
                    <td class="my_td" align="right">AMOUNT(£):</td>
                    <td class="my_td" align="left"><input id="income_amount" style="height:30px" required/></td>
               </tr>
               <tr>
                    <td class="my_td" align="right">COMMENTS:</td>
                    <td class="my_td" align="left" ><input id="income_comment" style="height:30px" /></td>
               </tr>
               <tr>
                    <td class="my_td" align="left"></td>
                    <td class="my_td" align="right">
                         <input id="IncomeCancel" type="button" value="Cancel" />
                         <input type="button" id="IncomeSave" value="  Save  " />
                    </td>
               </tr>
          </table>
     </div>
</div>

<div id="popupBankingEdit" hidden>
     <div>ADD BANKING DATA</div>
     <div style="overflow: hidden;">
          <table width=100%>
               <tr>
                    <td class="my_td" align="right">DATE:</td>
                    <td class="my_td" align="left"><div style='float: left; margin-top: 10px;' id='jqxBankingDate'></div></td>
               </tr>
               <tr>
                    <td class="my_td" align="right">AMOUNT(£):</td>
                    <td class="my_td" align="left"><input id="banking_amount" style="height:30px" required/></td>
               </tr>
               <tr>
                    <td class="my_td" align="right">COMMENTS:</td>
                    <td class="my_td" align="left" ><input id="banking_comment" style="height:30px" /></td>
               </tr>
               <tr>
                    <td class="my_td" align="left"></td>
                    <td class="my_td" align="right">
                         <input id="BankingCancel" type="button" value="Cancel" />
                         <input type="button" id="BankingSave" value="  Save  " />
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

