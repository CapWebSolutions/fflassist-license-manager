<?php 
/**
 * License Import Callback
 *
 * @return void
 */

function capweb_import_license_callback() {
    global $wpdb;

    if ( ! check_ajax_referer('capweb_import_nonce', 'nonce', false) ) {
        wp_send_json_error('Invalid nonce.');
        wp_die();
    }

    // Extract filename from metabox field object. 
    $files = rwmb_meta( 'license_import_file', [ 'object_type' => 'setting', 'limit' => 1 ], 'ffl-license-management' );
    $file = reset( $files );
  
    // $license_import_file_tmp = $file('url');

    $record_limit = isset($_POST['record_limit']) ? sanitize_text_field($_POST['record_limit']) : '0';
    $log_time = isset($_POST['log_time']) ? sanitize_text_field($_POST['log_time']) : '0';


    foreach ($file as $key => $value) {
        if ( 'url' == $key ) {
            // $my_license_import_file = $value;
            $result = capweb_perform_license_file_import( $value, $record_limit, $log_time );
            exit;
        }
    }
 
    if ( '' == $my_license_import_file )  {
        echo "<div class='import-license-wrap'>Please select a license file.</div>";
        wp_die();
    }
    if ( 0 == $record_limit) {
        echo 'Processing Entire Import File.' . __FILE__;
    }
 
    ?>
    <div class='import-license-wrap'>
    <h3>Import Details</h3>
    <table>
        <tbody>
        <tr>
            <td><strong>License File</strong></td>
            <td><?php echo esc_html($license_import_file); ?></td>
        </tr>   
        <tr>
            <td><strong>Record Limit</strong></td>
            <td><?php echo esc_html($record_limit); ?></td>
        </tr>
        <tr>
            <td><strong>Log Time</strong></td>
            <td><?php echo esc_html($log_time); ?></td>

        </tr>
        </tbody>
    </table>
    </div>
    <?php

    if ( $log_time ) { 
        $start_time = microtime(true); 
    }
    
    // Perform the import
    $result = capweb_perform_license_file_import( $my_license_import_file, $record_limit, $log_time );

    if ( $log_time ) { 
        $end_time = microtime(true); 
        $duration = $end_time - $start_time; 
        $short_dur = round($duration, 2);
        sprintf("Import completed in %s seconds.", $short_dur);
    } 

    if ( $result ) {
        ?>
        <div class='import-license-wrap'>
        <h3>License Details</h3>
        <div>License file imported successfully.</div>
        <div>Records imported: <?php echo esc_html($result); ?></div>
        </div>
        <?php
    } else {
        echo esc_html('License file not imported.') . wp_kses_post($license_import_file);
    }

    wp_die();
}
// Hook the function to handle the AJAX request
add_action('wp_ajax_capweb_import_license_callback', 'capweb_import_license_callback');