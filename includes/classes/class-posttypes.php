<?php

namespace Kitab;

class CustomPostType
{
    public function __construct()
    {
        // Register custom post type for books
        add_action('init', array($this, 'register_book_post_type'));
    }

    // Register custom post type for books
    public function register_book_post_type()
    {
        // Define labels and arguments for the custom post type
        $labels = array(
            'name' => esc_html__('Books', 'kitab'),
            'singular_name' => esc_html__('Book', 'kitab'),
            // Add more labels as needed
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            // Add more arguments as needed
        );

        register_post_type('book', $args);
    }
}



// Initialize the CustomPostType class
$custom_post_type = new CustomPostType();
