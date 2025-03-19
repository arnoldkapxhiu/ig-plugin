<?php
/*
 * Plugin Name: Independent Gallery
 * Plugin URI: https://wedigit.al/
 * Description: A standalone gallery plugin that allows users to upload and manage images below the WordPress editor.
 * Version: 1.0
 * Author: Arnold Kapxhiu
 * Author URI: https://wedigit.al
 * License: GPL2
 * Text Domain: independent-gallery
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load text domain for translations
function ig_load_textdomain() {
    load_plugin_textdomain('independent-gallery', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'ig_load_textdomain');

// Enqueue admin CSS and JS
function ig_enqueue_admin_assets($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_style('ig-admin-css', plugins_url('css/ig-admin.css', __FILE__));
        wp_enqueue_script('jquery-ui-sortable'); // Enqueue jQuery UI Sortable
        wp_enqueue_script('ig-admin-js', plugins_url('js/ig-admin.js', __FILE__), array('jquery', 'jquery-ui-sortable'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'ig_enqueue_admin_assets');

// Enqueue frontend CSS and JS
function ig_enqueue_frontend_assets() {
    // Enqueue plugin's frontend CSS
    wp_enqueue_style('ig-frontend-css', plugins_url('css/ig-frontend.css', __FILE__));

    // Enqueue local Fancybox CSS
    wp_enqueue_style('fancybox-css', plugins_url('css/jquery.fancybox.min.css', __FILE__));

    // Enqueue local Fancybox JS
    wp_enqueue_script('fancybox-js', plugins_url('js/jquery.fancybox.min.js', __FILE__), array('jquery'), null, true);

    // Enqueue plugin's lightbox JS
    wp_enqueue_script('ig-lightbox-js', plugins_url('js/ig-lightbox.js', __FILE__), array('jquery', 'fancybox-js'), null, true);

    // Enqueue WordPress Dashicons
    wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'ig_enqueue_frontend_assets');

// Add meta box below the editor
function ig_add_gallery_meta_box() {
    add_meta_box(
        'ig_gallery_meta_box', // Meta box ID
        __('Gallery', 'independent-gallery'), // Meta box title
        'ig_render_gallery_meta_box', // Callback function
        'post',                // Post type (can be changed to 'page' or custom post types)
        'normal',              // Context
        'high'                 // Priority
    );
}
add_action('add_meta_boxes', 'ig_add_gallery_meta_box');

// Render the gallery meta box
function ig_render_gallery_meta_box($post) {
    // Get the current gallery images
    $gallery_images = get_post_meta($post->ID, '_ig_gallery_images', true);
    $gallery_images = is_array($gallery_images) ? $gallery_images : array();

    // Get gallery settings
    $gallery_columns = get_post_meta($post->ID, '_ig_gallery_columns', true);
    $gallery_thumbnail_size = get_post_meta($post->ID, '_ig_gallery_thumbnail_size', true);
    $gallery_image_height = get_post_meta($post->ID, '_ig_gallery_image_height', true);

    // Default values
    $gallery_columns = !empty($gallery_columns) ? absint($gallery_columns) : 4;
    $gallery_thumbnail_size = !empty($gallery_thumbnail_size) ? sanitize_text_field($gallery_thumbnail_size) : 'medium';
    $gallery_image_height = !empty($gallery_image_height) ? sanitize_text_field($gallery_image_height) : '200px';

    // Nonce field for security
    wp_nonce_field('ig_gallery_nonce', 'ig_gallery_nonce');

    // Display the gallery images and settings
    ?>
    <div class="ig-gallery-admin">
        <p><strong><?php esc_html_e('Shortcode:', 'independent-gallery'); ?></strong> <?php esc_html_e('Use', 'independent-gallery'); ?> <code>[ig_gallery]</code> <?php esc_html_e('to display the gallery in your post.', 'independent-gallery'); ?></p>

        <!-- Gallery Settings -->
        <div class="ig-gallery-settings">
            <label for="ig_gallery_columns"><?php esc_html_e('Number of Columns:', 'independent-gallery'); ?></label>
            <select name="ig_gallery_columns" id="ig_gallery_columns">
                <option value="1" <?php selected($gallery_columns, 1); ?>><?php esc_html_e('1 Column', 'independent-gallery'); ?></option>
                <option value="2" <?php selected($gallery_columns, 2); ?>><?php esc_html_e('2 Columns', 'independent-gallery'); ?></option>
                <option value="3" <?php selected($gallery_columns, 3); ?>><?php esc_html_e('3 Columns', 'independent-gallery'); ?></option>
                <option value="4" <?php selected($gallery_columns, 4); ?>><?php esc_html_e('4 Columns', 'independent-gallery'); ?></option>
                <option value="5" <?php selected($gallery_columns, 5); ?>><?php esc_html_e('5 Columns', 'independent-gallery'); ?></option>
                <option value="6" <?php selected($gallery_columns, 6); ?>><?php esc_html_e('6 Columns', 'independent-gallery'); ?></option>
                <option value="7" <?php selected($gallery_columns, 7); ?>><?php esc_html_e('7 Columns', 'independent-gallery'); ?></option>
                <option value="8" <?php selected($gallery_columns, 8); ?>><?php esc_html_e('8 Columns', 'independent-gallery'); ?></option>
            </select>

            <label for="ig_gallery_thumbnail_size"><?php esc_html_e('Thumbnail Size:', 'independent-gallery'); ?></label>
            <select name="ig_gallery_thumbnail_size" id="ig_gallery_thumbnail_size">
                <?php
                $sizes = get_intermediate_image_sizes();
                foreach ($sizes as $size) : ?>
                    <option value="<?php echo esc_attr($size); ?>" <?php selected($gallery_thumbnail_size, $size); ?>><?php echo esc_html($size); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="ig_gallery_image_height"><?php esc_html_e('Image Height:', 'independent-gallery'); ?></label>
            <input type="text" name="ig_gallery_image_height" id="ig_gallery_image_height" value="<?php echo esc_attr($gallery_image_height); ?>" placeholder="<?php esc_attr_e('e.g., 200px', 'independent-gallery'); ?>">
        </div>

        <!-- Gallery Images -->
        <ul class="ig-gallery-images">
            <?php foreach ($gallery_images as $attachment_id) : ?>
                <li class="ig-gallery-image" data-attachment-id="<?php echo esc_attr($attachment_id); ?>">
                    <?php echo wp_get_attachment_image($attachment_id, 'thumbnail'); ?>
                    <a href="#" class="ig-remove-image"><?php esc_html_e('Remove', 'independent-gallery'); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
        <input type="hidden" id="ig_gallery_images" name="ig_gallery_images" value="<?php echo esc_attr(implode(',', $gallery_images)); ?>">
        <button type="button" class="button ig-upload-gallery-images"><?php esc_html_e('Add Images', 'independent-gallery'); ?></button>
    </div>
    <?php
}

// Save gallery data when the post is saved
function ig_save_gallery_meta($post_id) {
    // Verify nonce
    if (!isset($_POST['ig_gallery_nonce']) || !wp_verify_nonce(
        wp_unslash($_POST['ig_gallery_nonce']), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        'ig_gallery_nonce'
    )) {
        return;
    }

    // Check for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save gallery images
    if (isset($_POST['ig_gallery_images'])) {
        $gallery_images = array_map('absint', explode(',', sanitize_text_field(wp_unslash($_POST['ig_gallery_images']))));
        update_post_meta($post_id, '_ig_gallery_images', $gallery_images);
    } else {
        delete_post_meta($post_id, '_ig_gallery_images');
    }

    // Save gallery columns
    if (isset($_POST['ig_gallery_columns'])) {
        $gallery_columns = absint(wp_unslash($_POST['ig_gallery_columns']));
        update_post_meta($post_id, '_ig_gallery_columns', $gallery_columns);
    }

    // Save gallery thumbnail size
    if (isset($_POST['ig_gallery_thumbnail_size'])) {
        $gallery_thumbnail_size = sanitize_text_field(wp_unslash($_POST['ig_gallery_thumbnail_size']));
        update_post_meta($post_id, '_ig_gallery_thumbnail_size', $gallery_thumbnail_size);
    }

    // Save gallery image height
    if (isset($_POST['ig_gallery_image_height'])) {
        $gallery_image_height = sanitize_text_field(wp_unslash($_POST['ig_gallery_image_height']));
        update_post_meta($post_id, '_ig_gallery_image_height', $gallery_image_height);
    }
}
add_action('save_post', 'ig_save_gallery_meta');

// Shortcode to display the gallery on the frontend
function ig_gallery_shortcode($atts) {
    $post_id = get_the_ID();
    $gallery_images = get_post_meta($post_id, '_ig_gallery_images', true);

    if (empty($gallery_images)) {
        return '<p>' . esc_html__('No gallery images found.', 'independent-gallery') . '</p>';
    }

    // Get gallery settings
    $gallery_columns = get_post_meta($post_id, '_ig_gallery_columns', true);
    $gallery_thumbnail_size = get_post_meta($post_id, '_ig_gallery_thumbnail_size', true);
    $gallery_image_height = get_post_meta($post_id, '_ig_gallery_image_height', true);

    // Default values
    $gallery_columns = !empty($gallery_columns) ? absint($gallery_columns) : 4;
    $gallery_thumbnail_size = !empty($gallery_thumbnail_size) ? sanitize_text_field($gallery_thumbnail_size) : 'medium';
    $gallery_image_height = !empty($gallery_image_height) ? sanitize_text_field($gallery_image_height) : '200px';

    ob_start();
    ?>
    <div class="ig-gallery" style="--columns: <?php echo esc_attr($gallery_columns); ?>;">
        <div class="ig-gallery-images">
            <?php foreach ($gallery_images as $attachment_id) : ?>
                <div class="ig-gallery-image" style="height: <?php echo esc_attr($gallery_image_height); ?>;">
                    <a href="<?php echo esc_url(wp_get_attachment_url($attachment_id)); ?>" data-fancybox="ig-gallery">
                        <?php echo wp_get_attachment_image($attachment_id, $gallery_thumbnail_size); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('ig_gallery', 'ig_gallery_shortcode');