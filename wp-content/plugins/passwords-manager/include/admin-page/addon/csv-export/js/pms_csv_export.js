jQuery(document).ready(function () {
jQuery('#import_btn').click(function(){

 jQuery('#pwdms_csvim_upload_file').trigger("click");
})
    jQuery("#pwdms_select_all_csv").click(function () {
        var pwdms_select_all = jQuery(this).prop('checked');

        if (pwdms_select_all == true) {
            jQuery("#pwdms_form_csv_export .form-table input[type=checkbox]").each(function () {
                jQuery(this).prop("checked", true);
            });
        } else {
            jQuery("#pwdms_form_csv_export .form-table input[type=checkbox]").each(function () {
                jQuery(this).prop("checked", false);
            });
        }
    });

    jQuery('#pwdms_export_btn').click(function (e) {
        e.preventDefault();
        export_csv_deatil_list()
    });


    function export_csv_deatil_list() {
        var progressLabel = jQuery(".progress-label");
        var form = jQuery('#pwdms_form_csv_export').serializeArray();
        var data = {};
        jQuery(form).each(function (index, obj) {
            data[obj.name] = obj.value;
        });
        jQuery.ajax({
            url: MyAjax.ajaxurl,
            method: "POST",
            dataType: "json",
            data: { from_data: data, action: 'pwdms_export_detail_list' }, //form_data,        
            success: function (response) {
                if (typeof response.data.page !== 'undefined') {
                    jQuery('#page_num').val(response.data.page);
                    if (response.data.done == false) {
                        jQuery("#csv_export_progressbar").progressbar({
                            value: 1000,
                            change: function () {
                                progressLabel.text(jQuery("#csv_export_progressbar").progressbar("value") + "%");
                            },
                        });
                        export_csv_deatil_list();
                    } else {
                        jQuery("#csv_export_progressbar").progressbar({
                            value: 1000,
                            change: function () {
                                jQuery("#csv_export_progressbar").text('');
                            },
                        });
                        jQuery("#csv_export_progressbar").hide();
                        jQuery('#page_num').val(1);
                        jQuery('.export_error').remove();
                        if (response.data.found_posts > 0) {
                            pwdms_export_csv();
                            jQuery('.export_successfully').remove();
                            jQuery('#pwdms_csv_export_table').after('<div class="export_successfully"><p>' + MyAjax.export_data + '</p></div>');
                        } else {
                            jQuery('.export_successfully').remove();
                            jQuery('#pwdms_csv_export_table').after('<div class="export_error"><p>' + MyAjax.no_export_data + '</p></div>');
                        }

                    }
                }

            },
        });
    }

    function pwdms_export_csv() {
        jQuery.get(MyAjax.ajaxUrl, {
            action: 'pwdms_export_csv_dummy'
        }).done(function (response) {
            window.location = MyAjax.ajaxurl + '?action=pwdms_export_csv&document_title=' + jQuery('#pwdms_document_title').val()+'&_wpnonce='+MyAjax.security_nonce;
        });
    }
});