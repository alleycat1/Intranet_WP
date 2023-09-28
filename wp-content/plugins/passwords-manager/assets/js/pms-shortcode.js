(function () {
    tinymce.create('tinymce.plugins.pmsinsertshortcode', {
        init: function (editor, url) {

            //Add button and functionality

            editor.addButton('pwdms_shrtcd', {
                text: 'PWDMS',
                selector: "textarea",  // add shortcode in the textarea
                title: 'Insert password table shortcode',
                icon: false,
                id: 'pwdms_shrtcd_btn',

                onclick: function () {
                    editor.selection.setContent('[pms_pass cat_name=""]');
                }
            });


        }
    });
    tinymce.PluginManager.add('pmsinsertshortcode', tinymce.plugins.pmsinsertshortcode);
})();