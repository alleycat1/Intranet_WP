function getpwd(th) {
	var temp = th.id.split('_');
	var pid = temp[1];
	var act = jQuery(th).attr('class');
	var security_nonce = MyAjax.security_nonce;
	var crypwd = jQuery('#user_pwd' + pid).val();
	if (act == 'decrypt') {
		var key = crypwd;
		var btn_action = act;
	} else {
		var did = jQuery('#pwds_data #' + th.id).data('id');
		var key = crypwd;
		var btn_action = act;
	}
	jQuery.ajax({
		url: MyAjax.ajaxurl,
		method: "POST",
		data: { user_pwd: key, saction: btn_action, did: did, security_nonce:security_nonce, module: 'password', action: "decrypt_pass" },
		success: function (rest) {
			show_multi_action();
			var input = jQuery('#pwds_data #user_pwd' + pid);
			if (input.attr("type") == "password") {
				jQuery('#pwds_data #' + th.id).find('.fa').removeClass("fa-eye").addClass('fa-eye-slash');
				input.attr("type", "text");
				input.attr("value", rest);
				jQuery('#pwds_data #' + th.id).attr("class", "encrypt"); // decrypt
			} else {
				jQuery('#pwds_data #' + th.id).find('i').removeClass("fa-eye-slash").addClass('fa-eye');
				input.attr("type", "password");
				input.attr("value", rest);
				jQuery('#pwds_data #' + th.id).attr("class", "decrypt");
			}
			
		}
	});
}

