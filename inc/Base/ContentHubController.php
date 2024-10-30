<?php
/**
 * @package ContentHubPlugin
 */

namespace CHPS_Includes\Base;

use CHPS_Includes\Base\BaseController;

class ContentHubController extends BaseController
{
    public $settings;

    public function register()
    {
        // CPT: Content Hub
        add_action('init', array($this, 'chps_cpt'));
        add_action('add_meta_boxes', array($this, 'add_meta_box'), 1);
        add_action('rest_api_init', array($this, 'add_meta_to_rest_api'));
        add_action('save_post', array($this, 'save_custom_fields'));
        add_action('manage_chps-content-hub_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_chps-content-hub_posts_custom_column', array($this, 'set_custom_columns_data'), 10, 2); // this method has 2 attributes
    }

    /**
     * CPT: Content Hub
     */

    public static function chps_cpt()
    {
        $labels = array(
            'name' => __('Content Hubs', 'content-hubs'),
            'singular_name' => __('Content Hub', 'content-hubs'),
            'add_new' => __('Add Content Hub', 'content-hubs'),
            'add_new_item' => __('Add new Content Hub', 'content-hubs'),
            'edit_item' => __('Edit Content Hub', 'content-hubs'),
            'new_item' => __('New Content Hub', 'content-hubs'),
            'all_items' => __('All Content Hubs', 'content-hubs'),
            'view_item' => __('View Content Hub', 'content-hubs'),
            'search_items' => __('Search Content Hubs', 'content-hubs'),
            'not_found' => __('No Content Hubs found', 'content-hubs'),
            'not_found_in_trash' => __('No Content Hub in trash', 'content-hubs'),
            'parent_item_colon' => '',
            'menu_name' => __('Content Hubs', 'content-hubs'),
        );
        $args = array(
            'labels' => $labels,
            'description' => __('Content Hub', 'content-hubs'),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'exclude_from_search' => true,
            'show_in_nav_menus' => false,
            'has_archive' => false,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-grid-view',
            'supports' => array('title'),
        );

        register_post_type('chps-content-hub', $args);
    }

    public function add_meta_box()
    {
        add_meta_box(
            'content-hub_customfields',
            __('Settings', 'content-hubs'),
            array($this, 'render_customfields_box'),
            'chps-content-hub',
            'normal',
            'core'
        );
    }

    public function add_meta_to_rest_api()
    {
      // add new API field "post_meta_fields" to be used by JS
      register_rest_field( 'chps-content-hub', 'post_meta_fields', array(
        'get_callback'    => function ($object) {
          
          $post_id = $object['id'];

          //return the post meta
          return get_post_meta($post_id, '_chps_content-hub_key', true);
        },
        'schema'          => null,
      ));
    }

