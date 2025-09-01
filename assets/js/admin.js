if (typeof (wlyr_jquery) == 'undefined') {
    wlyr_jquery = jQuery.noConflict();
}

wlyr_jquery(document).on('click', '#wlyr-main-page #wlyr-save-settings', function () {
    wlyr_jquery.ajax({
        data: {
            action: 'wlyr_save_settings',
            wlyr_nonce: wlyr_localize_data.nonce,
            secret_key: wlyr_jquery('#wlyr-secret_key').val()
        },
        type: 'post',
        url: wlyr_localize_data.ajax_url,
        beforeSend: function () {
            wlyr_jquery('#wlyr-save-settings').attr('disabled', true);
            wlyr_jquery('#wlyr-save-settings').html(wlyr_localize_data.saving_button_label);
        },
        success: function (json) {
            alertify.set('notifier', 'position', 'top-right');
            wlyr_jquery('#wlyr-save-settings').attr('disabled', false);
            wlyr_jquery('#wlyr-save-settings').html(wlyr_localize_data.saved_button_label);
            if (json.success === true) {
                alertify.success(json.data.message);
            } else {
                alertify.error(json.data.message);
            }
        }
    });
});

// Copy webhook URL to clipboard functionality
wlyr_jquery(document).on('click', '#wlyr-main-page #wlyr-copy-webhook-url', function () {
    const button = wlyr_jquery(this);
    const webhookUrlText = wlyr_jquery('#wlyr-webhook-url-text').text();
    const originalButtonContent = button.html();
    
    // Try to copy using the modern Clipboard API
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(webhookUrlText).then(function() {
            // Success feedback
            showCopySuccess(button, originalButtonContent);
        }).catch(function(err) {
            // Fallback to legacy method
            fallbackCopyToClipboard(webhookUrlText, button, originalButtonContent);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyToClipboard(webhookUrlText, button, originalButtonContent);
    }
});

function showCopySuccess(button, originalContent) {
    // Change button appearance to show success
    button.addClass('copied');
    button.html('<i class="wlr wlrf-check"></i><span>' + wlyr_localize_data.copied_button_label + '</span>');
    
    // Show success notification
    if (typeof alertify !== 'undefined') {
        alertify.set('notifier', 'position', 'top-right');
        alertify.success(wlyr_localize_data.copied_notification_label);
    }
    
    // Add a subtle animation to the webhook container
    const webhookContainer = wlyr_jquery('.wlyr-webhook-url-container');
    webhookContainer.addClass('copied-animation');
    
    // Reset button after 2.5 seconds
    setTimeout(function() {
        button.removeClass('copied');
        button.html(originalContent);
        webhookContainer.removeClass('copied-animation');
    }, 2500);
}

function fallbackCopyToClipboard(text, button, originalContent) {
    // Create a temporary textarea element
    const textarea = wlyr_jquery('<textarea>');
    textarea.val(text);
    textarea.css({
        position: 'fixed',
        top: -1000,
        left: -1000,
        opacity: 0
    });
    
    wlyr_jquery('body').append(textarea);
    textarea[0].select();
    textarea[0].setSelectionRange(0, 99999); // For mobile devices
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(button, originalContent);
        } else {
            showCopyError();
        }
    } catch (err) {
        showCopyError();
    }
    
    // Remove the temporary textarea
    textarea.remove();
}

function showCopyError() {
    if (typeof alertify !== 'undefined') {
        alertify.set('notifier', 'position', 'top-right');
        alertify.error(wlyr_localize_data.copy_error_label);
    } else {
        alert(wlyr_localize_data.copy_error_label);
    }
}