jQuery(document).ready(function () {

	var clipboard = new ClipboardJS('.copy_clipboard');
    var security_nonce = MyAjax.security_nonce;
	//add password from reset
	jQuery('#add_user_pass').click(function () {
		jQuery('#pwds_form')[0].reset();
		jQuery('#user_name').css('border', '1px solid #7e8993');
		jQuery('#user_err').css('display', 'none');
		jQuery('.modal-title').html("<i class='fa fa-plus'></i>"+MyAjax.add_password);
		jQuery('#saction').val(MyAjax.add);
		jQuery('#btn_action').val('Add');
		jQuery('#saction').attr('disabled', false);
		jQuery('#pass_category').removeClass('cat_wrng');
		jQuery('#slct_wrng').css('display', 'none');
	});
	jQuery('.close').click(function () {
		jQuery('#wcbnl_overlay').fadeOut(1000);

	});

	var userdataTable = jQuery('#pwds_data').DataTable({
		"processing": true,
		"order": [[1, 'desc']],
		"language": {
			"processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw border-0"></i>',
			"lengthMenu": MyAjax.shw_menu+" _MENU_",
			"sSearch": MyAjax.search_bar+':',
			"sZeroRecords": MyAjax.no_records,
			"sInfo":  MyAjax.filter_record,
			"sInfoEmpty": MyAjax.empty_record,
			"oPaginate": {
				"sNext": MyAjax.nxt_pg,   
				"sPrevious": MyAjax.prev_pg,
			},
			
		},
		"serverSide": true,
		"ajax": {
			url: MyAjax.ajaxurl,
			type: "POST",

			data: {
				"module": 'password',
				"action": 'get_new_pass',
				'security_nonce':security_nonce
			}
		},
		"columnDefs": [
			{
				"targets": [1, 2],
				"orderable": false,
			},
			{className: "td-text-center text-center", targets: [6,5,3]},
			{className: "td-text-center text-end", targets: [4]},
			{width    : "150px", targets: [6]},
		],

		"pageLength": 10,
		rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
	});


	jQuery(document).on('submit', '#pwds_form', function (event) {
		event.preventDefault();
		let encryption = new Encryption();//include encrypt js class
			
		jQuery('#saction').attr('disabled', false);
		var user_name = jQuery('#user_name').val();
		var user_email = jQuery('#user_email').val();
		var user_pass = jQuery('#user_password').val();
		var pass_cat = jQuery('#pass_category').val();
		var user_note = jQuery('#user_note').val();
		var user_url = jQuery('#user_url').val();
		var pass_id = jQuery('#pwds_id').val();
		var enc_key = jQuery('#setting_key_enc').val();
		var btn_action = jQuery('#btn_action').val();
		if (pass_cat === '') {
			jQuery('#pass_category').addClass('cat_wrng');
			jQuery('#slct_wrng').css('display', 'block');			
		} else {
			
			var enc_pass = encryption.encrypt(user_pass, enc_key);//encrypt password by js
			jQuery.ajax({
				url: MyAjax.ajaxurl,
				method: "POST",
				data: { ency: enc_pass, pass_id: pass_id, user_name: user_name, user_email: user_email, user_url: user_url, pass_cat: pass_cat, user_note: user_note, btn_action: btn_action, security_nonce:security_nonce, action: 'post_new_pass' }, //form_data,
				dataType: "json",
				success: function (data) {
					var blank = data.blnkspc;
					if (blank === "blank") {
						jQuery('#user_name').css('border', '1px solid red');
						jQuery('#user_err').css('display', 'block');
					} else if (data) {
						jQuery('#pwdsModal').modal('hide');
						jQuery('#wcbnl_overlay').show();	
						jQuery('#wcbnl_overlay').css("z-index", "1");
						jQuery('#pwdsModal').css("z-index", "1050");
						jQuery('#pwds_form')[0].reset();
						jQuery('#alert_action').fadeIn().html('<div class="alert alert-success">' + data + '</div>');
						jQuery('#saction').attr('disabled', false);
						jQuery('#pass_category').removeClass('cat_wrng');
						jQuery('.modal-backdrop').remove();
						jQuery('#slct_wrng').css('display', 'none');
						jQuery('#wcbnl_overlay').fadeOut(1000);

						userdataTable.ajax.reload();
						show_multi_action();
					} else {
						alert('There is some problem. Data is not added.');
					}
				},
				error: function () {
					alert("You do not have any change. Please change first");
					jQuery('#wcbnl_overlay').fadeOut(1000);
					jQuery('#saction').attr('disabled', false);
				}
			});
		}
	});

	jQuery(document).on('click', '.update', function () {
		jQuery('#wcbnl_overlay').show();
		jQuery('#user_name').css('border', '1px solid #ddd');
		jQuery('#user_err').css('display', 'none');
		jQuery('#saction').attr('disabled', false);
		var pass_id = jQuery(this).attr("id");
		var btn_action = 'fetch_single';	
		jQuery.ajax({
			url: MyAjax.ajaxurl,
			method: "POST",
			data: { pass_id: pass_id, btn_action: btn_action,  security_nonce:security_nonce, module: 'password', action: 'edit_pass' },
			dataType: "json",
			success: function (data) {
				jQuery('#pwdsModal').modal('show');
				jQuery('#user_name').val(data.user_name);
				jQuery('#user_email').val(data.user_email);
				jQuery('#pass_category').val(data.user_category);
				jQuery('#user_password').val(data.user_password);
				jQuery('#user_note').val(data.user_note);
				jQuery('#user_url').val(data.user_url);
				jQuery('.modal-title').html('<i class="fas fa-edit"></i>'+MyAjax.edit_password);
				jQuery('#pwds_id').val(pass_id);
				jQuery('#saction').val(MyAjax.edit);
				jQuery('#btn_action').val("Edit");
				jQuery('#user_password').attr('required', false);
				jQuery('#wcbnl_overlay').fadeOut(1000);
				show_multi_action();
			},
		});
	});

	jQuery(document).on('click', '.note_preview', function () {
		jQuery('#wcbnl_overlay').css('display','block');
		jQuery('.modal-title').html('<i class="fa fa-sticky-note-o" aria-hidden="true"></i> Note');
		var pass_id = jQuery(this).attr("id");
		var btn_action = 'fetch_single';	
		jQuery.ajax({
			url: MyAjax.ajaxurl,
			method: "POST",
			data: { pass_id: pass_id, btn_action: btn_action, security_nonce:security_nonce, action: 'edit_pass' },
			dataType: "json",
			success: function (data) {
				jQuery('#user_note_view').val(data.user_note);
				jQuery('#pwdsnoteModal').modal('show');
				jQuery('#wcbnl_overlay').fadeOut(1000);
				show_multi_action();
			},
		});
	});



	jQuery(document).on('click', '.dlt', function () {
		//jQuery('#wcbnl_overlay').show();
		var pass_id = jQuery(this).attr('id');
		var btn_action = 'Delete';
		Swal.fire({			
			title: MyAjax.del_pswd_txt,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonText: MyAjax.cncl_btn_text,
			cancelButtonColor: '#d33',
			confirmButtonText: MyAjax.cnfrm_btn_text,

			preConfirm: (login) => {
				jQuery.ajax({
					url: MyAjax.ajaxurl,
					method: "POST",
					dataType: "json",
					data: { pass_id: pass_id, btn_action: btn_action, security_nonce:security_nonce, module: 'password', action: 'post_new_pass' },
					success: function (data) {
						jQuery('#alert_action').fadeIn().html('<div class="alert alert-info">' + data + '</div>');						
						userdataTable.ajax.reload();
						show_multi_action();
					},
					error: function () {
						alert('failure');
					}
				});
			},
		  }).then((result) => {
			if (result.value) {
			  Swal.fire({
				position: 'center',
				icon: 'success',
				title: MyAjax.deleted_title,
				text: MyAjax.deleted_text,
				showConfirmButton: false,
				timer: 2000,
				
			  });
			}
		  });
	});

	//before add see password	
	jQuery(".toggle-password").click(function () {
		jQuery(this).toggleClass("fa-eye fa-eye-slash");
		var input = jQuery(jQuery(this).attr("toggle"));
		if (input.attr("type") === "password") {
			input.attr("type", "text");
		} else {
			input.attr("type", "password");
		}
	});
	// 	jQuery('[data-toggle="tooltip"]').tooltip();
	
	// jQuery('.clonepass').each(function(){
		jQuery(document).on('click','.clonepass',function(){
			jQuery('#wcbnl_overlay').show();
			var pass_id = jQuery(this).attr("id");
			var btn_action = 'clone_record';	
			jQuery.ajax({
				url: MyAjax.ajaxurl,
				method: "POST",
				data: { pass_id: pass_id, btn_action: btn_action, security_nonce:security_nonce, action: 'clone_password' },
				// dataType: "json",
				success: function (data) {
					if(data == 'success'){
						jQuery('#wcbnl_overlay').fadeOut(1000);
						Swal.fire({
							position: 'center',
							icon: 'success',
							title: MyAjax.cloned,
							showConfirmButton: false,
							timer: 2000,
						  });
						  userdataTable.ajax.reload();
						  show_multi_action();
					}else{
						jQuery('#wcbnl_overlay').fadeOut(1000);
						Swal.fire({
							icon: 'error',
							title: 'Oops...',
							text: 'Something went wrong!',
						});
					}
				},
			});
		});		
	// });

	jQuery( document ).ajaxStop(function() {
		show_multi_action();
	});
	
});

function show_multi_action(){
	jQuery(".label").click(function(){
		jQuery(this).parent().find(".social").toggleClass("clicked");
	 });
	 var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	 var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	   return new bootstrap.Tooltip(tooltipTriggerEl)
	 });
	var clipboard = new ClipboardJS('.copy_clipboard');
}

//download password note
function pwdms_save_note(textToWrite, fileNameToSaveAs) {
	var textFileAsBlob = new Blob([textToWrite], { type: 'text/plain' });
	var downloadLink = document.createElement("a");
	downloadLink.download = fileNameToSaveAs;
	downloadLink.innerHTML = "Download File";
	if (window.webkitURL != null) {
		// Chrome allows the link to be clicked
		// without actually adding it to the DOM.
		downloadLink.href = window.webkitURL.createObjectURL(textFileAsBlob);
	}
	else {
		// Firefox requires the link to be added to the DOM
		// before it can be clicked.
		downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
		downloadLink.onclick = destroyClickedElement;
		downloadLink.style.display = "none";
		document.body.appendChild(downloadLink);
	}

	downloadLink.click();
}


