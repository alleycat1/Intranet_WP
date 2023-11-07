var banking_data = new Array();
var discrepancy_data = new Array();

function delete_banking(id) {
    jQuery('#banking_grid').jqxGrid('endcelledit');
    if(confirm("Are you sure you want to delete this banking data?"))
        delete_banking_data(id);
}

function initializeBankingWidgets() {
    jQuery("#jqxBankingType").jqxDropDownList({ source: bankingTypes, selectedIndex: 0, width: '200', height: '30px'});
    jQuery("#jqxBankingType").on('select', function (e) {
        jQuery('#banking_grid').jqxGrid('endcelledit');
        outlet = jQuery("#jqxOutlet").val();
        date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
        type = bankingTypes[e.args.index].value;
        document.getElementById('banking_add').innerHTML = jQuery("#jqxBankingType").text();
        if(tabId == 5)
            get_banking_data(outlet, type, date);
    });

    jQuery("#jqxBankingDate").jqxDateTimeInput({ animationType: 'fade', width: '177px', height: '30px', dropDownHorizontalAlignment: 'left'});

    jQuery("#popupBankingEdit").jqxWindow({
        width: 350, resizable: false,  isModal: true, autoOpen: false, cancelButton: jQuery("#BankingCancel"), modalOpacity: 0.01
    });

    var sourceBanking =
    {
         localdata: banking_data,
         datatype: "array",
         updaterow: function (rowid, rowdata, commit) {
            var year = rowdata.date.getFullYear();
            var month = rowdata.date.getMonth() + 1;
            var day = rowdata.date.getDate();
            var date = day + "/" + month + "/" + year;
            var row = { id:rowdata.id, date:date, amount:rowdata.amount, comment: rowdata.comment};
            outlet = jQuery("#jqxOutlet").val();
            type = jQuery("#jqxBankingType").val();
            set_banking_data(outlet, type, row);
         },
         datafields:
         [
              { name: 'id', type: 'number' },
              { name: 'date', type: 'date', format: 'dd/MM/yyyy' },
              { name: 'amount', type: 'number' },
              { name: 'comment', type: 'string' }
         ]
    };
    var dataAdapterBanking = new jQuery.jqx.dataAdapter(sourceBanking);

    var cellbeginedit = function (row, datafield, columntype, value) {
         if (row == banking_data.length - 1) return false;
    }

    var cellsrenderer_no = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if(row == banking_data.length - 1)
            element[0].innerHTML = "<center>TOTAL</center>";
        else
            element[0].innerHTML = row + 1;
        return element[0].outerHTML;
    }

    jQuery("#banking_grid").jqxGrid(
    {
         width: '1250',
         height: '400',
         source: dataAdapterBanking,
         columnsresize: true,
         editable: true,
         rowsheight: 30,
         selectionmode: 'singlerow',
         columns: [
              { text: 'NO', columntype: 'integerinput', width: 60, align: 'center', cellsalign: 'right', pinned: true, cellsrenderer:cellsrenderer_no, editable:false },
              { text: 'DATE', datafield: 'date' , columntype: 'datetimeinput', cellsformat: 'dd/MM/yyyy', width: 120, align: 'center', cellsalign: 'right', cellbeginedit: cellbeginedit
/*                    , validation: function (cell, value) {
                        let locale = "en-CA";
                        let options = {year: "numeric", month: "numeric", day: "numeric"};
                        var date_str = value.toLocaleDateString(locale, options);
                        
                        var monday = jQuery("#jqxCalendar").jqxDateTimeInput('getDate');
                        var mday = monday.getDay();
                        var date1 = monday.getDate();
                        var diff1 = date1 - mday + 1;
                        monday.setDate(diff1);
                        var start_date_str = monday.toLocaleDateString(locale, options);
                        var sunday = monday;
                        sunday.setDate(monday.getDate() + 6);
                        var end_date_str = sunday.toLocaleDateString(locale, options);

                        if (date_str < start_date_str || date_str > end_date_str) {
                            return { result: false, message: "Date should be in this week." };
                        }
                        return true;
                      }
*/
               },
              { text: 'AMOUNT', datafield: 'amount', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 120, cellbeginedit: cellbeginedit },
              { text: 'COMMENT', datafield: 'comment', columntype: 'textbox', align: 'center', cellsalign: 'left', width: 600, cellbeginedit: cellbeginedit},
              { text: '', datafield: 'id', width: 25, resizable:false,
                createwidget: function (row, column, value, htmlElement) {
                    if(value + "" != "")
                    {
                        var element = jQuery(htmlElement);
                        element[0].innerHTML = "<span class='material-symbols-outlined' style='cursor:pointer' onclick='javascript:delete_banking(" + value + ")'>delete</span>";
                    }
                },
                initwidget: function (row, column, value, htmlElement) {
                    if(value + "" != "")
                    {
                        var element = jQuery(htmlElement);
                        element[0].innerHTML = "<span class='material-symbols-outlined' style='cursor:pointer' onclick='javascript:delete_banking(" + value + ")'>delete</span>";
                    }
                }
            }
         ]
    });

    jQuery("#banking_add").click(function(event) {
        jQuery('#banking_grid').jqxGrid('endcelledit');
        jQuery("#popupBankingEdit").jqxWindow({ position: { x: 400, y: 330 } });
        jQuery("#popupBankingEdit").jqxWindow({title: jQuery("#jqxBankingType").text()});
        jQuery("#popupBankingEdit").jqxWindow('open');
        document.getElementById("banking_amount").value = "";
        document.getElementById("banking_comment").value = "";
        event.preventDefault();
    });


    jQuery("#BankingSave").click(function () {
        if(document.getElementById("banking_amount").value == "" || isNaN(parseFloat(document.getElementById("banking_amount").value)))
        {
            alert("Please input the correct amount value.");
            return;
        }

        /*
        let locale = "en-CA";
        let options = {year: "numeric", month: "numeric", day: "numeric"};
        var date_str = jQuery("#jqxBankingDate").jqxDateTimeInput('getDate').toLocaleDateString(locale, options);
        
        var monday = jQuery("#jqxCalendar").jqxDateTimeInput('getDate');
        var mday = monday.getDay();
        var date1 = monday.getDate();
        var diff1 = date1 - mday + 1;
        monday.setDate(diff1);
        var start_date_str = monday.toLocaleDateString(locale, options);
        var sunday = monday;
        sunday.setDate(monday.getDate() + 6);
        var end_date_str = sunday.toLocaleDateString(locale, options);

        if (date_str < start_date_str || date_str > end_date_str) {
            alert("Date should be in this week.");
            return;
        }
        */

        outlet_id = jQuery("#jqxOutlet").val();
        type = jQuery("#jqxBankingType").val();
        var row = { id:-1,
                    date: jQuery("#jqxBankingDate").val(),
                    amount: parseFloat(document.getElementById("banking_amount").value), 
                    comment: jQuery("#banking_comment").val()};
        set_banking_data(outlet_id, type, row);
        jQuery("#popupBankingEdit").jqxWindow('close');
    });
}