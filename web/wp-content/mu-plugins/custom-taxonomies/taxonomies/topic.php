<?php
// Imapct Area taxonomy
// Docs: https://github.com/johnbillion/extended-cpts/wiki/Registering-taxonomies
add_action('init', function () {
  register_extended_taxonomy('topic', [
    'post',
    'page',
    'event',
    'news'
  ], [
    'exclusive' => false,
    'hierarchical' => false,
    'meta_box' => 'simple',// 'radio', 'dropdown',  'simple' (supports multiple)
    'public' => true,// applies to “publicly_queryable”, “show_ui”, and “show_in_nav_menus”
    'query_var' => true,
    'required' => false,
    'show_admin_column' => true,
    'show_in_quick_edit' => true,
    'show_in_rest' => true,
  ], [
    'singular' => 'Topic',
    'plural' => 'Topics',
    'slug' => 'topic',
  ]);
});
