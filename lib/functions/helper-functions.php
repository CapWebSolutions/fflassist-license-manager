<?php
/**
 * Helper Functions
 *
 *
 * @package      License_Manager
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-license-manager
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
// namespace capweb;

/**
 * Detect if Gravity Forms plugin active. 
 */
// if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
    add_filter('gform_validation_1',  'validate_code');
// }
function validate_code($validation_result){
// http://www.gravityhelp.com/forums/topic/database-look-up-validation
// validate 9 digit code 
// For Form ID 1 - validate field 8
    if( !is_ffl_code_valid( $_POST['input_8']  ) ){
        $validation_result['is_valid'] = false;
        foreach($validation_result['form']['fields'] as &$field){
        // field 8 is the field we are validating  
            if($field['id'] == 8){
                $field['failed_validation'] = true;
                $field['validation_message'] = 'The license number you entered is not found. Please re-enter it.';
                break;
            }
        }
    }
    return $validation_result;
}