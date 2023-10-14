var paid_data = new Array();
var paid_type = 0;
var current_outlet = 0;
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
    current_outlet = outlet;
    term = jQuery("#jqxTerm").val();
    outletTxt = jQuery("#jqxOutlet").text();
    termTxt = jQuery("#jqxTerm").text();
    date = wta_data[row].date;
    zref = wta_data[row].zref;
    current_zref = zref;
    current_date = date;
    zref = zref.toString().padStart(4, '0');
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

function delete_paidout(id) {
    jQuery('#paid_grid').jqxGrid('endcelledit');
    if(confirm("Are you sure you want to delete this PAID OUT?"))
        delete_paid_data(paid_type, id);
}

function initializePaidoutWidgets() {
    jQuery("#jqxPaidOutType").jqxDropDownList({ source: paidOutTypes, selectedIndex: 0, width: '177', height: '30'});
    jQuery("#jqxSupplier").jqxDropDownList({ source: suppliers, selectedIndex: 0, width: '177', height: '30'});

    jQuery("#popup_paid").jqxWindow({ resizable:false, width: 1100, isModal: true, position: "right", autoOpen: false, title: "Paid out Till", cancelButton: jQuery("#btn_close")});
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
            var row = { id:rowdata.id, date:current_date, zref:current_zref, payout_type: rowdata.payout_type, supplier: rowdata.supplier, ex_vat:rowdata.ex_vat, 
                        vat_amount:rowdata.vat_amount, reference: rowdata.reference, description: rowdata.description};
            set_paid_data(paid_type, row);
         },
         datafields:
         [
              { name: 'id', type: 'number' },
              { name: 'date', type: 'date', format: 'dd/MM/yyyy' },
              { name: 'payout_type', type: 'number' },
              { name: 'supplier', type: 'number' },
              { name: 'ex_vat', type: 'number' },
              { name: 'vat_amount', type: 'number' },
              { name: 'total', type: 'number' },
              { name: 'reference', type: 'string' },
              { name: 'description', type: 'string' }
         ]
    };
    var dataAdapterPaid = new jQuery.jqx.dataAdapter(sourcePaid);

    var cellbeginedit = function (row, datafield, columntype, value) {
         if (row == paid_data.length - 1) return false;
    }

    var cellsrenderer_no = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if(row == paid_data.length - 1)
            element[0].innerHTML = "<center>TOTAL</center>";
        else
            element[0].innerHTML = row + 1;
        return element[0].outerHTML;
    }

    var cellsrenderer_paidouttype = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if(row < paid_data.length - 1)
            element[0].innerHTML = "<center>" + paidOutTypes_org[value] + "</center>";
        return element[0].outerHTML;
    }

    var cellsrenderer_supplier = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if(row < paid_data.length - 1)
            element[0].innerHTML = "<center>" + suppliers_org[value] + "</center>";
        return element[0].outerHTML;
    }

    var createGridEditor_paidOutType = function(row, cellValue, editor, cellText, width, height)
    {
        if(width == undefined)
        {
            width = 100;
            height = 30;
        }
        editor.jqxDropDownList({autoDropDownHeight: true,  width: width, height: height, source: paidOutTypes});
    }

    var createGridEditor_supplier = function(row, cellValue, editor, cellText, width, height)
    {
        if(width == undefined)
        {
            width = 100;
            height = 30;
        }
        editor.jqxDropDownList({autoDropDownHeight: true,  width: width, height: height, source: suppliers});
    }

    var initGridEditor = function (row, cellValue, editor, cellText, width, height) {
        editor.jqxDropDownList('selectItem', cellValue);
    }

    var gridEditorValue = function (row, cellValue, editor) {
        return editor.val();
    }

    jQuery("#paid_grid").jqxGrid(
    {
         width: '1050',
         height: '250',
         source: dataAdapterPaid,
         columnsresize: true,
         editable: true,
         rowsheight: 30,
         selectionmode: 'singlerow',
         columns: [
              { text: 'NO', columntype: 'integerinput', width: 60, align: 'center', cellsalign: 'right', pinned: true, cellsrenderer:cellsrenderer_no, editable:false },
              { text: 'DATE', datafield: 'date' , columntype: 'textbox', cellsformat: 'dd/MM/yyyy', width: 90, align: 'center', cellsalign: 'right', pinned: true, editable:false },
              { text: 'PAYOUT TYPE', datafield: 'payout_type', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit, cellsrenderer:cellsrenderer_paidouttype, createeditor:createGridEditor_paidOutType, initeditor:initGridEditor, geteditorvalue:gridEditorValue },
              { text: 'SUPPLIER', datafield: 'supplier', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit, cellsrenderer:cellsrenderer_supplier, createeditor:createGridEditor_supplier, initeditor:initGridEditor, geteditorvalue:gridEditorValue },
              { text: 'EX VAT', datafield: 'ex_vat', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
              { text: 'VAT AMOUNT', datafield: 'vat_amount', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit },
              { text: 'TOTAL', datafield: 'total', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 100, cellbeginedit: cellbeginedit, editable:false },
              { text: 'REFERENCE', datafield: 'reference', columntype: 'textbox', align: 'center', cellsalign: 'left', width: 150, cellbeginedit: cellbeginedit},
              { text: 'DESCRIPTION', datafield: 'description', columntype: 'textbox', align: 'center', cellsalign: 'left', width: 150, cellbeginedit: cellbeginedit},
              { text: '', datafield: 'id', width: 25, resizable:false,
                createwidget: function (row, column, value, htmlElement) {
                    if(value + "" != "")
                    {
                        var element = jQuery(htmlElement);
                        element[0].innerHTML = "<span class='material-symbols-outlined' style='cursor:pointer' onclick='javascript:delete_paidout(" + value + ")'>delete</span>";
                    }
                },
                initwidget: function (row, column, value, htmlElement) {
                    if(value + "" != "")
                    {
                        var element = jQuery(htmlElement);
                        element[0].innerHTML = "<span class='material-symbols-outlined' style='cursor:pointer' onclick='javascript:delete_paidout(" + value + ")'>delete</span>";
                    }
                }
            }
         ]
    });

    jQuery("#btn_add").click(function(event) {
        jQuery('#paid_grid').jqxGrid('endcelledit');
        jQuery("#popupEdit").jqxWindow({ position: { x: 400, y: 330 } });
        jQuery("#popupEdit").jqxWindow('open');
        document.getElementById("ex_vat").value = "";
        document.getElementById("vat_amount").value = "";
        document.getElementById("paidout_total_amount").value = "";
        document.getElementById("reference").value = "";
        document.getElementById("description").value = "";
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
        outletTxt = jQuery("#jqxOutlet").text();
        termTxt = jQuery("#jqxTerm").text();
        var row = { id:-1, date:current_date, zref:current_zref, 
                    payout_type: parseInt(jQuery("#jqxPaidOutType").val()),
                    supplier: parseInt(jQuery("#jqxSupplier").val()),
                    ex_vat: parseFloat(document.getElementById("ex_vat").value), 
                    vat_amount: parseFloat(document.getElementById("vat_amount").value), 
                    reference: jQuery("#reference").val(), description: jQuery("#description").val()};
        set_paid_data(paid_type, row);
        jQuery("#popupEdit").jqxWindow('close');
    });
}