<?php
/**
 * Manage Calameo Publications by Athlon
 * Manage Media Columns
 * @Package WordPress
 * @Version: 1.1
 * @Author: Athlon Production <dev@athlonproduction.com>
 */
add_filter( 'manage_media_columns', 'ath_add_upload_to_calameo_column' );
function ath_add_upload_to_calameo_column( $columns )
{
    end( $columns );
    $last_key = key( $columns) ;
    $last_column = array_pop( $columns );
    $columns['upload_to_calameo'] = 'Calameo';
    $columns[$last_key] = $last_column;

    return $columns;
}

add_action( 'manage_media_custom_column', 'ath_manage_upload_to_calameo_media_column' );
function ath_manage_upload_to_calameo_media_column( $column )
{
    global $post;
    $domain = AthlonCalameoManageConfiguration::$PLUGIN['localization_domain'];

    switch ( $column )
    {
        case 'upload_to_calameo':
            if( AthlonCalameoRegisterFunctions::ath_is_of_convertable_for_calameo_mimetype( $post->ID ) )
            {
                if( AthlonCalameoRegisterFunctions::ath_is_uploaded_to_calameo( $post->ID ) )
                {
                    echo '<input type="button" class="button-primary hide-if-no-js" value="Update" onclick="tb_show(\'' .  __( 'Update Calameo Publication', $domain ) . '\', \'' . AthlonManageCalameoPublications::$CALAMEO_DIRECTORY . '/thickbox_content.php?attachment_id=' . $post->ID . '\');"/>&nbsp;';
                    echo '<input type="button" class="button-primary hide-if-no-js" value="Delete" onclick="ath_delete_from_calameo(' . $post->ID . ');"/>';
                }
                else
                {
                    echo '<input type="button" class="button-primary hide-if-no-js" value="Upload" onclick="tb_show(\'' .  __( 'Upload Calameo Publication', $domain ) . '\', \'' . AthlonManageCalameoPublications::$CALAMEO_DIRECTORY . '/thickbox_content.php?attachment_id=' . $post->ID . '\')"/>';
                }
            }

            break;
    }
}

add_action( 'admin_init', 'ath_enqueue_js_to_media_library' );
function ath_enqueue_js_to_media_library()
{
    global $pagenow;

    if( $pagenow == 'upload.php' )
    {
        /* Add thickbox functionality and js files*/
        $path = AthlonManageCalameoPublications::$CALAMEO_DIRECTORY;
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_script( 'upload_calameo_publication', $path . '/assets/js/upload_calameo_publication.js', false, false, ASSETS_JAVASCRIPTS_IN_FOOTER );
        wp_enqueue_script( 'update_calameo_publication', $path . '/assets/js/update_calameo_publication.js', false, false, ASSETS_JAVASCRIPTS_IN_FOOTER );
        wp_enqueue_script( 'delete_calameo_publication', $path . '/assets/js/delete_calameo_publication.js', false, false, ASSETS_JAVASCRIPTS_IN_FOOTER );
        wp_enqueue_script( 'spinner', $path . '/assets/js/spinner.js', false, false, ASSETS_JAVASCRIPTS_IN_FOOTER );
    }
}

add_action( 'admin_init', 'ath_enqueue_css_to_media_library' );
function ath_enqueue_css_to_media_library()
{
    global $pagenow;

    if( $pagenow == 'upload.php' )
    {
        /* Add thickbox functionality */
        wp_enqueue_style( 'thickbox' );
        wp_enqueue_style( 'ath_manage_calameo_publications', AthlonManageCalameoPublications::$CALAMEO_DIRECTORY . '/assets/css/ath_manage_calameo_publications.css'  );
    }
}
