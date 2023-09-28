<div class="container-fluid">
	<!--Start Dashboard Content-->
	<div class="row crbox m-2 mt-3">
		<div class="col-xl-12 col-lg-12 col-md-12">
			<div class="row mb-3">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 mob-dash-cat">
					<h3> <?php echo __('All Categories','passwords-manager');?></h3>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-end mob-dash-catadd">
					<button type="button" name="add" id="add_button" data-bs-toggle="modal"
							data-bs-target="#categoryModal" class="btn btn-primary btn-xs"><i
																							  class="fa fa-plus-circle fa-2x"></i></button>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 table-responsive" id="table-responsive">
					<table id="category_data" class="table table-borderless table-striped mt-4">
						<thead class="bg-primary text-white" id="thead">
							<tr>
								<th> <?php echo __('No','passwords-manager');?>.</th>
								<th> <?php echo __('Category Name','passwords-manager');?> </th>
								<th> <?php echo __('No. of Passwords','passwords-manager');?></th>
								<th> <?php echo __('Action','passwords-manager');?></th>
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
	<!-- add category modal start  -->
	<div class="modal fade" id="categoryModal">
		<div class="modal-dialog">
			<form method="post" id="category_form">
				<div class="modal-content animated fadeInUp">
					<div class="modal-header">
						<h5 class="modal-title"><i class="fa fa-plus"></i><?php echo __('Category','passwords-manager');?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body position-relative">
						<div class="row mt-4">
							<div class="form-group col-lg-12">
								<label for="input-10"> <?php echo __('Name','passwords-manager');?><span class="export_error">*</span></label>
								<input type="text" name="category_name" id="category_name" class="form-control"
									   required />
								<span id="cat_error" class="text-danger"></span>
							</div>
						</div>
						<div class="row mt-4">
							<div class="form-group col-lg-12">
								<label for="input-10" class="w-100"> <?php echo __('Category Badge Color','passwords-manager');?></label>
								<input type="text" name="category_color" class="color-field" value="" id="category_color" data-default-color="#bada55"/>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="category_id" id="category_id" />
						<input type="hidden" name="btn_action" id="btn_action" value="Add" />
						<input type="submit" name="saction" id="saction" class="btn btn-primary" value="Add" />
					</div>
				</div>
			</form>
		</div>
	</div>
	<!-- end category modal end  -->
</div>