var cash_counts_data = new Array();
var cash_counts_editable = true;
var cash_counts_submit_times = new Array();
var enable_submit_time_select = true;

function initializeCashCountsWidgets() {
    jQuery("#jqxCashSubmitTime").jqxDropDownList({ source: cash_counts_submit_times, selectedIndex: 0, width: '200', height: '30px'});
    jQuery("#jqxCashSubmitTime").on('select', function (e) {
        if(enable_submit_time_select)
        {
            jQuery('#cash_counts_grid').jqxGrid('endcelledit');
            outlet = jQuery("#jqxOutlet").val();
            date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
            submit_time = cash_counts_submit_times[e.args.index].value;
            if(tabId == 4)
                get_cash_counts_data(outlet, date, submit_time);
        }
    });

    var sourceCashCounts =
    {
         localdata: cash_counts_data,
         datatype: "array",
         updaterow: function (rowid, rowdata, commit) {
            cash_counts_data[rowid].amount = rowdata.amount;
            var total = 0;
            for(var i=0; i<cash_counts_data.length-3; i++)
                total += parseFloat(cash_counts_data[i].amount);
            cash_counts_data[cash_counts_data.length - 3].amount = total;
            cash_counts_data[cash_counts_data.length - 1].amount = total - parseFloat(cash_counts_data[cash_counts_data.length - 2].amount);
            jQuery("#cash_counts_grid").jqxGrid('updatebounddata', 'cells');
         },
         datafields:
         [
              { name: 'id', type: 'number' },
              { name: 'amount', type: 'number' }
         ]
    };
    var dataAdapterCashCounts = new jQuery.jqx.dataAdapter(sourceCashCounts);

    var cellbeginedit = function (row, datafield, columntype, value) {
        if(jQuery("#jqxCashSubmitTime").val() != '') return false;
        if (row >= cash_counts_data.length - 3) return false;
        else return true;
    }

    var cellsrenderer_no = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if(row < cash_counts_data.length - 3)
            element[0].innerHTML = row + 1;
        return element[0].outerHTML;
    }

    var cellsrenderer_name = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if(row == cash_counts_data.length - 1)
            element[0].innerHTML = "<center><b>DIFFERENCE</b></center>";
        else if(row == cash_counts_data.length - 2)
            element[0].innerHTML = "<center><b>CASH EXPECTED ON SITE</b></center>";
        else if(row == cash_counts_data.length - 3)
            element[0].innerHTML = "<center><b>TOTAL</b></center>";
        else
            element[0].innerHTML = "<center>" + locations[row].label + "</center>";
        return element[0].outerHTML;
    }

    var cellsrenderer_amount = function (row, column, value, defaultHtml) {
        var element = jQuery(defaultHtml);
        if(row == cash_counts_data.length - 1)
        {
            if(parseFloat(value) > 0)
                element[0].innerHTML = "<b style='color:#008800'>" + element[0].innerHTML + "</b>";
            else if(parseFloat(value) < 0)
                element[0].innerHTML = "<b style='color:#880000'>" + element[0].innerHTML + "</b>";
        }
        return element[0].outerHTML;
    }

    jQuery("#cash_counts_grid").jqxGrid(
    {
         width: '1250',
         height: '400',
         source: dataAdapterCashCounts,
         columnsresize: true,
         editable: true,
         rowsheight: 30,
         selectionmode: 'singlerow',
         columns: [
              { text: 'NO', columntype: 'integerinput', width: 60, align: 'center', cellsalign: 'right', pinned: true, cellsrenderer:cellsrenderer_no, editable:false },
              { text: 'LOCATION', columntype: 'string', width: 200, align: 'center', cellsalign: 'center', cellsrenderer:cellsrenderer_name, editable:false },
              { text: 'AMOUNT', datafield: 'amount', cellsformat: 'c2', columntype: 'numberinput', align: 'center', cellsalign: 'right', width: 150, cellbeginedit: cellbeginedit, cellsrenderer:cellsrenderer_amount }
         ]
    });

    jQuery("#cash_counts_submit").click(function(event) {
        if(!cash_counts_editable)
            return;
        jQuery('#cash_counts_grid').jqxGrid('endcelledit');
        if(confirm("Are you sure you want to submit this CASH COUNTS now?"))
        {
            outlet = jQuery("#jqxOutlet").val();
            set_cash_counts_data(outlet, cash_counts_data);
        }
        event.preventDefault();
    });
    jQuery("#cash_counts_refresh").click(function(event) {
        jQuery('#cash_counts_grid').jqxGrid('endcelledit');
        outlet = jQuery("#jqxOutlet").val();
        date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
        get_cash_counts_data(outlet, date, '');
        event.preventDefault();
    });
}
