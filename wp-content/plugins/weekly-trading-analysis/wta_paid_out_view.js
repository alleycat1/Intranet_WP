var paidout_data1 = new Array();
var paidout_data2 = new Array();
function initializePaidoutViewWidgets() {
    var source_paidout1 =
    {
         localdata: paidout_data1,
         datatype: "array",
         datafields:
         [
              { name: 'date', type: 'date', format: 'dd/MM/yyyy' },
              { name: 'zref', type: 'number' },
              { name: 'supplier_name', type: 'string' },
              { name: 'payout_type', type: 'string' },
              { name: 'ex_vat', type: 'number' },
              { name: 'vat_amount', type: 'number' },
              { name: 'reference', type: 'string' },
              { name: 'description', type: 'string' }
         ]
    };

    var source_paidout2 =
    {
         localdata: paidout_data2,
         datatype: "array",
         datafields:
         [
              { name: 'date', type: 'date', format: 'dd/MM/yyyy' },
              { name: 'zref', type: 'number' },
              { name: 'supplier_name', type: 'string' },
              { name: 'payout_type', type: 'string' },
              { name: 'ex_vat', type: 'number' },
              { name: 'vat_amount', type: 'number' },
              { name: 'reference', type: 'string' },
              { name: 'description', type: 'string' }
         ]
    };
    var dataAdapter_paidout1 = new jQuery.jqx.dataAdapter(source_paidout1);
    var dataAdapter_paidout2 = new jQuery.jqx.dataAdapter(source_paidout2);

    var cellsrenderer_no1 = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if(row < paidout_data1.length - 1)
            element[0].innerHTML = row + 1;
        return element[0].outerHTML;
    }

    var cellsrenderer_no2 = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if(row < paidout_data2.length - 1)
            element[0].innerHTML = row + 1;
        return element[0].outerHTML;
    }

    var cellsrenderer_zref1 = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if (value > 0 && row < paidout_data1.length - 1) {
             value = value.toString().padStart(4, '0');
             element[0].innerHTML = value;
             return element[0].outerHTML;
        }
        else if(value == 0 && row < paidout_data1.length - 1)
        {
            element[0].innerHTML = '';
            return element[0].outerHTML;
        }
        return defaultHtml;
   }

   var cellsrenderer_zref2 = function (row, column, value, defaultHtml) {
    var element = jQuery(defaultHtml);
    if (value > 0 && row < paidout_data2.length - 1) {
         value = value.toString().padStart(4, '0');
         element[0].innerHTML = value;
         return element[0].outerHTML;
    }
    else if(value == 0 && row < paidout_data2.length - 1)
    {
        element[0].innerHTML = '';
        return element[0].outerHTML;
    }
    return defaultHtml;
}

    jQuery("#paidout_grid1").jqxGrid(
    {
         width: '1250',
         height: '180',
         source: dataAdapter_paidout1,
         columnsresize: true,
         editable: false,
         rowsheight: 30,
         columnsheight: 25,
         selectionmode: 'singlerow',
         columns: [
              { text: 'NO', columntype: 'integerinput', width: 60, align: 'center', cellsalign: 'right', pinned: true, cellsrenderer:cellsrenderer_no1},
              { text: 'DATE', datafield: 'date' , columntype: 'textbox', cellsformat: 'dd/MM/yyyy', width: 90, align: 'center', pinned: true },
              { text: 'Z REF', datafield: 'zref', columntype: 'integerinput', width: 90, align: 'center', cellsalign: 'right', cellsrenderer:cellsrenderer_zref1 },
              //{ text: 'SUPPLIER', datafield: 'supplier_name', cellsformat: 'textbox', align: 'center', cellsalign: 'center', width: 180 },
              { text: 'PAYOUT TYPE', datafield: 'payout_type', cellsformat: 'textbox', align: 'center', cellsalign: 'center', width: 180 },
              { text: 'TOTAL', datafield: 'ex_vat', cellsformat: 'c2', align: 'center', cellsalign: 'right', width: 110 },
              //{ text: 'VAT AMOUNT', datafield: 'vat_amount', cellsformat: 'c2', align: 'center', cellsalign: 'right', width: 110 },
              { text: 'REFERENCE', datafield: 'reference', cellsformat: 'textbox', align: 'center', cellsalign: 'right', width: 200 },
              { text: 'DESCRIPTION', datafield: 'description', cellsformat: 'textbox', align: 'center', cellsalign: 'right', width: 200 }
         ]
    });

    jQuery("#paidout_grid2").jqxGrid(
    {
            width: '1250',
            height: '180',
            source: dataAdapter_paidout2,
            columnsresize: true,
            editable: false,
            rowsheight: 30,
            columnsheight: 25,
            selectionmode: 'singlerow',
            columns: [
                { text: 'NO', columntype: 'integerinput', width: 60, align: 'center', cellsalign: 'right', pinned: true, cellsrenderer:cellsrenderer_no2 },
                { text: 'DATE', datafield: 'date' , columntype: 'textbox', cellsformat: 'dd/MM/yyyy', width: 90, align: 'center', pinned: true },
                { text: 'Z REF', datafield: 'zref', columntype: 'integerinput', width: 90, align: 'center', cellsalign: 'right', cellsrenderer:cellsrenderer_zref2 },
                //{ text: 'SUPPLIER', datafield: 'supplier_name', cellsformat: 'textbox', align: 'center', cellsalign: 'center', width: 180 },
                { text: 'PAYOUT TYPE', datafield: 'payout_type', cellsformat: 'textbox', align: 'center', cellsalign: 'center', width: 180 },
                { text: 'TOTAL', datafield: 'ex_vat', cellsformat: 'c2', align: 'center', cellsalign: 'right', width: 110 },
                //{ text: 'VAT AMOUNT', datafield: 'vat_amount', cellsformat: 'c2', align: 'center', cellsalign: 'right', width: 110 },
                { text: 'REFERENCE', datafield: 'reference', cellsformat: 'textbox', align: 'center', cellsalign: 'right', width: 200 },
                { text: 'DESCRIPTION', datafield: 'description', cellsformat: 'textbox', align: 'center', cellsalign: 'right', width: 200 }
            ]
    });
}
