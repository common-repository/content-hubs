<?php
/**
 * @package ContentHubPlugin
 */

namespace CHPS_Includes\Base;

class BaseController
{
    public $plugin_path;
    public $plugin_url;
    public $plugin;

    // Defaults
    public $chps_color_theme = 'light';
    public $basic_color_1 = '#1D2327';
    public $basic_color_2 = '#ffffff';
    public $bg_use_color = false;
    public $bg_color = '';
    public $space_between_items = 0;
    public $number_of_items = 9;
    public $number_of_loadmore_items = 9;
    public $number_of_total_items;

    public function __construct()
    {
        $this->number_of_total_items = apply_filters('chps_hook_number_of_total_items', 50);
        $this->plugin_path = plugin_dir_path(dirname(dirname(__FILE__)));
        $this->plugin_url = plugin_dir_url(dirname(dirname(__FILE__)));
        $this->plugin = plugin_basename(dirname(dirname(dirname(__FILE__)))) . '/content-hub.php';
    }

    public function render_content_hub($block_attributes, $content) {
        // error_log(print_r($block_attributes, true));
        // error_log(print_r($content, true));

        $ch_id = isset($block_attributes['id']) ? $block_attributes['id'] : 1;

        $ch_post_ids = get_posts(array(
            'post_type' => 'chps-content-hub',
            'fields' => 'ids'
        ));
    
        foreach($ch_post_ids as $post_id) {
            $data = get_post_meta($post_id, '_chps_content-hub_key', true);
        
            if($data['ch_id'] == $ch_id) {
                $ch = $data;
                $ch['post_id'] = $post_id;
                $ch['fallback_image'] = get_post_meta($post_id, '_chps_content-hub_fallback_image', true);
                $ch['chps_custom_id'] = ($ch['chps_custom_id'] != '') ? $ch['chps_custom_id'] : 'chps_id_'.$ch['ch_id'];
                break;
            }
        }

        // PREMIUM Feature: hide branding
        $html_branding = apply_filters(
            'chps_html_branding',
            '<div class="chps-branding">
                <a href="https://www.content-hub-plugin.com" target="_blank">' . __('performed by Content-Hub-Plugin', 'content-hubs') . '</a>
            </div>
            '
        );

        ob_start();

        wp_enqueue_style('chps_frontend_css', $this->plugin_url . 'assets/frontend-style.css');

        if(!isset($ch)) {
            // Content Hub does not exist
            echo '<div class="chps-alert"><strong>Content Hub Plugin:</strong> This Content Hub does not exist.</div>';
        } else {
            // render content hub
            wp_add_inline_style('chps_frontend_css', $this->custom_styles($ch));
            require "$this->plugin_path/templates/block-content-hub.php";
            wp_enqueue_script('chps_frontend_js', $this->plugin_url . 'assets/frontend-script.js', [], null, true);

        }

        return ob_get_clean();
    }

    public function custom_styles($ch) {
        $basic_color_1 = isset($ch['chps_basic_color_1']) ? $ch['chps_basic_color_1'] : $this->basic_color_1;
        $basic_color_1 = ($basic_color_1 == '') ? $this->basic_color_1 : $basic_color_1;
        $basic_color_1_rgb = $this->hex2rgb($basic_color_1);
        $basic_color_2 = isset($ch['chps_basic_color_2']) ? $ch['chps_basic_color_2'] : $this->basic_color_2;
        $basic_color_2 = ($basic_color_2 == '') ? $this->basic_color_2 : $basic_color_2;
        $basic_color_2_rgb = $this->hex2rgb($basic_color_2);
        $space_between_items = isset($ch['chps_space_between_items']) ? $ch['chps_space_between_items'] . 'px' : $this->space_between_items . 'px';

        $css = "";
        $css .= ".chps-grid#". $ch['chps_custom_id'] ." .chps-filterbar {color: $basic_color_2}";
        $css .= ".chps-grid#". $ch['chps_custom_id'] ." .chps-filterbar .chps-filter-items .chps-filter-item {border-color: rgba($basic_color_2_rgb, 0.85); background-color: $basic_color_2; color: $basic_color_1}";
        $css .= ".chps-grid#". $ch['chps_custom_id'] ." .chps-filterbar .chps-filter-items .chps-filter-item.active {border-color: rgba($basic_color_2_rgb, 0.85); background-color: $basic_color_1; color: $basic_color_2;}";
        $css .= ".chps-grid#". $ch['chps_custom_id'] ." .chps-grid-items .chps-grid-item > div {background-color: $basic_color_1}";
        $css .= ".chps-grid#". $ch['chps_custom_id'] ." .chps-grid-items .chps-grid-item > div > a {color: $basic_color_1}";
        $css .= ".chps-grid#". $ch['chps_custom_id'] ." .chps-load-more-btn {color: $basic_color_1 !important; background-color: $basic_color_2 !important; border-color: $basic_color_1 !important;}";

        $css .= ".chps-grid#". $ch['chps_custom_id'] ." .chps-grid-items .chps-grid-item > div > a .chps-inner-content .chps-text {background: $basic_color_2}";
        $css .= ".chps-grid#". $ch['chps_custom_id'] ." .chps-grid-items .chps-grid-item > div > a:hover .chps-inner-content .chps-text {background: rgba($basic_color_2_rgb, 0.85)}";
        if($ch['chps_bg_use_color'] && $ch['chps_bg_color'] != '') {
            $css .= ".chps-grid#". $ch['chps_custom_id'] ." {background-color: ". $ch['chps_bg_color'] ."}";
        }else{
            $css .= ".chps-grid#". $ch['chps_custom_id'] ." {background-color: none}";
        }

        $css .= ".chps-grid#". $ch['chps_custom_id'] ." .chps-grid-items {grid-gap: $space_between_items}";

        return $css;
    }

    public function hex2rgb($colour) {
        if ( $colour[0] == '#' ) {
            $colour = substr( $colour, 1 );
        }
        if ( strlen( $colour ) == 6 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
                list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
                return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return "$r,$g,$b";
    }
}
