<?php
/**
 * @package ContentHubPlugin
 */

namespace CHPS_Includes\Base;

use CHPS_Includes\Base\BaseController;

class BlockController extends BaseController
{
    public function register() {
        // Gutenberg Blocks
        add_action('init', array($this, 'set_gutenberg_blocks'));
    }

    public function set_shortcodes() {
        add_shortcode('content-hub', array($this, 'render_content_hub'));
    }

    public function set_gutenberg_blocks() {
        // Register Block: Content Hub (block.json)
        register_block_type(
          $this->plugin_path . 'blocks/content-hub-block',
          array(
            'render_callback' => array($this, 'render_content_hub')
          )
        );
    }
}
