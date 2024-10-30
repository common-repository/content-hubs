<?php

  // Vars
  $number_of_items = $ch['chps_number_of_items'] ? $ch['chps_number_of_items'] : $this->number_of_items;
  $number_of_loadmore_items = $number_of_items;
  $number_of_total_items = $this->number_of_total_items;

  $block_id = $ch['chps_custom_id'];

  $selected_sources_type = $ch['chps_sources_type'];

  // get current post types
  $selected_post_types = ($selected_sources_type == '1') ? array('post') : $ch['chps_post_types'];
  
  
  // exit, if no post types selected
  if(empty($selected_post_types)) {
    echo '<div class="chps-alert"><strong>Content Hub Plugin:</strong> Please select Post Types first (Content Hub Settings).</div>';
    return;
  }
  
  // get current taxonomy terms
  $selected_taxonomy = ($selected_sources_type == '1') ? 'post_tag' : 'chps-topic';
  $selected_taxonomy_term_ids = ($selected_sources_type == '1') ? $ch['chps_post_tag_taxonomy'] : $ch['chps_global_taxonomy'];
  
  // verify that every term_id exists otherwise remove
  foreach($selected_taxonomy_term_ids as $index => $term_id) {
    if(!get_term($term_id)) {
      unset($selected_taxonomy_term_ids[$index]);
    }
  }
  // reset array index
  $selected_taxonomy_term_ids = array_values($selected_taxonomy_term_ids);
  
  $has_filter = (!empty($selected_taxonomy_term_ids))? true : false;

  // get grid items by selected post types
  $query_args = array(
    'post_type' => $selected_post_types,
    'numberposts' => $number_of_total_items,
    'fields' => 'ids'
  );

  // extend query by selected taxonomy
  if($has_filter) {
    $query_args['tax_query'] = array(
      array(
        'taxonomy' => $selected_taxonomy,
        'field' => 'term_id',
        'terms' =>$selected_taxonomy_term_ids
      )
    );
  }

  $grid_item_ids = get_posts($query_args);

  // exit, if no post found
  if(empty($grid_item_ids)) {
    echo '<div class="chps-alert"><strong>Content Hub Plugin:</strong> No posts found. If you use filters only tagged posts will be shown.</div>';
    return;
  }

  $item_count = 0;

?>

<div class="chps-grid chps-sources-type-<?php echo $selected_sources_type; ?>" id="<?php echo $block_id; ?>" data-number_of_items="<?php echo $number_of_items; ?>">
  
  <?php if($has_filter): ?>
    <div class="chps-filterbar">
      <div class="chps-filter-items">
      <?php foreach($selected_taxonomy_term_ids as $selected_taxonomy_term_id): ?>
        <?php
        $selected_taxonomy_term = get_term($selected_taxonomy_term_id);
        ?>
        <div class="chps-filter-item" data-filter="<?php echo esc_attr($selected_taxonomy_term->slug); ?>">
          <?php echo '#' . esc_attr($selected_taxonomy_term->name); ?>
        </div>
      <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if($grid_item_ids): ?>
    <div class="chps-grid-items">
    <?php foreach($grid_item_ids as $post_id): ?>
      
      <?php
      // VARS
      $item_count++;
      $load_more_item = ($item_count > $number_of_items)? 'chps-load-more-item' : '';
      $topics = get_the_terms($post_id, $selected_taxonomy);
      $topics_list_slugs = '';
      $topics_list_names = '';
      if($topics) {
        foreach($topics as $topic) {
          $topics_list_slugs .= $topic->slug . ' ';
          $topics_list_names .= '#' . $topic->name . ' '; 
        }
      }
      
      $data = get_post_meta($post_id, '_chps_post_meta', true);
      
      $headline = (isset($data['headline']))? $data['headline'] : get_the_title($post_id); // Fallback
      $description = (isset($data['short_description']))? $data['short_description'] : substr(get_the_excerpt($post_id), 0, strpos(get_the_excerpt($post_id), '&hellip;')); // Fallback
      
      //shorten description
      $string = strip_tags($description);
      $length = 225;
      if (strlen($string) > $length) {
          $string = wordwrap($string, $length, 'breakhere');
          $string = substr($string, 0, strpos($string, "breakhere"));
          $string .= '...';
      }
      $short_description = $string;

      $link = get_post_meta($post_id, '_chps_post_meta_link', true)? get_post_meta($post_id, '_chps_post_meta_link', true) : get_the_permalink($post_id); // Fallback
      
      // image
      $image = false;
      if(get_post_meta($post_id, '_chps_post_meta_image', true)) {
        $image = get_post_meta($post_id, '_chps_post_meta_image', true);
      } else if($ch['fallback_image']) {
        $image = $ch['fallback_image'];
      } else if(has_post_thumbnail($post_id)) {
        $image = wp_get_attachment_image_url(get_post_thumbnail_id($post_id), 'large');
      }
      ?>
      
      <div class="chps-grid-item <?php echo $load_more_item; ?>" data-topics="<?php echo esc_attr($topics_list_slugs); ?>">
        <div <?php if($image): ?>style="background-image: url('<?php echo esc_url_raw($image); ?>')"<?php endif; ?>>
          <a href="<?php echo esc_url_raw($link); ?>" title="<?php echo esc_attr($headline); ?>">
            <div class="chps-inner-content">
              <div class="chps-text">
                <div class="chps-topics"><?php echo esc_attr($topics_list_names); ?></div>
                <div class="chps-headline"><?php echo esc_attr($headline); ?></div>
                <div class="chps-short_description"><?php echo esc_attr($short_description); ?></div>
              </div>
            </div>
          </a>
        </div>
      </div>


    <?php endforeach; ?>
    </div>

    <?php echo $html_branding; ?>
    
    <button class="chps-load-more-btn" style="display: none;"><?php echo __('Load more', 'content-hubs'); ?></button>
  <?php endif; ?>
</div>