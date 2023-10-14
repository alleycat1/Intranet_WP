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
            alert("Can not access the database!");
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
            alert("Can not access the database!");
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
            alert("Can not access the database!");
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
            alert("Can not access the database!");
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
            alert("Can not access the database!");
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
            alert("Can not access the database!");
            jQuery('#paid_grid').jqxGrid({ disabled: false});
        }
    });
}