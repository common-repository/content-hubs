<?php
// Template for "Edit Content Hub"
?>
<div class="intro">
  <img src="<?php echo $this->plugin_url; ?>assets/images/chps_logo.png" class="chps_logo">
  <?php echo $html_setting_intro_text; ?>
</div>

<input type="hidden" id="ch_id" name="ch_id" value="<?php echo esc_attr($ch_id); ?>">

<div class="shortcode">
  <strong><?php echo __('Shortcode', 'content-hubs'); ?>:</strong>
  <code>[content-hub id="<?php echo esc_attr($ch_id); ?>"]</code>
</div>

<div class="chps-wrap-settings">
  <div class="chps-settings">
    <nav class="nav-tab-wrapper">
      <a href="#tab-1" class="nav-tab nav-tab-active"><?php echo __('General', 'content-hubs'); ?></a>
      <a href="#tab-2" class="nav-tab"><?php echo __('Design', 'content-hubs'); ?></a>
      <a href="#tab-3" class="nav-tab"><?php echo __('Misc', 'content-hubs'); ?></a>
    </nav>

    <div class="tab-content">
      <div id="tab-1" class="tab-pane active">

        <table class="form-table">
          <tr valign="top">
            <th scope="row"><?php echo __('Type', 'content-hubs'); ?></th>
            <td>
              <select name="chps_sources_type" id="chps_sources_type">
                <option value="1" <?php echo ($chps_sources_type == '1') ? 'selected' : ''; ?>><?php echo __('Posts & Tags', 'content-hubs'); ?></option>
                <option value="2" <?php echo ($chps_sources_type == '2') ? 'selected' : ''; ?>><?php echo __('Custom Post Types & Topics', 'content-hubs'); ?></option>
              </select>
            </td>
          </tr>

          <tr valign="top" class="tr_select_tags" <?php echo ($chps_sources_type == '2') ? 'style="display:none"' : ''; ?>>
            <th scope="row"><?php echo __('Select Tags for filterbar', 'content-hubs'); ?></th>
            <td>
              <?php

              // render checkboxes
              foreach($all_post_tag_taxonomy_terms as $post_tag_taxonomy_term) {

                $checked = (in_array($post_tag_taxonomy_term->term_id, $selected_post_tag_taxonomy_terms))? 'checked' : '';

                echo '<div>
                  <label><input type="checkbox" id="post_tag_taxonomy_term_id-'.$post_tag_taxonomy_term->term_id.'" name="chps_post_tag_taxonomy[]" value="'.$post_tag_taxonomy_term->term_id.'" '.$checked.'>
                  <span>'.$post_tag_taxonomy_term->name.'</span></label>
                </div>';

              }
              
              ?>
            </td>
          </tr>

          <tr valign="top" class="tr_select_post_types" <?php echo ($chps_sources_type == '1') ? 'style="display:none"' : ''; ?>>
            <th scope="row"><?php echo __('Select Post Types', 'content-hubs'); ?></th>
            <td>
              <?php
              
              // render checkboxes
              foreach($all_post_types as $post_type) {

                $checked = (in_array($post_type->name, $selected_post_types))? 'checked' : '';

                echo '<div>
                  <label><input type="checkbox" id="'.$post_type->name.'" name="chps_post_types[]" value="'.$post_type->name.'" '.$checked.'>
                  <span>'.$post_type->label.'</span></label>
                </div>';

              }
              ?>

              <div class="hint"><?php echo __('Choose the post types which should be shown in the grid.', 'content-hubs'); ?></div>
            
            </td>
          </tr>

          <tr valign="top" class="tr_select_topics" <?php echo ($chps_sources_type == '1') ? 'style="display:none"' : ''; ?>>
            <th scope="row"><?php echo __('Select Topics for filterbar', 'content-hubs'); ?></th>
            <td>
              <?php

              // render checkboxes
              foreach($all_global_taxonomy_terms as $global_taxonomy_term) {

                $checked = (in_array($global_taxonomy_term->term_id, $selected_global_taxonomy_terms))? 'checked' : '';

                echo '<div>
                  <label><input type="checkbox" id="global_taxonomy_term_id-'.$global_taxonomy_term->term_id.'" name="chps_global_taxonomy[]" value="'.$global_taxonomy_term->term_id.'" '.$checked.'>
                  <span>'.$global_taxonomy_term->name.'</span></label>
                </div>';

              }
              
              ?>

              <div class="hint"><?php echo __('Filter can be set in the in the posts or pages.', 'content-hubs'); ?></div>

            </td>
          </tr>

          <?php echo $html_setting_fallback_image; ?>

        </table>
      
      </div>
      
      <div id="tab-2" class="tab-pane">
        <table class="form-table">

          <?php echo $html_setting_color_theme; ?>

          <tr valign="top">
            <th scope="row"><?php echo __('Set background color', 'content-hubs'); ?></th>
            <td>
              <input type="checkbox" id="chps_bg_use_color" name="chps_bg_use_color" <?php echo ($chps_bg_use_color)? 'checked' : ''; ?>>
              <br><br>
              <div id="chps_bg_color_wrap" <?php echo (!$chps_bg_use_color)? 'style="display:none"' : ''; ?>>
                <input type="text" class="chps_color" name="chps_bg_color" value="<?php echo esc_attr($chps_bg_color); ?>" placeholder="<?php echo $this->bg_color; ?>"></input>
              </div>
            </td>
          </tr>

          <?php echo $html_setting_space_between_items; ?>

          <?php echo $html_setting_grid_shows_initially; ?>

        </table>
      </div>
      <div id="tab-3" class="tab-pane">
        <table class="form-table">
          <tr valign="top">
            <th scope="row"><?php echo __('Custom ID', 'content-hubs'); ?></th>
            <td>
              <input type="text" class="chps_custom_id" name="chps_custom_id" value="<?php echo esc_attr($custom_id); ?>"></input>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <div class="chps-upgrade-info">
    <?php echo $html_setting_upgrade_info; ?>
  </div>
</div>