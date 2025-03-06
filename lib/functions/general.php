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

 // add_action( 'wp_ajax_capweb_start_it_up','capweb_start_it_up_callback' );

function capweb_start_it_up_callback() {

    $license_import_file = isset($_POST[$prefix . 'license_import_file']) ? sanitize_text_field($_POST[$prefix . 'license_import_file']) : '';
    $record_limit = isset($_POST[$prefix . 'record_limit']) ? intval($_POST[$prefix . 'record_limit']) : 0;

    ?><script>console.log('Importing...');</script><?php
    $status = capweb_start_it_up( $license_import_file, $record_limit );
    ?><script>console.log('Importing complete.');</script><?php
    if ( is_null($status) ) {
        $output = 'Success.';
    } else {
        $output = $status;
    }
    // echo $output;
    echo wp_kses_post($output); // Accept any code that is allowed in a WordPress post.
    wp_die;
}

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
        // echo '<p>No license found with the provided number. ' . $license_number . '</p>' ;
        echo esc_html('No license found with the provided number:  ') . wp_kses_post($license_number);
    }

    wp_die();
}
add_action('wp_ajax_capweb_search_license', 'capweb_search_license_callback');


function capweb_import_licenses_callback() { 
    global $wpdb;

    $license_import_file = isset($_POST['license_import_file']) ? sanitize_text_field($_POST['license_import_file']) : '';
    $license_record_limit = isset($_POST['record_limit']) ? sanitize_text_field($_POST['record_limit']) : '';


    if (empty($license_import_file)) {
        echo 'Please select a license import file from the media library.' . __FILE__;
        wp_die();
    }
    // Do it.  
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

}
add_action('wp_ajax_capweb_import_licenses', 'capweb_import_licenses_callback');