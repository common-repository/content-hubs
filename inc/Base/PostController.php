<?php
/**
 * @package ContentHubPlugin
 */

namespace CHPS_Includes\Base;

use CHPS_Includes\Base\BaseController;

class PostController extends BaseController
{
    public $short_description_length = 50;

    public function register()
    {
        // Post
        add_action('add_meta_boxes', array($this, 'add_meta_box_group'));
        add_action('save_post', array($this, 'save_custom_fields'));

        // // Taxonomy: Topic
        add_action('init', array($this, 'topic_tax'));
    }

    public function add_meta_box_group()
    {   
        $screen = get_current_screen();

        // exit if no screen
        if(!is_object($screen)) return;

        // exit if not on edit post screen
        if($screen->base !== 'post') return;

        // add meta box only for selected post types
        $selected_post_types = array();
        $ch_posts = get_posts(array('post_type' => 'chps-content-hub'));

        foreach($ch_posts as $ch_post) {
            $post_data = get_post_meta($ch_post->ID, '_chps_content-hub_key', true);
            foreach($post_data['chps_post_types'] as $selected_post_type) {
                $selected_post_types[] = $selected_post_type;
            }
        }

        $selected_post_types = array_values(array_unique($selected_post_types));
        
        // add meta box group (default way, compatible with all editors)
        // TODO: use Gutenbergs more modern approach to integrate meta fields (register_meta)
        add_meta_box(
            'chps_customfields',
            __('Content Hub Teaser', 'content-hubs'),
            array($this, 'render_customfields_box'),
            $selected_post_types,
            'side',
            'high'
        );

    }

    public function render_customfields_box($post)
    {
        wp_nonce_field('chps_post', 'chps_post_nonce');

        // Fields: Headline, Short Description, Image, Link, Tags
        $data = get_post_meta($post->ID, '_chps_post_meta', true); //serialized array
        $headline = isset($data['headline']) ? $data['headline'] : '';
        $short_description = isset($data['short_description']) ? $data['short_description'] : '';
        $image = get_post_meta($post->ID, '_chps_post_meta_image', true);
        $link = get_post_meta($post->ID, '_chps_post_meta_link', true);
        
        //Fallbacks
        $image = isset($image) ? $image : '';
        $link = isset($link) ? $link : '';
        $has_image = ($image != '')? 'has-image' : '';
        $image_tag = ($has_image)? '<img src="'.esc_attr($image).'" style="width: 100%;">' : '';

        echo '<div class="section">
          <label class="meta-label" for="chps_headline">'. __('Headline', 'content-hubs') .'</label>
          <input type="text" class="widefat" id="chps_headline" name="chps_headline" value="'. esc_attr($headline) .'"></input>
        </div>';

        echo '<div class="section">
          <label class="meta-label" for="chps_short_description">'. __('Short Description', 'content-hubs') .'</label>
          <textarea class="widefat" maxlength="'. $this->short_description_length .'" id="chps_short_description" name="chps_short_description">'. esc_attr($short_description) .'</textarea>
        </div>';

        echo '<div class="section">
          <label class="meta-label" for="chps_image">'. __('Image', 'content-hubs') .'</label><br>
          <a href="#" class="chps_upload_image_button button button-secondary" '. (($has_image)? 'style="display: none;"' : '') .'>'. __('Upload Image', 'content-hubs') .'</a>
          <input type="hidden"id="chps_image" name="chps_image" value="'. esc_attr($image) .'"></input>
          <div id="chps_image_preview" class="'. $has_image .'"><div class="image_tag">'. $image_tag .'</div><span id="clear_image">&#x2715;</span></div>
        </div>';

        echo '<div class="section">
          <label class="meta-label" for="chps_link">'. __('Link', 'content-hubs') .'</label>
          <input type="text" class="widefat" id="chps_link" name="chps_link" value="'. esc_attr($link) .'"></input>
        </div>';
        
    }

    public static function save_custom_fields($post_id)
    {
        // Dont save without nonce
        if (!isset($_POST['chps_post_nonce'])) {
            return $post_id;
        }

        // Dont save if nonce is incorrect
        $nonce = $_POST['chps_post_nonce'];
        if (!wp_verify_nonce($nonce, 'chps_post')) {
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
            'headline' => sanitize_text_field($_POST['chps_headline']),
            'short_description' => wp_kses_post($_POST['chps_short_description']),
        );
        update_post_meta($post_id, '_chps_post_meta', $data);

        // validate & store image seperately (to avoid serialized URLs [bad for search & replace due to domain change])
        $data_image = esc_url_raw($_POST['chps_image']);
        update_post_meta($post_id, '_chps_post_meta_image', $data_image);

        // validate & store link seperately (to avoid serialized URLs [bad for search & replace due to domain change])
        $data_link = esc_url_raw($_POST['chps_link']);
        update_post_meta($post_id, '_chps_post_meta_link', $data_link);
    }

    

    /**
     * Taxonomy: Topics
     */

    public static function topic_tax()
    {
        $labels = array(
            'name' => __('Topics', 'content-hubs'),
            'singular_name' => __('Topic', 'content-hubs'),
            'menu_name' => __('Content Hub Topics', 'content-hubs'),
            'all_items' => __('All Topics', 'content-hubs'),
            'edit_item' => __('Edit Topic', 'content-hubs'),
            'view_item' => __('Show Topic', 'content-hubs'),
            'update_item' => __('Update Topic', 'content-hubs'),
            'add_new_item' => __('Add new Topic', 'content-hubs'),
            'new_item_name' => __('New Topic name', 'content-hubs'),
            'search_items' => __('Search Topics', 'content-hubs'),
            'choose_from_most_used' => __('Choose from the most used Topics', 'content-hubs'),
            'popular_items' => __('Popular Topics', 'content-hubs'),
            'add_or_remove_items' => __('Add or remove Topics', 'content-hubs'),
            'separate_items_with_commas' => __('Separate Topics with commas', 'content-hubs'),
            'back_to_items' => __('Back to Topics', 'content-hubs'),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'show_admin_column' => false,
            'show_in_quick_edit' => true,
            'meta_box_cb' => false,
            'hierarchical' => false,
            'show_in_rest' => true //necessary for Gutenberg
        );

        // activate taxonomy only for selected post types
        $selected_post_types = array();
        $ch_posts = get_posts(array('post_type' => 'chps-content-hub'));

        foreach($ch_posts as $ch_post) {
            $post_data = get_post_meta($ch_post->ID, '_chps_content-hub_key', true);
            foreach($post_data['chps_post_types'] as $selected_post_type) {
                $selected_post_types[] = $selected_post_type;
            }
        }

        $selected_post_types = array_values(array_unique($selected_post_types));

        register_taxonomy('chps-topic', $selected_post_types, $args);
    }

    
}
