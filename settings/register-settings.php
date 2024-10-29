<?php
 /**
 * Manage Calameo Publications by Athlon
 * Register Calameo Settings Page
 * @Package WordPress
 * @Version: 1.1
 * @Author: Athlon Production <dev@athlonproduction.com>
 */
$domain = AthlonCalameoManageConfiguration::$PLUGIN['localization_domain'];

add_action( 'admin_init', 'ath_calameo_credentials_init' );

function ath_calameo_credentials_init()
{
    global $domain;
    wp_enqueue_script( 'get_account_calameo_info', AthlonManageCalameoPublications::$CALAMEO_DIRECTORY . '/assets/js/get_account_info.js', false, false, ASSETS_JAVASCRIPTS_IN_FOOTER );
    wp_enqueue_script( 'get_account_subscriptions', AthlonManageCalameoPublications::$CALAMEO_DIRECTORY . '/assets/js/get_account_subscriptions.js', false, false, ASSETS_JAVASCRIPTS_IN_FOOTER );

    register_setting(
                'calameo_credentials',
                'calameo_credentials',
                'ath_calameo_validate_credentials'
                );
    /*API credentials section*/
    add_settings_section(
                'calameo_credentials_section',
                __( 'Enter your API credentials', $domain ),
                'calameo_section_callback',
                __FILE__  );
    add_settings_field(
                'calameo_api_key',
                __( 'Calameo API key', $domain ),
                'ath_calameo_api_key_callback',
                __FILE__,
                'calameo_credentials_section'
                );

    add_settings_field(
                'calameo_api_secret',
                __( 'Calameo API secret', $domain ),
                'ath_calameo_api_secret_callback',
                __FILE__,
                'calameo_credentials_section' );
    add_settings_field(
                'calameo_check_account',
                '',
                'ath_calameo_check_account_callback',
                __FILE__,
                'calameo_credentials_section' );

}

add_action( 'admin_menu', 'ath_calameo_credentials_page' );
/* Add sub option page to the Settings Menu */
function ath_calameo_credentials_page()
{
    global $ath_calameo_credentials_page_hook, $domain;

    $ath_calameo_credentials_page_hook = add_options_page(
                    __( 'Calameo Credentials', $domain ),
                    __( 'Calameo Credentials', $domain ),
                    'manage_options',
                    'calameo_credentials',
                    'ath_calameo_credentials_form' );

/* Calling function which will add new tabs to contextual menu when loads */
add_action( 'load-'. $ath_calameo_credentials_page_hook, 'ath_calameo_credentials_help_tabs' );
add_action( 'load-'. $ath_calameo_credentials_page_hook, 'ath_calameo_add_setting_error_wp_calameo_not_installed' );
}

/* Callbacks function */
global $calameo_credentials;
$calameo_credentials = get_option( 'calameo_credentials' );

function ath_calameo_api_key_callback()
{
    global $calameo_credentials, $domain;

        add_settings_error(
                $calameo_credentials['calameo_api_secret'],
                'txt_multinumeric_error',
                __( 'Please enter a valid Calameo API Secret', $domain ),
                'error' );
?>
    <input type="text" id="calameo-api-key" name="calameo_credentials[calameo_api_key]" value="<?php esc_attr_e($calameo_credentials['calameo_api_key']); ?>" style="width: 50%" />

<?php
}

function ath_calameo_api_secret_callback()
{
    global $calameo_credentials, $domain;
?>
    <input type="text" id="calameo-api-secret" name="calameo_credentials[calameo_api_secret]" value="<?php esc_attr_e($calameo_credentials['calameo_api_secret']); ?>" style="width: 50%" />


<?php
}

