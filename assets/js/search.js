jQuery(document).ready(function($) {
    $('.js-search-license').on('click', function(e) {
        e.preventDefault();
        var licenseNumber = $('input[name="license_number"]').val();

        if (!licenseNumber) {
            alert('Please enter a license number.');
            return;
        }

        // $('#license-settings-page-search').append('<div class="license-wrap-notice"><p>Working . . .</p></div>');

        $.ajax({
            url: search_data.ajax_url,
            type: 'POST',
            data: {
                action: 'capweb_search_license',
                license_number: licenseNumber
            },
            success: function(response) {
                $('#search-results').html(response);
            },
            error: function() {
                $('#search-results').html('<p>An error occurred while processing your request. Please try again.</p>');
            }
        });
    });
});


