jQuery(document).ready(function ($) {
    $('#kitab-admin-notice').on('click', '.kitab-dismiss-notice', function (e) {
        e.preventDefault();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kitab_dismiss_notice'
            },
            success: function (response) {
                $('#kitab-admin-notice').remove();
            }
        });
    });

    $('#kitab-admin-notice').on('click', '.kitab-already-rated', function (e) {
        e.preventDefault();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kitab_already_rated'
            },
            success: function (response) {
                $('#kitab-admin-notice').replaceWith(response);
            }
        });
    });
});