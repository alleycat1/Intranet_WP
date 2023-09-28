<?php
if(isset($_POST) && !empty($_POST)){
extract($_POST);
    if (isset($pwdms_import_btn)) {
        @error_reporting( E_ERROR );
        @set_time_limit( 0 );
        @ini_set( 'max_input_time', 3600 * 3 );
        @ini_set( 'max_execution_time', 3600 * 3 );

        if(isset($pwdms_import_btn) && isset($security_nonce) && !empty($security_nonce)) { 
            $encry_key = get_option('pms_encrypt_key');
  
            if(!empty($encry_key)){

                if (sanitize_text_field($_FILES["pwdms_csvim_upload_file"]["size"]) > 0) {
                    //get the csv file 
                    $fileName = esc_html($_FILES["pwdms_csvim_upload_file"]["tmp_name"]);

                    $file = fopen($fileName, "r");
                    $data	 = fgetcsv( $file, 100000, ",", "'" ); 
                    $line	 = 0;
                    do {
                        $name	         = $data[ 0 ];
                        $email	         = $data[ 1 ];
                        $password	     = $data[ 2 ];
                        $url	         = $data[ 3 ];
                        $category		 = $data[ 4 ];
                        $category_color  = $data[ 5 ];
                        $note		     = $data[ 6 ];
                        
                        if ( ($line !== 0 )	&&	($line < 201 )	) {
                            $success = pwdms_custom_create_passwords_from_csv( $name, $email, $password,$url, $category,$category_color ,$note);
                        }
                        $line++;
                    
                    } while ( $data = fgetcsv( $file, 100000, ",", "'" ));
                }

                if($success == '1'){
                    $message = __('Data import successfully','passwords-manager');
                }else{
                    $message = __('Data failed to import','passwords-manager');
                }
            }
        }    
    } 
}

include (PWDMS_INC . 'admin-page/addon/csv-import/pms-csv-import-setting-page/pms_import_html.php');



?>