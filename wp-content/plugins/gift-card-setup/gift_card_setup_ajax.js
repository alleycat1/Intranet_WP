function save_gift_image(card_data){
    document.getElementById("disable_pane").style.visibility="visible";
    jQuery('#card_grid').jqxGrid('endcelledit');
    jQuery("#jqxCardGroup").jqxDropDownList({ disabled: true});
    jQuery("#jqxOutlets").jqxDropDownList({ disabled: true});
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { data:card_data, action:'save_gift_image', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            group = jQuery("#jqxCardGroup").val();
            outlet = jQuery("#jqxOutlets").val();
            get_gift_images(group, outlet);
        },
        error: function (e) {
            document.getElementById("disable_pane").style.visibility="hidden";
            jQuery("#jqxCardGroup").jqxDropDownList({ disabled: false});
            jQuery("#jqxOutlets").jqxDropDownList({ disabled: false});
            alert("Duplicate Card Number or Database Error.");
        }
    });
}

function update_gift_image(card_data){
    document.getElementById("disable_pane").style.visibility="visible";
    jQuery('#card_grid').jqxGrid('endcelledit');
    jQuery("#jqxCardGroup").jqxDropDownList({ disabled: true});
    jQuery("#jqxOutlets").jqxDropDownList({ disabled: true});
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { data:card_data, action:'update_gift_image', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            group = jQuery("#jqxCardGroup").val();
            outlet = jQuery("#jqxOutlets").val();
            get_gift_images(group, outlet);
        },
        error: function (e) {
            document.getElementById("disable_pane").style.visibility="hidden";
            jQuery("#jqxCardGroup").jqxDropDownList({ disabled: false});
            jQuery("#jqxOutlets").jqxDropDownList({ disabled: false});
            alert("Can not access the database! - 1");
        }
    });
}

function get_gift_images(group, outlet){
    document.getElementById("disable_pane").style.visibility="visible";
    jQuery('#card_grid').jqxGrid('endcelledit');
    jQuery("#jqxCardGroup").jqxDropDownList({ disabled: true});
    jQuery("#jqxOutlets").jqxDropDownList({ disabled: true});
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { group:group, outlet:outlet, action:'get_gift_images', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            card_data.length = 0;
            for(var i in data)
            {
                var row = new Array();
                for(var key in data[i])
                {
                    if(key.indexOf('outlet') == 0)
                        row[key] = (parseInt(data[i][key]) == 1);
                    else
                        row[key] = data[i][key];
                }
                card_data.push(row);
            }
            document.getElementById("disable_pane").style.visibility="hidden";
            jQuery("#card_grid").jqxGrid('updatebounddata', 'cells');
            jQuery("#jqxCardGroup").jqxDropDownList({disabled: false});
            jQuery("#jqxOutlets").jqxDropDownList({disabled: false});
        },
        error: function (e) {
            document.getElementById("disable_pane").style.visibility="hidden";
            jQuery("#jqxCardGroup").jqxDropDownList({disabled: false});
            jQuery("#jqxOutlets").jqxDropDownList({disabled: false});
            alert("Can not access the database! - 2");
        }
    });
}

function delete_card_image(ID){
    document.getElementById("disable_pane").style.visibility="true";
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { ID:ID, action:'delete_card_image', security_nonce:security_nonce },
        dataType: "json",
        success: function (d) {
            group = jQuery("#jqxCardGroup").val();
            outlet = jQuery("#jqxOutlets").val();
            get_gift_images(group, outlet);
        },
        error: function (e) {
            document.getElementById("disable_pane").style.visibility="hidden";
            alert("Can not access the database! - 3");
        }
    });
}
