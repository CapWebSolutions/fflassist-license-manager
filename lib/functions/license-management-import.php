<?php 

function capweb_perform_license_file_import( $import_file, $record_limit, $log_time ) {

    // Get file to import.
    $file_path = capweb_get_filename( $import_file );

    var_dump($file_path);

    // Initialize the import process.
    $ffl_table = capweb_initialize_ffl_license_db_table();
    var_dump($ffl_table);

    // Setup file and read it into memory.
    $stream = capweb_setup_import_file( $file_path );

    // Process the imported file stream and return the number of records processed.
    $result = capweb_process_imported_file_stream( $stream, $ffl_table, $record_limit );
    var_dump($result);

    return $result;
}


function capweb_get_filename( $file_param )
{

    // Check if the filename parameter is a full URL or just the name.ext
    // if (filter_var($file_param, FILTER_VALIDATE_URL) ) {
    //     // The $file_param name is valid formatted URL, ie not a HDD address 

    //     $file_path = get_attached_file(attachment_url_to_postid($file_param));
    //     error_log( '1 - $file_path ' . var_export( $file_path, true ) );
    
    // } elseif (file_exists($file_param) ) {
    //     // The $file_param is a valid file path on the local file system.
    //     $file_path = $file_param;
    //     error_log( '2 - $file_path ' . var_export(  $file_path, true ) );
    
    // } else {
        // Here it is a URL, decode the components and retrieve the file path. 
        // $attachments = get_posts(
        //     array(
        //     'post_type'   => 'attachment',
        //     'post_status' => 'inherit',
        //     'meta_query'  => array(
        //         array(
        //             'key'     => '_wp_attached_file',
        //             'value'   => $file_param,
        //             'compare' => 'LIKE',
        //         )
        //     )
        //     )
        // );

        // if (!empty($attachments)) {
        //     $file_path = get_attached_file($attachments[0]->ID);
        //     error_log( 'Full file path on server for media file provided: '  . var_export( $file_path, true ) );

        // } else {
        //     error_log( '$file_param} was not found in the media library or on the server. ' . var_export( $file_param, true ) );
        //     exit;
        // }
    // }
    global $wp_filesystem;

    // Ensure the $wp_filesystem global is initialized
    if ( ! function_exists('WP_Filesystem') ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    WP_Filesystem();
    
    // Get the WordPress upload directory
    $upload_dir = wp_upload_dir();
    
    // Convert the URL to a file path
    $file_path = str_replace(
        $upload_dir['baseurl'], // The base URL of the upload directory
        $upload_dir['basedir'], // The base directory of the upload directory
        $file_param
    );
    
    // Check if the file exists
    // if ( $wp_filesystem->exists($file_path) ) {
    //     // Read the file's contents
    //     $file_contents = $wp_filesystem->get_contents($file_path);
    // } else {
    //     echo 'File does not exist.';
    // }

    return $file_path;
}

function capweb_initialize_ffl_license_db_table() {    
    global $wpdb;
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

    return $table_name;
}

 
function capweb_setup_import_file( $file_path )
{

    // Open the file handling library
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        include_once ABSPATH . '/wp-admin/includes/file.php';
        WP_Filesystem();
    }

    // Read in the entire file to $file_contents
    $file_contents = $wp_filesystem->get_contents($file_path);

    if ($file_contents === false) {
        $error = error_get_last();
        // WP_CLI::error("Failed to open the file '{$file_path}' using WP_Filesystem. Error: " . $error['message']);
        exit;
    }

    // Handle file operations in memory rather than on disk. 
    $stream = fopen('php://memory', 'r+');
    if (!$stream) {                     // Opened memory stream okay?
        // WP_CLI::error("Failed to open the memory file: '{$file_path}' with error code: " . error_get_last()['message']);
        exit;
    }
    fwrite($stream, $file_contents);    // Copy the file to memory
    rewind($stream);                    // Return to beginning of memory file
    
    return $stream;

}

function capweb_process_imported_file_stream( $stream, $table_name, $row_limit )
{
    
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

            ?>
            <script>console.log("Processed {$row_count} records.");</script>
            <?php
            echo "<script>$('#import-results').html('<h2>Success!</h2><br>' + response );</script>";

        }
    }

    fclose($stream);

    // Return the row count; account for skipped record
    $row_count--;
    return $row_count;

}

