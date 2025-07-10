jQuery(document).ready(function ($) {
    $('.regenerate-tldr-btn').on('click', function () {
        var $button = $(this);
        var postId = $button.data('post-id');
        $.post(TurbolineAjax.ajax_url, {
            action: 'turboline_regenerate_tldr',
            post_id: postId,
            _ajax_nonce: TurbolineAjax.nonce
        }, function (response) {
            if (response.success) {
                $button.siblings('.turboline-tldr-inner').text(response.data.excerpt);
                $status.text('Regenerated successfully.');
            }
        });
    });
});

jQuery(document).ready(function ($) {
    $('.tldr-color-field').wpColorPicker();
});

