jQuery(document).ready(function($) {

    function handleModifyClick(event) {
        event.preventDefault();

        $.ajax({
            url: modify_data.ajax_url,
            type: 'post',
            data: {
                action: 'capweb_modify_license_callback',
                nonce: modify_data.nonce,
            },

            success: function(response) {
                console.log('AJAX Success:', response);
                $('#modify-results').html('<strong>Success!</strong> ');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                $('#modify-results').html('<p>A(n) ' + textStatus + '--- ' + errorThrown + '</p>');
            }
        });
    }

    // Ensure the document is ready before attaching the event handler
    $(document).on('click', '.js-modify-license', handleModifyClick);
    console.log('Event handler attached');
});