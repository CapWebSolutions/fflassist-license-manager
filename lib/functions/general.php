<?php
/**
 * General
 *
 * This file contains any general functions ofr the license manager plugin.
 *
 * @package      License_Manager
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-license-manager
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

function capweb_search_license_callback() {
    global $wpdb;

    $license_number = isset($_POST['license_number']) ? sanitize_text_field($_POST['license_number']) : '';

    if (empty($license_number)) {
        echo 'Please enter a license number.' . __FILE__;
        wp_die();
    }
    // Validate the entered license number 
    $result = capweb_is_ffl_code_valid($license_number);

    if ($result) {
        ?>
        <div class='license-wrap'>
        <h3>License Details</h3>
        <table>
            <tbody>
            <tr>
                <td><strong>License Number</strong></td>
                <td><?php echo esc_html($result['_ffl_license_number']); ?></td>
            </tr>
            <tr>
                <td><strong>License Name</strong></td>
                <td><?php echo esc_html($result['_ffl_license_name']); ?></td>
            </tr>
            <tr>
                <td><strong>Business Name</strong></td>
                <td><?php echo esc_html($result['_ffl_business_name']); ?></td>
            </tr>
            </tbody>
        </table>
        </div>
        <?php
    } else {
        echo esc_html('No license found with the provided number:  ') . wp_kses_post($license_number);
    }

    wp_die();
}
add_action('wp_ajax_capweb_search_license', 'capweb_search_license_callback');


// function capweb_import_licenses_callback() { 
//     global $wpdb;

//     // $license_import_file = isset($_POST['license_import_file']) ? sanitize_url($_POST['license_import_file']) : '';
//     // $license_import_file = isset($_POST['license_import_file']) ? sanitize_text_field($_POST['license_import_file']) : '';

//     // Get file name using metabox method
//     $license_import_files = rwmb_meta( 'license_import_file', [ 'limit' => 1 ], 'ffl-license-management' );
//     print_f($license_import_files);
//     $license_import_file = reset( $license_import_files );
//     var_dump($license_import_file['path']);
//     $license_import_file = $license_import_file['url'];
//     var_dump($license_import_file['url']);


//     $license_record_limit = $_POST['record_limit'] ?? 0 ;
//     var_dump($license_record_limit);

//     if (empty($license_import_file)) {
//         echo 'Please select a license import file from the media library.' . __FILE__;
//         wp_die();
//     }
//     // Do it.  
//     // $result = capweb_perform_license_file_import( $license_import_file, $record_limit );
//     if ($result) {

//     } else {
//         echo esc_html('File import was not sucecssful. ') . wp_kses_post($result);
//     }
//     wp_die();
// }
// add_action('wp_ajax_capweb_import_licenses', 'capweb_import_licenses_callback');


// New attempt using ajax calls. 
function capweb_import_licenses_callback() { 
    // Check for nonce security if needed
    check_ajax_referer('import_nonce', 'security');
    echo 'Nonce verified';
var_dump($_POST);
    // Check if the license_import_file parameter is set
    if (isset($_POST['license_import_file'])) {
        $license_import_file = sanitize_text_field($_POST['license_import_file']);
        $record_limit = sanitize_text_field($_POST['record_limit']);        
        // Log the received file URL for debugging
        error_log( print_r( (object)
            [
                'file' => __FILE__,
                'method' => __METHOD__,
                'line' => __LINE__,
                'dump' => [
                    'license_import_file' => $license_import_file,
                ],
            ], true ) );

        // Process the license import file
        $result = capweb_perform_license_file_import( $license_import_file, $record_limit );
        if ($result) {
            ?>
            <div class='license-import-wrap'>
                <h3>License Import Activity</h3>
            </div>
            <?php
        } else {
            echo esc_html('File import was not sucecssful. ') . wp_kses_post($result);
        }
        wp_die();
        // Send a success response
        wp_send_json_success('License import successful');
    } else {
        // Send an error response if the parameter is not set
        wp_send_json_error('No license import file provided');
    }
}

// Hook the function to handle the AJAX request
add_action('wp_ajax_capweb_import_licenses', 'capweb_import_licenses');
add_action('wp_ajax_nopriv_capweb_import_licenses', 'capweb_import_licenses');