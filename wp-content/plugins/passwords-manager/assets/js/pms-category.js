
jQuery(document).ready(function () {
	/*
	**open add category popup
	*/
	jQuery('#add_button').click(function () {
		jQuery('#category_form')[0].reset();
		jQuery('.modal-title').html("<i class='fa fa-plus'></i>"+MyAjax.add_category);
		jQuery('#saction').val(MyAjax.add);
		jQuery('#btn_action').val('Add');
		jQuery('#cat_error').css("display", 'none');
		jQuery('#category_name').removeClass("border-danger");
		jQuery('#category_color').trigger('change');
	});
	/*
	**category datatable value store
	*/
	var security_nonce = MyAjax.security_nonce;
	var categorydataTable = jQuery('#category_data').DataTable({
		"processing": true,
		"order": [[1, 'desc']],
		"language": {
			"processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw border-0"></i>',
			"lengthMenu": MyAjax.shw_menu+" _MENU_",
			"sSearch": MyAjax.search_bar+':',
			"sZeroRecords": MyAjax.no_records,
			"sInfo":  MyAjax.filter_record,
			"sInfoFiltered":  "(filtrado de un total de _MAX_ registros)",
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
				"module": 'categories',
				"action": 'get_new_cats',
				'security_nonce':security_nonce
			}
		},
		"columnDefs": [
			{
				"targets": [-1],
				"orderable": false,
			},
		],
		"pageLength": 10,
		rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
	});
	/*
	**category submit 
	*/
	jQuery(document).on('submit', '#category_form', function (event) {
		event.preventDefault();

		jQuery('#wcbnl_overlay').show();
		jQuery('#wcbnl_overlay').css("z-index", "99999");
		jQuery('#categoryModal').css("z-index", "0");
		jQuery('#saction').attr('disabled', false);
		var cat_name = jQuery('#category_name').val();
		var category_id = jQuery('#category_id').val();
		var category_color = jQuery('#category_color').val();
		var btn_action = jQuery('#btn_action').val();
		var security_nonce = MyAjax.security_nonce;
		jQuery.ajax({
			url: MyAjax.ajaxurl,
			method: "POST",
			data: { category: cat_name, category_id: category_id,category_color:category_color, btn_action: btn_action,security_nonce:security_nonce, action: 'post_new_cats' }, //form_data,
			dataType: "json",
			success: function (data) {
				var x = data.resp;
				var y = data.ecode;
				var z = data.al_exist;
				if (x === 'Success') {
					jQuery('#wcbnl_overlay').fadeOut(1000);
					jQuery('#categoryModal').modal('hide');
					jQuery('#wcbnl_overlay').css("z-index", "1");
					jQuery('#categoryModal').css("z-index", "1050");
					jQuery('#category_form')[0].reset();
					jQuery('#alert_action').fadeIn().html('<div class="alert alert-success">' + data + '</div>');
					jQuery('#saction').attr('disabled', false);
					jQuery('#cat_error').css("display", 'none');
					jQuery('#category_name').removeClass("border-danger");
					categorydataTable.ajax.reload();

				} else if (y === 'special character') {
					jQuery('#cat_error').css("display", 'block');
					jQuery('#category_name').addClass("border-danger");
					jQuery('#cat_error').html("Don't use special characters or blank values!");
					jQuery('#saction').attr('disabled', false);
					jQuery('#wcbnl_overlay').fadeOut(1000);
					jQuery('#wcbnl_overlay').css("z-index", "1");
					jQuery('#categoryModal').css("z-index", "1050");

				} else if (z === 'Exists') {
					jQuery('#category_form')[0].reset();
					jQuery('#categoryModal').modal('hide');
					jQuery('#wcbnl_overlay').fadeOut(1000);
					jQuery('#wcbnl_overlay').css("z-index", "1");
					jQuery('#categoryModal').css("z-index", "1050");
					categorydataTable.ajax.reload();

				} else {
					jQuery('#cat_error').css("display", 'block');
					jQuery('#category_name').addClass("border-danger");
					jQuery('#cat_error').html("Category Already Exists ");
					jQuery('#saction').attr('disabled', false);
					jQuery('#wcbnl_overlay').fadeOut(1000);
					jQuery('#wcbnl_overlay').css("z-index", "1");
					jQuery('#categoryModal').css("z-index", "1050");
				}
				
				// if(jQuery('#cat_error').not(':visible')){
				// 	jQuery('.modal-backdrop').remove();
				// }
			},
			error: function () {
				alert('failure');
				jQuery('#saction').attr('disabled', false);
				jQuery('#wcbnl_overlay').fadeOut(1000);
			}
		});
	});



	/*
	**category update
	*/
	jQuery(document).on('click', '.upcate', function () {
		jQuery('#cat_error').css("display", 'none');
		jQuery('#category_name').removeClass("border-danger");
		var category_id = jQuery(this).attr("id");
		var btn_action = 'fetch_single';
		var security_nonce = MyAjax.security_nonce;
		jQuery.ajax({
			url: MyAjax.ajaxurl,
			method: "POST",
			data: { category_id: category_id, btn_action: btn_action, security_nonce:security_nonce, module: 'categories', action: 'edit_cats' },
			dataType: "json",
			success: function (data) {
				jQuery('#categoryModal').modal('show');
				jQuery('#category_name').val(data.category_name);
				jQuery('#category_color').val(data.category_color);
				jQuery('.modal-title').html('<i class="fas fa-edit"></i>'+MyAjax.edit_category);
				jQuery('#category_id').val(category_id);
				jQuery('#saction').val(MyAjax.edit);
				jQuery('#btn_action').val("Edit");
				jQuery('#category_color').trigger('change');
			},
		});
	});

	/*
	**category delete
	*/
	jQuery(document).on('click', '.delete', function () {
		var btn_action = 'Delete';
		var wrng_id = jQuery(this).attr('id');
		var security_nonce = MyAjax.security_nonce;		
		Swal.fire({			
			text: MyAjax.del_cat_txt,
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
					data: { category_id: wrng_id, btn_action: btn_action,security_nonce:security_nonce, module: 'categories', action: 'post_new_cats' },
					success: function (data) {
						jQuery('#alert_action').fadeIn().html('<div class="alert alert-info">' + data + '</div>');
						categorydataTable.ajax.reload();
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
			  })
			}
		  });
	});
});