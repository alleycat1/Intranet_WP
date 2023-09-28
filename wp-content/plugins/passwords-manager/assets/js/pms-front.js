function getpwd(th) {
    var temp = th.id.split('_');
    var pid = temp[1];
    var act = jQuery(th).attr('class');
    var crypwd = jQuery('#user_pwd' + pid).val();
    if (act == 'decrypt') {
        var key = crypwd;
        var btn_action = act;
    } else {
        var did = jQuery('#' + th.id).data('id');
        var key = crypwd;
        var btn_action = act;
    }
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { user_pwd: key, saction: btn_action, did: did, module: 'password', action: "decrypt_pass",security_nonce : MyAjax.security_nonce },
        success: function (rest) {
            var input = jQuery('#user_pwd' + pid);
            if (input.attr("type") == "password") {
                jQuery('#' + th.id).find('i').removeClass("fa-eye").addClass('fa-eye-slash');
                input.attr("value", rest);
                input.attr("type", "text");
                jQuery('#' + th.id).attr("class", "encrypt"); // decrypt
            } else {
                jQuery('#' + th.id).find('i').removeClass("fa-eye-slash").addClass('fa-eye');
                input.attr("type", "password");
                input.attr("value", rest);
                jQuery('#' + th.id).attr("class", "decrypt");
            }
        }
    });
}

//page load get record
jQuery(document).ready(function () {
    var count = jQuery('.cname').length;
    var i;
    for (i = 1; i <= count; i++) {
        var cname = jQuery('#cname_' + i).html();
        jQuery('#front_pass_table_' + i).DataTable({
            "processing": false,
            "paging": true,
			"responsive": true,
            "language": {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw text-white"></i>',
            },
            "bLengthChange": false,
            "bFilter": false,
            "bInfo": true,
            "bAutoWidth": true,
            "serverSide": true,
            "ajax": {
                url: MyAjax.ajaxurl,
                type: "POST",

                data: {
                    "action": 'get_new_pass',
                    "cat_name": cname,
                    "security_nonce" : MyAjax.security_nonce 
                }
            },
            "columnDefs": [
                {
                    "targets": [1, 2],
                    "orderable": false,
                },
            ],

            "pageLength": 5,
        });
        jQuery('#cname_' + i).css('display', 'none');
    }
});


