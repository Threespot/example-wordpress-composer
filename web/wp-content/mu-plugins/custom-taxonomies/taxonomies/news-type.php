<?php
// News Type taxonomy
// Docs: https://github.com/johnbillion/extended-cpts/wiki/Registering-taxonomies
add_action('init', function () {
  register_extended_taxonomy('news_type', [
    'news',
  ], [
    'exclusive' => true,
    'has_archive' => true,
    'hierarchical' => false,
    'meta_box' => false,// hide default metabox since we’re using an ACF field to make it required
    'public' => true,// applies to “publicly_queryable”, “show_ui”, and “show_in_nav_menus”
    'query_var' => true,// also auto-enables ?news_type=… filtering on archives
    'required' => true,
    'show_admin_column' => true,
    'show_in_nav_menus' => false,// hide from “Add menu items” sidebar
    'show_in_quick_edit' => true,
    'show_in_rest' => true,
  ], [
    'singular' => 'News Type',
    'plural' => 'News Types',
    'slug' => 'news-type',
  ]);
});