    public function render_customfields_box($post)
    {
        wp_nonce_field('chps_content-hub', 'chps_content-hub_nonce');

        $data = get_post_meta($post->ID, '_chps_content-hub_key', true);

        
        // get highest ch_id from existing content-hubs
        $query = new \WP_Query(array('post_type' => 'chps-content-hub', 'post_status' => array('publish', 'pending', 'draft', 'future', 'private', 'trash')));
        $highest_id = 0;
        if($query->have_posts()) {
          while ($query->have_posts()) {
            $query->the_post();
            $post_data = get_post_meta(get_the_ID(), '_chps_content-hub_key', true);
            if(isset($post_data['ch_id'])) {
              if($post_data['ch_id'] > $highest_id) {
                $highest_id = $post_data['ch_id'];
              }
            }
          }
        }

        // get ch_id or set based on highest_id
        $ch_id = isset($data['ch_id']) ? $data['ch_id'] : $highest_id+1;

        $html_setting_intro_text = apply_filters(
          'chps_html_setting_intro_text', 
          __('
            <h2>You are on the Basic version</h2>', 
            'content-hubs'
          )
        );

        $html_setting_upgrade_info = apply_filters(
          'chps_html_setting_upgrade_info', 
          __('
            <div>
              <p><strong>Upgrade to the PREMIUM version to get more features enabled:</strong></p>
              <ul>
                <li>No grid limitation</li>
                <li>Set amount of initial items</li>
                <li>Grid margin options</li>
                <li>Custom color mode</li>
                <li>Set fallback image</li>
                <li>Remove branding</li>
                <li>High priority support</li>
              </ul>
              <a href="' . chps_fs()->get_upgrade_url() . '" class="button button-primary">✨ Upgrade to PREMIUM</a>
            </div>', 
            'content-hubs'
          )
        );

        // get custom id
        $custom_id = isset($data['chps_custom_id']) ? $data['chps_custom_id'] : '';

        // get post sources
        $chps_sources_type = isset($data['chps_sources_type']) ? $data['chps_sources_type'] : '1';

        // get current post_tag taxonomy terms
        $selected_post_tag_taxonomy_terms = isset($data['chps_post_tag_taxonomy']) ? $data['chps_post_tag_taxonomy'] : get_terms(
            array(
                'taxonomy' => 'post_tag',
                'hide_empty' => false,
                'fields' => 'ids'
            )
        );

        // get all post_tag taxonomy terms
        $all_post_tag_taxonomy_terms = get_terms(
          array(
            'taxonomy' => 'post_tag',
            'hide_empty' => false,
          )
        );

        // get current post types
        $selected_post_types = isset($data['chps_post_types']) ? $data['chps_post_types'] : array();

        // get all public post types as objects
        $all_post_types = get_post_types(
          array(
            'public' => true,
          ),
          'objects'
        );

        // get current global taxonomy terms
        $selected_global_taxonomy_terms = isset($data['chps_global_taxonomy']) ? $data['chps_global_taxonomy'] : array();

        // get all global taxonomy terms
        $all_global_taxonomy_terms = get_terms(
          array(
            'taxonomy' => 'chps-topic',
            'hide_empty' => false,
          )
        );

        // remove attachment post type (wordpress default)
        unset($all_post_types['attachment']);

        // get fallback image
        $data_fallback_image = get_post_meta($post->ID, '_chps_content-hub_fallback_image', true);
        $fallback_image = isset($data_fallback_image) ? $data_fallback_image : false;
        $has_image = ($fallback_image)? 'has-image' : '';
        $image_tag = ($has_image)? '<img src="'.esc_attr($fallback_image).'" style="width: 100%;">' : '';
        
        // PREMIUM Feature: fallback image
        $html_setting_fallback_image = apply_filters(
          'chps_html_setting_fallback_image', 
          '
          <tr valign="top" class="chps-gopro-tr">
            <th scope="row">
              ' . __('Fallback Image', 'content-hubs') . '
              <br><span class="pro-badge">PREMIUM</span><br>
              <a class="chps-gopro-text" href="' . chps_fs()->get_upgrade_url() . '">' . __('Upgrade to PREMIUM to set a fallback image.', 'content-hubs') . '</a>  
            </th>
            <td>
              <button disabled>' . __('Upload Image', 'content-hubs') . '</button>
            </td>
          </tr>
          ', 
          $has_image, 
          $fallback_image, 
          $image_tag
        );

        // color theme
        $chps_color_theme = isset($data['chps_color_theme']) ? $data['chps_color_theme'] : $this->chps_color_theme;

        // get basic color 1
        $basic_color_1 = isset($data['chps_basic_color_1']) ? $data['chps_basic_color_1'] : $this->basic_color_1;
        $basic_color_1 = ($basic_color_1 == '') ? $this->basic_color_1 : $basic_color_1;

        // get basic color 2
        $basic_color_2 = isset($data['chps_basic_color_2']) ? $data['chps_basic_color_2'] : $this->basic_color_2;
        $basic_color_2 = ($basic_color_2 == '') ? $this->basic_color_2 : $basic_color_2;

        // PREMIUM Feature: custom color theme
        $html_setting_color_theme = apply_filters(
          'chps_html_setting_color_theme',
          '
          <tr valign="top">
            <th scope="row">
              '. __('Color Theme', 'content-hubs') .'
              <br><span class="pro-badge">PREMIUM</span><br>
              <a class="chps-gopro-text" href="' . chps_fs()->get_upgrade_url() . '">' . __('Upgrade to PREMIUM to set a custom colors.', 'content-hubs') . '</a>  
            </th>
            <td>
              <select id="chps_color_theme" name="chps_color_theme">
                <option value="light" ' . (($chps_color_theme == 'light') ? 'selected' : '') . '>light</option>
                <option value="dark" ' . (($chps_color_theme == 'dark') ? 'selected' : '') . '>dark</option>
                <option value="custom" ' . (($chps_color_theme == 'custom') ? 'selected' : '') . ' ' . 'disabled>custom [PREMIUM]</option>
                <div class="hint">' . __('Toggle between dark and white mode or define your own colors.', 'content-hubs') . '</div>
              </select><br><br>
              <div id="chps_custom_colors" ' . (($chps_color_theme != 'custom')? 'style="display:none"' : '') . '>
                <label class="sublabel_color">' . __('Basic color 1', 'content-hubs') . ':</label><input type="text" class="chps_color" name="chps_basic_color_1" value="' . esc_attr($basic_color_1) . '" placeholder="' . esc_attr($this->basic_color_1) . '"></input><br>
                <label class="sublabel_color">' . __('Basic color 2', 'content-hubs') . ':</label><input type="text" class="chps_color" name="chps_basic_color_2" value="' . esc_attr($basic_color_2) . '" placeholder="' . esc_attr($this->basic_color_2) . '"></input>
              </div>
            </td>
          </tr>
          ',
          $chps_color_theme,
          $basic_color_1,
          $basic_color_2,
          $this->basic_color_1,
          $this->basic_color_2
        );

        // bg color
        $chps_bg_use_color = isset($data['chps_bg_use_color']) ? $data['chps_bg_use_color'] : $this->bg_use_color;
        $chps_bg_color = isset($data['chps_bg_color']) ? $data['chps_bg_color'] : $this->bg_color;

        // get space between items
        $space_between_items = isset($data['chps_space_between_items']) ? $data['chps_space_between_items'] : $this->space_between_items;

        // PREMIUM Feature: space between items
        $html_setting_space_between_items = apply_filters(
          'chps_html_setting_space_between_items',
          '
          <tr valign="top" class="chps-gopro-tr">
            <th scope="row">
              ' . __('Space between items', 'content-hubs') . '
              <br><span class="pro-badge">PREMIUM</span><br>
              <a class="chps-gopro-text" href="' . chps_fs()->get_upgrade_url() . '">' . __('Upgrade to PREMIUM to set space between content tiles.', 'content-hubs') . '</a>  
            </th>
            <td>
              <input type="number" class="small-text chps_space_between_items" disabled></input>
              <span class="description">px</span>
              <div class="hint">' . __('Set a padding between the content tiles.', 'content-hubs') . '</div>
            </td>
          </tr>
          ',
          $space_between_items
        );

        // number of items
        $number_of_items = isset($data['chps_number_of_items']) ? $data['chps_number_of_items'] : $this->number_of_items;

        // PREMIUM Feature: grid shows initially
        $html_setting_grid_shows_initially = apply_filters(
          'chps_html_setting_grid_shows_initially',
          '<tr valign="top" class="chps-gopro-tr">
            <th scope="row">
              ' . __('Grid shows initially', 'content-hubs') . '
              <br><span class="pro-badge">PREMIUM</span><br>
              <a class="chps-gopro-text" href="' . chps_fs()->get_upgrade_url() . '">' . __('Upgrade to PREMIUM to customize the number of items that are initially visible', 'content-hubs') . '</a>
            </th>
            <td>
              <input disabled type="number" class="small-text chps_number_of_items" value="' . esc_attr($number_of_items) . '"></input>
              <span class="description">' . __( 'items', 'content-hubs' ) . '</span><br>
            </td>
          </tr>
          ',
          $number_of_items
        );

        // render template
        require_once $this->plugin_path . 'templates/edit_chps-content-hub.php';
    }

