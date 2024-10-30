<?php

namespace Kitab;

class Layout
{
    public $post_meta = null;
    private $option_name = 'kitab_layout_settings';
    private $selected_layout_position;
    private $selected_show_fields;
    private $selected_show_thumbnail;
    private $selected_display_as;
    private $show_font_awesome;



    /**
     * Constructor to initialize the plugin.
     */
    public function __construct()
    {
        // Display book fields below content on the front end
        add_filter('the_content', array($this, 'display_book_fields'));
        // get is_post_type_allowed from PostMeta class with Kitab namespace
        $this->post_meta = new PostMeta();
        // get options
        $this->get_options();

        // Enqueue public assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
    }

    private function get_options()
    {
        $this->selected_layout_position = isset(get_option($this->option_name)['layout_position']) ? get_option($this->option_name)['layout_position'] : '';
        $this->selected_show_fields = isset(get_option($this->option_name)['show_fields']) ? get_option($this->option_name)['show_fields'] : '';
        $this->selected_show_thumbnail = isset(get_option($this->option_name)['show_thumbnail']);
        $this->selected_display_as = isset(get_option($this->option_name)['display_as']) ? get_option($this->option_name)['display_as'] : '';
        $this->show_font_awesome = isset(get_option($this->option_name)['show_font_awesome']);
    }

    public function get_book_fields_table_html($post_id)
    {
        $book_name = get_post_meta($post_id, "_kitab_book_name", true);
        $html = '';
        // Check if the post type is 'book'
        if ($this->post_meta->is_post_type_allowed($post_id) && !empty($book_name)) {
            // show_thumbnail option checkbox is enabled

            // filter before book info
            $html .= apply_filters('kitab_before_book_info', '');

            // wrapper div
            $html .= '<div class="kitab-book">';

            $html .= '<table class="kitab-book-fields">';
            // if selected_show_fields is array
            if (!is_array($this->selected_show_fields)) {
                return;
            }
            // Output HTML fields using a loop
            foreach ($this->post_meta->get_book_fields() as $field_key => $field_details) {
                // Check if the field is in selected_show_fields
                if (!in_array($field_key, $this->selected_show_fields)) {
                    continue;
                }
                $value = get_post_meta($post_id, "_{$field_key}", true);
                $label = $field_details['label'];
                $emoji = $field_details['emoji'];
                $font_awesome = $field_details['fontawesome'];
                // if font_awesome is enabled and show_font_awesome option, $icon = $font_awesome i with class else $icon = $emoji
                $icon = $this->show_font_awesome && $font_awesome ? "<i class='{$font_awesome}'></i>" : $emoji;


                // Allow changing the value of the field using a dynamic filter based on field name
                $value = apply_filters("kitab_field_{$field_key}_value", $value);


                // Check if the field has a value before displaying it
                if (!empty($value)) {
                    $html .= "<tr class='{$field_key}'><th>{$icon} {$label}</th><td>";

                    // Display URL fields as clickable links
                    // if not has filter
                    if ($field_details['type'] === 'url') {
                        switch ($field_key) {
                            case 'kitab_pdf_link':
                                // add filter to change pdf link
                                // $html .= apply_filters('kitab_pdf_link', "<a href='{$value}' target='_blank' rel='noopener noreferrer'>Télécharger PDF</a>");
                                $html .= "<a href='{$value}' target='_blank' rel='noopener noreferrer'>Télécharger PDF</a>";
                                break;
                            case 'kitab_epub_link':
                                $html .= "<a href='{$value}' target='_blank' rel='noopener noreferrer'>Télécharger Epub</a>";
                                break;
                            case 'kitab_buy_link':
                                $html .= "<a href='{$value}' target='_blank' rel='noopener noreferrer'>Buy {$book_name}</a>";
                                break;

                            default:
                                # code...
                                break;
                        }
                        // add filter to change value
                        // $html .= apply_filters("kitab_field_{$field_key}_html", esc_html__($value));
                    } else {
                        $html .= esc_html(sprintf(__('%s', 'kitab'), $value));
                    }

                    $html .= '</td></tr>';
                }
            }
            // filter to add new fields to book info - just in frontend
            $html .= apply_filters('kitab_add_new_table_field', '');

            $html .= '</table>';

            // Get the post thumbnail (book cover)
            if (has_post_thumbnail($post_id) && $this->selected_show_thumbnail) {
                $thumbnail = get_the_post_thumbnail($post_id, 'medium', array('class' => 'kitab-book-thumbnail'));
                $html .= $thumbnail;
            }

            $html .= '</div>';
            // filter after book info
            $html .= apply_filters('kitab_after_book_info', '');
        }

        return $html;
    }

