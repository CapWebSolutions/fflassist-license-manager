jQuery(document).ready(function($) {

    function handleImportClick(event) {
        event.preventDefault();
        var licenseImportFile = $('a.rwmb-file-title').attr('href');
        var recordLimit = $('#record_limit').val();
        var recordLimit = recordLimit ? recordLimit : 0;
        console.log('License import file: ' + licenseImportFile);
        console.log('Record Limit: ' + recordLimit);

        if (!licenseImportFile) {
            alert('Please select a license import file from the media library. If you do not have a file to import, please upload one.');
            return;
        }

        $('#import-results').html('<div class="license-wrap-notice"><p>Working . . .</p></div>');

        $.ajax({
            url: import_data.ajax_url,
            type: 'POST',
            data: {
                action: 'capweb_import_licenses_callback',
                license_import_file: licenseImportFile,
                record_limit: recordLimit,
            },
            success: function(response) {
                console.log('AJAX Success:', response);
                handleAjaxSuccess(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                handleAjaxError(jqXHR, textStatus, errorThrown, licenseImportFile);
            }
        });

    }

    function handleAjaxSuccess(response) {
        // $('#import-results').html(response);
        $('#import-results').html('<h2>Success!</h2><br>' + response );
        console.log('Success!');
    }

    function handleAjaxError(jqXHR, textStatus, errorThrown, licenseImportFile) {
        $('#import-results').html('<p>A(n) ' + textStatus + '--- ' + errorThrown + ' ---- occurred while processing ' + licenseImportFile + '.<br>Please try again.</p>');
    }

    // Ensure the document is ready before attaching the event handler
    $(document).on('click', '.js-import-license', handleImportClick);
    console.log('Event handler attached');
});