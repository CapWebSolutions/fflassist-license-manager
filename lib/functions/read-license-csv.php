<?php
/**
 * Import License CSV File
 *
 * This file includes the function to import a CSV file of FFL Licensees
 *
 * @package      License_Manager
 * @since        1.0.0
 * @link
*/

function capweb_start_it_up($input_file, $number_of_records ) {

    $status = NULL;
    $log_time = false;

    if ( empty ($number_of_records) ) {
        echo "Entire file will be processed.";
    } else {
        echo "Only the first " . wp_kses_post($number_of_records) . " records will be processed.";
    }

    // Check for row limiting import
    $row_limit = false;
    if ( !empty( $number_of_records ) ) $row_limit = intval( $number_of_records );
    
    echo "Importing file " . wp_kses_post($input_file) . "...";
    echo "Row limit is " . wp_kses_post($row_limit) . ".";

    // Check if the filename parameter is a full URL or just the name.ext
    $file_param = $input_file;
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
            echo "The file " . wp_kses_post($file_param) . " was not found in the media library.";
            $status = "error - file not found";
            return $status;
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
        _ffl_license_number CHAR(20) NOT NULL,
        _ffl_license_name CHAR(48) NOT NULL,
        _ffl_business_name CHAR(48) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Log start time if --log is provided 
    $start_time = microtime(true); 

    // Open the CSV file
    $handle = fopen($file_path, 'r');
    if (!$handle) {
        echo "Failed to open the file " . wp_kses_post($file_path) . ".";
        $status = "error - file not opened";
        return $status;
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
            echo "Processed " . wp_kses_post($row_count) . " records...";
        }
    }

    fclose($handle);
    // Log end time and output duration if --log is provided 
    if ( $log_time ) { 
        $end_time = microtime(true); 
        $duration = $end_time - $start_time; 
        $short_dur = round( $duration, 2 );
        echo "Import completed in " . wp_kses_post($short_dur) . " seconds."; 
    } 

    echo "Finished processing " . wp_kses_post($row_count) . " records.";
}
