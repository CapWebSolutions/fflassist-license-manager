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

/**
 * Detect if Gravity Forms plugin active. 
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
    add_filter('gform_validation_1',  'capweb_validate_code');
}

/**
 * capweb_validate_code function
 *
 * Validates the 9 digit code entered in the FFL field against the imported ATF FFL license database. 
 *   The FFL Number field is field 8 on form 1.
 * @param [type] $validation_result
 * @return void
 * 
 * @link http://www.gravityhelp.com/forums/topic/database-look-up-validation
 */
function capweb_validate_code($validation_result){
    if( !capweb_is_ffl_code_valid( $_POST['input_8']  ) ){
        $validation_result['is_valid'] = false;
        foreach($validation_result['form']['fields'] as &$field){
            if($field['id'] == 8){
                $field['failed_validation'] = true;
                $field['validation_message'] = 'The license number you entered is not found in our database. Please re-enter it or <a href="/contact">contact us for assistance</a>.';
                break;
            }
        }
    }
    return $validation_result;
}
