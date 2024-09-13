<?php
// Event custom post type definition
//
// Plugin docs: https://github.com/johnbillion/extended-cpts/wiki/Registering-Post-Types
// WP docs: https://developer.wordpress.org/reference/functions/register_post_type/
//
// NOTE: We neeed to flush rewrite rules after making updates.
//       The simplest method is to go to Settings->Permalinks and click “Save Changes”
//       https://developer.wordpress.org/reference/functions/flush_rewrite_rules/
add_action('init', function() {
  register_extended_post_type('event', [
    // Dashicon: https://developer.wordpress.org/resource/dashicons/
    'menu_icon' => 'dashicons-calendar-alt',
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
      'category',
      'post_tag',
      'event_type',
      'topic',
    ],
    'has_archive' => true,
    'hierarchical' => false,
    'publicly_queryable' => true,
    'show_in_nav_menus' => false,// hide from “Add menu items” sidebar
    'show_in_rest' => true,
    'enter_title_here' => 'Event title',
    # Add taxonomy filter in the admin list view
    'admin_filters' => [
      'event_type' => [
        'taxonomy' => 'event_type',
      ],
    ],
  ], [
    'singular' => 'Event',
    'plural' => 'Events',
    'slug' => 'events'
  ]);
});
