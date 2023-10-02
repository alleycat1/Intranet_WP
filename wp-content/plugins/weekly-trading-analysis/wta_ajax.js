function my_action_javascript(){
    var security_nonce = MyAjax.security_nonce;
    jQuery.ajax({
        url: MyAjax.ajaxurl,
        method: "POST",
        data: { category:'asdf', action:'wta_test_action', security_nonce:security_nonce },
        dataType: "json",
        success: function (data) {
            alert(data);
            //alert("success");
        },
        error: function (e) {
            alert(e.status);
            //alert("failed");
        }
    });
}
jQuery(document).ready(function($) {
    //my_action_javascript();
});
