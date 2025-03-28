<?php 
/**
 * License Modify Callback
 *
 * @package      License_Manager
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-license-manager
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2025, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

function capweb_modify_license_callback() {
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
add_action('wp_ajax_capweb_modify_license', 'capweb_modify_license_callback');