    public static function save_custom_fields($post_id)
    {
        // Dont save without nonce
        if (!isset($_POST['chps_content-hub_nonce'])) {
            return $post_id;
        }

        // Dont save if nonce is incorrect
        $nonce = $_POST['chps_content-hub_nonce'];
        if (!wp_verify_nonce($nonce, 'chps_content-hub')) {
            return $post_id;
        }

        // Dont save if wordpress just auto-saves
        if (defined('DOING AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Dont save if user is not allowed to do
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        // Validation

        $data = array(
            'ch_id' => intval($_POST['ch_id']),
            'chps_sources_type' => sanitize_text_field($_POST['chps_sources_type']),
            'chps_post_tag_taxonomy' => self::sanitize_taxonomy(isset($_POST['chps_post_tag_taxonomy']) ? $_POST['chps_post_tag_taxonomy'] : array()),
            'chps_post_types' => self::sanitize_post_types(isset($_POST['chps_post_types']) ? $_POST['chps_post_types'] : array()),
            'chps_global_taxonomy' => self::sanitize_taxonomy(isset($_POST['chps_global_taxonomy']) ? $_POST['chps_global_taxonomy'] : array()),
            'chps_color_theme' => sanitize_text_field(isset($_POST['chps_color_theme']) ? $_POST['chps_color_theme'] : 'dark'),
            'chps_basic_color_1' => sanitize_hex_color(isset($_POST['chps_basic_color_1']) ? $_POST['chps_basic_color_1'] : ''),
            'chps_basic_color_2' => sanitize_hex_color(isset($_POST['chps_basic_color_2']) ? $_POST['chps_basic_color_2'] : ''),
            'chps_bg_use_color' => rest_sanitize_boolean(isset($_POST['chps_bg_use_color']) ? $_POST['chps_bg_use_color'] : ''),
            'chps_bg_color' => sanitize_hex_color(isset($_POST['chps_bg_color']) ? $_POST['chps_bg_color'] : false),
            'chps_space_between_items' => intval(isset($_POST['chps_space_between_items']) ? $_POST['chps_space_between_items'] : ''),
            'chps_number_of_items' => intval(isset($_POST['chps_number_of_items']) ? $_POST['chps_number_of_items'] : ''),
            'chps_custom_id' => sanitize_text_field($_POST['chps_custom_id']),
        );
        update_post_meta($post_id, '_chps_content-hub_key', $data);

        // validate & store image seperately (to avoid serialized URLs [bad for search & replace due to domain change])
        $data_image = esc_url_raw($_POST['chps_fallback_image']);
        update_post_meta($post_id, '_chps_content-hub_fallback_image', $data_image);
    }

    public static function set_custom_columns($columns)
    {
        // preserve default columns
        $title = $columns['title'];
        $date = $columns['date'];
        unset($columns['title'], $columns['date']);

        $columns['title'] = $title;
        $columns['shortcode'] = __('Shortcode', 'content-hubs');
        $columns['date'] = $date;

        return $columns;
    }

    public static function set_custom_columns_data($column, $post_id)
    {
        $data = get_post_meta($post_id, '_chps_content-hub_key', true);

        $shortcode = isset($data['ch_id']) ? '<code>[content-hub id="' . $data['ch_id'] . '"]</code>' : '';

        switch ($column) {
            case 'shortcode':
                echo wp_kses_post($shortcode);
                break;
            default:
                break;
        }
    }

    /**
     * Check for valid post types
     */
    public static function sanitize_post_types($input)
    { 
        if(!is_array($input)) {
          return false;
        }
      
        if(is_array($input)) {
          foreach($input as $item) {
            if(get_post_type_object($item) == null) {
              return false;
            }
          }
        }

        return $input;
    }

    /**
     * Check for valid taxonomy terms
     */
    public static function sanitize_taxonomy($input)
    {
        if(!is_array($input)) {
          return false;
        }

        if(is_array($input)) {
          foreach($input as $item) {
            if(!get_term_by('term_taxonomy_id', $item)) {
              return false;
            }
          }
        }

        return $input;
    }
}
