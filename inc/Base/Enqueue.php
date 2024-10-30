<?php
/**
 * @package ContentHubPlugin
 */

namespace CHPS_Includes\Base;

use CHPS_Includes\Base\BaseController;

class Enqueue extends BaseController
{
    public function register()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_admin_assets()
    {
        // enqueue admin styles
        wp_enqueue_style('chps_admin_style', $this->plugin_url . 'assets/admin-style.css');
        wp_enqueue_style('wp-color-picker');
        
        // enqueue admin scripts
        
        // add media API (media uploader)
        if ( !did_action( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }

        wp_register_script(
            'chps_admin_script', 
            $this->plugin_url . 'assets/admin-script.js',
            array('jquery', 'wp-color-picker')
        );
        wp_enqueue_script('chps_admin_script');
    }
}
