function get_wta_data(outlet, term, date){
    jQuery('#wta_grid').jqxGrid({ disabled: true});
    jQuery("#jqxOutlet").jqxDropDownList({ disabled: true});
    jQuery("#jqxTerm").jqxDropDownList({ disabled: true});
    jQuery("#jqxCalendar").jqxDateTimeInput({ disabled: true});
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { outlet:outlet, term:term, date:date, action:'get_wta_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            wta_data.length = 0;
            for(var date in data)
                wta_data.push(data[date]);
            jQuery("#wta_grid").jqxGrid('updatebounddata', 'cells');
            jQuery('#wta_grid').jqxGrid({ disabled: false});
            jQuery("#jqxOutlet").jqxDropDownList({ disabled: false});
            jQuery("#jqxTerm").jqxDropDownList({ disabled: false});
            jQuery("#jqxCalendar").jqxDateTimeInput({ disabled: false});
        },
        error: function (e) {
            jQuery('#wta_grid').jqxGrid({ disabled: false});
            jQuery("#jqxOutlet").jqxDropDownList({ disabled: false});
            jQuery("#jqxTerm").jqxDropDownList({ disabled: false});
            jQuery("#jqxCalendar").jqxDateTimeInput({ disabled: false});
            alert("Can not access the database! - 1");
        }
    });
}

function get_summary_data(outlet, date){
    jQuery('#summary_grid').jqxGrid({ disabled: true});
    jQuery("#jqxOutlet").jqxDropDownList({ disabled: true});
    jQuery("#jqxCalendar").jqxDateTimeInput({ disabled: true});
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { outlet:outlet, date:date, action:'get_summary_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            summary_data.length = 0;
            for(var date in data)
                summary_data.push(data[date]);
            jQuery("#summary_grid").jqxGrid('updatebounddata', 'cells');
            jQuery('#summary_grid').jqxGrid({ disabled: false});
            jQuery("#jqxCalendar").jqxDateTimeInput({ disabled: false});
            jQuery("#jqxOutlet").jqxDropDownList({ disabled: false});
        },
        error: function (e) {
            jQuery('#summary_grid').jqxGrid({ disabled: false});
            jQuery("#jqxCalendar").jqxDateTimeInput({ disabled: false});
            jQuery("#jqxOutlet").jqxDropDownList({ disabled: false});
            alert("Can not access the database! - 2");
        }
    });
}

function get_cash_on_site(outlet, date){
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { outlet:outlet, date:date, action:'get_cash_on_site', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            document.getElementById('current_cash').innerHTML = "Current Cash on Site: £ " + parseFloat(data['CashOnSite']).toFixed(2);
        },
        error: function (e) {
            //alert("Can not access the database! - 3");
        }
    });
}

jQuery(document).ready(function($) {
    if(window.innerWidth < 1300)
    {
        document.getElementById("neve_body").style.width = "1300px";
        document.getElementById("neve_body").style.overflowX = "scroll";
    }
    
    outlet = jQuery("#jqxOutlet").val();
    term = jQuery("#jqxTerm").val();
    date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
    get_wta_data(outlet, term, date);
});

function set_wta_data(outlet, term, id, date, row, user_id){
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { outlet:outlet, term:term, id:id, date:date, row:row, user_id:user_id, action:'set_wta_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            get_wta_data(outlet, term, date);
        },
        error: function (e) {
            alert("Can not access the database! - 4");
        }
    });
}

function get_paid_data(paid_type, date, zref){
    jQuery('#paid_grid').jqxGrid({ disabled: true});
    if(paid_data.length > 0)
        jQuery("#paid_grid").jqxGrid("clearselection");
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { paid_type:paid_type, date:date, zref:zref, action:'get_paid_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            paid_data.length = 0;
            for(var id in data)
                paid_data.push(data[id]);
            jQuery("#paid_grid").jqxGrid('updatebounddata', 'cells');
            jQuery('#paid_grid').jqxGrid({ disabled: false});
        },
        error: function (e) {
            alert("Can not access the database! - 5");
            jQuery('#paid_grid').jqxGrid({ disabled: false});
        }
    });
}

function set_paid_data(paid_type, data){
    paid_changed = 1;
    var security_nonce = MyAjax.security_nonce;
    jQuery('#paid_grid').jqxGrid({ disabled: true});
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { paid_type:paid_type, data:data, action:'set_paid_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (d) {
            get_paid_data(paid_type, data.date, data.zref);

        },
        error: function (e) {
            alert("Can not access the database! - 6");
            jQuery('#paid_grid').jqxGrid({ disabled: false});
        }
    });
}

