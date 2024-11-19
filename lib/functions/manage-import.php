<?php
/**
 * Manage Import
 *
 * This file contains any functionality related to the license import process. 
 *
 * @package      License_Manager
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-license-manager
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2024, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
namespace capweb\license_manager;

/**
 * Delete Import File
 *
 * @param [type] $import_id
 * @return void
 */
function delete_import_file( $import_id ) {

    // Retrieve import object.
    $import = new PMXI_Import_Record();
    $import->getById( $import_id );

    // Confirm import object is valid.
    if ( ! $import->isEmpty() ) {
        
        // Retrieve file information.
        $history_file = new PMXI_File_Record(); 
        $history_file->getBy( 'import_id', $import_id );

        // Confirm file isn't empty. 
        if ( !$history_file->isEmpty() ) {

            // Retrieve file path.  
            $import_file = wp_all_import_get_absolute_path( $history_file->path );

            // Mark file for deletion.
            @unlink( $import_file );

		}
	}
}
add_action( 'pmxi_after_xml_import', __NAMESPACE__ . '\delete_import_file', 10, 1 );

function wpai_send_email($import_id) {
    // Only send emails for import ID 2.
    if($import_id != "2")
        return;
    
    // Retrieve the last import run stats.
    global $wpdb;
    $table = $wpdb->prefix . "pmxi_imports";

    if ( $soflyyrow = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `" . $table . "` WHERE `id` = '%d'", $import_id ) ) ) {
        
        $count = $soflyyrow->count;
        $imported = $soflyyrow->imported;
        $created = $soflyyrow->created;
        $updated = $soflyyrow->updated;
        $skipped = $soflyyrow->skipped;
        $deleted = $soflyyrow->deleted;

    }
    
    // Destination email address.
    $to = 'matt@capwebsolutions.com';

    // Email subject.
    $subject = 'Import ID: '.$import_id.' complete';

    // Email message.
    $body = 'Import ID: '.$import_id.' has completed at '. date("Y-m-d H:m:s"). "\r\n" . 'File Records:' .$count."\r\n".'Records Imported:'.$imported."\r\n".'Records Created:'.$created;
    $body .= "\r\n" . 'Records Updated:'. $updated . "\r\n" . 'Records Skipped:' . $skipped . "\r\n" . 'Records Deleted:' . $deleted;

    // Send the email as HTML.
    $headers = array('Content-Type: text/html; charset=UTF-8');
 
    // Send via WordPress email.
    wp_mail( $to, $subject, $body, $headers );
}
add_action('pmxi_after_xml_import', __NAMESPACE__ . '\wpai_send_email', 10, 1);

/**
 * Create License Settings Page
 *
 * @param [type] $settings_pages
 * @return void
 */
function create_license_settings_page( $settings_pages ) {
	$settings_pages[] = [
        'menu_title'      => __( 'FFL License Management', 'fflassist' ),
        'id'              => 'ffl-license-management',
        'parent'          => 'edit.php?post_type=ffl-licensee',
        'tabs'            => [
            'Import' => 'Import',
            'Other'  => 'Other',
        ],
        'customizer'      => false,
        'customizer_only' => false,
        'network'         => false,
        'icon_url'        => 'dashicons-admin-generic',
    ];

	return $settings_pages;
}
add_filter( 'mb_settings_pages', __NAMESPACE__ . '\create_license_settings_page' );

/**
 * Display License Settings
 *
 * @param [type] $meta_boxes
 * @return void
 */
function display_license_settings( $meta_boxes ) {
    $prefix = '';

    $meta_boxes[] = [
        'title'          => __( 'License Settings Page Fields', 'fflassist' ),
        'id'             => 'license-settings-page-fields',
        'settings_pages' => ['ffl-license-management'],
        'tab'            => 'Import',
        'fields'         => [
            [
                'name'              => __( 'License Import File', 'fflassist' ),
                'id'                => $prefix . 'license_import_file',
                'type'              => 'file_advanced',
                'label_description' => __( 'This is the label description. ', 'fflassist' ),
                'desc'              => __( 'CSV file expected. Input Description.', 'fflassist' ),
                'max_file_uploads'  => 1,
                'force_delete'      => false,
                'required'          => true,
                'clone'             => false,
                'clone_empty_start' => false,
                'columns'           => 4,
                'hide_from_rest'    => false,
                'hide_from_front'   => false,
            ],
            [
                'name'              => __( 'Last Imported File', 'fflassist' ),
                'id'                => $prefix . 'last_imported_file',
                'type'              => 'text',
                'required'          => false,
                'disabled'          => false,
                'readonly'          => true,
                'clone'             => false,
                'clone_empty_start' => false,
                'columns'           => 4,
                'hide_from_rest'    => false,
                'hide_from_front'   => false,
                'limit_type'        => 'character',
            ],
            [
                'name'              => __( 'Last Import Date', 'fflassist' ),
                'id'                => $prefix . 'last_import_date',
                'type'              => 'text',
                'required'          => false,
                'disabled'          => false,
                'readonly'          => true,
                'clone'             => false,
                'clone_empty_start' => false,
                'columns'           => 4,
                'hide_from_rest'    => false,
                'hide_from_front'   => false,
                'limit_type'        => 'character',
            ],
            [
                'name'            => __( 'Run Import Button', 'fflassist' ),
                'id'              => $prefix . 'run_import_button',
                'type'            => 'button',
                'std'             => __( 'Run Import', 'fflassist' ),
                'disabled'        => false,
                'columns'         => 4,
                'hide_from_rest'  => false,
                'hide_from_front' => false,
            ],
        ],
    ];

    return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', __NAMESPACE__ . '\display_license_settings' );
