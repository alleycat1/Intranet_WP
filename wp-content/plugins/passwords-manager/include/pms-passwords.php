<div class="container-fluid">
	<!--Start Dashboard Content-->
	<div class="row crbox m-2 mt-3">
		<div class="col-xl-12 col-lg-12 col-md-12">
			<div class="row mb-3">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 mob-dash">
					<h3><?php echo __('All Passwords','passwords-manager');?> </h3>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-end mob-dash"> <?php          			
					if(empty($stng_key)){?> <button type="button" class="btn btn-secondary btn-xs" data-bs-toggle="tooltip"
													data-bs-placement="bottom"
													title="This button is disabled.To enable this feature please add encryption key in settings page."><i
                            class="fa fa-plus-circle fa-2x"></i></button> <?php }else{?> <button type="button"
																								 name="add" id="add_user_pass" data-bs-toggle="modal" data-bs-target="#pwdsModal"
																								 class="btn btn-primary btn-xs"><i class="fa fa-plus-circle fa-2x"></i></button> <?php } ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 table-responsive" id="table-responsive">
					<table id="pwds_data" class="table table-borderless table-striped mt-4">
						<thead class="bg-primary text-white">
							<tr>
								<th><?php echo __('No','passwords-manager');?>.</th>
								<th> <?php echo __('Name','passwords-manager');?></th>
								<th><?php echo __('Username','passwords-manager');?> / <?php echo __('Email','passwords-manager');?></th>
								<th><?php echo __('Password','passwords-manager');?></th>
								<th><?php echo __('Category','passwords-manager');?></th>
								<th><?php echo __('Url','passwords-manager');?></th>
								<th><?php echo __('Action','passwords-manager');?></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- End container-fluid-->
	<div id="wcbnl_overlay">
		<div class="cv-spinner">
			<img src="<?php echo PWDMS_IMG.'loading.svg'?>">
		</div>
	</div>
	<!-- add password modal start  -->
	<div class="modal fade" id="pwdsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
		 aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog">
			<form method="post" id="pwds_form">
				<div class="modal-content animated fadeInUp">
					<div class="modal-header">
						<h5 class="modal-title" id="staticBackdropLabel"><i class="fas fa-plus"></i> <?php echo __('Add Password','passwords-manager');?> </h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="form-group col-md-6">
								<label for="input-10"> <?php echo __('Name','passwords-manager');?> <span class="export_error">*</span></label>
								<input type="text" name="user_name" id="user_name" class="form-control" required />
								<h6 id="user_err" class="text-danger" style="display: none"> <?php echo __('Please fill this field','passwords-manager');?> </h6>
							</div>
							<div class="form-group col-md-6">
								<label for="input-12"><?php echo __('Username','passwords-manager');?> / <?php echo __('Email','passwords-manager');?>  <span class="export_error">*</span></label>
								<input type="text" name="user_email" id="user_email" class="form-control" required />
							</div>
						</div>

						<div class="row my-3">
							<div class="form-group col-md-6">
								<label for="input-12"> <?php echo __('Category','passwords-manager');?> <span class="export_error">*</span></label>
								<select class="form-control" name="pass_category" id="pass_category"
										class="form-control" id="sel1">
									<option value=""><?php echo __('Please Select','passwords-manager');?></option> <?php
									$query_cate  = $wpdb->get_results("SELECT * FROM {$prefix}pms_category");
									$value= json_decode(json_encode($query_cate), True);
									foreach ($value as $row) {
										$cate_name = ucfirst(esc_html($row['category']));
										$cateory_id = absint($row['id']);?> <option value="<?php echo $cateory_id; ?>"><?php echo $cate_name;?>
									</option> <?php }?>
								</select>
								<h6 id="slct_wrng" class="text-danger" style="display: none"> <?php echo __('Select category once','passwords-manager');?> </h6>
							</div>
							<div class="form-group col-md-6">
								<label for="input-12"> <?php echo __('URL','passwords-manager');?> </label>
								<input type="url" id="user_url" name="user_url" rows="4" cols="50"
									   class="form-control" />
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="input-12"> <?php echo __('Password','passwords-manager');?> <span class="export_error">*</span></label>
								<div class="passcolmn">
									<input type="password" name="user_password" id="user_password" class="form-control"
										   required />
									<span toggle="#user_password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="input-12"> <?php echo __('Note','passwords-manager');?> </label>
								<textarea id="user_note" name="user_note" rows="4" cols="50" class="form-control"
										  placeholder="<?php echo __('Write note here','passwords-manager');?>....."></textarea>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="pwds_id" id="pwds_id" />
						<input type="hidden" name="setting_key_enc" id="setting_key_enc"
							   value="<?php echo esc_html($stng_key);?>" />
						<input type="hidden" name="btn_action" id="btn_action" value="Add" />
						<input type="submit" name="saction" id="saction" class="btn btn-primary" value="<?php echo __('Add','passwords-manager');?>"/>
					</div>
				</div>
			</form>
		</div>
	</div>
	<!-- add pasword modal end  -->
	<!-- add password modal start  -->
	<div class="modal fade" id="pwdsnoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
		 aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog">
			<form method="post">
				<div class="modal-content animated fadeInUp">
					<div class="modal-header">
						<h5 class="modal-title"><i class="fas fa-notes-medical"></i></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="form-row">
							<div class="form-group col-md-12">
								<textarea id="user_note_view" rows="10" cols="60" readonly
										  style="width:100%"></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button onclick=pwdms_save_note(user_note_view.value,'Pass-Note.txt')
									class="btn btn-info"><?php echo __('Download','passwords-manager');?> </button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<!-- add pasword modal end  -->
</div>