    public function get_book_fields_html($post_id)
    {
        $book_name = get_post_meta($post_id, "_kitab_book_name", true);
        $html = '';
        // Check if the post type is 'book'
        if ($this->post_meta->is_post_type_allowed($post_id) && !empty($book_name)) {

            // filter before book info
            $html .= apply_filters('kitab_before_book_info', '');
            // wrapper div
            $html .= '<div class="kitab-book">';
            $html .= '<dl class="kitab-book-fields">';
            // if selected_show_fields is array
            if (!is_array($this->selected_show_fields)) {
                return;
            }
            // Output HTML fields using a loop
            foreach ($this->post_meta->get_book_fields() as $field_key => $field_details) {
                // Check if the field is in selected_show_fields
                if (!in_array($field_key, $this->selected_show_fields)) {
                    continue;
                }
                $value = get_post_meta($post_id, "_{$field_key}", true);
                $label = $field_details['label'];
                $emoji = $field_details['emoji'];
                $font_awesome = $field_details['fontawesome'];
                // if font_awesome is enabled and show_font_awesome option, $icon = $font_awesome i with class else $icon = $emoji
                $icon = $this->show_font_awesome && $font_awesome ? "<i class='{$font_awesome}'></i>" : $emoji;


                // Allow changing the value of the field using a dynamic filter based on field name
                $value = apply_filters("kitab_field_{$field_key}_value", $value);


                // Check if the field has a value before displaying it
                if (!empty($value)) {
                    $html .= "<div class='{$field_key}'>";
                    $html .= "<dt>{$icon} {$label}</dt><dd>";

                    // Display URL fields as clickable links
                    if ($field_details['type'] === 'url') {
                        switch ($field_key) {
                            case 'kitab_pdf_link':
                                $html .= "<a href='{$value}' target='_blank' rel='noopener noreferrer'>Télécharger PDF</a>";
                                break;
                            case 'kitab_epub_link':
                                $html .= "<a href='{$value}' target='_blank' rel='noopener noreferrer'>Télécharger Epub</a>";
                                break;
                            case 'kitab_buy_link':
                                $html .= "<a href='{$value}' target='_blank' rel='noopener noreferrer'>Buy {$book_name}</a>";
                                break;

                            default:
                                # code...
                                break;
                        }
                    } else {
                        // TODO, esc_html is not working with html tags that returned with filter.
                        $html .= $value;
                    }

                    $html .= '</dd>';
                    $html .= '</div>';
                }
            }

            $html .= '</dl>';

            // Get the post thumbnail (book cover)
            if (has_post_thumbnail($post_id) && $this->selected_show_thumbnail) {
                $thumbnail = get_the_post_thumbnail($post_id, 'medium', array('class' => 'kitab-book-thumbnail'));
                $html .= $thumbnail;
            }
            $html .= '</div>';
            // filter after book info
            $html .= apply_filters('kitab_after_book_info', '');
        }
        return $html;
    }
    // Display book fields and post thumbnail below content on the front end
    public function display_book_fields($content)
    {
        // return if admin
        if (is_admin()) {
            return $content;
        }
        global $post;
        $book_name = get_post_meta($post->ID, "_kitab_book_name", true);
        // Check if the post type is 'book'
        if ($this->post_meta->is_post_type_allowed($post->ID) && !empty($book_name)) {
            // Get the book fields HTML
            // show table or dl based on display_as option
            $fields_html = $this->selected_display_as === 'table' ? $this->get_book_fields_table_html($post->ID) : $this->get_book_fields_html($post->ID);
            // Add the wrapper div to the content
            // $content .= $ads . $fields_html . $ads;

            switch ($this->selected_layout_position) {
                case 'above':
                    return $fields_html . $content;
                    break;
                case 'below':
                    return $content . $fields_html;
                    break;
                case 'both':
                    return $fields_html . $content . $fields_html;
                    break;
                case 'manually':
                    // TODO Removed Ads ton another plugin
                    return $content;
                    break;
                default:
                    break;
            }
        }
        // TODO Removed Ads ton another plugin
        return $content;
    }

    public function enqueue_public_assets($hook)
    {
        // check if we're on the single post screen for is_post_type_allowed
        if (is_single() && $this->post_meta->is_post_type_allowed(get_the_ID())) {
            // Enqueue the style with the version constant
            wp_enqueue_style('kitab-custom-style', KITAB_PLUGIN_URL . 'assets/public/css/kitab-fields.css', array(), KITAB_VERSION);
        }
    }
}


// use Kitab\PostMeta;

// Initialize the PostMeta class
$post_meta = new Layout();
