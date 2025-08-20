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