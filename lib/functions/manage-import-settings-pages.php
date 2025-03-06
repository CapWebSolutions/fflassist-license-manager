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


/**
 * Create License Settings Page
 *
 * @param [type] $settings_pages
 * @return void
 */
function capweb_create_license_settings_page( $settings_pages ) {
	$settings_pages[] = [
        'menu_title'      => __( 'FFL License Management', 'fflassist-license-manager' ),
        'id'              => 'ffl-license-management',
        'position'        => 3,
        'parent'          => 'tools.php',
        'columns'         => 1,
        'tabs'            => [
            'Import' => 'Import',
            'Search' => 'Search',
            'Modify' => 'Modify',
        ],
        'customizer'      => false,
        'customizer_only' => false,
        'network'         => false,
        'icon_url'        => 'dashicons-admin-generic',
    ];

	return $settings_pages;
}
add_filter( 'mb_settings_pages','capweb_create_license_settings_page' );
