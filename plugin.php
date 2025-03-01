<?php
/**
 * Plugin Name: FFLAssist License Manager
 * Plugin URI: https://github.com/CapWebSolutions/fflassist-license-manager
 * Description: This contains FFL License Manager functionality for FFLAssist. It should remain activated.
 * Version: 1.1.1
 * Author: Cap Web Solutions
 * Author URI: https://capwebsolutions.com
 * GitHub Plugin URI: https://github.com/CapWebSolutions/fflassist-license-manager
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

// Define needed constants
// define( 'LICENSE_MANAGER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); //location of plugin folder on disk
// define( 'LICENSE_MANAGER_PLUGIN_URI', plugin_dir_url( __FILE__ ) );  //location of plugin folder in wp-content

add_action( 'after_setup_theme','_license_manager_core_setup' );
function _license_manager_core_setup() {
	if( ! function_exists('get_plugin_data') ){
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	define( 'LICENSE_MANAGER_PLUGIN_VERSION', get_plugin_data(__FILE__ )['Version'] ); 
}

/**
 * Get all the include files for the theme.
 *
 * @author CapWebSolutions
 */
function include_license_manager_inc_files() {
	$files = [
		'includes/',
		'lib/functions/',
		// 'lib/metabox-io-example.php', // TGMPA library and related for Metabox.io
	];
	require_once dirname( __FILE__) . '/includes/class-tgm-plugin-activation.php';

	foreach ( $files as $include ) {
		$include = dirname( __FILE__) . '/' . $include;
		// Allows inclusion of individual files or all .php files in a directory.
		if ( is_dir( $include ) ) {
			foreach ( glob( $include . '*.php' ) as $file ) {
				require $file;  // all php files from directory
			}
		} else {
			require $include;    // single php file
		}
	}
}
include_license_manager_inc_files();