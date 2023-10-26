var cash_counts_data = new Array();

function delete_cash_counts(id) {
    jQuery('#cash_counts_grid').jqxGrid('endcelledit');
    if(confirm("Are you sure you want to delete this cash_counts data?"))
        delete_cash_counts_data(id);
}
function initializeCashCountsWidgets() {
    jQuery("#jqxCashCountsDate").jqxDateTimeInput({ animationType: 'fade', width: '177px', height: '30px', dropDownHorizontalAlignment: 'left'});

    jQuery("#popupCashCountsEdit").jqxWindow({
        width: 350, resizable: false,  isModal: true, autoOpen: false, cancelButton: jQuery("#CashCountsCancel"), modalOpacity: 0.01
    });

    var sourceCashCounts =
    {
         localdata: cash_counts_data,
         datatype: "array",
         updaterow: function (rowid, rowdata, commit) {
            var year = rowdata.date.getFullYear();
            var month = rowdata.date.getMonth() + 1;
            var day = rowdata.date.getDate();
            var date = day + "/" + month + "/" + year;
            var row = { id:rowdata.id, date:date, amount:rowdata.amount};
            outlet = jQuery("#jqxOutlet").val();
            location_id = jQuery("#jqxLocation").val();
            set_cash_counts_data(outlet, location_id, row);
         },
         datafields:
         [
              { name: 'id', type: 'number' },
              { name: 'date', type: 'date', format: 'dd/MM/yyyy' },
              { name: 'amount', type: 'number' }
         ]
    };
    var dataAdapterCashCounts = new jQuery.jqx.dataAdapter(sourceCashCounts);

    var cellbeginedit = function (row, datafield, columntype, value) {
         if (row == cash_counts_data.length - 1) return false;
    }

    var cellsrenderer_no = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if(row == cash_counts_data.length - 1)
            element[0].innerHTML = "<center>TOTAL</center>";
        else
            element[0].innerHTML = row + 1;
        return element[0].outerHTML;
    }

    jQuery("#cash_counts_grid").jqxGrid(
    {
         width: '1050',
         height: '250',
         source: dataAdapterCashCounts,
         columnsresize: true,
         editable: true,
         rowsheight: 30,
         selectionmode: 'singlerow',
         columns: [
              { text: 'NO', columntype: 'integerinput', width: 60, align: 'center', cellsalign: 'right', pinned: true, cellsrenderer:cellsrenderer_no, editable:false },
              { text: 'DATE', datafield: 'date' , columntype: 'datetimeinput', cellsformat: 'dd/MM/yyyy', width: 120, align: 'center', cellsalign: 'right', cellbeginedit: cellbeginedit
                    , validation: function (cell, value) {
                        let locale = "en-CA";
                        let options = {year: "numeric", month: "numeric", day: "numeric"};
                        var date_str = value.toLocaleDateString(locale, options);
                        
                        var monday = jQuery("#jqxCalendar").jqxDateTimeInput('getDate');
                        var sunday = monday;
                        
                        var mday = monday.getDay();
                        
                        var date1 = monday.getDate();
                        var diff1 = date1 - mday + 1;
                        var diff2 = diff1 + 6;

                        monday.setDate(diff1);
                        var start_date_str = monday.toLocaleDateString(locale, options);

                        sunday.setDate(diff2);
                        var end_date_str = sunday.toLocaleDateString(locale, options);

                        if (date_str < start_date_str || date_str > end_date_str) {
                            return { result: false, message: "Date should be in this week." };
                        }
                        return true;
                      }
               },
              { text: 'AMOUNT', datafield: 'amount', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 120, cellbeginedit: cellbeginedit },
              { text: '', datafield: 'id', width: 25, resizable:false,
                createwidget: function (row, column, value, htmlElement) {
                    if(value + "" != "")
                    {
                        var element = jQuery(htmlElement);
                        element[0].innerHTML = "<span class='material-symbols-outlined' style='cursor:pointer' onclick='javascript:delete_cash_counts(" + value + ")'>delete</span>";
                    }
                },
                initwidget: function (row, column, value, htmlElement) {
                    if(value + "" != "")
                    {
                        var element = jQuery(htmlElement);
                        element[0].innerHTML = "<span class='material-symbols-outlined' style='cursor:pointer' onclick='javascript:delete_cash_counts(" + value + ")'>delete</span>";
                    }
                }
            }
         ]
    });

    jQuery("#cash_counts_add").click(function(event) {
        jQuery('#cash_counts_grid').jqxGrid('endcelledit');
        jQuery("#popupCashCountsEdit").jqxWindow({ position: { x: 400, y: 330 } });
        jQuery("#popupCashCountsEdit").jqxWindow('open');
        document.getElementById("cash_counts_amount").value = "";
        event.preventDefault();
    });

    jQuery("#CashCountsSave").click(function () {
        if(document.getElementById("cash_counts_amount").value == "" || isNaN(parseFloat(document.getElementById("cash_counts_amount").value)))
        {
            alert("Please input the correct amount value.");
            return;
        }

        let locale = "en-CA";
        let options = {year: "numeric", month: "numeric", day: "numeric"};
        var date_str = jQuery("#jqxCashCountsDate").jqxDateTimeInput('getDate').toLocaleDateString(locale, options);
        
        var monday = jQuery("#jqxCalendar").jqxDateTimeInput('getDate');
        var sunday = monday;
        
        var mday = monday.getDay();
        
        var date1 = monday.getDate();
        var diff1 = date1 - mday + 1;
        var diff2 = diff1 + 6;

        monday.setDate(diff1);
        var start_date_str = monday.toLocaleDateString(locale, options);

        sunday.setDate(diff2);
        var end_date_str = sunday.toLocaleDateString(locale, options);

        if (date_str < start_date_str || date_str > end_date_str) {
            alert("Date should be in this week.");
            return;
        }

        outlet = jQuery("#jqxOutlet").val();
        location_id = jQuery("#jqxLocation").val();
        var row = { id:-1,
                    date: jQuery("#jqxCashCountsDate").val(),
                    amount: parseFloat(document.getElementById("cash_counts_amount").value)};
        set_cash_counts_data(outlet, location_id, row);
        jQuery("#popupCashCountsEdit").jqxWindow('close');
    });
}