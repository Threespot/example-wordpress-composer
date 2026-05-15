<?php
// News custom post type
//
// Plugin docs: https://github.com/johnbillion/extended-cpts/wiki/Registering-Post-Types
// WP docs: https://developer.wordpress.org/reference/functions/register_post_type/
//
// NOTE: We neeed to flush rewrite rules after making updates.
//       The simplest method is to go to Settings->Permalinks and click “Save Changes”
//       https://developer.wordpress.org/reference/functions/flush_rewrite_rules/
add_action('init', function () {
  register_extended_post_type('news', [
    'menu_icon' => 'dashicons-media-document',
    'supports' => [
      'author',
      'comments',
      'custom-fields',
      'editor',
      'excerpt',
      'revisions',
      'thumbnail',
      'title'
    ],
    'taxonomies' => [
      'critical_concern_topic',
      'news_type',
      'region',
      'target_audience',
    ],
    'has_archive' => true,
    'hierarchical' => false,
    'publicly_queryable' => true,
    'show_in_nav_menus' => false,
    'show_in_rest' => true,
    'enter_title_here' => 'Add title',
    # Add taxonomy filter in the admin list view
    'admin_filters' => [
      'news_type' => [
        'taxonomy' => 'news_type',
      ],
      'region' => [
        'taxonomy' => 'region',
      ],
    ],
  ], [
    'singular' => 'News',
    'plural' => 'News',
    'slug' => 'news',
  ]);
});
