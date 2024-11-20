<?php
// Event custom post type definition
//
// Plugin docs: https://github.com/johnbillion/extended-cpts/wiki/Registering-Post-Types
// WP docs: https://developer.wordpress.org/reference/functions/register_post_type/
//
// NOTE: We neeed to flush rewrite rules after making updates.
//       The simplest method is to go to Settings->Permalinks and click “Save Changes”
//       https://developer.wordpress.org/reference/functions/flush_rewrite_rules/

// Example of how to display the template name in a custom admin column
// https://github.com/johnbillion/extended-cpts/wiki/Admin-columns#custom-function
// function get_template_name() {
//   global $post;
//   $current_template = get_page_template_slug($post);
//   $templates = wp_get_theme()->get_page_templates($post, 'news');
//   $template_name = $templates[$current_template] ?? 'Default';
//   echo $template_name;
// };

add_action('init', function() {
  register_extended_post_type('news', [
    // Dashicon: https://developer.wordpress.org/resource/dashicons/
    'menu_icon' => 'dashicons-media-document',
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
      'topic',
    ],
    'has_archive' => true,
    'hierarchical' => false,
    'publicly_queryable' => true,
    'show_in_nav_menus' => false,// hide from “Add menu items” sidebar
    'show_in_rest' => true,
    'enter_title_here' => 'Title',
    # Add taxonomy filter in the admin list view
    'admin_filters' => [
      'event_type' => [
        'taxonomy' => 'news_type',
      ],
    ],
    // 'admin_cols' => [
    //   'template' => [
    //     'title' => 'Template',
    //     'function' => 'get_template_name',
    //   ],
    //   'last_modified' => [
    //   	'title' => 'Last Modified',
    //   	'post_field' => 'post_modified',
    //     'date_format' => 'Y/m/d \a\t g:i\&\n\b\s\p\;a',
    //   ],
    //   'author' => [
    //   	'title' => 'Author',
    //   	'meta_key' => 'author',
    //   ],
    // ]
  ], [
    'singular' => 'News',
    'plural' => 'News',
    'slug' => 'news'
  ]);
});
