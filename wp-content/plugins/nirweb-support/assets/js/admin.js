function delete_alert_nirweb(form) {
    jQuery('body').append(
    `<div class="bg_alert__nirweb">
            <div class="box_alert_nirweb">
            <svg xmlns="http://www.w3.org/2000/svg" height="50pt" viewBox="-21 0 512 512" width="50pt"><path d="m448 232.148438c-11.777344 0-21.332031-9.554688-21.332031-21.332032 0-59.839844-23.296875-116.074218-65.601563-158.402344-8.339844-8.339843-8.339844-21.820312 0-30.164062 8.339844-8.339844 21.824219-8.339844 30.164063 0 50.371093 50.367188 78.101562 117.335938 78.101562 188.566406 0 11.777344-9.554687 21.332032-21.332031 21.332032zm0 0" fill="#4caf50"></path><path d="m21.332031 232.148438c-11.773437 0-21.332031-9.554688-21.332031-21.332032 0-71.230468 27.734375-138.199218 78.101562-188.566406 8.339844-8.339844 21.824219-8.339844 30.164063 0 8.34375 8.34375 8.34375 21.824219 0 30.164062-42.304687 42.304688-65.597656 98.5625-65.597656 158.402344 0 11.777344-9.558594 21.332032-21.335938 21.332032zm0 0" fill="#4caf50"></path><path d="m320 426.667969c0 47.128906-38.203125 85.332031-85.332031 85.332031-47.128907 0-85.335938-38.203125-85.335938-85.332031 0-47.128907 38.207031-85.335938 85.335938-85.335938 47.128906 0 85.332031 38.207031 85.332031 85.335938zm0 0" fill="#ffa000"></path><path d="m234.667969 85.332031c-11.777344 0-21.335938-9.554687-21.335938-21.332031v-42.667969c0-11.773437 9.558594-21.332031 21.335938-21.332031 11.773437 0 21.332031 9.558594 21.332031 21.332031v42.667969c0 11.777344-9.558594 21.332031-21.332031 21.332031zm0 0" fill="#ffa000"></path><path d="m434.753906 360.789062c-32.257812-27.265624-50.753906-67.09375-50.753906-109.3125v-59.476562c0-82.347656-67.007812-149.332031-149.332031-149.332031-82.328125 0-149.335938 66.984375-149.335938 149.332031v59.476562c0 42.21875-18.496093 82.070313-50.941406 109.503907-8.300781 7.082031-13.058594 17.429687-13.058594 28.351562 0 20.589844 16.746094 37.335938 37.335938 37.335938h352c20.585937 0 37.332031-16.746094 37.332031-37.335938 0-10.921875-4.757812-21.269531-13.246094-28.542969zm0 0" fill="#ffc107"></path></svg>
                <h4>${wpyarticket.ques}</h4>
                <p>${wpyarticket.subdel}</p>
                    <div class="btns_alert_nirweb">
                            <button class="cancel">${wpyarticket.cancel}</button>
                            <button class="send">${wpyarticket.ok}</button>
                     </div>
                </div>
        </div>`
        )

    jQuery('body').on('click', '.bg_alert__nirweb', function (e) {
        jQuery(this).hide();
    })
    jQuery('body').on('click', '.box_alert_nirweb .cancel', function (e) {
        jQuery('.bg_alert__nirweb').hide();
    })


    jQuery('body').on('click', '.box_alert_nirweb', function (e) {
        e.preventDefault();
        e.stopPropagation();

    })


}

//------------ Delay For Search Ajax ---------
function delay_nirweb_ticket(callback, ms) {
    var timer = 0;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}