function delete_paid_data(paid_type, id){
    paid_changed = 1;
    var security_nonce = MyAjax.security_nonce;
    jQuery('#paid_grid').jqxGrid({ disabled: true});
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { paid_type:paid_type, id:id, action:'delete_paid_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (d) {
            get_paid_data(paid_type, current_date, current_zref);
        },
        error: function (e) {
            alert("Can not access the database! - 7");
            jQuery('#paid_grid').jqxGrid({ disabled: false});
        }
    });
}

function get_paidout_view_data(outlet, date){
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { outlet:outlet, date:date, action:'get_paidout_view_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            paidout_data1.length = 0;
            for(var id in data['data1'])
                paidout_data1.push(data['data1'][id]);
            paidout_data2.length = 0;
            for(var id in data['data2'])
                paidout_data2.push(data['data2'][id]);
            jQuery("#paidout_grid1").jqxGrid('updatebounddata', 'cells');
            jQuery("#paidout_grid2").jqxGrid('updatebounddata', 'cells');
        },
        error: function (e) {
            alert("Can not access the database! - 8");
        }
    });
}

function get_income_data(outlet, income, date){
    var security_nonce = MyAjax.security_nonce;
    jQuery('#income_grid').jqxGrid({ disabled: true});
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { outlet:outlet, income:income, date:date, action:'get_income_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            income_data.length = 0;
            for(var id in data)
                income_data.push(data[id]);
            jQuery("#income_grid").jqxGrid('updatebounddata', 'cells');
            jQuery('#income_grid').jqxGrid({ disabled: false});
        },
        error: function (e) {
            alert("Can not access the database! - 9");
        }
    });
}

function set_income_data(outlet, income, data){
    var security_nonce = MyAjax.security_nonce;
    jQuery('#income_grid').jqxGrid({ disabled: true});
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { outlet:outlet, income:income, data:data, action:'set_income_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (d) {
            outlet = jQuery("#jqxOutlet").val();
            income = jQuery("#jqxIncomeType").val();
            date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
            get_income_data(outlet, income, date);

        },
        error: function (e) {
            alert("Can not access the database! - 10");
            jQuery('#income_grid').jqxGrid({ disabled: false});
        }
    });
}

function delete_income_data( id){
    var security_nonce = MyAjax.security_nonce;
    jQuery('#income_grid').jqxGrid({ disabled: true});
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { id:id, action:'delete_income_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (d) {
            outlet = jQuery("#jqxOutlet").val();
            income = jQuery("#jqxIncomeType").val();
            date = jQuery("#jqxCalendar").jqxDateTimeInput('getText');
            get_income_data(outlet, income, date);
        },
        error: function (e) {
            alert("Can not access the database! - 11");
            jQuery('#income_grid').jqxGrid({ disabled: false});
        }
    });
}

function get_cash_counts_editable(outlet){
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { outlet:outlet, action:'get_cash_counts_editable', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            secs = data.elapsed_seconds;
            if(data.elapsed_seconds >= 900)
                get_cash_counts_data(outlet);
        },
        error: function (e) {
            alert("Can not access the database! - 12");
        }
    });
}

function get_cash_counts_data(outlet){
    var security_nonce = MyAjax.security_nonce;
    jQuery('#cash_counts_grid').jqxGrid({ disabled: true});
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { outlet:outlet, action:'get_cash_counts_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            cash_counts_data.length = 0;
            for(var id in data.data)
                cash_counts_data.push(data.data[id]);
            date = data.date;
            jQuery("#jqxCashSubmitTime").jqxInput('val', date);
            jQuery("#cash_counts_grid").jqxGrid('updatebounddata', 'cells');
            cash_counts_editable = data.elapsed_seconds >= 900;
            jQuery('#cash_counts_grid').jqxGrid({ disabled: false});
            if(!cash_counts_editable)
                cash_counts_check_timer = setInterval(getCashCountsEditable, 60*1000);
            else
                clearInterval(cash_counts_check_timer);
        },
        error: function (e) {
            alert("Can not access the database! - 12");
        }
    });
}

function set_cash_counts_data(outlet, data){
    var security_nonce = MyAjax.security_nonce;
    jQuery('#cash_counts_grid').jqxGrid({ disabled: true});
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { outlet:outlet, data:data, action:'set_cash_counts_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (d) {
            outlet = jQuery("#jqxOutlet").val();
            get_cash_counts_data(outlet);
        },
        error: function (e) {
            alert("Can not access the database! - 13");
            jQuery('#cash_counts_grid').jqxGrid({ disabled: false});
        }
    });
}

