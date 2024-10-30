<?php

/**
 * @package ContentHubPlugin
 */
/*
Plugin Name: Content Hubs
Plugin URI: https://content-hub-plugin.com/
Description: Show multiple posts teaser in a grid.
Author: Wildstyle Network GmbH
Version: 1.0.8
Author URI: https://www.wildstyle-network.com/
License: GPLv3 or later
Text Domain: content-hubs
*/
/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.

Copyright 2022 Wildstyle Network GmbH
*/
defined( 'ABSPATH' ) or die( 'Direct access is not allowed.' );

if ( function_exists( 'chps_fs' ) ) {
    chps_fs()->set_basename( false, __FILE__ );
} else {
    // FREEMIUS INTEGRATION CODE
    
    if ( !function_exists( 'chps_fs' ) ) {
        // Create a helper function for easy SDK access.
        function chps_fs()
        {
            global  $chps_fs ;
            
            if ( !isset( $chps_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $chps_fs = fs_dynamic_init( array(
                    'id'             => '9541',
                    'slug'           => 'content-hub-plugin',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_0dac7aaa4f636cc5ed3b21c1e39d6',
                    'is_premium'     => false,
                    'premium_suffix' => 'Premium',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                    'days'               => 7,
                    'is_require_payment' => false,
                ),
                    'menu'           => array(
                    'slug'       => 'edit.php?post_type=chps-content-hub',
                    'first-path' => 'edit.php?post_type=chps-content-hub',
                    'contact'    => false,
                    'support'    => false,
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $chps_fs;
        }
        
        // Init Freemius.
        chps_fs();
        // Signal that SDK was initiated.
        do_action( 'chps_fs_loaded' );
    }
    
    // Special uninstall routine with Freemius
    function chps_fs_uninstall_cleanup()
    {
        // Clear database stored data
        global  $wpdb ;
        $wpdb->query( "DELETE FROM " . $wpdb->prefix . "posts WHERE post_type='chps-content-hub'" );
        $wpdb->query( "DELETE FROM " . $wpdb->prefix . "postmeta WHERE meta_key LIKE '%chps_%'" );
        $wpdb->query( "DELETE FROM " . $wpdb->prefix . "term_taxonomy WHERE taxonomy LIKE 'chps-topic'" );
        $wpdb->query( "DELETE FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'chps_%'" );
    }
    
    chps_fs()->add_action( 'after_uninstall', 'chps_fs_uninstall_cleanup' );
    // ... Your plugin's main file logic ...
    // Require once the composer autoload
    if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
        require_once dirname( __FILE__ ) . '/vendor/autoload.php';
    }
    /**
     * The code that runs during plugin activation
     */
    function chps_activate_plugin()
    {
        CHPS_Includes\Base\Activate::activate();
    }
    
    register_activation_hook( __FILE__, 'chps_activate_plugin' );
    /**
     * The code that runs during plugin deactivation
     */
    function chps_deactivate_plugin()
    {
        CHPS_Includes\Base\Deactivate::deactivate();
    }
    
    register_deactivation_hook( __FILE__, 'chps_deactivate_plugin' );
    /**
     * Initialize all the core classes of the plugin
     */
    if ( class_exists( 'CHPS_Includes\\Init' ) ) {
        CHPS_Includes\Init::register_services();
    }
}
