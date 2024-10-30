<?php

namespace Kitab;


class Taxonomies
{
    public function __construct()
    {
        // Register taxonomies for authors and publishers
        add_action('init', array($this, 'register_taxonomies'));
    }

    // Register taxonomies for authors and publishers
    public function register_taxonomies()
    {
        // Register taxonomy for authors
        $labels = array(
            'name' => esc_html__('Authors', 'kitab'),
            'singular_name' => esc_html__('Author', 'kitab'),
            // Add more labels as needed
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'show_in_rest' => true,
            // Add more arguments as needed
        );

        register_taxonomy('authors', 'post', $args);
        // register_taxonomy for multiple post types from plugin settings
        // $post_types = get_option('kitab_post_types');
        // if (!empty($post_types)) {
        //     foreach ($post_types as $post_type) {
        //         register_taxonomy('authors', $post_type, $args);
        //     }
        // }


        // Register taxonomy for publishers
        $labels = array(
            'name' => esc_html__('Publishers', 'kitab'),
            'singular_name' => esc_html__('Publisher', 'kitab'),
            // Add more labels as needed
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'show_in_rest' => true,
            // Add more arguments as needed
        );

        register_taxonomy('publishers', 'post', $args);
    }
}

// Initialize the Taxonomies class
$taxonomies = new Taxonomies();
