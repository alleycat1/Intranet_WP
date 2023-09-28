<?php
$query  = get_option('pms_encrypt_key');
if(isset($query)){
	$skey = esc_html($query);
}
?>
<div class="container-fluid clear">
    <!--Start Dashboard Content-->
    <div class="row">
        <div class="col-md-12">
            <div class="mt-3">
                <div class="card text-dark bg-light p-0">
                    <div class="card-header"> <?php echo __('Password Encryption Key','passwords-manager');?> :</div>
                    <div class="card-body">
                        <form method="post" id="setting_form">                         
							<div class="form-group col-md-12 mb-0">
								<label for="input-10"><?php echo __('Password Encryption Key','passwords-manager');?><span class="export_error">*</span></label>
							</div>
							<div class="form-group d-flex align-items-center">
								<?php
								if(!empty($skey)){
									?>
									<div class="rlt">
										<input type="password" name="setting_key" id="setting_key" data-toggle="tooltip" data-placement="bottom" title="Do not try to change it again else all your older passwords will stop working." class="form-control" value="<?php echo esc_html($skey);?>" readonly />
										<span toggle="#setting_key" class="fa fa-fw fa-eye field-icon toggle-password"></span>
									</div>
								<?php 
								}else{?>
								<div class="rlt">
									<input type="password" name="setting_key" id="setting_key" class="form-control stng_error" required />
									<span toggle="#setting_key" class="fa fa-fw fa-eye field-icon toggle-password"></span>
								</div>
								<a class="btn btn-primary" id="generate">  <?php echo __('Generate Key','passwords-manager');?></a>	
								</div> 
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mt-2">       
									<span class="wrngTooltip"> <?php echo __('Do not try to change it again else all your older passwords will stop working','passwords-manager');?>.</span>
									<h6 class="text-danger" id="error_show" style="display: none"> <?php echo __('Please enter valid encryption key first','passwords-manager');?> </h6>                        
									<input type="hidden" name="btn_action" id="btn_action" value="Save" />
									<input type="submit" name="saction" id="saction" class="btn btn-info" value="Save" />
								</div>
							
							<?php }//else ?>
							<?php
								if(!empty($skey)){
									?>
									</div>
								<div class="" id="key_warning">
									<div class="alert-dismissible fade show mt-2 pe-0">
										<p class="text-danger ml-1 mb-1"><strong> <?php echo __('Note','passwords-manager');?>: </strong><?php echo __('You can enter encryption key only once. So, make sure to use secure key','passwords-manager');?> .</p>
									</div>
								</div>
							<?php }else{?>
								<div class="" id="key_warning" style="display:none">
									<div class="alert-dismissible fade show mt-2 pe-0">
										<p class="text-danger ml-1 mb-1"><strong><?php echo __('Note','passwords-manager');?>: </strong><?php echo __('You can enter encryption key only once. So, make sure to use secure key','passwords-manager');?>.</p>
									</div>
								</div>
							<?php }?>
							<input type="hidden" name="user_id" id="user_id" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End container-fluid-->
</div>
<script>
/**
 *Genrate Encryption key
 */
function createRandomString(length) {
    var str = "";
    for (; str.length < length; str += Math.random().toString(36).substr(2));
    return str.substr(0, length);
}
document.addEventListener("DOMContentLoaded", function() {
    var button = document.querySelector("#generate"),
        output = document.querySelector("#setting_key");
    if (button) {
        button.addEventListener("click", function() {
            var str = createRandomString(25);
            output.value = '';
            output.value = str;
        }, false)
    }
});
</script>