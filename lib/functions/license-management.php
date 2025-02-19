<?php
/**
 * License Management Helpers
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

// Assumes that $license has been syntax checked and is formatted as x-xx-xxx-xx-xx-xxxx.
function is_ffl_code_valid( $license ) {
    global $wpdb;

    // Check if $license is formatted. If not, format it. 
    $formatted_license = reformat_ffl_code($license);

    if ( ! $formatted_license ) {
        return false;
    }

    // Prepare the SQL statement
    $license_db = $wpdb->prefix . 'ffl_licensees';
    $query = $wpdb->prepare("SELECT _ffl_license_number, _ffl_license_name, _ffl_business_name FROM %i WHERE _ffl_license_number = %s", $license_db, $formatted_license);

    // Execute the query
    $result = $wpdb->get_row($query, ARRAY_A);
    // Check if a result was found
    if ($result) {
        return $result;
    } else {
        return false;
    }
}

function reformat_ffl_code($license) {
    // Check if the license parameter is empty
    if (empty($license)) {
        return false;
    }

    // Remove any non-alphanumeric characters
    $license = preg_replace('/[^a-zA-Z0-9]/', '', $license);
    // error_log( '$license ' . var_export( $license, true ) );
    // Check the length of the license code
    $length = strlen($license);
    if ($length > 20 || $length < 15) {
        return false;
    }

    // Format the license code
    $formatted_license = substr($license, 0, 1) . '-' .
                         substr($license, 1, 2) . '-' .
                         substr($license, 3, 3) . '-' .
                         substr($license, 6, 2) . '-' .
                         substr($license, 8, 2) . '-' .
                         substr($license, 10);

    return $formatted_license;
}