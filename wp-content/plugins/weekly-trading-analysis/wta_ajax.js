function my_action_javascript(){
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { category:'asdf', action:'wta_test_action', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            alert(data);
        },
        error: function (e) {
            alert(e.status);
        }
    });
}

function get_wta_data(outlet, term, date){
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
        },
        error: function (e) {
            jQuery('#wta_grid').jqxGrid({ disabled: false});
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