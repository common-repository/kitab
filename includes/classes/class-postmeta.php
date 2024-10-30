<?php

namespace Kitab;

class PostMeta
{



    public function __construct()
    {
        // Add custom fields to the book post type
        add_action('add_meta_boxes', array($this, 'add_book_meta_boxes'));
        add_action('save_post', array($this, 'save_book_meta'));

        // Enqueue JavaScript and CSS
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }



    private function get_allowed_post_types()
    {
        $selected_post_types = get_option('kitab_post_type');

        // تحقق من أن $selected_post_types هو من نوع مصفوفة
        if (is_array($selected_post_types)) {
            $post_types = array();
            foreach ($selected_post_types as $post_type) {
                $post_types[] = $post_type;
            }
            return $post_types;
        } else {
            // يمكنك إضافة رمز أو إجراء إضافي حسب حاجتك هنا في حالة عدم توافق النوع
            return array(); // أو أي قيمة أخرى تعتبر مناسبة في هذه الحالة
        }
    }

    // Add custom meta boxes for book details
    public function add_book_meta_boxes()
    {
        add_meta_box('kitab_book_details', esc_html__('Book Details', 'kitab'), array($this, 'render_book_meta_box'), $this->get_allowed_post_types(), 'normal', 'high');
        // another meta box for adding a checkbox to the post for enable/disable ads
        // DISABLED FOR NOW - Added from temeplate
        // add_meta_box('kitab_book_ads', esc_html__('Ads', 'kitab'), array($this, 'render_book_ads_box'), $this->get_allowed_post_types(), 'side', 'high');
    }

    public function render_book_ads_box()
    {
        // render the checkbox
        global $post;
        $disable_ads = boolval(get_post_meta($post->ID, '_kitab_disable_ads', true));
?>
        <label for="kitab_disable_ads">
            <input type="checkbox" name="kitab_disable_ads" id="kitab_disable_ads" value="1" <?php checked($disable_ads, true); ?>>
            <?php esc_html_e('Disable ads', 'kitab'); ?>
        </label>
<?php
    }




    // Enqueue JavaScript and CSS
    public function enqueue_assets($hook)
    {
        // Check if we're on the post editor screen for the 'book' post type
        if ($hook === 'post-new.php' || $hook === 'post.php') {
            global $post_type;
            if ($this->is_post_type_allowed($post_type)) {
                // Enqueue your JavaScript and CSS files here
                wp_enqueue_script('kitab-post-meta-script', KITAB_PLUGIN_URL . 'assets/admin/js/kitab-post-meta.js', array('jquery'), KITAB_VERSION, true);
                wp_enqueue_style('kitab-post-meta-style', KITAB_PLUGIN_URL . 'assets/admin/css/kitab-post-meta.css', array(), KITAB_VERSION);
            }
        }
    }

    // Render the custom meta box for book details
    public function render_book_meta_box($post)
    {
        //  check if the post type is in allowed post types array from kitab_post_type checkbox option
        if (!$this->is_post_type_allowed($post->ID)) {
            return;
        }
        // Add an nonce field so we can check for it later.
        wp_nonce_field('kitab_book_details_nonce', 'kitab_book_details_nonce');

        echo '<div class="kitab-fields">';
        // Output HTML fields using a loop
        foreach ($this->get_book_fields() as $field_key => $field_details) {
            $value = get_post_meta($post->ID, "_{$field_key}", true);
            $label = $field_details['label'];
            $type = $field_details['type'];
            $description = isset($field_details['description']) ? $field_details['description'] : '';
            $dashicon = isset($field_details['dashicon']) ? $field_details['dashicon'] : '';

            echo '<div class="form-field">';
            // Use Dashicons class for icons based on the field key
            echo "<label for='" . esc_attr($field_key) . "'>";
            // Use Dashicons class for icons based on the field key
            if (!empty($dashicon)) {
                echo "<span class='dashicons " . esc_attr($dashicon) . "'></span>";
            }
            echo esc_html($label) . "</label>";
            if ($type === 'text') {
                echo "<input type='text' id='" . esc_attr($field_key) . "' name='" . esc_attr($field_key) . "' value='" . esc_attr($value) . "' />";
            } elseif ($type === 'url') {
                echo "<input type='url' id='" . esc_attr($field_key) . "' name='" . esc_attr($field_key) . "' value='" . esc_url($value) . "' />";
            }

            echo '</div>'; // .form-field
        }
        echo '</div>'; // .kitab-fields
    }

