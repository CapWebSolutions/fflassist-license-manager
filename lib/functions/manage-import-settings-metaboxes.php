<?php
/**
 * Manage Import Meta Boxes
 *
 * This file contains any meta box functionality related to the license import process. 
 *
 * @package      License_Manager
 * @since        1.0.0
 * @link         https://github.com/capwebsolutions/fflassist-license-manager
 * @author       Matt Ryan <matt@capwebsolutions.com>
 * @copyright    Copyright (c) 2025, Matt Ryan
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Display License Settings Import Meta Boxes
 *
 * @param [type] $meta_boxes
 * @return void
 */
function capweb_display_license_settings_import_mb( $meta_boxes ) {
    $prefix = '';

    $meta_boxes[] = [
        'title'          => __( 'FFL License Management - Import', 'fflassist-license-manager' ),
        'id'             => 'license-settings-page-import',
        'settings_pages' => ['ffl-license-management'],
        'tab'            => 'Import',
        'fields'         => [
            [
                'name'              => __( 'License Import File', 'fflassist-license-manager' ),
                'id'                => $prefix . 'license_import_file',
                'type'              => 'file_advanced',
                'label_description' => __( 'Select file from media library. ', 'fflassist-license-manager' ),
                'desc'              => __( 'CSV file expected. Importing is a destructive process. Existing FFL Licensee records are deleted.', 'fflassist-license-manager' ),
                'max_file_uploads'  => 1,
                'force_delete'      => true,
                'required'          => true,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
                'hide_from_front'   => false,
                'mime_type'         => 'text/csv',
            ],
            [
                'name'              => __( 'Record Limit', 'fflassist-license-manager' ),
                'id'                => $prefix . 'record_limit',
                'type'              => 'number',
                'desc'              => __( 'Maximum number of records to import. All records will be processed if left blank. ', 'fflassist-license-manager' ),
                'required'          => false,
                'min'               => 0,
                'default_value'     => 0,
                'size'          => 20,
                'placeholder'       => '0', 
                'disabled'          => false,
                'readonly'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
                'hide_from_front'   => false,
            ],
            [
                'name'            => __( 'Execute License Import', 'fflassist-license-manager' ),
                'id'              => $prefix . 'import_button',
                'type'            => 'button',
                'std'             => __( 'RUN IMPORT', 'fflassist-license-manager' ),
                'attributes' => [
                    'data-section' => 'advanced-section',
                    'class'        => 'js-import-license',
                ],
                'disabled'        => false,
                'hide_from_rest'  => false,
                'hide_from_front' => false,
            ],
            [
                'type'            => 'custom_html',
                'std'             => '<div id="import-results">Import activty displayed here.</div>',
                'hide_from_rest'  => false,
                'hide_from_front' => false,
            ],
        ],
    ];


    return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'capweb_display_license_settings_import_mb' );

/**
 * Display License Modify Settings Meta Boxes
 *
 * @param [type] $meta_boxes
 * @return void
 */
function capweb_display_license_settings_modify_mb( $meta_boxes ) {
    $prefix = '';

    $meta_boxes[] = [
        'title'          => __( 'License Settings Page - Modify', 'fflassist-license-manager' ),
        'id'             => 'license-settings-page-modify',
        'settings_pages' => ['ffl-license-management'],
        'tab'            => 'Modify',
        'fields'         => [
            [
                'type'            => 'heading',
                'name'            => __( 'Modify - Under Development.', 'fflassist-license-manager' ),
                'hide_from_rest'  => false,
                'hide_from_front' => false,
                'save_field'      => false,
            ],
        ],
    ];

    return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes','capweb_display_license_settings_modify_mb' );

/**
 * Display License Search Settings Meta Boxes
 *
 * @param [type] $meta_boxes
 * @return void
 */
function capweb_display_license_settings_search_mb($meta_boxes) {
    $prefix = '';

    $meta_boxes[] = [
        'title'          => __('License Settings Page - Search', 'fflassist-license-manager'),
        'id'             => 'license-settings-page-search',
        'settings_pages' => ['ffl-license-management'],
        'tab'            => 'Search',
        'fields'         => [
            [
                'name'            => __('FFL License Number', 'fflassist-license-manager'),
                'id'              => $prefix . 'license_number',
                'type'            => 'text',
                'desc' => __( 'Enter license code to search. Dashes not required.', 'fflassist-license-manager' ),
                'required'        => true,
                'placeholder'     => FFL_LICENSE_PLACEHOLDER,
                'hide_from_rest'  => false,
                'hide_from_front' => false,
                'size'          => 20,
            ],
            [
                // 'name'            => __('Search', 'fflassist-license-manager'),
                'id'              => $prefix . 'search_button',
                'type'            => 'button',
                'std'             => __('SEARCH', 'fflassist-license-manager'),
                'attributes'      => [
                    'data-section' => 'advanced-section',
                    'class'        => 'js-search-license',
                ],
                'hide_from_rest'  => false,
                'hide_from_front' => false,
            ],
            [
                'type'            => 'custom_html',
                'std'             => '<div id="search-results">Search Results Displayed Here.</div>',
                'hide_from_rest'  => false,
                'hide_from_front' => false,
            ],
        ],
    ];

    return $meta_boxes;
}
add_filter('rwmb_meta_boxes', 'capweb_display_license_settings_search_mb');