// jQuery( function( $ ) {
//     $( '.js-toggle' ).on( 'click', function() {
//         console.log('here i am')
//     } );
// } );
function runImport() {
    const filename = jQuery('#license-settings-page-fields\\license_import_file').val();
    const numRows = jQuery('#license-settings-page-fields\\record_limit').val();

    jQuery.ajax({
        url: ffl_import_data.ajax_url,
        method: 'POST',
        data: {
            action: 'capweb_start_it_up',
            filename: filename,
            num_rows: numRows
        },
        success: function(response) {
            jQuery('.js-toggle').after('<div>' + response + '</div>');
        },
        error: function(xhr, status, error) {
            jQuery('.js-toggle').after('<div>Error: ' + error + '</div>');
        }
    });
}
