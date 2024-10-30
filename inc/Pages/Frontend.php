<?php
/**
 * @package ContentHubPlugin
 */

namespace CHPS_Includes\Pages;

use CHPS_Includes\Base\BaseController;

class Frontend extends BaseController
{
    public function register() {
        // Shortcodes
        add_action('init', array($this, 'set_shortcodes'));
    }

    public function set_shortcodes() {
        add_shortcode('content-hub', array($this, 'render_content_hub'));
    }
}
