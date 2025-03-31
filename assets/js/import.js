jQuery(document).ready(function($) {

    function handleImportClick(event) {
        event.preventDefault();

        var licenseImportFile = $('a.rwmb-file-title').attr('href');
        var recordLimit = $('#record_limit').val() ? $('#record_limit').val() : 0;
        var logTime = $('#log_time').is(':checked') ? 1 : 0;

        console.log('License import file: ' + licenseImportFile);
        console.log('Record Limit: ' + recordLimit);
        console.log('Log Time: ' + logTime);

        if (!licenseImportFile) {
            alert('Please select a license import file from the media library. If you do not have a file to import in the media library, please upload one.');
            return;
        }

        if (!recordLimit) {
            alert('Importing entire file. This may take a while.');
        }

        $('#import-results').html('<div class="import-license-wrap-notice"><p>Working . . .</p></div>');

        $.ajax({
            url: import_data.ajax_url,
            type: 'post',
            data: {
                action: 'capweb_import_license_callback',
                nonce: import_data.nonce,
                license_import_file: licenseImportFile,
                record_limit: recordLimit,
                log_time: logTime,
            },

            success: function(response) {
                console.log('AJAX Success:', response);
                $('#import-results').html('<strong>Success!</strong> ' + response + ' Licenses imported from ' + licenseImportFile + '.');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                $('#import-results').html('<p>A(n) ' + textStatus + '--- ' + errorThrown + ' ---- occurred while processing ' + licenseImportFile + '.<br>Please try again.</p>');
            }
        });
    }

    // Ensure the document is ready before attaching the event handler
    $(document).on('click', '.js-import-license', handleImportClick);
    console.log('Event handler attached');
});