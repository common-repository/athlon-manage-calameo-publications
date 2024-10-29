<?php
/**
 * Manage Calameo Publications by Athlon
 * @Package WordPress
 * @Version: 1.1
 * @Author: Athlon Production <dev@athlonproduction.com>
 */
if( !class_exists( 'AthlonCalameoRegisterFunctions' ) ){
    class AthlonCalameoRegisterFunctions
    {
        private static
            $CALAMEO_API_KEY,
            $CALAMEO_API_SECRET,
            $CALAMEO_BOOK_ID,
            $CALAMEO_CURRENT_API_KEY,
            $CALAMEO_CURRENT_API_SECRET,
            $CALAMEO_ATTACHMENT_LINK,
            $CALAMEO_ATTACHMENT_ID,
            $CALAMEO_POST_PARAM;

        public static function init()
        {
            add_action( 'init',                                     array( 'AthlonCalameoRegisterFunctions', 'ath_add_calameo_button' ) );
            add_action( 'wp_ajax_ath_upload_to_calameo',            array( 'AthlonCalameoRegisterFunctions', 'ath_upload_to_calameo' ) );
            add_action( 'wp_ajax_ath_update_calameo_publication',   array( 'AthlonCalameoRegisterFunctions', 'ath_update_calameo_publication' ) );
            add_action( 'wp_ajax_ath_delete_calameo_publication',   array( 'AthlonCalameoRegisterFunctions', 'ath_delete_calameo_publication' ) );
            add_action( 'wp_ajax_ath_get_account_info',             array( 'AthlonCalameoRegisterFunctions', 'ath_get_account_info' ) );
            add_action( 'wp_ajax_ath_get_account_subscriptions',    array( 'AthlonCalameoRegisterFunctions', 'ath_get_account_subscriptions' ) );
        }

        function ath_calameo_variables()
        {
            global $calameo_credentials;

            self::$CALAMEO_ATTACHMENT_ID                 = $_POST['attachment_id'];
            self::$CALAMEO_POST_PARAM                    = isset($_POST['params']) ? $_POST['params'] : '';
            self::$CALAMEO_CURRENT_API_KEY               = $calameo_credentials['calameo_api_key'];
            self::$CALAMEO_CURRENT_API_SECRET            = $calameo_credentials['calameo_api_secret'];
            self::$CALAMEO_ATTACHMENT_LINK               = wp_get_attachment_url((int)self::$CALAMEO_ATTACHMENT_ID);
            self::$CALAMEO_API_KEY                       = self::ath_get_calameo_publication(self::$CALAMEO_ATTACHMENT_ID)->api_key;
            self::$CALAMEO_API_SECRET                    = self::ath_get_calameo_publication(self::$CALAMEO_ATTACHMENT_ID)->api_secret;
            self::$CALAMEO_BOOK_ID                       = self::ath_get_calameo_publication(self::$CALAMEO_ATTACHMENT_ID)->calameo_id;
        }

        function ath_get_account_info()
        {
            $calameo = array( 'status' => 'failure' );
            $api_key = $_POST["calameo_credentials"]["calameo_api_key"];
            $api_secret = $_POST["calameo_credentials"]["calameo_api_secret"];
            $info = array(
                        'action'    => AthlonCalameoManageConfiguration::$API['verify_account']['action'],
                        'apikey'    => $api_key, );
                $url_to_call = AthlonCalameoManageConfiguration::$API['verify_account']['url'] . self::ath_get_url_params( $info, $api_secret );
                $url_response = wp_remote_get( $url_to_call, array( 'timeout' => 100 ));
                if( !is_wp_error( $url_response ) )
                {
                    $calameo = json_decode( $url_response["body"] );
                    if( defined( 'DOING_AJAX' ) && DOING_AJAX )
                    {
                        echo json_encode( $calameo->response );
                        exit;
                    }
                    return $calameo->response->status;
                }
        }

        function ath_get_account_subscriptions()
        {
            $calameo = array( 'status' => 'failure' );
            $subscription_ids = array();
            $api_key = $_POST["calameo_credentials"]["calameo_api_key"];
            $api_secret = $_POST["calameo_credentials"]["calameo_api_secret"];

            if( defined( 'DOING_AJAX' ) && DOING_AJAX )
            {
                self::ath_calameo_variables();
                $api_key = self::$CALAMEO_CURRENT_API_KEY;
                $api_secret = self::$CALAMEO_CURRENT_API_SECRET;
            }

            $info = array(
                        'action'    => AthlonCalameoManageConfiguration::$API['subscriptions']['action'],
                        'apikey'    => $api_key, );

            $url_to_call = AthlonCalameoManageConfiguration::$API['subscriptions']['url'] . self::ath_get_url_params( $info, $api_secret );
            $url_response = wp_remote_get( $url_to_call, array( 'timeout' => 100 ));
            if( !is_wp_error( $url_response ) )
            {
                $calameo = json_decode( $url_response["body"] );
                if( $calameo->response->status == 'ok' )
                {
                    $subscriptions = $calameo->response->content->items;
                    if( is_array($subscriptions) )
                    {
                        foreach( $subscriptions as $subscription )
                        {
                            $id = $subscription->ID;
                            $subscription_name = $subscription->Name;
                            $subscription_ids[$subscription_name] = $id;
                        }
                        if( get_option('calameo_subscription_ids') == false )
                        {
                            add_option('calameo_subscription_ids', serialize($subscription_ids));
                        }
                        else
                        {
                            update_option('calameo_subscription_ids', serialize($subscription_ids));
                        }
                        if( defined( 'DOING_AJAX' ) && DOING_AJAX )
                        {
                            echo json_encode( array('status' => 'success', 'subscriptions' => $subscriptions) );
                            exit;
                        }
                    }
                }
                else if( defined( 'DOING_AJAX' ) && DOING_AJAX )
                {
                    echo json_encode( array('status' => 'failure') );
                    exit;
                }
            }
        }

        function ath_upload_to_calameo()
        {
            self::ath_calameo_variables();

            $calameo = array( 'status' => 'failure' );

            if( self::$CALAMEO_ATTACHMENT_LINK != 'Missing Attachment' )
            {
                $params = array_merge( self::$CALAMEO_POST_PARAM, array(
                                                                    'action'    => AthlonCalameoManageConfiguration::$API['upload']['action'],
                                                                    'apikey'    => self::$CALAMEO_CURRENT_API_KEY,
                                                                    'url'       => self::$CALAMEO_ATTACHMENT_LINK
                                                                    ));

                $url_to_call = AthlonCalameoManageConfiguration::$API['upload']['url'] . self::ath_get_url_params( $params, self::$CALAMEO_CURRENT_API_SECRET );
                $url_response = wp_remote_get( $url_to_call, array( 'timeout' => 100 ));
                if( !is_wp_error( $url_response ) )
                {
                    $calameo = json_decode( $url_response['body'] );

                    if( $calameo->response->status == 'ok' )
                    {
                        self::ath_insert_calameo_data( $calameo->response );
                    }
                    echo json_encode( $calameo->response );
                }
                else
                {
                    echo json_encode( array(
                                            'status' => 'failure',
                                            'error' => array( 'message' => AthlonCalameoManageConfiguration::$UI['error']['message'] )
                                            ) );
                }
            }
            exit;
        }

        function ath_update_calameo_publication()
        {
            self::ath_calameo_variables();
            $calameo = array( 'status' => 'failure' );

            if( self::$CALAMEO_ATTACHMENT_LINK != 'Missing Attachment' )
            {
                $params = array_merge( self::$CALAMEO_POST_PARAM, array(
                                                                    'action'    => AthlonCalameoManageConfiguration::$API['update']['action'],
                                                                    'apikey'    => self::$CALAMEO_API_KEY,
                                                                    'book_id'   => self::$CALAMEO_BOOK_ID,
                                                                    'url'       => self::$CALAMEO_ATTACHMENT_LINK
                                                                    ));

                $url_to_call = AthlonCalameoManageConfiguration::$API['update']['url'] . self::ath_get_url_params( $params, self::$CALAMEO_API_SECRET );

                $url_response = wp_remote_get( $url_to_call, array( 'timeout' => 100 ) );

                if( !is_wp_error( $url_response ) )
                {
                    $calameo = json_decode( $url_response['body'] );

                    if( $calameo->response->status == 'ok' )
                    {
                        self::ath_update_calameo_data();
                    }
                    echo json_encode( $calameo->response );
                }
                else
                {
                    echo json_encode( array(
                                            'status' => 'failure',
                                            'error' => array( 'message' => AthlonCalameoManageConfiguration::$UI['error']['message'] )
                                            ) );
                }
            }
            exit;
        }

        function ath_delete_calameo_publication()
        {
            self::ath_calameo_variables();
            $calameo = array( 'status' => 'failure' );

            if( self::$CALAMEO_ATTACHMENT_LINK != 'Missing Attachment' )
            {
                $params = array(
                            'action'    => AthlonCalameoManageConfiguration::$API['delete']['action'],
                            'apikey'    => self::$CALAMEO_API_KEY,
                            'book_id'   => self::$CALAMEO_BOOK_ID,
                            'url'       => self::$CALAMEO_ATTACHMENT_LINK
                            );

                $url_to_call = AthlonCalameoManageConfiguration::$API['delete']['url'] . self::ath_get_url_params( $params, self::$CALAMEO_API_SECRET );
                $url_response = wp_remote_get( $url_to_call, array( 'timeout' => 100 ) );

                if( !is_wp_error( $url_response ) )
                {
                    $calameo = json_decode( $url_response['body'] );

                    if( $calameo->response->status == 'ok' )
                    {
                        self::ath_delete_calameo_data();
                    }
                    echo json_encode( $calameo->response );
                }
                else
                {
                    echo json_encode( $calameo );
                }
            }
            exit;
        }

        function ath_insert_calameo_data( $data )
        {
            global $wpdb;
            self::ath_calameo_variables();

            $subscription_id = self::$CALAMEO_POST_PARAM['subscription_id'];
            array_shift( self::$CALAMEO_POST_PARAM );
            $wpdb->insert(
                        AthlonManageCalameoPublications::$TABLE_NAME,
                        array(
                            'media_id'          => self::$CALAMEO_ATTACHMENT_ID,
                            'calameo_id'        => $data->content->ID,
                            'api_key'           => self::$CALAMEO_CURRENT_API_KEY,
                            'api_secret'        => self::$CALAMEO_CURRENT_API_SECRET,
                            'subscription_id'   => $subscription_id,
                            'file_options'      => serialize( self::$CALAMEO_POST_PARAM )
                            ));
        }

        function ath_update_calameo_data()
        {
            global $wpdb;
            self::ath_calameo_variables();

            $wpdb->update(
                    AthlonManageCalameoPublications::$TABLE_NAME,
                    array( 'file_options'   => serialize( self::$CALAMEO_POST_PARAM ) ),
                    array( 'media_id'       => self::$CALAMEO_ATTACHMENT_ID )
                    );
        }

        function ath_delete_calameo_data()
        {
            global $wpdb;
            self::ath_calameo_variables();

            $wpdb->query( $wpdb->prepare(
                    'DELETE FROM ' . AthlonManageCalameoPublications::$TABLE_NAME . '
                     WHERE media_id = ' . self::$CALAMEO_ATTACHMENT_ID )
                );
        }

        function ath_get_url_params( $params, $api_secret )
        {
            $params['expires']         = mktime() + 3600;
            $params['output']          = 'JSON';

            ksort( $params );

            $signature = $api_secret;

            foreach( $params as $key => $value )
            {
                $signature .= $key;
                $signature .= $value;
            }

            $params['signature'] = md5( $signature );

            return http_build_query($params);
        }

        /* Checks if the attachment has the appropriate mimetype */
        function ath_is_of_convertable_for_calameo_mimetype( $attachment_id )
        {
            global $post;
            $allowed_mime_types = AthlonCalameoManageConfiguration::$SUPPORTED_DOCUMENTS['mimetypes'];

            if( in_array( get_post_mime_type( $post->ID ), $allowed_mime_types) ) return true;

            return false;
        }

        /* Get data for all uploaded files */
        public static function ath_get_calameo_publications()
        {
            global $wpdb;
            if( $wpdb->get_var("SHOW TABLES LIKE '" . AthlonManageCalameoPublications::$TABLE_NAME . "'") )
            {
                return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . AthlonManageCalameoPublications::$TABLE_NAME, ARRAY_A ) );
            }

            return false;
        }

        /* Get data for a specific attachment */
        public static function ath_get_calameo_publication( $attachment_id )
        {
            global $wpdb;
            if( $wpdb->get_var("SHOW TABLES LIKE '" . AthlonManageCalameoPublications::$TABLE_NAME . "'") )
            {
                $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . AthlonManageCalameoPublications::$TABLE_NAME . " WHERE media_id=" . $attachment_id , ARRAY_A ) );
            }
            if( $row && !empty( $row ) )
            {
                return $row;
            }

            return false;
        }

        /* Checks if the media attachment is uploaded to Calameo */
        function ath_is_uploaded_to_calameo( $attachment_id )
        {
            if( $publications = self::ath_get_calameo_publications() )
            {
                foreach( $publications as $key => $publication )
                {
                    if( $publication->media_id == $attachment_id ) return true;
                }
            }
            return false;
        }

        function ath_add_calameo_button()
        {
           /* Don't bother doing this stuff if the current user lacks permissions*/
           if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
             return;
           /* Add only in Rich Editor mode*/
           if ( get_user_option( 'rich_editing') == 'true' )
           {
             add_filter( 'mce_external_plugins',    array( 'AthlonCalameoRegisterFunctions', 'ath_add_calameo_button_tinymce_plugin' ) );
             add_filter( 'mce_buttons',             array( 'AthlonCalameoRegisterFunctions', 'ath_register_calameo_button' ) );
           }
        }

        function ath_register_calameo_button( $buttons )
        {
           array_push( $buttons, 'separator', 'calameo_button' );
           return $buttons;
        }

        /* Load the TinyMCE plugin */
        function ath_add_calameo_button_tinymce_plugin( $plugin_array )
        {
           $plugin_array['calameo_button'] = AthlonManageCalameoPublications::$CALAMEO_DIRECTORY . '/assets/js/add_calameo_button.js';
           return $plugin_array;
        }

        function ath_wp_calameo_not_installed_warning()
        {
            global $pagenow;

            $pages = array( 'upload.php', 'post.php' );
            if( !is_plugin_active( 'wp-calameo/wp-calameo.php' ) && in_array( $pagenow, $pages ) )
            {
                echo "<div id='message' class='error'>";
                echo AthlonCalameoManageConfiguration::$WP_CALAMEO_NOT_INSTALLED_WARNING;
                echo "</div>";
            }
        }
    }
}
AthlonCalameoRegisterFunctions::init();
add_action( 'admin_footer', array( 'AthlonCalameoRegisterFunctions', 'ath_wp_calameo_not_installed_warning' ) );
?>