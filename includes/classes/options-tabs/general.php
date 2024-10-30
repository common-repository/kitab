<?php

namespace Kitab;

class GeneralSettings
{
    // declare option name
    public $option_name = 'kitab_general_settings';
    public function render()
    {
?>

        <!-- TODO 3 checkboxes options for creating the book post type, publishers and authors taxonomies  -->

        <!-- Move the post types field here -->
        <tr>
            <th scope="row"><?php esc_html_e('Select Post Type', 'kitab'); ?>
                <p class="kitab-tooltip">
                    <span class="description"><?php esc_html_e('Select the post types you want to display the book post meta on.', 'kitab'); ?></span>
                    <span class="dashicons dashicons-editor-help"></span>
                </p>
            </th>

            <td>
                <?php
                $selected_post_type = isset(get_option($this->option_name)['post_type']) ? get_option($this->option_name)['post_type'] : [];
                $post_types = get_post_types(array('public' => true), 'objects');
                ?>
                <?php foreach ($post_types as $post_type) : ?>
                    <?php if ($post_type->name !== 'attachment') : // Exclude "media" post type 
                    ?>
                        <label for="<?php echo esc_attr($this->option_name); ?>_post_type_<?php echo esc_attr($post_type->name); ?>">
                            <input type="checkbox" id="<?php echo esc_attr($this->option_name); ?>_post_type_<?php echo esc_attr($post_type->name); ?>" name="<?php echo esc_attr($this->option_name); ?>[post_type][]" value="<?php echo esc_attr($post_type->name); ?>" <?php checked(in_array($post_type->name, (array)$selected_post_type)); ?>>
                            <?php echo esc_html(sprintf(__('%s', 'kitlab'), $post_type->label)); ?>
                        </label><br>
                    <?php endif; ?>
                <?php endforeach; ?>
            </td>
        </tr>
<?php
    }
}
