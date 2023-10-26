var income_data = new Array();

function delete_income(id) {
    jQuery('#income_grid').jqxGrid('endcelledit');
    if(confirm("Are you sure you want to delete this income data?"))
        delete_income_data(id);
}
function initializeIncomeWidgets() {
    jQuery("#jqxIncomeDate").jqxDateTimeInput({ animationType: 'fade', width: '177px', height: '30px', dropDownHorizontalAlignment: 'left'});

    jQuery("#popupIncomeEdit").jqxWindow({
        width: 350, resizable: false,  isModal: true, autoOpen: false, cancelButton: jQuery("#IncomeCancel"), modalOpacity: 0.01
    });

    var sourceIncome =
    {
         localdata: income_data,
         datatype: "array",
         updaterow: function (rowid, rowdata, commit) {
            var year = rowdata.date.getFullYear();
            var month = rowdata.date.getMonth() + 1;
            var day = rowdata.date.getDate();
            var date = day + "/" + month + "/" + year;
            var row = { id:rowdata.id, date:date, amount:rowdata.amount, comment: rowdata.comment};
            outlet = jQuery("#jqxOutlet").val();
            income_id = jQuery("#jqxIncomeType").val();
            set_income_data(outlet, income_id, row);
         },
         datafields:
         [
              { name: 'id', type: 'number' },
              { name: 'date', type: 'date', format: 'dd/MM/yyyy' },
              { name: 'amount', type: 'number' },
              { name: 'comment', type: 'string' }
         ]
    };
    var dataAdapterIncome = new jQuery.jqx.dataAdapter(sourceIncome);

    var cellbeginedit = function (row, datafield, columntype, value) {
         if (row == income_data.length - 1) return false;
    }

    var cellsrenderer_no = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if(row == income_data.length - 1)
            element[0].innerHTML = "<center>TOTAL</center>";
        else
            element[0].innerHTML = row + 1;
        return element[0].outerHTML;
    }

    jQuery("#income_grid").jqxGrid(
    {
         width: '1050',
         height: '250',
         source: dataAdapterIncome,
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
              { text: 'COMMENT', datafield: 'comment', columntype: 'textbox', align: 'center', cellsalign: 'left', width: 600, cellbeginedit: cellbeginedit},
              { text: '', datafield: 'id', width: 25, resizable:false,
                createwidget: function (row, column, value, htmlElement) {
                    if(value + "" != "")
                    {
                        var element = jQuery(htmlElement);
                        element[0].innerHTML = "<span class='material-symbols-outlined' style='cursor:pointer' onclick='javascript:delete_income(" + value + ")'>delete</span>";
                    }
                },
                initwidget: function (row, column, value, htmlElement) {
                    if(value + "" != "")
                    {
                        var element = jQuery(htmlElement);
                        element[0].innerHTML = "<span class='material-symbols-outlined' style='cursor:pointer' onclick='javascript:delete_income(" + value + ")'>delete</span>";
                    }
                }
            }
         ]
    });

    jQuery("#income_add").click(function(event) {
        jQuery('#income_grid').jqxGrid('endcelledit');
        jQuery("#popupIncomeEdit").jqxWindow({ position: { x: 400, y: 330 } });
        jQuery("#popupIncomeEdit").jqxWindow('open');
        document.getElementById("income_amount").value = "";
        document.getElementById("income_comment").value = "";
        event.preventDefault();
    });

    jQuery("#IncomeSave").click(function () {
        if(document.getElementById("income_amount").value == "" || isNaN(parseFloat(document.getElementById("income_amount").value)))
        {
            alert("Please input the correct amount value.");
            return;
        }

        let locale = "en-CA";
        let options = {year: "numeric", month: "numeric", day: "numeric"};
        var date_str = jQuery("#jqxIncomeDate").jqxDateTimeInput('getDate').toLocaleDateString(locale, options);
        
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

        outlet_id = jQuery("#jqxOutlet").val();
        income_id = jQuery("#jqxIncomeType").val();
        var row = { id:-1,
                    date: jQuery("#jqxIncomeDate").val(),
                    amount: parseFloat(document.getElementById("income_amount").value), 
                    comment: jQuery("#income_comment").val()};
        set_income_data(outlet_id, income_id, row);
        jQuery("#popupIncomeEdit").jqxWindow('close');
    });
}