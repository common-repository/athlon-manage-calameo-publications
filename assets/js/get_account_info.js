function ath_check_account() {
    var CalameoSettingPage = {
        action: 'ath_get_account_info',
        form: {
            wrapper     : jQuery('.calameo-account-info'),
            api_key     : jQuery('#calameo-api-key'),
            api_secret  : jQuery('#calameo-api-secret'),
        },
        buttons:{
            check   : jQuery('#check-calameo-account'),
            please  : jQuery('#calameo-please-wait')
        },
        table: {
            tbody   : jQuery('tbody'),
            twrapper: jQuery('.calameo-account-info table tbody'),
        },
        check: {
            success : jQuery('.calameo-account-info .account-success'),
            error   : jQuery('.calameo-account-info .account-error')
        },
    };

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: CalameoSettingPage.action,
            calameo_credentials: {
                calameo_api_key: CalameoSettingPage.form.api_key.val(),
                calameo_api_secret: CalameoSettingPage.form.api_secret.val()
            }
        },
        beforeSend: function(){
            CalameoSettingPage.table.twrapper.html('');
            CalameoSettingPage.form.wrapper.addClass('hidden');
            CalameoSettingPage.buttons.check.addClass('hidden');
            CalameoSettingPage.buttons.please.removeClass('hidden');
        },
        success: function(response) {
            CalameoSettingPage.buttons.check.removeClass('hidden');
            CalameoSettingPage.buttons.please.addClass('hidden');
            CalameoSettingPage.form.wrapper.removeClass('hidden');

            if(response.status == 'ok') {
                CalameoSettingPage.check.error.hide();

                var content = response.content;
                var dashboard = jQuery('table', CalameoSettingPage.form.wrapper);

                dashboard.removeClass('hidden');
                jQuery.each(content, function(key, value) {
                    jQuery('tbody', dashboard).append('<tr class="alternate"><td class="column-columnname"><strong>' + key + '</strong></td><td class="column-columnname">' + (value ? value : '--') + '</td></tr>');
                });
                CalameoSettingPage.check.success.fadeIn('fast');
            }
             else {
               CalameoSettingPage.check.success.hide();
               CalameoSettingPage.check.error.fadeIn('fast');
            }
        },
        dataType: 'json'
    })
}