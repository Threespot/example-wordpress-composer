<?php
// People custom post type definition (e.g. staff and board)
//
// Plugin docs: https://github.com/johnbillion/extended-cpts/wiki/Registering-Post-Types
// WP docs: https://developer.wordpress.org/reference/functions/register_post_type/
//
// NOTE: We neeed to flush rewrite rules after making updates.
//       The simplest method is to go to Settings->Permalinks and click “Save Changes”
//       https://developer.wordpress.org/reference/functions/flush_rewrite_rules/
add_action('init', function() {
  register_extended_post_type('person', [
    // Dashicon: https://developer.wordpress.org/resource/dashicons/
    'menu_icon' => 'dashicons-id-alt',
    // Optional: Custom SVG icon
    // 'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode('<svg>…</svg>'),
    'supports' => [
      'author',
      'custom-fields',
      'editor',
      'excerpt',
      'revisions',
      'thumbnail',
      'title'
    ],
    # FIXME: Add applicable custom taxonomies here
    'taxonomies' => [
      'person_type',
    ],
    'has_archive' => true,
    'hierarchical' => false,
    'publicly_queryable' => true,
    'show_in_nav_menus' => false,// hide from “Add menu items” sidebar
    'show_in_rest' => true,
    'enter_title_here' => 'Enter full name',
    # Add taxonomy filter in the admin list view
    'admin_filters' => [
      'person_type' => [
        'taxonomy' => 'person_type',
      ],
    ],
  ], [
    'singular' => 'Person',
    'plural' => 'People',
    'slug' => 'people',
  ]);
});
