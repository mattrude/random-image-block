<?php
/*
Plugin Name: Random Image Block
Plugin URI: http://mattrude.com/projects/random-image-block/
Description: Display a random image from your native WordPress photo galley or in-beaded images.
Version: 0.4
Author: Matt Rude
Author URI: http://mattrude.com/
*/


class random_image_widget extends WP_Widget {
  function random_image_widget() {
    $currentLocale = get_locale();
    if(!empty($currentLocale)) {
      $moFile = dirname(__FILE__) . "/languages/random_image_widget_" .  $currentLocale . ".mo";
      if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('', $moFile);
    }
    $random_image_widget_name = __('Random Image Widget', 'random-image-block');
    $random_image_widget_description = __('Displays a random gallery image.', 'random-image-block');
    $widget_ops = array('classname' => 'random_image_widget', 'description' => $random_image_widget_description );
    $this->WP_Widget('random_image_widget', $random_image_widget_name, $widget_ops);
  }  
  
  function widget($args, $instance) {
    extract($args);
    $riw_widget_title = empty($instance['widget_title']) ? '&nbsp;' : apply_filters('widget_title', $instance['widget_title']);
    $riw_cat_single = empty($instance['single_category']) ? 'off' : apply_filters('single_category', $instance['single_category']);
    $riw_cat_slug = empty($instance['gallery_category']) ? 'empty' : apply_filters('gallery_category', $instance['gallery_category']);
    global $wpdb;

    if ($riw_widget_title == "&nbsp;") {
      $riw_widget_title = __('Random Image','random-image-block');
    }

    $args = array(
       'post_type' => 'attachment',
       'post_mime_type' => 'image',
       'numberposts' => -1,
       'post_status' => null,
       'post_parent' => $post->ID,
       'orderby' => 'rand'
    );

    $attachments = get_posts($args);
    if (get_category_by_slug($riw_cat_slug) == null) {
      $riw_cat_single = "off";
    } else {
      $riw_cat_id = get_category_by_slug($riw_cat_slug)->term_id;
    }
    $noimages = count($attachments);
    if ($attachments) {
      foreach ($attachments as $attachment) {
        if ( $riw_cat_single == "on" ) {
          $albumid = $attachment->post_parent;
        } else {
          $albumid = $attachment->post_parent;
          $riw_cat_id = $riw_cat_id_pre->cat_ID;
	  foreach((get_the_category($albumid)) as $category) { 
          $riw_cat_id = $category->cat_ID; 
          }

	}

        if (in_category($riw_cat_id, $albumid)) { 
          $imgid = $attachment->ID;
          $meta = wp_get_attachment_metadata($imgid);

          // construct the image
          echo "<div class='widget bookmarks widget-bookmarks'>";
            echo "<h3 class='widget-title' >$riw_widget_title</h3>";
            echo "<div class='random-image'>";
              echo "<a href=".get_permalink( $imgid )." >";
              echo "<img width='".$meta['sizes']['thumbnail']['width']."'  height='".$meta['sizes']['thumbnail']['height']."' src='".wp_get_attachment_thumb_url($imgid)."' alt='Random image: ".$attachment->post_title."' />";
              echo "</a>";
              echo "<p class='random-image-caption'><strong>$attachment->post_excerpt</strong></p>";
              echo "<p class='random-image-album'><small>".__('Album:','random-image-block')." <a href=".get_permalink( $albumid ).">".get_the_title($albumid)."</a></small></p>";
            echo "</div>";
          echo "</div>";
          break;
	}
      }
    }
  }
  
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['widget_title'] = strip_tags($new_instance['widget_title']);
    $instance['single_category'] = strip_tags($new_instance['single_category']);
    $instance['gallery_category'] = strip_tags($new_instance['gallery_category']);
    return $instance;
  }
  
  function form($instance) {
    $riw_widget_title = strip_tags($instance['widget_title']);
    $riw_cat_single = $instance['single_category'];
    $riw_cat_slug = strip_tags($instance['gallery_category']);
    ?><p><label for="<?php echo $this->get_field_id('widget_title'); ?>"><?php _e('widget title', 'random-image-block')?>:<input class="widefat" id="<?php echo $this->get_field_id('widget_title'); ?>" name="<?php echo $this->get_field_name('widget_title'); ?>" type="text" value="<?php echo attribute_escape($riw_widget_title); ?>" /></label></p>

    <p><input class="checkbox" type="checkbox" <?php if ("$riw_cat_single" == "on" ){echo 'checked="checked"';} ?> id="<?php echo $this->get_field_id('single_category'); ?>" name="<?php echo $this->get_field_name('single_category'); ?>" />
    <label for="<?php echo $this->get_field_id('single_category'); ?>"><?php _e('Display from a single Category?', 'random-image-block')?></label></p>

    <p><label for="<?php echo $this->get_field_id('gallery_category'); ?>"><?php _e('Category slug (only 1)', 'random-image-block')?>:<input class="widefat" id="<?php echo $this->get_field_id('gallery_category'); ?>" name="<?php echo $this->get_field_name('gallery_category'); ?>" type="text" value="<?php echo attribute_escape($riw_cat_slug); ?>" /></label></p><?php
  }
}

add_action('widgets_init', 'random_image_widget_init');
function random_image_widget_init() {
        register_widget('random_image_widget');
}

?>
