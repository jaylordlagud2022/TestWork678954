jQuery(document).ready(function($) {
    // Search city via AJAX
    $('#city-search-form').on('submit', function(e) {
        e.preventDefault();
        var searchQuery = $('#city-search').val();

        // Make AJAX request
        $.ajax({
            url: ajaxurl,
            method: 'GET',
            data: {
                action: 'search_city',
                query: searchQuery
            },
            success: function(response) {
                // Update the cities table with the response
                $('#cities-table tbody').html(response);
            }
        });
    });
});
