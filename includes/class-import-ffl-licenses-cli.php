<?php
/**
 * Import data from a CSV file into a custom database table.
 * 
 * ## OPTIONS
 * 
 * --file <file.csv> 
 *   : The name of the csv FFL Licenses file to be imported. Use URL from media library.  
 * 
 * --log
 *    : If present, a duration time will be displayed at the end of processing. 
 * 
 * ## EXAMPLES
 * 
 *      wp import_ffl_data --file 0924-ffl-list-complete.csv --log-file
 *
 * @when after_wp_load
 */
class Import_Ffl_Data {

     public function __invoke($args, $assoc_args) {
            global $wpdb;

        // Check if the "file" parameter is provided
        // https://make.wordpress.org/cli/handbook/guides/commands-cookbook/#accepting-arguments

        if ( empty( $assoc_args['file'] ) ) {
            WP_CLI::error("Please provide a CSV file using the '--file' parameter.");
            exit;
        }

        // Check for row limiting import
        $row_limit = false;
        if ( !empty( $assoc_args['limit'] ) ) $row_limit = intval( $args[1] );
        
        // Check if the filename parameter is a full URL or just the name.ext
        $file_param = $assoc_args['file'];
        if (filter_var($file_param, FILTER_VALIDATE_URL)) {
            $file_path = get_attached_file(attachment_url_to_postid($file_param));
        } else {
            // Here it is a URL, decode the components and retrieve the file path. 
            $attachments = get_posts(array(
                'post_type'   => 'attachment',
                'post_status' => 'inherit',
                'meta_query'  => array(
                    array(
                        'key'     => '_wp_attached_file',
                        'value'   => $file_param,
                        'compare' => 'LIKE',
                    )
                )
            ));

            if (!empty($attachments)) {
                $file_path = get_attached_file($attachments[0]->ID);
            } else {
                WP_CLI::error("The file '{$file_param}' was not found in the media library.");
                exit;
            }
        }

        // Check if the file exists
        if (!file_exists($file_path)) {
            WP_CLI::error("The file '{$file_path}' does not exist.");
            exit;
        }
        
        // Define the table name with the prefix
        $table_name = $wpdb->prefix . 'ffl_licensees';

        // Drop the table if it already exists
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        // Create the table
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            _ffl_license_number CHAR(20) NOT NULL,
            _ffl_license_name CHAR(48) NOT NULL,
            _ffl_business_name CHAR(48) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Log start time if --log is provided 
        $log_time = !empty( $assoc_args['log'] ); 
        if ( $log_time ) { 
            $start_time = microtime(true); 
        }

        // Open the CSV file
        $handle = fopen($file_path, 'r');
        if (!$handle) {
            WP_CLI::error("Failed to open the file '{$file_path}'.");
            exit;
        }

        $row_count = 0;
        // Skip the 1st record. 
        $trash_first = fgetcsv($handle, length: null, separator: ",", enclosure: "\"", escape: "\\" );
        
        while (($data = fgetcsv($handle, length: null, separator: ",", enclosure: "\"", escape: "\\" ) ) !== false) {
            // Skip empty rows. Process only the first 8 columns
            if ( count($data) < 8  || ( $row_limit && ($row_count > $row_limit ) )  ) continue;

            // Concatenate the first 6 columns with a "-" separator
            $license = sanitize_text_field( substr(implode('-', array_slice($data, 0, 6)), 0, 20) );

            // Read the next two columns into appropriate fields
            $license_name = sanitize_text_field($data[6]);
            $business_name = sanitize_text_field($data[7]);

            // Insert the data into the database table
            $wpdb->insert($table_name, [
                '_ffl_license_number' => $license,
                '_ffl_license_name' => $license_name,
                '_ffl_business_name' => $business_name,
            ]);

            $row_count++;
            if ($row_count % 1000 == 0) {
                WP_CLI::log("Processed {$row_count} records...");
            }
        }

        fclose($handle);
        // Log end time and output duration if --log is provided 
        if ( $log_time ) { 
            $end_time = microtime(true); 
            $duration = $end_time - $start_time; 
            $short_dur = round( $duration, 2 );
            WP_CLI::log("Import completed in {$short_dur} seconds."); 
        } 

        WP_CLI::success("Finished processing {$row_count} records.");
    }

}

// If CLI is installed register the new WP-CLI command
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('import_ffl_data', 'Import_Ffl_Data');
}