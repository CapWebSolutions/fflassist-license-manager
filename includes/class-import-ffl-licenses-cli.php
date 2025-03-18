<?php
/**
 * Import FFL License data from a CSV file.
 * 
 * ## OPTIONS
 * 
 * --file=<file.csv> 
 * : The name of the csv FFL Licenses file to be imported. Use URL from media library.  
 * 
 * [--log]
 * : Displays the duration time of the import. 
 * 
 * [--limit=<number>]
 * : Limits to number of records to import. Defautls to entire file.
 * 
 * ## EXAMPLES
 * 
 *    wp import_ffl_data --file=0924-ffl-list-complete.csv --log --limit=5000 
 *         // Reads the file from the current disk location and processes the first 5000 records.
 * 
 *    wp import_ffl_data --media=0924-ffl-list-10000-records.csv --log --limit=2500
 *         // Reads the file from the media library and processes the first 2500 records.
 *
 * @when after_wp_load
 */

class Import_Ffl_Data
{

    public function __invoke($args, $assoc_args)
    {
           global $wpdb;

        // Check if the "file" parameter is provided
        // https://make.wordpress.org/cli/handbook/guides/commands-cookbook/#command-internals

        if (empty($assoc_args['file']) && empty($assoc_args['media']) ) {
            WP_CLI::error("Please provide a CSV file using either the '--file' or '--media' parameter.");
            exit;
        }

        // Grab file parameter from either --file or --media. --media takes presedence if both present.
        $file_param = $assoc_args['media'] ?? $assoc_args['file'];

        WP_CLI::debug('file parameter used ' . $file_param);
        // Check for row limiting import
        $row_limit = false;
        if (!empty($assoc_args['limit']) ) { $row_limit = intval($assoc_args['limit']);
        }
        WP_CLI::debug('row limit ' . $row_limit);

        // Check if the filename parameter is a full URL or just the name.ext
        if (filter_var($file_param, FILTER_VALIDATE_URL) ) {
            // The $file_param name is valid formatted URL, ie not a HDD address 

            $file_path = get_attached_file(attachment_url_to_postid($file_param));
            WP_CLI::debug('Full file path on server ' . $file_path);
        } elseif (file_exists($file_param) ) {
            // The $file_param is a valid file path on the local file system.
            $file_path = $file_param;
            WP_CLI::debug('File exists path provided directly ' . $file_path);
        } else {
            // Here it is a URL, decode the components and retrieve the file path. 
            WP_CLI::debug('file URL provided ' . $file_param);
            $attachments = get_posts(
                array(
                'post_type'   => 'attachment',
                'post_status' => 'inherit',
                'meta_query'  => array(
                    array(
                        'key'     => '_wp_attached_file',
                        'value'   => $file_param,
                        'compare' => 'LIKE',
                    )
                )
                )
            );

            if (!empty($attachments)) {
                $file_path = get_attached_file($attachments[0]->ID);
                WP_CLI::debug('Full file path on server for media file provided: ' . $file_path);

            } else {
                WP_CLI::error("The file '{$file_param}' was not found in the media library or on the server.");
                exit;
            }
        }
        
        // Define the table name with the prefix
        $table_name = $wpdb->prefix . 'ffl_licensees';

        // Drop the table if it already exists
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        // Create the table
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            _ffl_license_number VARCHAR(20) NOT NULL,
            _ffl_license_name VARCHAR(48) NOT NULL,
            _ffl_business_name VARCHAR(48) NOT NULL,
            _ffl_lic_seg1 VARCHAR(1) NOT NULL,
            _ffl_lic_seg2 VARCHAR(2) NOT NULL,
            _ffl_lic_seg3 VARCHAR(3) NOT NULL,
            _ffl_lic_seg4 VARCHAR(2) NOT NULL,
            _ffl_lic_seg5 VARCHAR(2) NOT NULL,
            _ffl_lic_seg6 VARCHAR(5) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        include_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Log start time if --log is provided 
        $log_time = !empty($assoc_args['log']); 
        if ($log_time ) { 
            $start_time = microtime(true); 
        }

        // Open the CSV file
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            include_once ABSPATH . '/wp-admin/includes/file.php';
            WP_Filesystem();
        }

        // Read in the entire file to $file_contents
        $file_contents = $wp_filesystem->get_contents($file_path);

        if ($file_contents === false) {
            $error = error_get_last();
            WP_CLI::error("Failed to open the file '{$file_path}' using WP_Filesystem. Error: " . $error['message']);
            exit;
        }

        // Handle file operations in memory rather than on disk. 
        $stream = fopen('php://memory', 'r+');
        if (!$stream) {                     // Opened memory stream okay?
            WP_CLI::error("Failed to open the memory file: '{$file_path}' with error code: " . error_get_last()['message']);
            exit;
        }
        fwrite($stream, $file_contents);    // Copy the file to memory
        rewind($stream);                    // Return to beginning of memory file

        $row_count = 0;
        // Process the entire file contents which now resides in memory -> $stream
        // Skip the 1st record. 
        $trash_first = fgetcsv($stream, 0, ",", "\"", "\\");
        while (($data = fgetcsv($stream, 0, ",", "\"", "\\") ) !== false) {
            if (count($data) < 8  || ( $row_limit && ( $row_count > $row_limit ) )  ) { continue;
            }

            // Concatenate the first 6 columns with a "-" separator
            $license_segs = array_slice($data, 0, 6);
            $license = sanitize_text_field(substr(implode('-', $license_segs), 0, 20));

            // Read the next two columns into appropriate fields
            $license_name = sanitize_text_field($data[6]);
            $business_name = sanitize_text_field($data[7]);
            // Insert the data into the database table
            $wpdb->insert(
                $table_name, [
                '_ffl_license_number' => $license,
                '_ffl_license_name' => $license_name,
                '_ffl_business_name' => $business_name,
                '_ffl_lic_seg1' => $license_segs[0],
                '_ffl_lic_seg2' => $license_segs[1],
                '_ffl_lic_seg3' => $license_segs[2],
                '_ffl_lic_seg4' => $license_segs[3],
                '_ffl_lic_seg5' => $license_segs[4],
                '_ffl_lic_seg6' => $license_segs[5],
                ]
            );

            $row_count++;
            if ($row_count % 1000 == 0) {
                WP_CLI::log("Processed {$row_count} records.");
            }
        }

        fclose($stream);
        // Log end time and output duration if --log is provided 
        if ($log_time ) { 
            $end_time = microtime(true); 
            $duration = $end_time - $start_time; 
            $short_dur = round($duration, 2);
            WP_CLI::log("Import completed in {$short_dur} seconds."); 
        } 

        WP_CLI::success("Finished processing {$row_count} records.");
    }

}

// If CLI is installed register the new WP-CLI command
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('import_ffl_data', 'Import_Ffl_Data');
}
