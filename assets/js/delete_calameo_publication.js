function ath_delete_from_calameo(attachment_id) {

    jQuery.ajax({
        url: ajaxurl,
        beforeSend: function(){
            tb_start_loading_animation();
        },
        type: 'POST',
        data: {
            action: 'ath_delete_calameo_publication',
            attachment_id: attachment_id
        },
        success: function(response) {
            if(response.status == 'ok') {
                window.location.reload();
            }
             else {
                alert('Try again later!')
                tb_stop_loading_animation();
            }
        },
        dataType: 'json'
    })
}
