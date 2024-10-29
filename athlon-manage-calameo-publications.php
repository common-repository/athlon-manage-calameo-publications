<?php
/**
 * @Package WordPress
 * @Version: 1.1.1
 * @Author: Athlon Production <we@athlonproduction.com>
 */
/*
 Plugin Name: Manage Calameo Publications by  Athlon
 Description: Tool for management and embedding various types of documents and present them in a custom styled Calameo viewer.
 Tags: athlon, calameo, share, publication, document, embed
 Package WordPress
 Version: 1.1.1
 Author: Athlon Production <we@athlonproduction.com>
 Author URI: http://athlonproduction.com
 License: GPL2
*/
/*
 Copyright (c) 2013 Athlon Production

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
if ( !class_exists( 'AthlonManageCalameoPublications' ) ) {
    class AthlonManageCalameoPublications {

        public static
            $CALAMEO_DIRECTORY,
            $LOGPATH,
            $TABLE_NAME,
            $CALAMEO_FILE_SLUG;

        public static function init()
        {
            global $wpdb;

            self::$CALAMEO_DIRECTORY    = plugins_url( 'athlon-manage-calameo-publications' );
            self::$CALAMEO_FILE_SLUG    = __FILE__;
            self::$TABLE_NAME           = $wpdb->prefix . AthlonCalameoManageConfiguration::$DB['table_name'];
            self::$LOGPATH              = str_replace('\\', '/', WP_CONTENT_DIR).'/plugin-logs/';

            /* Register hooks */
            register_activation_hook(   self::$CALAMEO_FILE_SLUG,   array( 'AthlonManageCalameoPublications', 'activate' ) );
            register_deactivation_hook( self::$CALAMEO_FILE_SLUG,   array( 'AthlonManageCalameoPublications', 'deactivate' ) );
            register_uninstall_hook(    self::$CALAMEO_FILE_SLUG,   array( 'AthlonManageCalameoPublications', 'uninstall' ) );

            /* Add actions */
            add_action( 'plugins_loaded',   array( 'AthlonManageCalameoPublications', 'update_db_check' ) );
        }

        /**
         * Activates the calameo
         */
        static function activate()
        {
            /* Create table */
            self::create_table();
        }

        /**
         * Deactivates the calameo
         */
        static function deactivate()
        {
        }

        /**
         * Uninstalls the calameo
         */
        static function uninstall()
        {
            self::drop_table();
            $options = AthlonCalameoManageConfiguration::$PLUGIN['options_name'];
            foreach( $options as $option )
            {
                delete_option( $option );
            }
        }

        /**
         * Checks if current DB Version is newer than the installed one and updates it
         */
        static function update_db_check()
        {
            if( get_site_option( 'AthlonManageCalameoPublications_db_version' ) != AthlonCalameoManageConfiguration::$DB['version'] )
            {
               self::create_table();
            }
        }

        /**
         * Creates AthlonManageCalameoPublications table in the database
         */
        static function create_table()
        {
            global $wpdb;

            if( @is_file( ABSPATH.'/wp-admin/includes/upgrade.php' ) )
            {
                include_once( ABSPATH.'/wp-admin/includes/upgrade.php' );
            }
            else
            {
                die( 'We have problem finding your \'/wp-admin/upgrade-functions.php\' and \'/wp-admin/includes/upgrade.php\'' );
            }

            $charset_collate = '';
            if( $wpdb->has_cap('collation') )
            {
                if( !empty( $wpdb->charset ) )
                {
                    $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
                }
                if( !empty( $wpdb->collate ) )
                {
                    $charset_collate .= " COLLATE $wpdb->collate";
                }
            }
            /* AthlonManageCalameoPublications create database table */
            if( !$wpdb->get_var( "SHOW TABLES LIKE '" . self::$TABLE_NAME . "'" ) )
            {
                $create_table = "CREATE TABLE " . self::$TABLE_NAME . " (".
                                    "id int(10) NOT NULL AUTO_INCREMENT,".
                                    "media_id int(10) NOT NULL,".
                                    "calameo_id varchar(100) NOT NULL default '',".
                                    "api_key varchar(200) NOT NULL default '',".
                                    "api_secret varchar(200) NOT NULL default '',".
                                    "subscription_id varchar(200) NOT NULL default '',".
                                    "file_options longtext default '',".
                                    "submitted_on datetime NOT NULL default '0000-00-00 00:00:00',".
                                    "UNIQUE KEY id (id),".
                                    "UNIQUE KEY media_id (media_id)".
                                    ");";
                dbDelta( $create_table );
            }
            update_option( 'AthlonManageCalameoPublications_db_version', AthlonCalameoManageConfiguration::$DB['version'] );
        }

        /**
         * Drops AthlonManageCalameoPublications table in the database
         */
        static function drop_table()
        {
            global $wpdb;
             if( $wpdb->get_var( "SHOW TABLES LIKE '" . self::$TABLE_NAME . "'" ) )
            {
                $wpdb->query( 'DROP TABLE IF EXISTS ' . self::$TABLE_NAME );
            }
        }
    }
}
include_once dirname( __FILE__ ) . '/settings/athlon_calameo_manage_configuration.php';
AthlonManageCalameoPublications::init();
include_once dirname( __FILE__ ) . '/post_types/media.php';
include_once dirname( __FILE__ ) . '/functions.php';
include_once dirname( __FILE__ ) . '/settings/register-settings.php';
?>