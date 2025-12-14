<?php
/**
 * Professional Project Theme functions and definitions
 *
 * @package Professional Project Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup
 */
if (!function_exists('professional_project_theme_setup')) :
    function professional_project_theme_setup() {
        // Make theme available for translation
        load_theme_textdomain('professional-project-theme', get_template_directory() . '/languages');

        // Add theme support
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ));
        add_theme_support('customize-selective-refresh-widgets');
        add_theme_support('automatic-feed-links');
        
        // Editor styles
        add_theme_support('editor-styles');
        add_editor_style('assets/css/editor-style.css');
        
        // Gutenberg align-wide support
        add_theme_support('align-wide');
        
        // Responsive embeds
        add_theme_support('responsive-embeds');
    }
endif;
add_action('after_setup_theme', 'professional_project_theme_setup');

/**
 * Enqueue scripts and styles
 */
if (!function_exists('professional_project_theme_scripts')) :
    function professional_project_theme_scripts() {
        // Main CSS
        wp_enqueue_style('professional-project-theme-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
        
        // Custom CSS
        wp_enqueue_style('professional-project-theme-custom', get_template_directory_uri() . '/assets/css/theme.css', array(), wp_get_theme()->get('Version'));
        
        // Responsive CSS
        wp_enqueue_style('professional-project-theme-responsive', get_template_directory_uri() . '/assets/css/responsive.css', array(), wp_get_theme()->get('Version'));
        
        // Main JavaScript
        wp_enqueue_script('professional-project-theme-script', get_template_directory_uri() . '/assets/js/theme.js', array('jquery'), wp_get_theme()->get('Version'), true);
        
        // Navigation script
        wp_enqueue_script('professional-project-theme-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), wp_get_theme()->get('Version'), true);
        
        // Localize script for AJAX
        wp_localize_script('professional-project-theme-navigation', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax_nonce')
        ));
    }
endif;
add_action('wp_enqueue_scripts', 'professional_project_theme_scripts');

/**
 * Register navigation menus
 */
if (!function_exists('professional_project_theme_menus')) :
    function professional_project_theme_menus() {
        register_nav_menus(array(
            'primary' => __('Primary Menu', 'professional-project-theme'),
            'footer' => __('Footer Menu', 'professional-project-theme'),
        ));
    }
endif;
add_action('init', 'professional_project_theme_menus');

/**
 * Register widget areas
 */
if (!function_exists('professional_project_theme_widgets_init')) :
    function professional_project_theme_widgets_init() {
        register_sidebar(array(
            'name'          => __('Main Sidebar', 'professional-project-theme'),
            'id'            => 'sidebar-1',
            'description'   => __('Add widgets here.', 'professional-project-theme'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ));
        
        register_sidebar(array(
            'name'          => __('Footer Widget Area 1', 'professional-project-theme'),
            'id'            => 'footer-1',
            'description'   => __('Add footer widgets here.', 'professional-project-theme'),
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
        ));
        
        register_sidebar(array(
            'name'          => __('Footer Widget Area 2', 'professional-project-theme'),
            'id'            => 'footer-2',
            'description'   => __('Add footer widgets here.', 'professional-project-theme'),
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
        ));
    }
endif;
add_action('widgets_init', 'professional_project_theme_widgets_init');

/**
 * Custom Post Type: Projects
 */
if (!function_exists('professional_project_cpt_projects')) :
    function professional_project_cpt_projects() {
        $labels = array(
            'name'                  => _x('Projects', 'Post type general name', 'professional-project-theme'),
            'singular_name'         => _x('Project', 'Post type singular name', 'professional-project-theme'),
            'menu_name'             => _x('Projects', 'Admin Menu text', 'professional-project-theme'),
            'name_admin_bar'        => _x('Project', 'Add New on Toolbar', 'professional-project-theme'),
            'add_new'               => __('Add New', 'professional-project-theme'),
            'add_new_item'          => __('Add New Project', 'professional-project-theme'),
            'new_item'              => __('New Project', 'professional-project-theme'),
            'edit_item'             => __('Edit Project', 'professional-project-theme'),
            'view_item'             => __('View Project', 'professional-project-theme'),
            'all_items'             => __('All Projects', 'professional-project-theme'),
            'search_items'          => __('Search Projects', 'professional-project-theme'),
            'parent_item_colon'     => __('Parent Projects:', 'professional-project-theme'),
            'not_found'             => __('No projects found.', 'professional-project-theme'),
            'not_found_in_trash'    => __('No projects found in Trash.', 'professional-project-theme'),
            'featured_image'        => _x('Project Cover Image', 'Overrides the "Featured Image" phrase', 'professional-project-theme'),
            'set_featured_image'    => _x('Set cover image', 'Overrides the "Set featured image" phrase', 'professional-project-theme'),
            'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" phrase', 'professional-project-theme'),
            'use_featured_image'    => _x('Use as cover image', 'Overrides the "Use as featured image" phrase', 'professional-project-theme'),
            'archives'              => _x('Project archives', 'The post type archive label used in nav menus', 'professional-project-theme'),
            'insert_into_item'      => _x('Insert into project', 'Overrides the "Insert into post" phrase', 'professional-project-theme'),
            'uploaded_to_this_item' => _x('Uploaded to this project', 'Overrides the "Uploaded to this post" phrase', 'professional-project-theme'),
            'filter_items_list'     => _x('Filter projects list', 'Screen reader text for the filter links', 'professional-project-theme'),
            'items_list_navigation' => _x('Projects list navigation', 'Screen reader text for the pagination', 'professional-project-theme'),
            'items_list'            => _x('Projects list', 'Screen reader text for the items list', 'professional-project-theme'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'projects'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields'),
            'taxonomies'         => array('category', 'post_tag'),
            'show_in_rest'       => true, // Enable Gutenberg editor
        );

        register_post_type('projects', $args);
    }
endif;
add_action('init', 'professional_project_cpt_projects', 0);

/**
 * Add custom fields for projects
 */
if (!function_exists('professional_project_add_custom_fields')) :
    function professional_project_add_custom_fields() {
        // Only run on projects post type
        if (isset($_GET['post_type']) && $_GET['post_type'] === 'projects') {
            add_meta_box(
                'project-details',
                __('Project Details', 'professional-project-theme'),
                'professional_project_render_custom_fields',
                'projects',
                'normal',
                'high'
            );
        } elseif (isset($_GET['post']) && get_post_type($_GET['post']) === 'projects') {
            add_meta_box(
                'project-details',
                __('Project Details', 'professional-project-theme'),
                'professional_project_render_custom_fields',
                'projects',
                'normal',
                'high'
            );
        }
    }
endif;
add_action('add_meta_boxes', 'professional_project_add_custom_fields');

/**
 * Render custom fields
 */
if (!function_exists('professional_project_render_custom_fields')) :
    function professional_project_render_custom_fields($post) {
        // Security nonce
        wp_nonce_field('save_project_details', 'project_details_nonce');
        
        // Get current values
        $budget = get_post_meta($post->ID, '_project_budget', true);
        $status = get_post_meta($post->ID, '_project_status', true);
        $client = get_post_meta($post->ID, '_project_client', true);
        $start_date = get_post_meta($post->ID, '_project_start_date', true);
        $end_date = get_post_meta($post->ID, '_project_end_date', true);
        $priority = get_post_meta($post->ID, '_project_priority', true);
        
        // Status options
        $status_options = array(
            '' => __('Select Status', 'professional-project-theme'),
            'open' => __('Open', 'professional-project-theme'),
            'in_progress' => __('In Progress', 'professional-project-theme'),
            'closed' => __('Closed', 'professional-project-theme')
        );
        
        // Priority options
        $priority_options = array(
            '' => __('Select Priority', 'professional-project-theme'),
            'low' => __('Low', 'professional-project-theme'),
            'medium' => __('Medium', 'professional-project-theme'),
            'high' => __('High', 'professional-project-theme'),
            'urgent' => __('Urgent', 'professional-project-theme')
        );
        ?>
        <table class="form-table">
            <tr>
                <th><label for="_project_budget"><?php _e('Budget ($)', 'professional-project-theme'); ?></label></th>
                <td><input type="number" id="_project_budget" name="_project_budget" value="<?php echo esc_attr($budget); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="_project_status"><?php _e('Status', 'professional-project-theme'); ?></label></th>
                <td>
                    <select id="_project_status" name="_project_status">
                        <?php foreach ($status_options as $key => $value): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($status, $key); ?>><?php echo esc_html($value); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="_project_client"><?php _e('Client', 'professional-project-theme'); ?></label></th>
                <td><input type="text" id="_project_client" name="_project_client" value="<?php echo esc_attr($client); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="_project_start_date"><?php _e('Start Date', 'professional-project-theme'); ?></label></th>
                <td><input type="date" id="_project_start_date" name="_project_start_date" value="<?php echo esc_attr($start_date); ?>" /></td>
            </tr>
            <tr>
                <th><label for="_project_end_date"><?php _e('End Date', 'professional-project-theme'); ?></label></th>
                <td><input type="date" id="_project_end_date" name="_project_end_date" value="<?php echo esc_attr($end_date); ?>" /></td>
            </tr>
            <tr>
                <th><label for="_project_priority"><?php _e('Priority', 'professional-project-theme'); ?></label></th>
                <td>
                    <select id="_project_priority" name="_project_priority">
                        <?php foreach ($priority_options as $key => $value): ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($priority, $key); ?>><?php echo esc_html($value); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
endif;

/**
 * Save custom fields
 */
if (!function_exists('professional_project_save_custom_fields')) :
    function professional_project_save_custom_fields($post_id) {
        // Check if nonce is valid
        if (!isset($_POST['project_details_nonce']) || !wp_verify_nonce($_POST['project_details_nonce'], 'save_project_details')) {
            return;
        }
        
        // Check if user has permission to edit
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check post type
        if (get_post_type($post_id) !== 'projects') {
            return;
        }
        
        // Sanitize and save custom fields
        if (isset($_POST['_project_budget'])) {
            update_post_meta($post_id, '_project_budget', sanitize_text_field($_POST['_project_budget']));
        }
        
        if (isset($_POST['_project_status'])) {
            update_post_meta($post_id, '_project_status', sanitize_text_field($_POST['_project_status']));
        }
        
        if (isset($_POST['_project_client'])) {
            update_post_meta($post_id, '_project_client', sanitize_text_field($_POST['_project_client']));
        }
        
        if (isset($_POST['_project_start_date'])) {
            update_post_meta($post_id, '_project_start_date', sanitize_text_field($_POST['_project_start_date']));
        }
        
        if (isset($_POST['_project_end_date'])) {
            update_post_meta($post_id, '_project_end_date', sanitize_text_field($_POST['_project_end_date']));
        }
        
        if (isset($_POST['_project_priority'])) {
            update_post_meta($post_id, '_project_priority', sanitize_text_field($_POST['_project_priority']));
        }
    }
endif;
add_action('save_post', 'professional_project_save_custom_fields');

/**
 * Add custom columns to projects admin screen
 */
if (!function_exists('professional_project_add_custom_columns')) :
    function professional_project_add_custom_columns($columns) {
        if (get_current_screen() && get_current_screen()->post_type === 'projects') {
            $columns['project_budget'] = __('Budget', 'professional-project-theme');
            $columns['project_status'] = __('Status', 'professional-project-theme');
            $columns['project_client'] = __('Client', 'professional-project-theme');
            $columns['project_priority'] = __('Priority', 'professional-project-theme');
            
            // Reorder columns
            $new_columns = array();
            foreach ($columns as $key => $value) {
                if ($key === 'title') {
                    $new_columns[$key] = $value;
                    $new_columns['project_budget'] = __('Budget', 'professional-project-theme');
                } elseif ($key !== 'project_budget') {
                    $new_columns[$key] = $value;
                }
            }
            return $new_columns;
        }
        return $columns;
    }
endif;
add_filter('manage_projects_posts_columns', 'professional_project_add_custom_columns');

/**
 * Display custom column content
 */
if (!function_exists('professional_project_custom_column_content')) :
    function professional_project_custom_column_content($column, $post_id) {
        if (get_post_type($post_id) === 'projects') {
            switch ($column) {
                case 'project_budget':
                    $budget = get_post_meta($post_id, '_project_budget', true);
                    echo $budget ? '$' . number_format($budget) : '—';
                    break;
                case 'project_status':
                    $status = get_post_meta($post_id, '_project_status', true);
                    $status_labels = array(
                        'open' => __('Open', 'professional-project-theme'),
                        'in_progress' => __('In Progress', 'professional-project-theme'),
                        'closed' => __('Closed', 'professional-project-theme')
                    );
                    echo isset($status_labels[$status]) ? $status_labels[$status] : '—';
                    break;
                case 'project_client':
                    $client = get_post_meta($post_id, '_project_client', true);
                    echo $client ? esc_html($client) : '—';
                    break;
                case 'project_priority':
                    $priority = get_post_meta($post_id, '_project_priority', true);
                    $priority_labels = array(
                        'low' => __('Low', 'professional-project-theme'),
                        'medium' => __('Medium', 'professional-project-theme'),
                        'high' => __('High', 'professional-project-theme'),
                        'urgent' => __('Urgent', 'professional-project-theme')
                    );
                    echo isset($priority_labels[$priority]) ? $priority_labels[$priority] : '—';
                    break;
            }
        }
    }
endif;
add_action('manage_projects_posts_custom_column', 'professional_project_custom_column_content', 10, 2);

/**
 * Make custom columns sortable
 */
if (!function_exists('professional_project_sortable_columns')) :
    function professional_project_sortable_columns($columns) {
        $columns['project_budget'] = 'project_budget';
        $columns['project_status'] = 'project_status';
        $columns['project_client'] = 'project_client';
        $columns['project_priority'] = 'project_priority';
        return $columns;
    }
endif;
add_filter('manage_edit-projects_sortable_columns', 'professional_project_sortable_columns');

/**
 * Handle sorting of custom columns
 */
if (!function_exists('professional_project_handle_sorting')) :
    function professional_project_handle_sorting($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }
        
        if (isset($_GET['post_type']) && $_GET['post_type'] === 'projects') {
            if ($query->get('orderby') === 'project_budget') {
                $query->set('meta_key', '_project_budget');
                $query->set('orderby', 'meta_value_num');
            } elseif ($query->get('orderby') === 'project_status') {
                $query->set('meta_key', '_project_status');
                $query->set('orderby', 'meta_value');
            } elseif ($query->get('orderby') === 'project_client') {
                $query->set('meta_key', '_project_client');
                $query->set('orderby', 'meta_value');
            } elseif ($query->get('orderby') === 'project_priority') {
                $query->set('meta_key', '_project_priority');
                $query->set('orderby', 'meta_value');
            }
        }
    }
endif;
add_action('pre_get_posts', 'professional_project_handle_sorting');

/**
 * Excerpt length for projects
 */
if (!function_exists('professional_project_excerpt_length')) :
    function professional_project_excerpt_length($length) {
        if (is_singular('projects')) {
            return 30; // Show more excerpt for projects
        }
        return 20; // Default length
    }
endif;
add_filter('excerpt_length', 'professional_project_excerpt_length');

/**
 * Add theme support for selective refresh for widgets
 */
add_theme_support('customize-selective-refresh-widgets');

/**
 * Security: Prevent XSS and SQL injection
 */
if (!function_exists('professional_project_clean_input')) :
    function professional_project_clean_input($data) {
        if (is_array($data)) {
            return array_map('professional_project_clean_input', $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
endif;

/**
 * Add custom body classes
 */
if (!function_exists('professional_project_body_classes')) :
    function professional_project_body_classes($classes) {
        // Add a class of hfeed to non-singular pages
        if (!is_singular()) {
            $classes[] = 'hfeed';
        }

        // Add a class if the site is using a static front page
        if (is_front_page() && is_home()) {
            $classes[] = 'home-page';
        } elseif (is_front_page()) {
            $classes[] = 'front-page';
        } elseif (is_home()) {
            $classes[] = 'blog-home';
        }

        return $classes;
    }
endif;
add_filter('body_class', 'professional_project_body_classes');

/**
 * Custom template tags
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}