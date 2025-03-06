jQuery(document).ready(function($) {

    function handleImportClick(event) {
        event.preventDefault();
        var licenseImportFile = $('a.rwmb-file-title').attr('href');
        console.log('License import file: ' + licenseImportFile);

        if (!licenseImportFile) {
            alert('Please select a license import file from the media library. If you do not have a file to import, please upload one.');
            return;
        }

        $('#import-results').html('<div class="license-wrap-notice"><p>Working . . .</p></div>');

        $.ajax({
            url: import_data.ajax_url,
            type: 'POST',
            data: {
                action: 'capweb_import_licenses',
                license_import_file: licenseImportFile
            },
            success: handleAjaxSuccess,
            error: handleAjaxError
        });
    }

    function handleAjaxSuccess(response) {
        $('#import-results').html(response);
    }

    function handleAjaxError( jqXHR, textStatus, errorThrown) {
        $('#import-results').html('<p>A(n) ' + textStatus + '--- ' + errorThrown + ' ---- occurred while processing your request.<br>Please try again.</p>');
    }

    // Ensure the document is ready before attaching the event handler
    $(document).on('click', '.js-import-license', handleImportClick);
    console.log('Event handler attached');
});