var summary_data = new Array();
function initializeSummaryWidgets() {
    var source_summary =
    {
         localdata: summary_data,
         datatype: "array",
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
    var dataAdapter_summary = new jQuery.jqx.dataAdapter(source_summary);

    var cellsrenderer_disrepancy = function (row, column, value, defaultHtml) {
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

    jQuery("#summary_grid").jqxGrid(
    {
         width: '1250',
         height: '300',
         source: dataAdapter_summary,
         columnsresize: true,
         editable: true,
         rowsheight: 30,
         columnsheight: 25,
         selectionmode: 'singlerow',
         columns: [
              { text: 'DAY', datafield: 'day', columntype: 'textbox', width: 70, align: 'center', cellsalign: 'left', pinned: true, editable:false },
              { text: 'DATE', datafield: 'date' , columntype: 'textbox', cellsformat: 'dd/MM/yyyy', width: 90, align: 'center', pinned: true, editable:false },
              { text: 'Z REF', datafield: 'zref', columntype: 'integerinput', width: 90, align: 'center', cellsalign: 'right', cellsformat: 'n3', editable:false },
              { text: 'EX VAT', columngroup: 'sales', datafield: 'sale_ex', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, editable:false },
              { text: 'VAT', columngroup: 'sales', datafield: 'vat', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, editable:false },
              { text: 'INC VAT', columngroup: 'sales', datafield: 'sale_inc', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, editable:false },
              { text: 'DISREPANCY', datafield: 'disrepancy', cellsformat: 'c2', align: 'center', cellsalign: 'right', width: 100, editable:false, cellsrenderer: cellsrenderer_disrepancy },
              { text: 'FROM TILL', columngroup: 'paid_out', datafield: 'paid_out_from_till', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, editable:false },
              { text: 'FROM SAFE', columngroup: 'paid_out', datafield: 'paid_out_from_safe', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, editable:false },
              { text: 'SALES', columngroup: 'account', datafield: 'account_sales', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, editable:false },
              { text: 'RECEIPTS', columngroup: 'account', datafield: 'account_receipts', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, editable:false },
              { text: 'BANKING', columngroup: 'cards', datafield: 'cards_banking', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, editable:false },
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
}