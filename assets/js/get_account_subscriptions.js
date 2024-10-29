function ath_get_subscriptions() {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'ath_get_account_subscriptions',
            calameo_credentials: {
                calameo_api_key: jQuery('#calameo_api_key').val(),
                calameo_api_secret: jQuery('#calameo_api_secret').val()
            }
        },
        success: function(response) {
            if(response.status == 'success') {
                var select = jQuery('select[name=subscription_id]');
                select.html('');
                jQuery.each(response.subscriptions, function(key, subscription) {
                    var option = document.createElement('option');
                    option.value = subscription.ID;
                    option.innerHTML = subscription.Name;
                    select.append(option);
                });
            }
            else {
            }
        },
        dataType: 'json'
    })
}