function ath_calameo_check_account_callback()
{ global $calameo_credentials, $domain;
?>
<div id="check-calameo-account">
    <input type="button" class="button-primary" id="check-calameo-account" name="check_calameo_account" value="<?php _e( 'Check your account', $domain ); ?>" onclick="ath_check_account()"/>
</div>
<div id="calameo-please-wait" class="hidden">
    <input type="button" class="button-primary" id="calameo-please-wait" name="please_wait" value="<?php _e( 'Please wait...', $domain ); ?>" disabled="disabled" />
</div>
<div class="calameo-account-info hidden" style="padding-top:20px">
    <div class="account-success" style="display:none;">
        <div class="updated settings-error" id="setting-error-settings_updated" style="margin-left: 0px; width:50%; padding:0px;">
            <p style="padding:0px 10px;"><strong><?php _e( 'Account verified!', $domain ); ?></strong></p>
        </div>
        <table class="widefat" cellspacing="0" cellpadding="0" style="margin-top: 20px; width: 50%;">
            <thead>
                <tr>
                    <tr>
                        <th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'Parameter', $domain ); ?></th>
                        <th id="columnname" class="manage-column column-columnname num" scope="col"><?php _e( 'Value', $domain ); ?></th>
                    </tr>
                </tr>
            </thead>

            <tbody>

            </tbody>
        </table>
   </div>
   <div class="account-error" style="display:none;">
       <div class="error settings-error" id="setting-error-txt_multinumeric_error" style="margin-left: 0px; width:50%; padding:0px;">
            <p style="padding:0px 10px;"><strong><?php _e( 'Please enter a valid Calameo Account!', $domain ); ?></strong></p>
       </div>
   </div>
</div>

<?php
}

function calameo_section_callback()
{
}
/**
 * Sanitize setting before save
 *
 */
function ath_calameo_validate_credentials($new_value)
{
    global $calameo_credentials, $domain;

    $account_response = AthlonCalameoRegisterFunctions::ath_get_account_info();
    AthlonCalameoRegisterFunctions::ath_get_account_subscriptions();
    if( $account_response != 'ok' )
    {
        $new_value['calameo_api_key'] = $calameo_credentials['calameo_api_key'];
        add_settings_error(
                    $calameo_credentials['calameo_api_key'],
                    'txt_multinumeric_error',
                    __( 'Please enter a valid Calameo Account', $domain ),
                    'error' );
    }
   return $new_value;
}

/* Add error if WP Calameo is not installed */
function ath_calameo_add_setting_error_wp_calameo_not_installed ()
{
    global $ath_calameo_credentials_page_hook;

    $screen = get_current_screen();
    if ( !is_plugin_active( 'wp-calameo/wp-calameo.php') && $screen->id == $ath_calameo_credentials_page_hook )
    {
        add_settings_error(
                $calameo_credentials,
                'wp-calameo-not-installed',
                AthlonCalameoManageConfiguration::$WP_CALAMEO_NOT_INSTALLED_WARNING,
                'error' );
    }
}

/* Add contextual help tabs on calameo credentials page */
function ath_calameo_credentials_help_tabs ()
{
    global $ath_calameo_credentials_page_hook, $domain;
    $screen = get_current_screen();

    /*
     * Check if current screen is My Admin Page
     * Don't add help tab if it's not
     */
    if ( $screen->id != $ath_calameo_credentials_page_hook )
        return;

    /* Add overview tab if current screen is My Admin Page*/
    $screen->add_help_tab( array(
        'id'        => 'overview',
        'title'     => __( 'Overview', $domain ),
        'content'   => '<p>' . __( 'On this page you can change the credentials that will be used to connect to the Calameo API.<br />Note that your API credentials should have at least commons and publish access rights.', $domain ) . '</p>',
    ) );
    $screen->add_help_tab( array(
        'id'        => 'account',
        'title'     => __( 'Account Info', $domain ),
        'content'   => '<p>' . __( 'Calaméo offers you some types of accounts according to the features you wish to have: FREE, PREMIUM, PLATINUM and SOLO account.<br /> If account is FREE you don\'t have to access Calaméo API.', $domain )  . '</p>',
    ) );

    $screen->set_help_sidebar( __( '<p><strong>For more information </strong>:<br /><a href="http://help.calameo.com/index.php?title=API:Get_your_API_key" class="external" target="_blank" >Get Calameo API Key</a></p>', $domain ) );
}

/**
 * Show the section settings forms
 *
 * This function displays every sections in a different form
 */
function ath_calameo_credentials_form()
{
?>
    <h3><?php print AthlonCalameoManageConfiguration::$PLUGIN['title'] . ' ver ' . AthlonCalameoManageConfiguration::$PLUGIN['version']; ?></h3>
    <div class="group">
        <form method="post" action="options.php" id="calameo_upload">
           <?php
                settings_fields( 'calameo_credentials' );
                do_settings_sections( __FILE__ );
                submit_button();
            ?>
        </form>
    </div>
<?php
}
?>