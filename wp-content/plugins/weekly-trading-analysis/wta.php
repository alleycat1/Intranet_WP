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

$menus = array();
$menus['Tickety_Boo'] = '001 - Tickety Boo\'s';
$menus['19th_Hole_Hotel'] = '002 - 19th Hole Hotel';
$menus['Half_A_Tanner'] = '004 - Half A Tanner';
$menus['Scone_Arms'] = '005 - Scone Arms';
$menus['The_Venue'] = '006 - The Venue';

$menuIDs = array();
$menuIDs['Tickety_Boo'] = 1;
$menuIDs['19th_Hole_Hotel'] = 2;
$menuIDs['Half_A_Tanner'] = 3;
$menuIDs['Scone_Arms'] = 4;
$menuIDs['The_Venue'] = 5;

$user_menu = array();
$user = wp_get_current_user();
if(in_array("administrator", $user->roles))
{
     foreach($menus as $role => $data)
          $user_menu[$role] = $data;
}
else
{
     foreach($user->roles as $role => $data)
          if(isset($menus[$role]))
               $user_menu[$role] = $data;
}
if(count($user_menu) == 0)
{
     echo "Please sign up first!";
}

if($conn && count($user_menu) > 0)
{
     sqlsrv_close($conn);

     echo "<script>";
     echo "var user_id = $user->id;";
     echo "var wta_data = new Array();";
     echo "var terms = Array();";
     echo "var outlets=[";
     foreach($user_menu as $menu => $data)
     {
          echo "{label:\"" . $data . "\", value:" . $menuIDs[$menu] . "},";
     }
     echo "];";
     if(isset($user_menu['Tickety_Boo']))
     {
          echo "terms[0] = {label:'TBEPOS1', value:1};";
          echo "terms[1] = {label:'TBEPOS2', value:2};";
     }
     else if(isset($user_menu['19th_Hole_Hotel']))
     {
          echo "terms[0] = {label:'NHEPOS1', value:1};";
          echo "terms[1] = {label:'NHEPOS2', value:2};";
     }
     else if(isset($user_menu['Half_A_Tanner']))
     {
          echo "terms[0] = {label:'HTEPOS1', value:1};";
          echo "terms[1] = {label:'HTEPOS2', value:2};";
     }
     else if(isset($user_menu['Scone_Arms']))
     {
          echo "terms[0] = {label:'SAEPOS1', value:1};";
          echo "terms[1] = {label:'SAEPOS2', value:2};";
     }
     else if(isset($user_menu['The_Venue']))
     {
          echo "terms[0] = {label:'TVEPOS1', value:1};";
          echo "terms[1] = {label:'TVEPOS2', value:2};";
     }
     echo "</script>";
?>

<script>
function initializeWidgets() {
     jQuery("#jqxMenu").jqxMenu({ width: '220', height: '300', mode: 'vertical'});
     jQuery("#jqxMenu").css('visibility', 'visible');

     jQuery("#jqxOutlet").jqxDropDownList({ source: outlets, selectedIndex: 0, width: '200', height: '30px'});
     jQuery("#jqxOutlet").on('select', function (e) {
          terms.length = 0;
          if(outlets[e.args.index].value == 1)
          {
               terms[0] = {label:"TBEPOS1", value:1};
               terms[1] = {label:"TBEPOS2", value:2};
          }
          else if(outlets[e.args.index].value == 2)
          {
               terms[0] = {label:"NHEPOS1", value:1};
               terms[1] = {label:"NHEPOS2", value:2};
          }
          else if(outlets[e.args.index].value == 3)
          {
               terms[0] = {label:"HTEPOS1", value:1};
               terms[1] = {label:"HTEPOS2", value:2};
          }
          else if(outlets[e.args.index].value == 4)
          {
               terms[0] = {label:"SAEPOS1", value:1};
               terms[1] = {label:"SAEPOS2", value:2};
          }
          else if(outlets[e.args.index].value == 5)
          {
               terms[0] = {label:"TVEPOS1", value:1};
               terms[1] = {label:"TVEPOS2", value:2};
          }
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
          width: '100%',
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
}
jQuery(document).ready(function ($) {
     initializeWidgets();
});

</script>
<table border=0 hight=450>
     <tr>
          <td style="border:0px">
               <div id='jqxMenuWidget' style='width: 220px; height: 300px'>
                    <div id='jqxMenu' style="visibility: hidden;">
                         <ul style="margin:0px">
                              <li>Weekly Trading Analysis</li>
                              <li>Paid out</li>
                              <li>Cash Counts</li>
                              <li>Summary</li>
                         </ul>
                    </div>
               </div>
          </td>
          <td style="border:0px; width:100%">
               <table border=0>
                    <tr>
                         <td style="border:0px;">
                              <div style='float: left; margin-top: 10px;' id='jqxOutlet'></div>
                              <div style='float: left; margin-top: 10px; margin-left: 100px;' id='jqxCalendar'></div>
                              <div style='float: left; margin-top: 10px; margin-left: 100px;' id='jqxTerm'></div>
                              <br>
                              <br>
                              <div style="border: none;" id='jqxGrid'>
                                   <div id="wta_grid"></div>
                                   <div style="font-size: 12px; font-family: Verdana, Geneva, 'DejaVu Sans', sans-serif; margin-top: 30px;">
                                        <div id="cellbegineditevent"></div>
                                        <div style="margin-top: 10px;" id="cellendeditevent"></div>
                                   </div>
                              </div>
                         </td>
                    </tr>
               </table>
          </td>
     </tr>
</table>

<?php
     wp_enqueue_script(WTA_NAME . '_wta_ajax',  WTA_PLUGIN_DIR . '/wta_ajax.js', array('jquery'), WTA_VAR, true);
}
else if(!$conn)
     print_r(sqlsrv_errors());
?>

