<?php
/**
 * General
 *
 * This file contains the admin menu and settings pages management.
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
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function fflassist_license_settings_init() {
	// Register a new setting for "fflassist_license" page.
	register_setting( 'fflassist_license', 'fflassist_license_options' );

	// Register a new section in the "fflassist_license" page.
	add_settings_section(
		'fflassist_license_section_developers',
		__( 'License Settings & Debug', 'fflassist_license' ), __NAMESPACE__ . '\fflassist_license_section_developers_callback',
		'fflassist_license'
	);

	// Register a new field in the "fflassist_license_section_developers" section, inside the "fflassist_license" page.
	add_settings_field(
		'fflassist_license_field_code', 
		__( 'License Code', 'fflassist_license' ),
		__NAMESPACE__ . '\fflassist_license_field_code_cb',
		'fflassist_license',
		'fflassist_license_section_developers',
		array(
			'label_for'         => 'fflassist_license_field_code',
			'class'             => 'fflassist_license_row',
			'fflassist_license_custom_data' => 'custom',
		)
	);
}

/**
 * Register our fflassist_license_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', __NAMESPACE__ . '\fflassist_license_settings_init' );


/**
 * Custom option and settings:
 *  - callback functions
 */


/**
 * Developers section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
function fflassist_license_section_developers_callback( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Follow the white rabbit.', 'fflassist_license' ); ?></p>
	<?php
}

/**
 * Pill field callback function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function fflassist_license_field_code_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'fflassist_license_options' );
	?>
        <div class="wrap">
            <form method="post" action="options.php">
                <?php
                settings_fields('custom_form_group');
                do_settings_sections('custom-form');
                submit_button();
                ?>
            </form>
        </div>
    <?php
}


// function custom_admin_menu() {
//     add_menu_page(
//         'Custom Form', // Page title
//         'Custom Form', // Menu title
//         'manage_options', // Capability
//         'custom-form', // Menu slug
//         'custom_form_page', // Function to display the page content
//         'dashicons-admin-generic', // Icon
//         6 // Position
//     );
// }
// add_action('admin_menu', 'custom_admin_menu');

function custom_form_page() {
    ?>
    <div class="wrap">
    <form method="post" action="">
            <input type="text" id="custom_code" name="custom_code" maxlength="15" pattern="[A-Za-z0-9]{15}" required />
            <?php submit_button('Submit'); ?>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $custom_code = sanitize_text_field($_POST['custom_code']);
            if (is_ffl_code_valid($custom_code)) {
                echo '<p>Code is valid</p>';
            } else {
                echo '<p>Code is not in database. Please try again.</p>';
            }
        }
        ?>
    </div>
    <?php
}

function custom_form_settings() {
    register_setting('custom_form_group', 'custom_code');

    add_settings_section(
        'custom_form_section',
        'License Debug Section',
        null,
        'custom-form'
    );

    add_settings_field(
        'custom_code',
        '15-Digit Alphanumeric Code',
        __NAMESPACE__ . '\custom_code_callback',
        'custom-form',
        'custom_form_section'
    );
}
add_action('admin_init', __NAMESPACE__ . '\custom_form_settings');

function custom_code_callback() {
    $custom_code = get_option('custom_code');
    echo '<input type="text" id="custom_code" name="custom_code" value="' . esc_attr($custom_code) . '" maxlength="15" pattern="[A-Za-z0-9]{15}" required />';
}






/**
 * Adds a submenu page under a custom post type parent.
 */
function fflassist_license_options_page() {
	add_submenu_page(
		'edit.php?post_type=ffl-licensee',
        __( 'FFL License Management', 'textdomain' ),
        __( 'FFL License Settings', 'textdomain' ),
		'manage_options',
		'fflassist_license',
		__NAMESPACE__ . '\fflassist_license_options_page_html'
	);
}

/**
 * Register our fflassist_license_options_page to the admin_menu action hook.
 */
add_action( 'admin_menu', __NAMESPACE__ . '\fflassist_license_options_page' );

/**
 * Top level menu callback function
 */
function fflassist_license_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'fflassist_license_messages', 'fflassist_license_message', __( 'Settings Saved', 'fflassist_license' ), 'updated' );
	}

	// show error/update messages
	settings_errors( 'fflassist_license_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "fflassist_license"
			settings_fields( 'fflassist_license' );
			// output setting sections and their fields
			// (sections are registered for "fflassist_license", each field is registered to a specific section)
			do_settings_sections( 'fflassist_license' );
			// output save settings button
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}

