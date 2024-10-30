<?php

/**
 * Plugin Name: Kitab - Books Management System
 * Description: Enhance your WordPress website with custom book functionality, including post types, meta, taxonomies, and options.
 * Version: 1.0.1
 * Author: Jlil
 * Author URI: https://jlil.net
 * Text Domain: kitab
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

//  Hey, it's great to see you have Neve active for a few days now. How is everything going? If you can spare a few moments to rate it on WordPress.org it would help us a lot (and boost my motivation). Cheers!
// Ok, I will gladly help.No, thanks.
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('KITAB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KITAB_PLUGIN_PATH', plugin_dir_path(__FILE__));
// if logged in set the version to time() to force browser to reload the css and js files
//  Uncaught Error: Call to undefined function is_user_logged_in()
define('KITAB_VERSION', '1.0');

// if (is_user_logged_in()) {
//     define('KITAB_VERSION', time());
// } else {
//     define('KITAB_VERSION', '1.0');
// }

class Kitab_Plugin
{
    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        register_uninstall_hook(__FILE__, array(__CLASS__, 'uninstall'));
        add_action('init', array($this, 'load_textdomain'));
        add_action('admin_notices', array($this, 'show_admin_notice'));
        add_action('admin_notices', array($this, 'show_setup_notice'));
        add_action('kitab_loaded', array($this, 'kitab_loaded'));
        add_action('wp_ajax_kitab_dismiss_notice', array($this, 'dismiss_notice'));
        add_action('wp_ajax_kitab_already_rated', array($this, 'already_rated'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        // Add settings link to plugin page
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));
    }

    public function add_settings_link($links)
    {
        $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=kitab-plugin')) . '">' . esc_html__('Settings', 'kitab') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function activate()
    {

        if (!get_option('kitab_activation_date')) {
            $activation_date = time();
            update_option('kitab_activation_date', $activation_date);
        }
        if (!get_option('kitab_general_settings')) {
            $general_settings = array(
                'post_type' => array('post')
            );
            update_option('kitab_general_settings', $general_settings);
        }

        if (!get_option('kitab_layout_settings')) {
            $layout_settings = array(
                'show_fields' => array(
                    'kitab_book_name',
                    'kitab_book_author',
                    'kitab_pages',
                    'kitab_isbn',
                    'kitab_pdf_link'
                ),
                'display_as' => 'table',
                'layout_position' => 'above',
            );
            update_option('kitab_layout_settings', $layout_settings);
        }
    }

    public function show_setup_notice()
    {
        // Check if current page is the Kitab settings page
        $screen = get_current_screen();
        if ($screen->id === 'toplevel_page_kitab-plugin') {
            // Set a flag to not show the notice again
            update_option('kitab_setup_notice_dismissed', true);
            return;
        }

        // Check if the notice has been dismissed before
        $dismissed = get_option('kitab_setup_notice_dismissed');
        if ($dismissed) {
            return;
        }
?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php echo wp_kses_post(
                    sprintf(
                        /* translators: Link to the Kitab settings page */
                        __('Thank you for activating Kitab! Please go to the <a href="%s">Kitab settings page</a> to complete the setup.', 'kitab'),
                        esc_url(admin_url('admin.php?page=kitab-plugin'))
                    )
                ); ?>

            </p>
        </div>


        <?php
    }


    public function deactivate()
    {
        // Remove any scheduled events
        // wp_clear_scheduled_hook('my_custom_event');

        // Flush rewrite rules
        flush_rewrite_rules();

        // Do any other cleanup tasks here
    }

    public static function uninstall()
    {
        // Remove all options related to the plugin
        delete_option('kitab_activation_date');
        delete_option('kitab_already_rated');
        delete_option('kitab_dismissed_notice');
    }

    public function load_textdomain()
    {
        load_plugin_textdomain('kitab', false, dirname(plugin_basename(__FILE__)) . "/languages");
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('kitab-admin', KITAB_PLUGIN_URL . 'assets/admin/js/kitab-admin.js', array('jquery'), KITAB_VERSION, true);
        // add css file for admin page only 
        $screen = get_current_screen();
        if ($screen->id == 'toplevel_page_kitab-plugin') {
            wp_enqueue_style('kitab-admin', KITAB_PLUGIN_URL . 'assets/admin/css/kitab-options.css', array(), KITAB_VERSION, 'all');
        }
    }

    public function show_admin_notice()
    {
        $kitab_activation_date = get_option('kitab_activation_date');
        if (!$kitab_activation_date) {
            update_option('kitab_activation_date', time());
        } else {
            $days_since_activation = round((time() - $kitab_activation_date) / (60 * 60 * 24));
            if ($days_since_activation >= 7) {
                $already_rated = get_option('kitab_already_rated');
                $dismissed = get_option('kitab_dismissed_notice');
                $rating_url = 'https://wordpress.org/support/plugin/kitab/reviews/?filter=5#new-post';

                if (!$already_rated && !$dismissed) {
        ?>
                    <div id="kitab-admin-notice" class="notice notice-info is-dismissible">
                        <p><?php esc_html_e('Thank you for using Kitab! If you enjoy this plugin, please consider leaving us a rating on the WordPress plugin repository.', 'kitab'); ?></p>
                        <p>
                            <a href="<?php echo esc_url($rating_url); ?>" target="_blank"><?php esc_html_e('Rate now', 'kitab'); ?></a> |
                            <a href="#" class="kitab-dismiss-notice"><?php esc_html_e('Dismiss', 'kitab'); ?></a> |
                            <a href="#" class="kitab-already-rated"><?php esc_html_e('Already rated', 'kitab'); ?></a>
                        </p>
                    </div>
                    <script>

                    </script>
                <?php
                } elseif ($already_rated) {
                ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php esc_html_e('Thank you for rating Kitab! ♥', 'kitab'); ?></p>
                    </div>
<?php
                }
            }
        }
    }

    public function kitab_loaded()
    {
        foreach (glob(KITAB_PLUGIN_PATH . 'includes/classes/*.php') as $file) {
            require_once $file;
        }

        require_once "includes/public/class-layout.php";
    }

    public function dismiss_notice()
    {
        update_option('kitab_dismissed_notice', true);
        wp_die();
    }

    public function already_rated()
    {
        update_option('kitab_already_rated', true);
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Thank you for rating Kitab!', 'kitab') . '</p></div>';
        wp_die();
    }
}


new Kitab_Plugin();


do_action('kitab_loaded');



// filter usage example to add author url field to book info , using add filter
add_filter('kitab_add_new_table_field', function () {
    $value = get_post_meta(get_the_ID(), "_kitab_author_url", true);
    $label = 'Author URL';
    $emoji = '👨‍💻';
    $html = '';
    if (!empty($value)) {
        $html .= "<tr class='kitab_author_url'><th>{$emoji} {$label}</th><td>";
        $html .= "<a href='{$value}' target='_blank' rel='noopener noreferrer'>{$value}</a>";
        $html .= '</td></tr>';
    }
    return $html;
});
