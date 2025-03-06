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

// Assumes that $license has been syntax checked and is formatted as x-xx-xxx-xx-xx-xxxx.
function capweb_is_ffl_code_valid( $license ) {
    global $wpdb;

    // Check if $license is formatted. If not, format it. 
    $formatted_license = capweb_reformat_ffl_code($license);

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

function capweb_reformat_ffl_code($license) {
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


function capweb_perform_license_file_import( $license_import_file, $record_limit ) {
    // Check if the file exists
    if (!file_exists($license_import_file)) {
        return new WP_Error('file_not_found', __('The specified file does not exist.', 'fflassist-license-manager'));
    }

    // Include the CLI class file
    require_once plugin_dir_path(__FILE__) . '../includes/class-import-ffl-licenses-cli.php';

    // Create an instance of the CLI class
    $importer = new Import_Ffl_Data();

    // Call the import method from the CLI class
    $result = $importer->import_ffl_data($license_import_file, $record_limit);

    // Return the result
    return $result;

}