//-------------------------- Editor Wordpress ---------------------------//
   function tmce_getContent(editor_id, textarea_id) {
    if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
    if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;
    
    if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
      return tinyMCE.get(editor_id).getContent();
    }else{
      return jQuery('#'+textarea_id).val();
    }
  }
    function tmce_setContent(content, editor_id, textarea_id) {
    if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
    if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;
    
    if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
      return tinyMCE.get(editor_id).setContent(content);
    }else{
      return jQuery('#'+textarea_id).val(content);
    }
  }
    function tmce_focus(editor_id, textarea_id) {
    if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
    if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;
    
    if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
      return tinyMCE.get(editor_id).focus();
    }else{
      return jQuery('#'+textarea_id).focus();
    }
  }
  jQuery(document).ready(function () {
      //----------------- Select All CheckBox
      jQuery('body').on('click', '#selectAll', function (e) {
          jQuery(this).closest('table').find('tbody th input:checkbox').prop('checked', this.checked);
      });


      //------------- Siplay Search Box Admin      
      jQuery('.box_search_del i.search').click(function(e){
            jQuery('.ajax_search').slideToggle(150)
      })
 

//---------------- Search In Table Ticket ---------------------//
jQuery("#serch_support_wpy").on("keyup", delay_nirweb_ticket(function () {
    var value = jQuery(this).val();
    let once = jQuery("#nirweb_ticket_ajax_search").val()

         if(value){
             jQuery('.ajax_search_loading_ticket').show();
            jQuery.ajax({
                url: wpyarticket.ajax_url,
                type: "post",
                data: {
                    value,once,
                    action: "ajax_search_in_ticketes_wpyar",
                        },
                success: function (response) {
                      jQuery('.ajax_search_loading_ticket').hide();
                            jQuery('.ajax_search ul').slideDown(150).html(response);
                    return false;
                },
            })
        }else{
              jQuery('.ajax_search_loading_ticket').hide();
            jQuery('.ajax_search ul').hide()      
        }
},500) );
//---------------- Select type user
    jQuery('.nirweb_ticket_frm__receiver').click(function () {
        jQuery('.nirweb_ticket_frm_list_type_receiver').slideDown(200);
    });
    jQuery('.nirweb_ticket_frm_list_type_receiver li').click(function () {
        var text = jQuery(this).text();
        jQuery('.nirweb_ticket_frm__receiver').val(text);
        jQuery('.nirweb_ticket_frm_list_type_receiver').slideUp(150);
    })
//---------------- GET Resiverd User ----------------//
        jQuery('.nirweb_ticket_frm_type_receiver select').change(function () {
        jQuery('.nirweb_ticket_frm_final_items_receiver select').html('<option>'+wpyarticket.send_info+'</option>');
        var selectedtypsender = jQuery(this).children("option:selected").val();
        let once = jQuery("#admin_send_ticket").val();
           jQuery.ajax({
                url: wpyarticket.ajax_url,
                type: "post",
                data: {
                action: "send_type_role_user",
                selectedtypsender,once
                },
                success: function (response) {
                 jQuery('.nirweb_ticket_frm_final_items_receiver select').html(response);
                    return false;
                },

            })
        return false;
    
    })
    jQuery("#selUser").select2();
    jQuery("#nirweb_ticket_frm_product_send_ticket").select2();
//---------------- upload file ----------------//
    jQuery(function (jQuery) {
        jQuery('body').on('click', '.wpyt_upload_image_button', function (e) {
            e.preventDefault();
            var button = jQuery(this),
                custom_uploader = wp.media({
                    title: wpyarticket.add_file,
                    library: {
                        type: 'image'
                    },
                    button: {
                        text:  wpyarticket.use_file // button label text
                    },
                    multiple: false // for multiple image selection set to true
                }).on('select', function () { // it also has "open" and "close" events
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    jQuery(button).removeClass('button').html('<img id="true_pre_image" class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />').prev().val(attachment.url).next().show();
                    
                })
                    .open();
        });
        jQuery('body').on('click', '.misha_remove_image_button', function () {
            jQuery(this).hide().prev().val('').prev().addClass('button').html('Upload image');
            return false;
        });

    });
//---------------- Send Ticket ----------------//
jQuery('.btn_send_ticket').click(function (e) {
 
            let check_function = jQuery('.btn_send_ticket').attr('data-name')
            var check_function_id = jQuery('.btn_send_ticket').attr('data-id')
            let receiver =jQuery('#selUser').val();
            let receiver_name =jQuery('#selUser option:selected').text();
            let receiver_mail =jQuery('#selUser option:selected').attr('data-mail');
            let tyreceiver =jQuery('#nirweb_ticket_frm_type_receiver').val();
            let subj =jQuery('#nirweb_ticket_frm_subject_send_ticket').val();
            let check_mail = false;
            let send_content =tmce_getContent('nirweb_ticket_frm_custom_editor');   
            let id_receiver =jQuery('#selUser option:selected').val() ;   
            let receiver_type =jQuery('#nirweb_ticket_frm_type_receiver option:selected').val() ;   
            let department =jQuery('#nirweb_ticket_frm_department_send_ticket option:selected').text();
            let priority=jQuery('#nirweb_ticket_frm_priority_send_ticket option:selected').text();
            let priority_id=jQuery('#nirweb_ticket_frm_priority_send_ticket option:selected').val();
            let department_id=jQuery('#nirweb_ticket_frm_department_send_ticket option:selected').val();
            let subject=jQuery('#nirweb_ticket_frm_subject_send_ticket').val();
            let website=jQuery('#nirweb_ticket_frm_website_send_ticket').val();
            let product=jQuery('#nirweb_ticket_frm_product_send_ticket option:selected').val();
            let status=jQuery('#nirweb_ticket_frm_status_send_ticket option:selected').val();
            let once = jQuery('#admin_send_ticket_nirweb').val();
            let file_url=jQuery('#true_pre_image').attr('src');
            
            
 
            
            if (receiver && tyreceiver !=0 && subj &&send_content ){
                if (jQuery('#chk_email').is(':checked')) {
                   check_mail = true;
                }
            }else{
                alert(wpyarticket.comp_sec);
                return false;
            }
 
            
            jQuery.ajax({
                url: wpyarticket.ajax_url,
                type: "post",
                data: {
                    id_receiver,receiver_type,subject,send_content,department_id,priority_id,website,product,status,file_url,
                    action: "send_new_ticket",
                    check: check_function,
                    check_id: check_function_id,
                    check_mail:check_mail,
                    receiver_name:receiver_name,
                    receiver_mail:receiver_mail,
                    department,
                    priority,once
                },
                success: function (response) {
                    jQuery('#send_form_ticket').trigger('reset');
                    jQuery("#select2-nirweb_ticket_frm_type_receiver-container").empty();
                    jQuery("#select2-selUser-container").empty();
                    jQuery("#select2-nirweb_ticket_frm_priority_send_ticket-u3-container").empty();
                    jQuery("#select2-nirweb_ticket_frm_department_send_ticket-82-container").empty();
                    jQuery("#select2-nirweb_ticket_frm_product_send_ticket-container").empty();
                    alert(wpyarticket.send_tik_success);
                    return false;
                },

            })
        return false;
    });
//----------------- Answered Tickets ---------------//
    jQuery('.display_content_ticket h3').click(function (e) {
        jQuery(this).parent('.display_content_ticket').find('.text_ticket').slideToggle(250)
    })
//------------------- Edit Ticket ----------------//
jQuery("body").on("click", ".war_pre_answer_wp_yar", function (e) {
    e.stopPropagation()
e.preventDefault()
    jQuery('.list_pre_Answer_wp_yar ').slideToggle(200);
})
jQuery("body").on("click", ".insert_text_into_editor_wp", function (e) {
    e.stopPropagation()
    e.preventDefault()
       var text_pre_answerd= jQuery(this).parents('.li_list_question').find('.answer_wpys_faq').html()
       tmce_setContent( text_pre_answerd, 'nirweb_ticket_answer_editor' );
    })
jQuery("body").on("click", ".btn_send_answered", function (e) {
    
     old_text =  jQuery(this).text();
    jQuery('.base_loarder').css('display', 'flex');
    let send_content_answer =tmce_getContent('nirweb_ticket_answer_editor');

        jQuery.ajax({
            url: wpyarticket.ajax_url,
            type: "POST",
            data: {
                   content  : send_content_answer,
                   id_form  : jQuery('#send_answerd_ticket').attr('data-id'),
                   file_url : jQuery('#nirweb_ticket_frm_file_send_ticket').val(),
                   department  : jQuery('#nirweb_ticket_frm_department_send_ticket').val(),
                   department_name  : jQuery('#nirweb_ticket_frm_department_send_ticket option:selected').text(),
                   status : jQuery('#nirweb_ticket_frm_status_send_ticket').val(),
                   proname : jQuery('.proname').text(),
                   status_name : jQuery('#nirweb_ticket_frm_status_send_ticket option:selected').text(),
                   sender_id : jQuery('.sender').attr('user-id'),
                   resivered_id : jQuery('.resivered').attr('data-id'),
                   subject : jQuery('.subject').text(),
                   once : jQuery('#admin_answer_nirweb_ticker').val(),

                   action : "answerd_ticket",
                    },
            success: function (response) {
                jQuery('.base_loarder').css('display', 'none');
                jQuery('#send_answerd_ticket').trigger('reset');
                alert(wpyarticket.send_ans_success);
                jQuery('.list_all_answered').html(response) 
                return false;
            },
            error: function (response) {
                jQuery('.base_loarder').css('display', 'none');
                alert(wpyarticket.send_ans_err);
                return false;
            },

        }) 
        return false;
      
})
//---------------- Delete Tickets ----------------//
jQuery("body").on("click", "#frm_btn_delete", function (t) {

    var checkeds = new Array();
    jQuery('input[name="frm_check_items[]"]:checked').each(function (i) {
        checkeds.push(jQuery(this).val());
    });
     if(checkeds.length <=0){
            alert(nirwebTicketAdmin.select_req)
    }else{
        delete_alert_nirweb();
        jQuery('body').on('click', '.box_alert_nirweb .send', function (e) {
            jQuery.ajax({
                url: wpyarticket.ajax_url,
                type: "post",
                 data: {
                    check: checkeds,
                    action: "delete_tickets_admin",
                     once:jQuery('#delete_ticket_admin').val()
                },
                success: function (response) {
                  location.reload()
                },
            })
        });
    }
 
    })
//---------------- Add Department  ----------------//
jQuery("body").on("click", "#submit_new_department", function (e) { 
    var department_name =  jQuery('#nirweb_ticket_name_department').val();
    var once = jQuery('#add_department_wpyt_once').val();
    if(!department_name){
        alert(wpyarticket.name_dep_err);
        return false
    } 
    var id_poshtiban =  jQuery('#nirweb_ticket_support_department option:selected').val();
            if(id_poshtiban == '-1'){
                alert(wpyarticket.sup_dep_err);
                return false
            } 
    jQuery.ajax({
        url: wpyarticket.ajax_url,
        type: "post",
         data: {
            department_name: department_name,
            id_poshtiban: id_poshtiban,
            action: "add_department_wpyt",
             once
        },
        success: function (response) {
            window.location.reload();       
        },
    })
    return false;
})
//---------------- Delete Departments ----------------//
jQuery("body").on("click", "#frm_btn_delete_dep", function (t) {

    var checkeds = new Array();
    jQuery('input[name="frm_check_items[]"]:checked').each(function (i) {
        checkeds.push(jQuery(this).val());
    });
     if(checkeds.length <=0){
            alert(wpyarticket.select_req)
    }else{
        delete_alert_nirweb();
        jQuery('body').on('click', '.box_alert_nirweb .send', function (e) {
            jQuery.ajax({
                url: wpyarticket.ajax_url,
                type: "post",
                 data: {
                    check: checkeds,
                    action: "delete_department",
                     once:jQuery('#del_department_wpyt_once').val()
                },
                success: function (response) {
                    location.reload()
                },
            })
        });
    }

  
})
//---------------- Edite Departments ----------------//
jQuery("body").on("click", ".edit_dep_wpys", function (t) {
    jQuery('#update_department').remove();
    jQuery('#cancel_department').remove();
    jQuery('#id_row').remove();
     var row=  jQuery(this).parents('.row_dep');
     var user = row.find('.name_user').attr('data-user_id')
     var dep_name = row.find('.dep_name').text()
     var dep_id = row.find('.dep_name').attr('data-id')
     jQuery('#nirweb_ticket_name_department').val(dep_name);
     jQuery('#nirweb_ticket_support_department').val(user);
     jQuery('#submit_new_department').addClass('');
     jQuery('#submit_new_department').remove();
     jQuery('form').append('<button id="update_department" class="sueccess">'+wpyarticket.ok+'</button>')
     jQuery('form').append('<button id="cancel_department" class="warning">'+wpyarticket.cancel+'</button>')
     jQuery('form').append(" <?php wp_nonce_field( 'edit_department_wpyt_once_act', 'edit_department_wpyt_once' ); ?>")
     jQuery('form').append('<input type="hidden" id="id_row" value="'+dep_id+'">');
     t.preventDefault();
})

//---------------- Cancell  Edite Departments ----------------//
jQuery("body").on("click", "#cancel_department", function (t) {
    jQuery('#nirweb_ticket_name_department').val('');
    jQuery('#nirweb_ticket_support_department').val('-1');
    jQuery('#update_department').remove();
    jQuery('#cancel_department').remove();
    jQuery('form').append('<button name="submit_new_department" id="submit_new_department" class="button button-primary">'+wpyarticket.add_dep+'</button>');
    return false;
})
//---------------- Cancell  Edite Departments ----------------//
jQuery("body").on("click", "#update_department", function (t) {
    var department_name =  jQuery('#nirweb_ticket_name_department').val()
     if(!department_name){
        swal(wpyarticket.name_dep_err , "", "error");
        return false
    } 
    var id_poshtiban =  jQuery('#nirweb_ticket_support_department option:selected').val();
     var depa_id = jQuery('#id_row').val();
    jQuery.ajax({
        url: wpyarticket.ajax_url,
        type: "post",
         data: {
            department_name: department_name,
            id_poshtiban: id_poshtiban,
            depa_id: depa_id,
            action: "edite_department",
             once:jQuery('#add_department_wpyt_once').val()
        },
        success: function (response) {
            swal(wpyarticket.chenge_dep , "", "success");
             setTimeout(() => {
                 window.location.reload();
             }, 2000);  
        }
    })
    t.preventDefault();
})
//------------------- FAQ ------------------//
jQuery("body").on("click", ".question_wpy_faq", function (t) {

        jQuery(this).parents('.li_list_question').find('.answer_wpys_faq').slideToggle(150);
        jQuery(this).find('.arrow_wpyt').toggleClass('cret').toggleClass('cret_t');
        t.stopPropagation()
          t.preventDefault()
        });
//---------------- Add FAQ ----------------//
jQuery("body").on("click", "#submit_new_faq", function (t) {  
        t.preventDefault();
        var text_question_faq = jQuery('#nirweb_ticket_frm_subject_faq_ticket').val();
        if(!text_question_faq){
            alert(wpyarticket.add_ques_err);
            return false
        } 
        var  content_question_faq =tmce_getContent('nirweb_ticket_frm_faq_ticket');
        if(!content_question_faq){
            alert(wpyarticket.add_text_faq_err);
            return false
        } 
  jQuery.ajax({
        url: wpyarticket.ajax_url,
        type: "post",
         data: {
            text_question_faq: text_question_faq,
            content_question_faq: content_question_faq,
             action: "add_question_faq",
             once:jQuery('#add_question_faq_once').val()
        },
        success: function (response) {
            window.location.reload(); 
        }
    })
})
//---------------- Remove FAQ ----------------//
jQuery("body").on("click", ".remove_faq", function (e) {  

    var col_id = jQuery(this).attr('data-id');
        delete_alert_nirweb();
        jQuery('body').on('click', '.box_alert_nirweb .send', function (e) {

            jQuery.ajax({
                url: wpyarticket.ajax_url,
                type: "post",
                 data: {
                   col_id,
                    action: "delete_faq",
                     once:jQuery('#del_question_faq_once').val()
                },
                success: function (response) {
                    location.reload()
                },
            })
        });

 
})

//--------------------- Delete Files in settings


jQuery("body").on("click", "#frm_btn_delete_files_users", function (t) {
       if (confirm(wpyarticket.subdel)) {
               t.preventDefault();
            var checkeds = new Array();
            var checkeds_id_file = new Array();
            jQuery('input[name="frm_check_items[]"]:checked').each(function(i) {
                checkeds.push(jQuery(this).val());
                checkeds_id_file.push(jQuery(this).attr('data-file'));
            });
              
           jQuery.ajax({
                url: wpyarticket.ajax_url,
                type: "POST",
                 data: {
                      once: jQuery('#admin_del_files').val(),
                    check: checkeds,
                    checkeds_id_file: checkeds_id_file,
                    action: "ticket_wpyar_file_user_delete",
                   
                },
                success: function (response) {
                     location.reload()
                },
            })
        } 
       
    })

 //-------------- Setting -----
    jQuery('.list_tabs_settings li a').on('click',function (){
        jQuery('.list_tabs_settings li a').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('.content_settings >div').hide();
        jQuery('#'+ jQuery(this).attr('data-toggle')).show();
    });



})//-------------- end document ready