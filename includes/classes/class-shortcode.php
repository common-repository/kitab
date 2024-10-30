<?php
namespace Kitab;

class Shortcode {
    public function __construct() {
        add_shortcode( 'kitab', array( $this, 'display_book_fields_shortcode' ) );
    }

     // Shortcode to display book fields and post thumbnail
     public function display_book_fields_shortcode($atts)
     {
         // Get the post ID from the shortcode attributes or use the current post ID
         $post_id = isset($atts['id']) ? $atts['id'] : get_the_ID();
 
         // Get the book fields HTML
         $layout = new Layout();
         $fields_html = $layout->get_book_fields_html($post_id);
 
         // Return the book fields HTML
         return $fields_html;
     }
}
// usage of shortcode in the frontend
// [kitab id="123"]



// Initialize the Shortcode class
$shortcode = new Shortcode();