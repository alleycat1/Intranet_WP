var wta_data = new Array();
function initializeInputWidgets() {
    jQuery("#jqxMenu").jqxMenu({ width: '1250', height: '50', mode: 'horizontal'});
    jQuery("#jqxMenu").css('visibility', 'visible');

    jQuery("#jqxOutlet").jqxDropDownList({ source: outlets, selectedIndex: 0, width: '200', height: '30px'});
    jQuery("#jqxOutlet").on('select', function (e) {
        terms.length = 0;
        for(var i in terminals[outlets[e.args.index].value])
            terms[i] = terminals[outlets[e.args.index].value][i];
        jQuery("#jqxTerm").jqxDropDownList('clear');
        jQuery("#jqxTerm").jqxDropDownList({ source: terms, selectedIndex: 0, width: '200', height: '30px'});
        if(tabId == 1)
        {
            outlet = jQuery("#jqxOutlet").val();
            get_summary_data(outlet, date);
        }
    });

    jQuery("#jqxTerm").jqxDropDownList({ source: terms, selectedIndex: 0, width: '200', height: '30px'});
    jQuery("#jqxTerm").on('select', function (e) {
        jQuery('#wta_grid').jqxGrid('endcelledit');
        outlet = jQuery("#jqxOutlet").val();
        term = terms[e.args.index].value;
        date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
        if(tabId == 0)
            get_wta_data(outlet, term, date);
    });

    jQuery("#jqxCalendar").jqxDateTimeInput({ animationType: 'slide', width: '150px', height: '30px', dropDownHorizontalAlignment: 'left'});
    jQuery("#jqxCalendar").on('change', function (e) {
        jQuery('#wta_grid').jqxGrid('endcelledit');
        outlet = jQuery("#jqxOutlet").val();
        term = jQuery("#jqxTerm").val();
        date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
        if(tabId == 0)
            get_wta_data(outlet, term, date);
        if(tabId == 1)
        {
            get_summary_data(outlet, date);
        }
    });


    var source =
    {
         localdata: wta_data,
         datatype: "array",
         updaterow: function (rowid, rowdata, commit) {
              outlet = jQuery("#jqxOutlet").val();
              term = jQuery("#jqxTerm").val();
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

    var cellsrenderer_disrepancy = function (row, column, value, defaultHtml) {
         var element = jQuery(defaultHtml);
         if (value < 0) {
              element.css('color', '#880000');
              return element[0].outerHTML;
         }
         else if (value == 0) {
              element.css('color', '#000000');
              return element[0].outerHTML;
         }
         else{
              element.css('color', '#008800');
              return element[0].outerHTML;
         }
         return defaultHtml;
    }

    var cellsrenderer_zref = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if (value > 0 && row < 7) {
             value = value.toString().padStart(4, '0');
             element[0].innerHTML = value;
             return element[0].outerHTML;
        }
        else if(value == 0 && row < 7)
        {
            element[0].innerHTML = '';
            return element[0].outerHTML;
        }
        return defaultHtml;
   }

    var cellsrenderer_paidout_till = function (row, column, value, defaultHtml) {
         if (row >= 0 && row < 7) {
              var element = jQuery(defaultHtml);
              element[0].innerHTML = "<span class='paid_out' onclick='show_paid_popup(0," + row.toString() + ")'>" + element[0].innerHTML + "</span>";
              return element[0].outerHTML;
         }
         return defaultHtml;
    }

    var cellsrenderer_paidout_safe = function (row, column, value, defaultHtml) {
         if (row >= 0 && row < 7) {
              var element = jQuery(defaultHtml);
              element[0].innerHTML = "<span class='paid_out' onclick='show_paid_popup(1,"  + row.toString() + ")'>" + element[0].innerHTML + "</span>";
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
              { text: 'Z REF', datafield: 'zref', columntype: 'integerinput', width: 90, align: 'center', cellsalign: 'right', cellsformat: 'n3', cellsrenderer:cellsrenderer_zref },
              { text: 'EX VAT', columngroup: 'sales', datafield: 'sale_ex', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
              { text: 'VAT', columngroup: 'sales', datafield: 'vat', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
              { text: 'INC VAT', columngroup: 'sales', datafield: 'sale_inc', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit, editable:false },
              { text: 'DISREPANCY', datafield: 'disrepancy', cellsformat: 'c2', align: 'center', cellsalign: 'right', width: 100, cellsrenderer: cellsrenderer_disrepancy, cellbeginedit: cellbeginedit },
              { text: 'FROM TILL', columngroup: 'paid_out', datafield: 'paid_out_from_till', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit, editable:false, cellsrenderer:cellsrenderer_paidout_till },
              { text: 'FROM SAFE', columngroup: 'paid_out', datafield: 'paid_out_from_safe', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit, editable:false, cellsrenderer:cellsrenderer_paidout_safe },
              { text: 'SALES', columngroup: 'account', datafield: 'account_sales', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
              { text: 'RECEIPTS', columngroup: 'account', datafield: 'account_receipts', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
              { text: 'BANKING', columngroup: 'cards', datafield: 'cards_banking', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
              { text: 'RETAINED', columngroup: 'cash', datafield: 'cash_retained', cellsformat: 'c2', align: 'center', cellsalign: 'right', width: 100, editable:false }
         ],
         columngroups:[
              { text: 'SALES', align: 'center', name: 'sales' },
              { text: 'PAID OUT',align: 'center', name: 'paid_out' },
              { text: 'ACCOUNT', align: 'center', name: 'account' },
              { text: 'CARDS', align: 'center', name: 'cards' },
              { text: 'CASH', align: 'center', name: 'cash' }
         ]
    });

    var defaultBorder = document.getElementById("btnWTA").style.border;
    document.getElementById("btnWTA").style.border = "1px solid #000088";
    document.getElementById("tabWTA").hidden = "";
    document.getElementById("tabSummary").hidden = "hidden";
    document.getElementById("tabCashCount").hidden = "hidden";
    document.getElementById("popup_paid").hidden = "hidden";
    jQuery("#btnWTA").on('click', function (e) {
        tabId = 0;
        document.getElementById("btnWTA").style.border = "1px solid #000088";
        document.getElementById("btnSummary").style.border = defaultBorder;
        document.getElementById("btnPaidOuts").style.border = defaultBorder;
        document.getElementById("btnOtherIncome").style.border = defaultBorder;
        document.getElementById("btnCashCounts").style.border = defaultBorder;
        document.getElementById("btnBankingAdjustments").style.border = defaultBorder;
        document.getElementById("tabWTA").hidden = "";
        document.getElementById("tabSummary").hidden = "hidden";
        document.getElementById("tabCashCount").hidden = "hidden";

        jQuery('#wta_grid').jqxGrid('endcelledit');
        outlet = jQuery("#jqxOutlet").val();
        term = jQuery("#jqxTerm").val();
        date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
        get_wta_data(outlet, term, date);
        document.getElementById("jqxOutlet").hidden = "";
        document.getElementById("jqxTerm").hidden = "";
    });
    jQuery("#btnSummary").on('click', function (e) {
        tabId = 1;
        document.getElementById("btnWTA").style.border = defaultBorder;
        document.getElementById("btnSummary").style.border = "1px solid #000088";
        document.getElementById("btnPaidOuts").style.border = defaultBorder;
        document.getElementById("btnOtherIncome").style.border = defaultBorder;
        document.getElementById("btnCashCounts").style.border = defaultBorder;
        document.getElementById("btnBankingAdjustments").style.border = defaultBorder;
        document.getElementById("tabWTA").hidden = "hidden";
        document.getElementById("tabSummary").hidden = "";
        document.getElementById("tabCashCount").hidden = "hidden";
        date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
        outlet = jQuery("#jqxOutlet").val();
        get_summary_data(outlet, date);
        document.getElementById("jqxOutlet").hidden = "";
        document.getElementById("jqxTerm").hidden = "hidden";
    });
    jQuery("#btnPaidOuts").on('click', function (e) {
        tabId = 2;
        document.getElementById("btnWTA").style.border = defaultBorder;
        document.getElementById("btnSummary").style.border = defaultBorder;
        document.getElementById("btnPaidOuts").style.border = "1px solid #000088";
        document.getElementById("btnOtherIncome").style.border = defaultBorder;
        document.getElementById("btnCashCounts").style.border = defaultBorder;
        document.getElementById("btnBankingAdjustments").style.border = defaultBorder;
        document.getElementById("tabWTA").hidden = "hidden";
        document.getElementById("tabSummary").hidden = "hidden";
        document.getElementById("tabCashCount").hidden = "";
        document.getElementById("jqxOutlet").hidden = "";
        document.getElementById("jqxTerm").hidden = "";
    });
    jQuery("#btnOtherIncome").on('click', function (e) {
          tabId = 3;
          document.getElementById("btnWTA").style.border = defaultBorder;
          document.getElementById("btnSummary").style.border = defaultBorder;
          document.getElementById("btnPaidOuts").style.border = defaultBorder;
          document.getElementById("btnOtherIncome").style.border = "1px solid #000088";
          document.getElementById("btnCashCounts").style.border = defaultBorder;
          document.getElementById("btnBankingAdjustments").style.border = defaultBorder;
          document.getElementById("tabWTA").hidden = "hidden";
          document.getElementById("tabSummary").hidden = "hidden";
          document.getElementById("tabCashCount").hidden = "";
          document.getElementById("jqxOutlet").hidden = "";
          document.getElementById("jqxTerm").hidden = "";
     });
    jQuery("#btnCashCounts").on('click', function (e) {
        tabId = 4;
        document.getElementById("btnWTA").style.border = defaultBorder;
        document.getElementById("btnSummary").style.border = defaultBorder;
        document.getElementById("btnPaidOuts").style.border = defaultBorder;
        document.getElementById("btnOtherIncome").style.border = defaultBorder;
        document.getElementById("btnCashCounts").style.border = "1px solid #000088";
        document.getElementById("btnBankingAdjustments").style.border = defaultBorder;
        document.getElementById("tabWTA").hidden = "hidden";
        document.getElementById("tabSummary").hidden = "hidden";
        document.getElementById("tabCashCount").hidden = "";
        document.getElementById("jqxOutlet").hidden = "";
        document.getElementById("jqxTerm").hidden = "";
    });
    jQuery("#btnBankingAdjustments").on('click', function (e) {
          tabId = 5;
          document.getElementById("btnWTA").style.border = defaultBorder;
          document.getElementById("btnSummary").style.border = defaultBorder;
          document.getElementById("btnPaidOuts").style.border = defaultBorder;
          document.getElementById("btnOtherIncome").style.border = defaultBorder;
          document.getElementById("btnCashCounts").style.border = defaultBorder;
          document.getElementById("btnBankingAdjustments").style.border = "1px solid #000088";
          document.getElementById("tabWTA").hidden = "hidden";
          document.getElementById("tabSummary").hidden = "hidden";
          document.getElementById("tabCashCount").hidden = "";
          document.getElementById("jqxOutlet").hidden = "";
          document.getElementById("jqxTerm").hidden = "";
     });
}