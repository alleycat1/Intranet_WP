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

// enqueue jQWidgets CSS files
wp_enqueue_style( 'jqx.base' );
wp_enqueue_style( 'jqx.orange' );

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
     echo "var wta_data = new Array();";
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
     echo "for(var i in terminals[$first_id]) terms[i] = terminals[$first_id][i];";
     echo "</script>";
?>

<script>
function initializeWidgets() {
     jQuery("#jqxMenu").jqxMenu({ width: '1250', height: '50', mode: 'horizontal'});
     jQuery("#jqxMenu").css('visibility', 'visible');

     jQuery("#jqxOutlet").jqxDropDownList({ source: outlets, selectedIndex: 0, width: '200', height: '30px'});
     jQuery("#jqxOutlet").on('select', function (e) {
          terms.length = 0;
          for(var i in terminals[outlets[e.args.index].value])
               terms[i] = terminals[outlets[e.args.index].value][i];
          jQuery("#jqxTerm").jqxDropDownList('clear');
          jQuery("#jqxTerm").jqxDropDownList({ source: terms, selectedIndex: 0, width: '200', height: '30px'});
     });

     jQuery("#jqxTerm").jqxDropDownList({ source: terms, selectedIndex: 0, width: '200', height: '30px'});
     jQuery("#jqxTerm").on('select', function (e) {
          jQuery('#wta_grid').jqxGrid('endcelledit');
          outlet = jQuery("#jqxOutlet").val();
          term = terms[e.args.index].value;
          date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
          jQuery('#wta_grid').jqxGrid({ disabled: true});
          get_wta_data(outlet, term, date);
     });

     jQuery("#jqxCalendar").jqxDateTimeInput({ animationType: 'fade', width: '150px', height: '30px', animationType: 'fade', dropDownHorizontalAlignment: 'right'});
     jQuery("#jqxCalendar").jqxDateTimeInput({ animationType: 'slide' });
     jQuery("#jqxCalendar").on('change', function (e) {
          jQuery('#wta_grid').jqxGrid('endcelledit');
          outlet = jQuery("#jqxOutlet").val();
          term = jQuery("#jqxTerm").val();
          date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
          jQuery('#wta_grid').jqxGrid({ disabled: true});
          get_wta_data(outlet, term, date);
     });


     var options = { style: 'currency', currency: 'GBP' };
     var source =
     {
          localdata: wta_data,
          datatype: "array",
          updaterow: function (rowid, rowdata, commit) {
               outlet = jQuery("#jqxOutlet").val();
               term = jQuery("#jqxTerm").val();
               date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
               jQuery('#wta_grid').jqxGrid({ disabled: true});
               set_wta_data(outlet, term, wta_data[rowid].id, wta_data[rowid].date, rowdata, user_id);
          },
          datafields:
          [
               { name: 'day', type: 'string' },
               { name: 'date', type: 'date', format: 'dd/MM/yyyy' },
               { name: 'zref', type: 'number' },
               { name: 'sale_ex', type: 'number' },
               { name: 'vat', type: 'number' },
               { name: 'sale_inc', type: 'number' },
               { name: 'disrepancy', type: 'number' },
               { name: 'paid_out_from_till', type: 'number' },
               { name: 'paid_out_from_safe', type: 'number' },
               { name: 'account_sales', type: 'number' },
               { name: 'account_receipts', type: 'number' },
               { name: 'cards_banking', type: 'number' },
               { name: 'cash_retained', type: 'number' }
          ]
     };
     var dataAdapter = new jQuery.jqx.dataAdapter(source);

     var cellbeginedit = function (row, datafield, columntype, value) {
          if (row == 7) return false;
     }

     var cellsrenderer = function (row, column, value, defaultHtml) {
          if (value < 0) {
               var element = jQuery(defaultHtml);
               element.css('color', '#880000');
               return element[0].outerHTML;
          }
          else if (value == 0) {
               var element = jQuery(defaultHtml);
               element.css('color', '#000000');
               return element[0].outerHTML;
          }
          else{
               var element = jQuery(defaultHtml);
               element.css('color', '#008800');
               return element[0].outerHTML;
          }
          return defaultHtml;
     }

     var cellsrenderer_zref = function (row, column, value, defaultHtml) {
          if (value > 0 && row < 7) {
               var element = jQuery(defaultHtml);
               value = value.toString().padStart(3, '0');
               element[0].innerHTML = value;
               return element[0].outerHTML;
          }
          return defaultHtml;
     }

     jQuery("#wta_grid").jqxGrid(
     {
          width: '1250',
          height: '300',
          source: dataAdapter,
          columnsresize: true,
          editable: true,
          rowsheight: 30,
          columnsheight: 25,
          selectionmode: 'singlerow',
          columns: [
               { text: 'DAY', datafield: 'day', columntype: 'textbox', width: 70, align: 'center', cellsalign: 'left', pinned: true, editable:false },
               { text: 'DATE', datafield: 'date' , columntype: 'textbox', cellsformat: 'dd/MM/yyyy', width: 90, align: 'center', pinned: true, editable:false },
               { text: 'Z REF', datafield: 'zref', columntype: 'integerinput', width: 90, cellsalign: 'right', cellsformat: 'n3', cellsrenderer:cellsrenderer_zref },
               { text: 'EX VAT', columngroup: 'sales', datafield: 'sale_ex', cellsformat: 'c2', columntype: 'numberinput', align: 'right', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
               { text: 'VAT', columngroup: 'sales', datafield: 'vat', cellsformat: 'c2', columntype: 'numberinput', align: 'right', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
               { text: 'INC VAT', columngroup: 'sales', datafield: 'sale_inc', cellsformat: 'c2', columntype: 'numberinput', align: 'right', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit, editable:false },
               { text: 'DISREPANCY', datafield: 'disrepancy', cellsformat: 'c2', align: 'right', cellsalign: 'right', width: 100, cellsrenderer: cellsrenderer, cellbeginedit: cellbeginedit },
               { text: 'FROM TILL', columngroup: 'paid_out', datafield: 'paid_out_from_till', cellsformat: 'c2', columntype: 'numberinput', align: 'right', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit, editable:false },
               { text: 'FROM SAFE', columngroup: 'paid_out', datafield: 'paid_out_from_safe', cellsformat: 'c2', columntype: 'numberinput', align: 'right', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit, editable:false },
               { text: 'SALES', columngroup: 'account', datafield: 'account_sales', cellsformat: 'c2', columntype: 'numberinput', align: 'right', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
               { text: 'RECEIPTS', columngroup: 'account', datafield: 'account_receipts', cellsformat: 'c2', columntype: 'numberinput', align: 'right', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
               { text: 'BANKING', columngroup: 'cards', datafield: 'cards_banking', cellsformat: 'c2', columntype: 'numberinput', align: 'right', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
               { text: 'RETAINED', columngroup: 'cash', datafield: 'cash_retained', cellsformat: 'c2', align: 'right', cellsalign: 'right', width: 100, editable:false }
          ],
          columngroups:[
               { text: 'SALES', align: 'center', name: 'sales' },
               { text: 'PAID OUT',align: 'center', name: 'paid_out' },
               { text: 'ACCOUNT', align: 'center', name: 'account' },
               { text: 'CARDS', align: 'center', name: 'cards' },
               { text: 'CASH', align: 'center', name: 'cash' }
          ]
     });

     jQuery("#btnWTA").on('click', function (e) {
          //alert("asdf");
     });
     jQuery("#btnSummary").on('click', function (e) {
          //alert("qwer");
     });
     jQuery("#btnCashCounts").on('click', function (e) {
          //alert("zxcv");
     });
}
jQuery(document).ready(function ($) {
     initializeWidgets();
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
               <div id="tabWTA">
                    <div style='float: left; margin-top: 10px;' id='jqxOutlet'></div>
                    <div style='float: left; margin-top: 10px; margin-left: 100px;' id='jqxCalendar'></div>
                    <div style='float: left; margin-top: 10px; margin-left: 100px;' id='jqxTerm'></div>
                    <br>
                    <br>
                    <div style="border: none;" id='jqxGrid'>
                         <div id="wta_grid" style="width:1250px"></div>
                         <div style="font-size: 12px; font-family: Verdana, Geneva, 'DejaVu Sans', sans-serif; margin-top: 30px;">
                              <div id="cellbegineditevent"></div>
                              <div style="margin-top: 10px;" id="cellendeditevent"></div>
                         </div>
                         <div style="width:1200px">&nbsp;</div>
                    </div>
               </div>
               <div id="tabSummary">>
                    <div style="border: none;" id='jqxSumarry'>
                         <div id="summary_grid" style="width:1250px"></div>
                         <div style="font-size: 12px; font-family: Verdana, Geneva, 'DejaVu Sans', sans-serif; margin-top: 30px;">
                              <div id="cellbegineditevent"></div>
                              <div style="margin-top: 10px;" id="cellendeditevent"></div>
                         </div>
                         <div style="width:1200px">&nbsp;</div>
                    </div>
               </div>
               <div id="tabCashCount">>
               </div>
          </td>
     </tr>
</table>

<?php
     wp_enqueue_script(WTA_NAME . '_wta_ajax',  WTA_PLUGIN_DIR . '/wta_ajax.js', array('jquery'), WTA_VAR, true);
}
else if(!$conn)
     print_r(sqlsrv_errors());
?>

