<?php
/**
 * Manage Calameo Publications by Athlon
 * @Package WordPress
 * @Version: 1.1
 * @Author: Athlon Production <dev@athlonproduction.com>
 */
include_once dirname( __FILE__ ) . '/assets/js/libs/lib-bootstrap.php';
include_once dirname( __FILE__ ) . '/athlon-manage-calameo-publications.php';

if( esc_attr($_GET['attachment_id']) )
{
	$attachment_id = esc_attr($_GET['attachment_id']);
    $path   = AthlonManageCalameoPublications::$CALAMEO_DIRECTORY;
    $domain = AthlonCalameoManageConfiguration::$PLUGIN['localization_domain'];

    $is_uploaded                = AthlonCalameoRegisterFunctions::ath_is_uploaded_to_calameo( $attachment_id );
    $get_publication            = AthlonCalameoRegisterFunctions::ath_get_calameo_publication( $attachment_id );
    $categories                 = AthlonCalameoManageConfiguration::$UI['categories'];
    $formats                    = AthlonCalameoManageConfiguration::$UI['formats'];
    $dialects                   = AthlonCalameoManageConfiguration::$UI['dialects'];
    $comments                   = AthlonCalameoManageConfiguration::$UI['comments'];
    $directions                 = AthlonCalameoManageConfiguration::$UI['directions'];
    $licenses                   = AthlonCalameoManageConfiguration::$UI['licenses'];
    $skins                      = AthlonCalameoManageConfiguration::$UI['skins'];
    $print_and_download_options = AthlonCalameoManageConfiguration::$UI['print_and_download_options'];
    $plugin_skins_urls          = str_replace(plugin_dir_path(__FILE__), plugin_dir_url(__FILE__), glob(plugin_dir_path(__FILE__) . 'skins/*/*.xml'));
    $plugin_skins               = array();

    foreach( $plugin_skins_urls as $skin_url )
    {
        $skin_name = basename( dirname( $skin_url ) );
        $plugin_skins[$skin_name] = $skin_url;
    }

    $file_options = array(
                        'category'          => 'BUSINESS',
                        'format'            => 'MISC',
                        'dialect'           => 'en',
                        'direction'         => 0,
                        'publishing_mode'   => 1,
                        'is_published'      => 1,
                        'comment'           => 4,
                        'mini'              => 1,
                        'adult'             => 0,
                        'name'              => get_the_title( $attachment_id ),
                    );

	if( $is_uploaded )
	{
	    $attachment_calameo_options    = $get_publication->file_options;
	    $get_calameo_subscription_id   = $get_publication->subscription_id;
        $attachment_calameo_options    = unserialize( $attachment_calameo_options );

        if( $attachment_calameo_options && is_array( $attachment_calameo_options ) )
        {
            $subscription_id    = $get_calameo_subscription_id;
            $file_options       = array_merge( $file_options, $attachment_calameo_options );
        }
	    $button_label   = __( 'Update', $domain );
        $on_click_event = "ath_update_calameo_publication({$attachment_id})";
	}

    else
    {
	    $button_label   = __( 'Upload', $domain );
        $on_click_event = "ath_upload_calameo_publication({$attachment_id})";
    }
}
?>
<div class="ath-manage-calameo-form-wrapper">
    <table>
        <tbody>
            <tr class="ath-manage-calameo-required-field subscription-id">
                <th> <?php _e( 'Choose Folder *', $domain ); ?></th>
                <td>
                    <select name="subscription_id" <?php if( $is_uploaded ): ?> disabled="disabled" <?php endif; ?> >
                        <?php
                            $ids = unserialize( get_option('calameo_subscription_ids') );
                            if( is_array( $ids ) ) :
                        ?>
                             <?php foreach( $ids as $name=>$id ) : ?>
                                    <option value="<?php echo $id; ?>" <?php selected( $file_options['subscription_id'], $id ); ?> <?php if( $is_uploaded && $get_calameo_subscription_id == $id ): ?> selected="selected" <?php endif; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                       <?php endif; ?>
                    </select>
                    <?php if( !$is_uploaded ) : ?>
                        <a onclick="ath_get_subscriptions()" ><img src="<?php echo $path; ?>/assets/images/refresh.gif" title="<?php _e( 'Refresh', $domain ); ?>" /></a>
                    <?php endif; ?>
                </td>
            </tr>
            <tr class="ath-manage-calameo-required-field">
                <th><?php _e( 'Category *', $domain ); ?></th>
                <td>
                    <select name="category">
                        <?php foreach( $categories as $category ) : ?>
                            <?php if( $file_options ) : ?>
                                <option value="<?php echo $category; ?>" <?php selected( $file_options['category'], $category ); ?>><?php echo $category; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr class="ath-manage-calameo-required-field">
                <th><?php _e( 'Format *', $domain ); ?></th>
                <td>
                    <select name="format">
                        <?php foreach( $formats as $format ) : ?>
                            <option value="<?php echo $format; ?>" <?php selected( $file_options['format'], $format ); ?>><?php echo $format; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr class="ath-manage-calameo-required-field">
                <th><?php _e( 'Dialect *', $domain ); ?></th>
                <td>
                    <select name="dialect">
                        <?php foreach( $dialects as $key => $dialect ) : ?>
                            <option value="<?php echo $dialect; ?>" <?php selected( $file_options['dialect'], $dialect ); ?>><?php echo $key; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Name', $domain ); ?></th>
                <td>
                    <input type="text" value="<?php if( isset($file_options['name']) ) echo $file_options['name']; ?>" name="name" />
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Description', $domain ); ?></th>
                <td>
                    <textarea cols="60" rows="5" name="description"><?php if( isset($file_options['description']) ) echo $file_options['description']; ?></textarea>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Publishing mode', $domain ); ?></th>
                <td>
                    <label>
                        <input type="radio" name="publishing_mode" value="1" <?php checked( '1', $file_options['publishing_mode'] ); ?> />
                        <?php _e( 'Public', $domain ); ?>
                    </label>
                    <label>
                        <input type="radio" name="publishing_mode" value="2" <?php checked( '2', $file_options['publishing_mode'] ); ?> />
                        <?php _e( 'Private', $domain ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Activated', $domain ); ?></th>
                <td>
                    <input type="checkbox" name="is_published" value="1" <?php checked( '1', $file_options['is_published'] ); ?> />
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Allow Subscription', $domain ); ?></th>
                <td>
                    <input type="checkbox" name="subscribe" value="1" <?php checked( '1', $file_options['subscribe'] ); ?> />
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Allow Comments', $domain ); ?></th>
                <td>
                    <select name="comment">
                        <?php foreach( $comments as $key => $description ) : ?>
                            <option value="<?php echo $key; ?>" <?php selected( $file_options['comment'], $key ); ?>><?php echo $description; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Allow Downloads', $domain ); ?></th>
                <td>
                    <select name="download">
                        <?php foreach( $print_and_download_options as $key => $description ) : ?>
                            <option value="<?php echo $key; ?>" <?php selected( $file_options['download'], $key ); ?>><?php echo $description; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Allow Print', $domain ); ?></th>
                <td>
                    <select name="print">
                        <?php foreach( $print_and_download_options as $key => $description ) : ?>
                            <option value="<?php echo $key; ?>" <?php selected( $file_options['print'], $key ); ?>><?php echo $description; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Allow MiniCalameo', $domain ); ?></th>
                <td>
                    <input type="checkbox" name="mini" value="1" <?php checked( '1', $file_options['mini'] ); ?> />
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Restrict access to adults', $domain ); ?></th>
                <td>
                    <input type="checkbox" name="adult" value="1" <?php checked( '1', $file_options['adult'] ); ?> />
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Choose Direction', $domain ); ?></th>
                <td>
                    <select name="direction">
                        <?php foreach( $directions as $key => $direction ) : ?>
                            <option value="<?php echo $key; ?>" <?php selected( $file_options['direction'], $key ); ?>><?php echo $direction; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th> <?php _e( 'Choose License', $domain ); ?></th>
                <td>
                    <select name="license">
                        <option value=""><?php _e( 'Traditional Copyright', $domain ); ?></option>
                        <?php foreach( $licenses as $license => $description ) : ?>
                            <option value="<?php echo $license; ?>" <?php selected( $file_options['license'], $license ); ?>><?php echo $description; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Skin URL', $domain ); ?></th>
                <td>
                    <select id="skin_url" onchange="jQuery(this).siblings('input').val(jQuery(this).val());">
                        <option value=""><?php _e( 'Custom skin url', $domain ); ?></option>
                        <?php foreach( $skins as $skin => $skin_url ) : ?>
                            <option value="<?php echo $skin_url; ?>" <?php selected( $file_options['skin_url'], $skin_url ); ?>><?php echo $skin; ?></option>
                        <?php endforeach; ?>
                        <?php foreach( $plugin_skins as $skin => $skin_url ) : ?>
                            <option value="<?php echo $skin_url; ?>" <?php selected( $file_options['skin_url'], $skin_url ); ?>><?php echo $skin; ?></option>
                        <?php endforeach; ?>
                    </select><br />
                    <input type="text" value="<?php if( isset($file_options['skin_url']) ) echo $file_options['skin_url']; ?>" name="skin_url" class="skin_url"/>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Logo URL', $domain ); ?></th>
                <td>
                    <input type="text" value="<?php if( isset($file_options['logo_url']) ) echo $file_options['logo_url']; ?>" name="logo_url" />
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Logo Link', $domain ); ?></th>
                <td>
                    <input type="text" value="<?php if( isset($file_options['logo_link_url']) ) echo $file_options['logo_link_url']; ?>" name="logo_link_url " />
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Background URL', $domain ); ?></th>
                <td>
                    <input type="text" value="<?php if( isset($file_options['background_url']) ) echo $file_options['background_url']; ?>" name="background_url  " />
                </td>
            </tr>
        </tbody>
    </table>

    <input type="button" class="button-primary upload" value="<?php echo $button_label; ?>" onclick="<?php echo $on_click_event; ?>" is_up/>
    <input type="button" class="button-primary" value="<?php _e( 'Cancel', $domain ); ?>" onclick="tb_remove()" style="float:right;"/>

</div>
<script type="text/javascript">
jQuery(function() {
    var tableWrapper = jQuery('.ath-manage-calameo-form-wrapper');
    jQuery('input, select, textarea', tableWrapper).each(function(key, element) {

        jQuery(element).on('change', function() {
            if(!jQuery('#skin_url').val() == '') {
                jQuery('.skin_url').attr('readonly', 'readonly');
            }
            else {
                jQuery('.skin_url').removeAttr('readonly');
            }
        })
    });

});
</script>