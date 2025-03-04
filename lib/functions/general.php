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
    echo $output;
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
        <p>
            <strong>License Number:&nbsp;</strong><?php echo esc_html($result['_ffl_license_number']); ?><br>
            <strong>License Name:&nbsp;&nbsp;</strong><?php echo esc_html($result['_ffl_license_name']); ?><br>
            <strong>Business Name:&nbsp;</strong> <?php echo esc_html($result['_ffl_business_name']); ?>
        </p>
        </div>
        <?php
    } else {
        echo '<p>No license found with the provided number. ' . $license_number . '</p>' ;
    }

    wp_die();
}
add_action('wp_ajax_capweb_search_license', 'capweb_search_license_callback');



// add_action( 'rwmb_enqueue_scripts','capweb_enqueue_custom_script' );
function capweb_enqueue_custom_script() {
    wp_enqueue_script( 'script-id', dirname( __FILE__) . '/assets/js/admin.js', [ 'jquery' ], '', true );
    // wp_localize_script( 'script-id', 'ffl_import_data', [
    //     'ajax_url' => admin_url( 'admin-ajax.php' ),
    // ]);
}