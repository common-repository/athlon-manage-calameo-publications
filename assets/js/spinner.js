function tb_start_loading_animation() {
    jQuery("body").append("<div id='ath_tb_custom_overlay'></div>");
    jQuery("#ath_tb_custom_overlay").addClass("TB_overlayBG");
    jQuery("body").append("<div id='ath_tb_custom_load'><img src='"+imgLoader.src+"' width='208' /></div>");//add loader to the page
    jQuery('#ath_tb_custom_load').show();/*show loader*/
}

function tb_stop_loading_animation() {
    jQuery("#ath_tb_custom_overlay").remove();
    jQuery("#ath_tb_custom_load").remove();
}
