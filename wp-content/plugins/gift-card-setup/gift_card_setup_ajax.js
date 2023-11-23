function save_gift_card(card_data){
    jQuery('#card_grid').jqxGrid({ disabled: true});
    jQuery('#card_grid').jqxGrid('endcelledit');
    jQuery("#jqxCardGroup").jqxDropDownList({ disabled: true});
    jQuery("#jqxCardStatus").jqxDropDownList({ disabled: true});
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { data:card_data, action:'save_gift_card', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            group = jQuery("#jqxCardGroup").val();
            stats = jQuery("#jqxCardStatus").val();
            get_gift_cards(group, stats);
        },
        error: function (e) {
            jQuery('#card_grid').jqxGrid({ disabled: false});
            jQuery("#jqxCardGroup").jqxDropDownList({ disabled: false});
            jQuery("#jqxCardStatus").jqxDropDownList({ disabled: false});
            alert("Duplicate Card Number or Database Error.");
        }
    });
}

function update_gift_card(card_data){
    jQuery('#card_grid').jqxGrid({ disabled: true});
    jQuery('#card_grid').jqxGrid('endcelledit');
    jQuery("#jqxCardGroup").jqxDropDownList({ disabled: true});
    jQuery("#jqxCardStatus").jqxDropDownList({ disabled: true});
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { data:card_data, action:'update_gift_card', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            group = jQuery("#jqxCardGroup").val();
            stats = jQuery("#jqxCardStatus").val();
            get_gift_cards(group, stats);
        },
        error: function (e) {
            jQuery('#card_grid').jqxGrid({ disabled: false});
            jQuery("#jqxCardGroup").jqxDropDownList({ disabled: false});
            jQuery("#jqxCardStatus").jqxDropDownList({ disabled: false});
            alert("Can not access the database! - 1");
        }
    });
}

function get_gift_cards(group, stats){
    jQuery('#card_grid').jqxGrid({ disabled: true});
    jQuery('#card_grid').jqxGrid('endcelledit');
    jQuery("#jqxCardGroup").jqxDropDownList({ disabled: true});
    jQuery("#jqxCardStatus").jqxDropDownList({ disabled: true});
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { group:group, stats:stats, action:'get_gift_cards', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            card_data.length = 0;
            for(var i in data)
                card_data.push(data[i]);
            jQuery("#card_grid").jqxGrid('updatebounddata', 'cells');
            jQuery('#card_grid').jqxGrid({ disabled: false});
            jQuery("#jqxCardGroup").jqxDropDownList({ disabled: false});
            jQuery("#jqxCardStatus").jqxDropDownList({ disabled: false});
        },
        error: function (e) {
            jQuery('#card_grid').jqxGrid({ disabled: false});
            jQuery("#jqxCardGroup").jqxDropDownList({ disabled: false});
            jQuery("#jqxCardStatus").jqxDropDownList({ disabled: false});
            alert("Can not access the database! - 2");
        }
    });
}

function delete_card_data(imageId){
    jQuery('#card_grid').jqxGrid('endcelledit');
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { imageId:imageId, action:'delete_card_data', security_nonce:security_nonce },
        dataType: "json",
        success: function (d) {
            group = jQuery("#jqxCardGroup").val();
            stats = jQuery("#jqxCardStatus").val();
            get_gift_cards(group, stats);
        },
        error: function (e) {
            alert("Can not access the database! - 3");
        }
    });
}
