function ath_upload_calameo_publication(attachment_id)
{
    var requiredFields = jQuery('.ath-manage-calameo-required-field input, .ath-manage-calameo-required-field select, .ath-manage-calameo-required-field textarea');

    if(check_required_fields(requiredFields)) {

        var tableWrapper = jQuery('.ath-manage-calameo-form-wrapper');

        var params = {};

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

        })
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            ifModified: true,
            beforeSend: function(){
                tb_start_loading_animation();
            },
            data: {
                action: 'ath_upload_to_calameo',
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
        })


    }
    else {
        alert('Please fill all the required fields!');
    }
}

function check_required_fields(elements) {
    var filled = true;

    if(elements.size() > 1) {
        elements.each(function(key, element) {
            if(!jQuery(this).val()) {
                jQuery(this).addClass('ath-error');
                filled = false;
            };
        })
    }
    else {
        if(!elements.val()) {
            elements.addClass('ath-error');
            filled = false;
        };
    }

    return filled;
}