    // Define book fields in a method
    public function get_book_fields()
    {

        $fields = array(
            'kitab_book_name' => array(
                'label' => esc_html__('Book Name', 'kitab'),
                'type' => 'text',
                'dashicon' => 'dashicons-book',
                'emoji' => '📚',
                'fontawesome' => 'fas fa-book',
                'description' => esc_html__('Enter the name of the book', 'kitab'),
                'priority' => 10
            ),
            // book author
            'kitab_book_author' => array(
                'label' => esc_html__('Author', 'kitab'),
                'type' => 'text',
                'dashicon' => 'dashicons-admin-users',
                'emoji' => '👨‍🏫',
                'fontawesome' => 'fas fa-user',
                'description' => esc_html__('Enter the name of the author', 'kitab'),
                'priority' => 11
            ),
            'kitab_pages' => array(
                'label' => esc_html__('Pages', 'kitab'),
                'type' => 'text',
                'dashicon' => 'dashicons-admin-links',
                'emoji' => '📄',
                'fontawesome' => 'fas fa-file',
                'description' => esc_html__('Enter the number of pages in the book', 'kitab'),
                'priority' => 20
            ),
            'kitab_edition' => array(
                'label' => esc_html__('Edition', 'kitab'),
                'type' => 'text',
                'dashicon' => 'dashicons-clipboard',
                'emoji' => '📑',
                'fontawesome' => 'fas fa-clipboard',
                'description' => esc_html__('Enter the edition of the book', 'kitab'),
                'priority' => 30
            ),
            'kitab_isbn' => array(
                'label' => esc_html__('ISBN', 'kitab'),
                'type' => 'text',
                'dashicon' => 'dashicons-tag',
                'emoji' => '🏷️',
                'fontawesome' => 'fas fa-tag',
                'description' => esc_html__('Enter the ISBN of the book', 'kitab'),
                'priority' => 40
            ),
            'kitab_copyright_year' => array(
                'label' => esc_html__('Copyright Year', 'kitab'),
                'type' => 'text',
                'dashicon' => 'dashicons-calendar',
                'emoji' => '📅',
                'fontawesome' => 'fas fa-calendar',
                'description' => esc_html__('Enter the year of copyright', 'kitab'),
                'priority' => 50
            ),
            'kitab_copyright_holder' => array(
                'label' => esc_html__('Copyright Holder', 'kitab'),
                'type' => 'text',
                'dashicon' => 'dashicons-businessman',
                'emoji' => '👨‍💼',
                'fontawesome' => 'fas fa-user-tie',
                'description' => esc_html__('Enter the name of the copyright holder', 'kitab'),
                'priority' => 60
            ),
            'kitab_pdf_link' => array(
                'label' => esc_html__('PDF Download', 'kitab'),
                'type' => 'url',
                'dashicon' => 'dashicons-download',
                'emoji' => '📥',
                'fontawesome' => 'fas fa-download',
                'description' => esc_html__('Enter the URL of the PDF download link', 'kitab'),
                'priority' => 70
            ),
            'kitab_epub_link' => array(
                'label' => esc_html__('EPUB Download', 'kitab'),
                'type' => 'url',
                'dashicon' => 'dashicons-media-archive',
                'emoji' => '📦',
                'fontawesome' => 'fas fa-archive',
                'description' => esc_html__('Enter the URL of the EPUB download link', 'kitab'),
                'priority' => 80
            ),
            'kitab_buy_link' => array(
                'label' => esc_html__('Buy Link', 'kitab'),
                'type' => 'url',
                'dashicon' => 'dashicons-cart',
                'emoji' => '🛒',
                'fontawesome' => 'fas fa-shopping-cart',
                'description' => esc_html__('Enter the URL of the buy link', 'kitab'),
                'priority' => 90
            ),
        );




        // use filter to add more fields
        $fields = apply_filters('kitab_book_fields', $fields);
        // sort fields by priority
        uasort($fields, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
        return $fields;
    }

    /**
     * Checks if the given post type is allowed based on the selected post types in the plugin settings.
     *
     * @param int|string $post_id The post ID or post type to check.
     * @return bool True if the post type is allowed, false otherwise.
     */
    public function is_post_type_allowed($post_id)
    {
        $selected_post_types = isset(get_option('kitab_general_settings')['post_type']) ? get_option('kitab_general_settings')['post_type'] : '';

        // Check if $selected_post_types is an array
        if (!is_array($selected_post_types)) {
            return false;
        }

        if (is_int($post_id)) {
            // $post_id is a post ID
            $post_type = get_post_type($post_id);
            return in_array($post_type, $selected_post_types);
        } else {
            // $post_id is a post type
            return in_array($post_id, $selected_post_types);
        }
    }


    public function save_book_meta($post_id)
    {

        // Check if the current user is authorized
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        // Check if the nonce field was set and if the nonce is valid

        if (!isset($_POST['kitab_book_details_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['kitab_book_details_nonce'])), 'kitab_book_details_nonce')) {
            return;
        }


        // Sanitize and save each field
        $fields = $this->get_book_fields();
        // $fields[] = 'kitab_disable_ads';

        foreach ($fields as $field_name => $field) {
            if (isset($_POST[$field_name])) {
                // If the field is a checkbox, update the post meta with a boolean value
                $value = ($field['type'] === 'checkbox') ? (bool)$_POST[$field_name] : sanitize_text_field($_POST[$field_name]);

                // For other field types, further sanitize the value
                switch ($field['type']) {
                    case 'url':
                        $value = esc_url_raw($value);
                        break;
                    case 'email':
                        $value = sanitize_email($value);
                        break;
                    case 'number':
                        $value = intval($value);
                        break;
                }

                update_post_meta($post_id, "_{$field_name}", $value);
            }
        }
    }
}
// Initialize the PostMeta class
new PostMeta();
