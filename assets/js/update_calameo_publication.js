function ath_update_calameo_publication(attachment_id) {

    var params = {};
    var tableWrapper = jQuery('.ath-manage-calameo-form-wrapper');

    jQuery('input, select, textarea', tableWrapper).each(function(key, element) {
        if(jQuery(element).attr('name')) {
            var name = jQuery(element).attr('name');
            var value = null;

               if(jQuery(element).attr('type') == 'radio') {
                   if(jQuery(element).is(':checked')) {
                       value = jQuery(element).val();
                   }
               }
               else if(jQuery(element).attr('type') == 'checkbox') {
                   value = jQuery(element).is(':checked') ? jQuery(element).val() : '0';
               }
               else {
                   value = jQuery(element).val()
               }

                if(value) {
                    params[name] = value;
                }
        }
    });

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        ifModified: true,
        beforeSend: function(){
            tb_start_loading_animation();
        },
        data: {
            action: 'ath_update_calameo_publication',
            attachment_id: attachment_id,
            params: params
        },
        success: function(response) {
            if(response.status == 'ok') {
                window.location.reload();
            }
            else {
                alert(response.error.message);
                tb_stop_loading_animation();
            }
        },
        dataType: 'json'
    });

}