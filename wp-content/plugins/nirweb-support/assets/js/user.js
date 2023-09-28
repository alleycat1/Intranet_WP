function alert_success(message) {
    jQuery('body').append(
        `<div class="bg_alert__nirweb">
            <div class="box_alert_nirweb">
            <svg width="60" height="60" enable-background="new 0 2 98 98" version="1.1" viewBox="0 2 98 98" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
<style type="text/css">
\t.st0{fill:url(#b);}
\t.st1{fill:url(#a);}
</style> <linearGradient id="b" x1="57.767" x2="57.767" y1="96" y2="6.3234" gradientTransform="matrix(1 0 0 -1 0 104)" gradientUnits="userSpaceOnUse">
\t\t<stop stop-color="#00EFD1" offset="0"/>
\t\t<stop stop-color="#00ACEA" offset="1"/>
\t</linearGradient>
\t<path class="st0" d="m33.3 45.9c-1.1-1.2-3-1.3-4.2-0.2s-1.3 3-0.2 4.2l15.1 16.4c0.6 0.6 1.3 1 2.1 1h0.1c0.8 0 1.6-0.3 2.1-0.9l38.2-38.1c1.2-1.2 1.2-3.1 0-4.2s-3.1-1.2-4.2 0l-36 35.9-13-14.1z"/>
\t\t<linearGradient id="a" x1="49" x2="49" y1="96" y2="6.3234" gradientTransform="matrix(1 0 0 -1 0 104)" gradientUnits="userSpaceOnUse">
\t\t<stop stop-color="#00EFD1" offset="0"/>
\t\t<stop stop-color="#00ACEA" offset="1"/>
\t</linearGradient>
\t<path class="st1" d="m85.8 50c-1.7 0-3 1.3-3 3 0 18.6-15.2 33.8-33.8 33.8s-33.8-15.2-33.8-33.8 15.2-33.8 33.8-33.8c1.7 0 3-1.3 3-3s-1.3-3-3-3c-21.9 0-39.8 17.9-39.8 39.8s17.9 39.8 39.8 39.8 39.8-17.9 39.8-39.8c0-1.7-1.3-3-3-3z"/>
</svg>
                 <h4>${message}</h4>
                </div>
        </div>`
    )
}function alert_error(message) {
    jQuery('body').append(
        `<div class="bg_alert__nirweb">
            <div class="box_alert_nirweb">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="Capa_1" viewBox="0 0 384 384" style="enable-background:new 0 0 384 384"><g><g><path d="M368 176c-8.832.0-16 7.168-16 16 0 88.224-71.776 160-160 160S32 280.224 32 192 103.776 32 192 32c42.952.0 83.272 16.784 113.536 47.264 6.224 6.264 16.36 6.304 22.624.08 6.272-6.224 6.304-16.36.08-22.632C291.928 20.144 243.536.0 192 0 86.128.0.0 86.128.0 192s86.128 192 192 192c105.864.0 192-86.128 192-192C384 183.168 376.832 176 368 176z" style="fill:#e43539"/></g></g><g style="fill:#e43539;"><g><path d="M214.624 192l36.688-36.688c6.248-6.248 6.248-16.376.0-22.624s-16.376-6.248-22.624.0L192 169.376l-36.688-36.688c-6.24-6.248-16.384-6.248-22.624.0-6.248 6.248-6.248 16.376.0 22.624L169.376 192l-36.688 36.688c-6.248 6.248-6.248 16.376.0 22.624C135.808 254.44 139.904 256 144 256s8.192-1.56 11.312-4.688L192 214.624l36.688 36.688C231.816 254.44 235.904 256 240 256s8.184-1.56 11.312-4.688c6.248-6.248 6.248-16.376.0-22.624L214.624 192z"/></g></g><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/></svg>
                 <h4>${message}</h4>
                </div>
        </div>`
    )
}

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
jQuery(document).ready(function () {
      //---------------- Ajax Filter Ticket

    jQuery('body').on('click', '.filter_ajax_wpytu', function () {
        let status_id = jQuery('#select_wpytu_status option:selected').val()
        let asns = jQuery('#selcet_filter_ans option:selected').val()
        jQuery('.lds-dual-ring').css('display', 'flex')
        jQuery.ajax({
            url: wpyarticket.ajax_url,
            type: "POST",
            data: {
                status_id,
                asns,
                action: "filter_ajax_ticket"
            },
            success: function (data) {
                 
                jQuery('.wpyt_table tbody').html(data);
                jQuery('.lds-dual-ring').css('display', 'none')

            },
        })
    })


 

//------------- Remove Upload By user

jQuery('body').on('click','.remove_file_by_user',function(e){
                 e.preventDefault();
                 e.stopPropagation();
jQuery('.wpyar_upfile_base').html(`<div class="upfile_wpyartick">
             
        <label for="main_image" class="label_main_image">
             <span class="remove_file_by_user"><i class="fal fa-times-circle"></i></span>  
            <i class="fal fa-arrow-up upicon" style="font-size: 30px;margin-bottom: 10px;"></i>
            <span class="text_label_main_image">${wpyarticket.attach_file}</span>
   
        </label>

        <input type="file" name="main_image" id="main_image" accept=".png,.jpg,.jpeg">
            
            </div>`)

})

//------------------- Send Ticket ----------------------

    //------------------- FAQ  --- Start
    
    jQuery('body').on('click','.li_list_of_faq_wpyar',function(e){
        jQuery(this).parent('li').toggleClass('open');
        jQuery(this).parent('li').find('.content_faq_wpyar').slideToggle(150);
    })
    
    jQuery('body').on('click','.not_found_answer span',function(e){
            jQuery('#send_ticket_form').slideDown(250);
            jQuery('.list_of_faq_wpyar').remove();
            jQuery('.not_found_answer').remove();
            
    })
    
    //------------------- FAQ  --- End
    
    //------------- Start Custom Select For send Ticket
    
    jQuery('.select_custom_wpyar').click(function(e){
                  e.stopPropagation();
        jQuery('.select_custom_wpyar').find('i').removeClass('top');
        jQuery('.select_custom_wpyar').find('ul').fadeOut();
                  e.stopPropagation();
        jQuery(this).children('i').toggleClass('top')
        jQuery(this).children('ul').fadeToggle();
          e.stopPropagation();
    })
    
    //------ Preview Image Attach file
    
    
    function readURL(input) {
 
        var formData = new FormData();
        formData.append('updoc', jQuery('input[type=file]')[0].files[0]);
        jQuery('.text_label_main_image').html(input.files[0]['name'])
        jQuery('.upicon').remove()
       
     }
  jQuery('body').on('change','#main_image',function(e){
        jQuery('.remove_file_by_user').fadeIn();
      readURL(this);
    });
    
    //-------------------- End Custom Select For send Ticket
    
    jQuery('.select_custom_wpyar ul li').click(function(e){
        var text_li = jQuery(this).text()
        var date_id_li = jQuery(this).attr('data-id')
        var data_user_li = jQuery(this).attr('data-user')
        var tar_get = jQuery(this).parents('.select_custom_wpyar').find('.custom_input_wpyar_send_ticket')
        jQuery(tar_get).text(text_li)
        jQuery(tar_get).attr('data-id',date_id_li)
        jQuery(tar_get).attr('data-user',data_user_li)
            
        jQuery(this).parents('.select_custom_wpyar').find('i').removeClass('top');
        jQuery(this).parents('ul').fadeOut();
                 e.preventDefault();
                 e.stopPropagation();
    })
    
    
    //------------- End Custom Select For send Ticket
    
    
//---------------- Remove Custom List After Click in body an Other Place    
    jQuery('body').click(function(e){
         jQuery('.select_custom_wpyar').children('ul').fadeOut();
         jQuery('.select_custom_wpyar').find('i').removeClass('top');
        
    })
    
//----------------- Filter List Ticket    
 jQuery('body').on('click','.col_box_status_ticket_wpyar',function(event){
    jQuery('.ajax_result').html('<h3 style="color:red;font-weight: 400;text-align: center;font-size: 18px;">'+wpyarticket.recv_info+'</h3>')
    var status = jQuery(this).attr('id');
    jQuery.ajax({
        url: wpyarticket.ajax_url,
        type: "post",
        data: {
            status,
            action: "filtter_ticket_status",
            once:jQuery('#nirweb_ticket_filtter_ticket_status').val()
                    },
        success: function (response) {
           jQuery('.ajax_result').html(response)
            return false;
        },

    })
 })


    });/////------------------ End document ready
