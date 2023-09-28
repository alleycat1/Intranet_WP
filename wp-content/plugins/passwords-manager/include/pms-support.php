<div class="container-fluid clear">
    <!--Start Dashboard Content-->
    <div class="row">
        <div class="col-md-12">
            <div class="card text-dark bg-light p-0">
                <div class="card-header"><?php echo __('Support','passwords-manager');?> </div>
                <div class="card-body">
					<div class="ref_lnk">
						<form id="pwdms_sprt_form" method="post"> 
							<?php wp_nonce_field( 'security_nonce', 'security_nonce' ); ?>
							<ul class="pwdms_fdtype p-0">
								<li>
									<input type="radio" class="pwdms_fdtypes" id="pwdms_fdtype_1" name="pwdms-review" value="review" />
									<a id="pwdms_fdtype_lnk1" href="https://wordpress.org/support/plugin/passwords-manager/reviews/" target="_blank">
										<i></i>
										<span> <?php echo __('I would like to review this plugin','passwords-manager');?> </span>
									</a>
								</li>
								<li>
									<input type="radio" class="pwdms_fdtypes" id="pwdms_fdtype_4" name="pwdms-review" value="more-info" />
									<a id="pwdms_fdtype_lnk4" href="https://hirewebxperts.com/wpPluginDocs/pwdms/" target="_blank">
										<i></i>
										<span> <?php echo __('How to use this plugin','passwords-manager');?> </span>
									</a>
								</li>
								<li>
									<input type="radio" class="pwdms_fdtypes" id="pwdms_fdtype_2" name="pwdms-suggest" value="suggestions" />
									<label for="pwdms_fdtype_2">
										<i></i>
										<span> <?php echo __('I have ideas to improve this plugin','passwords-manager');?> </span>
									</label>
								</li>
								<li>
									<input type="radio" class="pwdms_fdtypes" id="pwdms_fdtype_3" name="pwdms-help" value="help-needed" />
									<label for="pwdms_fdtype_3">
										<i></i>
										<span> <?php echo __('I need help with this plugin','passwords-manager');?> </span>
									</label>
								</li>
							</ul>
							<div class="pwdms_fdback_form">
								<div class="pwdms_field">
									<input placeholder=" <?php echo __('Enter your email address.','passwords-manager');?>" type="email" id="pwdms-feedback-email" class="pwdms-feedback-email" name="pwdms-feedback-email"/>
								</div>
								<div class="pwdms_field">
									<textarea rows="4" id="pwdms-feedback-message" class="pwdms-feedback-message" placeholder=" <?php echo __('Leave plugin developers any feedback here...','passwords-manager');?>"></textarea>                     
								</div>
								<div class="pwdms_field pwdms_fdb_terms_s">
									<input type="checkbox" class="pwdms_fdb_terms" id="pwdms_fdb_terms" name="pwdms_fdb_terms"/>
									<label for="pwdms_fdb_terms"><?php echo __('I agree that by clicking the send button below my email address and comments will be send to a','passwords-manager');?> <a href="https://www.hirewebxperts.com">hirewebxperts.com</a></label>
								</div>
								<div class="pwdms_field">
									<div class="pwdms_sbmt_buttons">
										<button class="btn btn-warning text-white" type="submit" id="pwdms-feedback-submit">
											<i class="fa fa-send"></i> <?php echo __('Send','passwords-manager');?>	
											<img src="<?php echo PWDMS_IMG.'sms-loading.gif'?>" height="15px" id="sms_loading" style="display:none">			
										</button>
										<input type="hidden" id="form_type" name="form_type">
										<a class="pwdms_fd_cancel btn" id="pwdms_fd_cancel" href="#"><?php echo __('Cancel','passwords-manager');?></a>
									</div>
								</div>
							</div> 
						</form>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
