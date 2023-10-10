var paid_data = new Array();
var paid_type = 0;
var current_zref = 0;
var current_date = 0;
var paid_changed = 0;
function show_paid_popup(type, row)
{
    jQuery('#wta_grid').jqxGrid('endcelledit');
    if(wta_data[row].zref == "")
    {
        alert("Please input the Z-Ref value.");
        return;
    }
    paid_type = type;
    var title = "";
    outlet = jQuery("#jqxOutlet").val();
    term = jQuery("#jqxTerm").val();
    outletTxt = jQuery("#jqxOutlet").text();
    termTxt = jQuery("#jqxTerm").text();
    date = wta_data[row].date;
    zref = wta_data[row].zref;
    current_zref = zref;
    current_date = date;
    if(type == 0)
        title = "<span style='font-size:16px'>PAID OUT FROM TILL ( <b>" + outletTxt + "</b> : <b>" +  termTxt + "</b>, Date:<b>" + date + "</b>,   ZREF:<b>" + zref + "</b> )</span>";
    else
        title = "<span style='font-size:16px'>PAID OUT FROM SAFE ( <b>" + outletTxt + "</b> : <b>" +  termTxt + "</b>, Date:<b>" + date + "</b>,   ZREF:<b>" + zref + "</b> )</span>";
    jQuery("#popup_paid").jqxWindow({title: title});
    jQuery("#popup_paid").jqxWindow({ position: { x: 150, y: 340 } });
    jQuery("#popup_paid").jqxWindow("open");
    paid_changed = 0;

    get_paid_data(paid_type, date, zref);
}

function initializePaidoutWidgets() {
    jQuery("#popup_paid").jqxWindow({ resizable:false, width: 1080, isModal: true, position: "right", autoOpen: false, title: "Paid out Till", cancelButton: jQuery("#btn_close")});
    jQuery("#popup_paid").on("close", function(event){
        if(paid_changed == 1)
        {
            outlet = jQuery("#jqxOutlet").val();
            term = jQuery("#jqxTerm").val();
            get_wta_data(outlet, term, current_date);
        }
    });
    
    jQuery("#popupEdit").jqxWindow({
        width: 350, resizable: false,  isModal: true, autoOpen: false, cancelButton: jQuery("#Cancel"), modalOpacity: 0.01
    });

    var sourcePaid =
    {
         localdata: paid_data,
         datatype: "array",
         updaterow: function (rowid, rowdata, commit) {
            var row = { id:rowdata.id, date:current_date, zref:current_zref, ex_vat:rowdata.ex_vat, 
                    vat_amount: rowdata.vat_amount, payout_type: rowdata.payout_type,
                    reference: rowdata.reference, description: rowdata.description};
            set_paid_data(paid_type, row);
         },
         datafields:
         [
              { name: 'id', type: 'number' },
              { name: 'date', type: 'date', format: 'dd/MM/yyyy' },
              { name: 'zref', type: 'number' },
              { name: 'ex_vat', type: 'number' },
              { name: 'vat_amount', type: 'number' },
              { name: 'payout_type', type: 'number' },
              { name: 'reference', type: 'string' },
              { name: 'description', type: 'string' }
         ]
    };
    var dataAdapterPaid = new jQuery.jqx.dataAdapter(sourcePaid);

    var cellbeginedit = function (row, datafield, columntype, value) {
         if (row == paid_data.length - 1) return false;
    }

    var cellsrenderer_no = function (row, column, value, defaultHtml) {
        if(row == paid_data.length - 1)
        {
            var element = jQuery(defaultHtml);
            element[0].innerHTML = "<center>TOTAL</center>";
        }
        else
        {
            var element = jQuery(defaultHtml);
            element[0].innerHTML = row + 1;
        }
        return element[0].outerHTML;
    }

    var cellsrenderer_zref = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if (row < paid_data.length - 1) {
             value = value.toString().padStart(4, '0');
             element[0].innerHTML = value;
        }
        else
        {
            var element = jQuery(defaultHtml);
            element[0].innerHTML = '';
        }
        return element[0].outerHTML;
    }

    jQuery("#paid_grid").jqxGrid(
    {
         width: '1000',
         height: '250',
         source: dataAdapterPaid,
         columnsresize: true,
         editable: true,
         rowsheight: 30,
         columnsheight: 25,
         selectionmode: 'singlerow',
         columns: [
              { text: 'NO', datafield: 'id', columntype: 'integerinput', width: 60, align: 'center', cellsalign: 'right', pinned: true, cellsrenderer:cellsrenderer_no, editable:false },
              { text: 'DATE', datafield: 'date' , columntype: 'textbox', cellsformat: 'dd/MM/yyyy', width: 90, align: 'center', cellsalign: 'right', pinned: true, editable:false },
              { text: 'Z REF', datafield: 'zref', columntype: 'integerinput', width: 90, align: 'center', cellsalign: 'right', cellsformat: 'n3', pinned: true, cellsrenderer:cellsrenderer_zref, editable:false },
              { text: 'EX VAT', datafield: 'ex_vat', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
              { text: 'VAT AMOUNT', datafield: 'vat_amount', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
              { text: 'PAYOUT TYPE', datafield: 'payout_type', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
              { text: 'REFERENCE', datafield: 'reference', columntype: 'textbox', align: 'center', cellsalign: 'left', width: 200, cellbeginedit: cellbeginedit},
              { text: 'DESCRIPTION', datafield: 'description', columntype: 'textbox', align: 'center', cellsalign: 'left', width: 240, cellbeginedit: cellbeginedit}
         ]
    });

    jQuery("#btn_add").click(function(event) {
        jQuery("#popupEdit").jqxWindow({ position: { x: 400, y: 330 } });
        jQuery("#popupEdit").jqxWindow('open');
        event.preventDefault();
    });

    jQuery("#Save").click(function () {
        if(document.getElementById("ex_vat").value == "" || isNaN(parseFloat(document.getElementById("ex_vat").value)))
        {
            alert("Please input the correct EX VAT value.");
            return;
        }
        if(document.getElementById("vat_amount").value == "" || isNaN(parseFloat(document.getElementById("vat_amount").value)))
        {
            alert("Please input VAT AMOUNT value.");
            return;
        }
        if(document.getElementById("payout_type").value == ""|| isNaN(parseInt(document.getElementById("payout_type").value)))
        {
            alert("Please input PAYOUT TYPE value.");
            return;
        }
        outletTxt = jQuery("#jqxOutlet").text();
        termTxt = jQuery("#jqxTerm").text();
        var row = { id:-1, date:current_date, zref:current_zref, ex_vat: parseFloat(document.getElementById("vat_amount").value), 
                    vat_amount: parseFloat(document.getElementById("vat_amount").value), 
                    payout_type: parseInt(document.getElementById("payout_type").value),
                    reference: jQuery("#reference").val(), description: jQuery("#description").val()};
        set_paid_data(paid_type, row);
        jQuery("#popupEdit").jqxWindow('close');
    });

    jQuery("#btn_remove").click(function(event) {
        row_id = jQuery("#paid_grid").jqxGrid("getselectedrowindex");
        if(row_id == -1)
        {
            alert("Please select the paid out row first.");
            return;
        }
        delete_paid_data(paid_type, paid_data[row_id].id);
        event.preventDefault();
    });
}