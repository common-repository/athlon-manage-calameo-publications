<?php require_once( dirname( __FILE__ ) . '/lib-bootstrap.php'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php $domain = AthlonCalameoManageConfiguration::$PLUGIN['localization_domain']; ?>
    <title><?php _e( 'Calameo Options Viewer', $domain ); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script language="javascript" type="text/javascript" src="<?php echo includes_url(); ?>js/tinymce/tiny_mce_popup.js"></script>
    <script language="javascript" type="text/javascript" src="<?php echo includes_url(); ?>js/jquery/jquery.js"></script>
    <script language="javascript" src="../calameo_dialog.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo AthlonManageCalameoPublications::$CALAMEO_DIRECTORY . '/assets/css/ath_manage_calameo_publications.css'; ?>" />
</head>
<body onload="ath_default_size()">
    <form action="" id="calameodialog">
        <?php
            $get_calameo_publications   = AthlonCalameoRegisterFunctions::ath_get_calameo_publications();
            $get_calameo_modes          = AthlonCalameoManageConfiguration::$UI['mode'];
            $get_calameo_views          = AthlonCalameoManageConfiguration::$UI['view'];
            $get_calameo_sizes          = AthlonCalameoManageConfiguration::$UI['size'];
            $get_calameo_destinations   = AthlonCalameoManageConfiguration::$UI['clickto'];
            $get_calameo_targets        = AthlonCalameoManageConfiguration::$UI['clicktarget'];
        ?>
        <table id="calameo_wrapper">
            <tbody>
            <tr>
                <th><?php _e( 'Viewer', $domain ); ?></th>
                <td>
                    <select id="file" name="files">
                            <option value=""><?php _e( 'Select a file...', $domain ); ?></option>
                        <?php foreach( $get_calameo_publications as $key=>$get_calameo_publication ) : ?>
                          <?php
                               $get_calameo_publication_id = $get_calameo_publications[$key]->calameo_id;
                               $get_calameo_publication_options = unserialize( $get_calameo_publications[$key]->file_options );
                               $get_calameo_title_publication = $get_calameo_publication_options['name'];
                           ?>
                            <option value="<?php echo $get_calameo_publication_id; ?>"><?php echo $get_calameo_title_publication; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Mode', $domain ); ?></th>
                <td>
                    <select id="mode" name="mode">
                        <option value="default"><?php _e( 'Default', $domain ); ?></option>
                        <?php foreach( $get_calameo_modes as $key=>$modes ) : ?>
                            <option value="<?php echo $key; ?>"><?php echo $modes; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'View', $domain ); ?></th>
                <td>
                    <select id="view" name="view">
                    <option value="default"><?php _e( 'Default', $domain ); ?></option>
                    <?php foreach( $get_calameo_views as $key=>$views ) : ?>
                        <option value="<?php echo $views; ?>"><?php echo $views; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr class="ath-calameo-size">
                <th><?php _e( 'Size', $domain ); ?></th>
                <td>
                    <select id="size" name="size" onchange="ath_select_size(this)">
                        <?php foreach( $get_calameo_sizes as $key=>$sizes ) : ?>
                            <option value="<?php echo $key; ?>"><?php echo $key; ?></option>
                        <?php endforeach; ?>
                        <option value="custom" ><?php _e( 'Custom', $domain ); ?></option>
                    </select>
                    <input id="width" type="text" name="width" value="" onkeypress="ath_validate_number_input(event)"/>x
                    <input id="height" type="text" name="height" value="" onkeypress="ath_validate_number_input(event)"/>px
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Page', $domain ); ?></th>
                <td>
                    <input type="text" id="page" value="" name="page" onkeypress="ath_validate_number_input(event)" size="5"/>
                </td>
            </tr>
            <tr id="destination" class="ath-hidden">
                <th><?php _e( 'Destination', $domain ); ?></th>
                <td>
                    <select id="clickto" name="clickto">
                        <?php foreach( $get_calameo_destinations as $destination => $description ) : ?>
                            <option value="<?php echo $destination; ?>" ><?php echo $description; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr id="target" class="ath-hidden">
                <th><?php _e( 'Target', $domain ); ?></th>
                <td>
                    <select id="clicktarget" name="clicktarget">
                        <?php foreach( $get_calameo_targets as $target => $description ) : ?>
                            <option value="<?php echo $target; ?>" ><?php echo $description; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Options', $domain ); ?></th>
                <td>
                    <label class="autoflip ath-hidden"><input type="checkbox" value="4" id="autoflip" name="autoflip" /><?php echo AthlonCalameoManageConfiguration::$UI['options']['autoflip']; ?></label><br />
                    <label class="sharemenu"><input type="checkbox" value="1" id="showsharemenu" name="showsharemenu" checked="checked" /><?php echo AthlonCalameoManageConfiguration::$UI['options']['showsharemenu']; ?></label><br />
                    <label><input type="checkbox" value="1" id="hidelinks" name="hidelinks" checked="checked"/><?php echo AthlonCalameoManageConfiguration::$UI['options']['hidelinks']; ?></label>
                </td>
            </tr>
            <tr>
                <th><?php _e( 'Shortcode', $domain ); ?></th>
                <td>
                    <textarea name="shortcode" readonly="readonly" rows="5" cols="5" disabled="disabled"></textarea>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="mceActionPanel">
            <div style="float: left;">
                <input type="submit" class="button ath-disabled" name="insert" value="<?php _e( 'Insert', $domain ); ?>" disabled="disabled" />
            </div>
            <div style="float: right;">
                <input type="reset" class="button" name="cancel" value="<?php _e( 'Cancel', $domain  ); ?>" />
            </div>
        </div>
    </form>
<script type="text/javascript">
    var shortcode_sizes = <?php echo json_encode(AthlonCalameoManageConfiguration::$UI['size']); ?>;

    function ath_default_size() {
        var default_size = shortcode_sizes.Small;
        if(jQuery('#size').val() == 'Small') {
            jQuery('#width').val(default_size.width).prop('disabled', true);
            jQuery('#height').val(default_size.height).prop('disabled', true);
        }
    }

    function ath_select_size(element) {
        var select_value = jQuery(element).val();
        if(select_value != 'custom') {
            var size = shortcode_sizes[select_value];
            jQuery('#width').val(size.width).prop('disabled', true);
            jQuery('#height').val(size.height).prop('disabled', true);
        }
        else {
            jQuery('#width').val('').prop('disabled', false);
            jQuery('#height').val('').prop('disabled', false);
        }
    };
</script>
</body>
</html>