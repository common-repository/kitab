<?php

namespace Kitab;

class LayoutSettings
{
    // declare option name
    public $option_name = 'kitab_layout_settings';
    // constructor
    public function __construct()
    {
        // add new tab to options page
        add_filter('kitab_options_tabs', function ($tabs) {
            $tabs['layout'] = array(
                'title' => 'Layout',
                'icon' => 'dashicons-layout',
                'callback' => array($this, 'render'),
                'option_name' => 'kitab_layout_settings' // new key for option name
            );
            return $tabs;
        });
    }

    public function render()
    {
?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php esc_html_e('Select Layout Position', 'kitab'); ?></th>
                    <td>
                        <?php
                        $selected_layout_position = isset(get_option($this->option_name)['layout_position']) ? get_option($this->option_name)['layout_position'] : '';
                        ?>
                        <select name="<?php echo esc_attr($this->option_name); ?>[layout_position]">
                            <option value="above" <?php selected('above', $selected_layout_position); ?>><?php esc_html_e('Above Content', 'kitab'); ?></option>
                            <option value="below" <?php selected('below', $selected_layout_position); ?>><?php esc_html_e('Below Content', 'kitab'); ?></option>
                            <option value="both" <?php selected('both', $selected_layout_position); ?>><?php esc_html_e('Above and Below', 'kitab'); ?></option>
                            <option value="manually" <?php selected('manually', $selected_layout_position); ?>><?php esc_html_e('Manually', 'kitab'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Display as', 'kitab'); ?></th>
                    <td>
                        <?php
                        $selected_display_as = isset(get_option($this->option_name)['display_as']) ? get_option($this->option_name)['display_as'] : '';
                        ?>
                        <select name="<?php echo esc_attr($this->option_name); ?>[display_as]">
                            <option value="dl" <?php selected('dl', $selected_display_as); ?>><?php esc_html_e('Data List', 'kitab'); ?></option>
                            <option value="table" <?php selected('table', $selected_display_as); ?>><?php esc_html_e('Table', 'kitab'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Show Thumbnail', 'kitab'); ?></th>
                    <td>
                        <?php
                        $selected_show_thumbnail = isset(get_option($this->option_name)['show_thumbnail']) ? get_option($this->option_name)['show_thumbnail'] : '';
                        ?>
                        <label>
                            <input type="checkbox" name="<?php echo esc_attr($this->option_name); ?>[show_thumbnail]" value="yes" <?php checked('yes', $selected_show_thumbnail); ?>>
                            <?php esc_html_e('Yes', 'kitab'); ?>
                        </label>
                    </td>
                </tr>
                <!-- checkbox for showing font awesome icon, ''must have font awoesome library on theme or plugin -->
                <tr>
                    <th scope="row"><?php esc_html_e('Show Font Awesome Icons', 'kitab'); ?>
                        <p class="kitab-tooltip">
                            <span class="description"><?php esc_html_e('Must have font awesome library on theme or plugin.', 'kitab'); ?></span>
                            <span class="dashicons dashicons-editor-help"></span>
                        </p>
                    </th>
                    <td>
                        <?php
                        $selected_show_font_awesome = isset(get_option($this->option_name)['show_font_awesome']) ? get_option($this->option_name)['show_font_awesome'] : '';
                        ?>
                        <label>
                            <input type="checkbox" name="<?php echo esc_attr($this->option_name); ?>[show_font_awesome]" value="yes" <?php checked('yes', $selected_show_font_awesome); ?>>
                            <?php esc_html_e('Yes', 'kitab'); ?>
                        </label>
                    </td>
                <tr>
                    <th scope="row"><?php esc_html_e('Disable Downloads in Copyrighted Claims Books', 'kitab'); ?></th>
                    <td>
                        <?php
                        $selected_disable_downloads = isset(get_option($this->option_name)['disable_downloads']) ? get_option($this->option_name)['disable_downloads'] : '';
                        ?>
                        <label>
                            <input type="checkbox" name="<?php echo esc_attr($this->option_name); ?>[disable_downloads]" value="yes" <?php checked('yes', $selected_disable_downloads); ?>>
                            <?php esc_html_e('Yes, Disable', 'kitab'); ?>
                        </label>
                    </td>
                </tr>
                <!-- add a new checkbox field to select which fields want to show in the frontend, based on get_book_fields array -->
                <tr>
                    <th scope="row"><?php esc_html_e('Select Fields to Show', 'kitab'); ?></th>
                    <td>
                        <?php
                        $selected_show_fields = isset(get_option($this->option_name)['show_fields']) ? get_option($this->option_name)['show_fields'] : [];
                        // get book fields from PostMeta class
                        $PostMeta = new PostMeta();
                        $book_fields = $PostMeta->get_book_fields();
                        ?>
                        <?php foreach ($book_fields as $key => $field) : ?>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr($this->option_name); ?>[show_fields][]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $selected_show_fields)); ?>>
                                <?php echo esc_html(sprintf(__('%s', 'kitab'), $field['label'])); ?>
                            </label>
                            <br>
                        <?php endforeach; ?>
                    </td>
                </tr>

            </tbody>
        </table>
<?php
    }
}
new LayoutSettings();
