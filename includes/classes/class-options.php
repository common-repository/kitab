<?php

namespace Kitab;

class Options
{
    /**
     * Constructor to initialize the plugin.
     */
    public function __construct()
    {
        // Add admin menu page
        add_action('admin_menu', array($this, 'addAdminMenu'));

        // Register settings
        add_action('admin_init', array($this, 'registerSettings'));
    }

    // tabs for options page
    public function optionsTabs()
    {
        return $tabs = apply_filters('kitab_options_tabs', array(
            'general' => array(
                'title' => 'General Settings',
                'icon' => 'dashicons-admin-generic',
                'callback' => array(new GeneralSettings(), 'render'),
                'option_name' => 'kitab_general_settings' // new key for option name
            ),

        ));
    }

    /**
     * Add an admin menu page for the "Kitab" plugin.
     */
    public function addAdminMenu()
    {
        add_menu_page(
            'Kitab Plugin Settings',
            'Kitab',
            'manage_options',
            'kitab-plugin',
            array($this, 'renderOptionsPage'),
            'dashicons-book-alt',
            20
        );

        // // Loop through tabs and add them to the menu
        // foreach ($tabs as $tab_slug => $tab_title) {
        //     add_submenu_page(
        //         'kitab-plugin',
        //         $tab_title,
        //         $tab_title,
        //         'manage_options',
        //         'kitab-plugin-' . $tab_slug,
        //         array($this, 'renderOptionsPage')
        //     );
        // }
    }

    // combine all settings fields

    /**
     * Register settings for the plugin options.
     */
    public function registerSettings()
    {
        // loop through tabs and register settings for each tab
        foreach ($this->optionsTabs() as $tab_slug => $tab_data) {
            register_setting('kitab-plugin-settings-' . $tab_slug, $tab_data['option_name']);
        }
    }

    /**
     * Render the options page when the admin menu item is clicked.
     */
    public function renderOptionsPage()
    {
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general'; // Default tab is 'general'
?>
        <div class="wrap kitab-options-wrapper">
            <h2 class="nav-tab-wrapper">
                <?php // loop all tabs in optionsTabs() 
                $tabs = $this->optionsTabs();
                // Loop through the tabs and create a navigation tab for each tab
                foreach ($tabs as $tab_slug => $tab_data) {
                    $tab_url = add_query_arg(array('page' => 'kitab-plugin', 'tab' => $tab_slug), admin_url('admin.php'));
                    $is_current_tab = ($current_tab === $tab_slug) ? 'nav-tab-active' : '';
                    echo '<a href="' . esc_url($tab_url) . '" class="nav-tab ' . esc_attr($is_current_tab) . '">';
                    if (!empty($tab_data['icon'])) {
                        echo '<span class="dashicons ' . esc_attr($tab_data['icon']) . '"></span>';
                    }
                    // echo esc_html__($tab_data['title']) . '</a>';
                    echo esc_html(sprintf(__('%s', 'kitab'), $tab_data['title'])) . '</a>';
                }
                ?>
            </h2>
            <form method="post" action="options.php">
                <?php
                foreach ($tabs as $tab_slug => $tab_data) {
                    if ($current_tab === $tab_slug) {
                        settings_fields('kitab-plugin-settings-' . $tab_slug);
                        do_settings_sections('kitab-plugin-settings-' . $tab_slug);
                        echo '<table class="form-table">';
                        if (isset($tab_data['callback']) && is_callable($tab_data['callback'])) {
                            call_user_func($tab_data['callback']);
                        }
                        echo '</table>';
                    }
                }
                ?>

                <?php submit_button(); ?>
            </form>
        </div>
<?php
    }

    /**
     * Sanitize and validate user input from the options page.
     *
     * @param mixed $input The input value.
     * @return mixed Sanitized and validated input.
     */
    public function sanitizeInput($input)
    {
        // $valid_post_types = get_post_types(array('public' => true), 'names');

        // // Exclude "media" post type from selected post types
        // $input = in_array($input, $valid_post_types) && $input !== 'attachment' ? $input : '';

        return $input;
    }
}
// include all files in options-tabs folder
foreach (glob(plugin_dir_path(__FILE__) . 'options-tabs/*.php') as $file) {
    require_once $file;
}
// Initialize the KitabPlugin class
$kitabPlugin = new Options();
