<?php
// Event type taxonomy
// Docs: https://github.com/johnbillion/extended-cpts/wiki/Registering-taxonomies
add_action('init', function () {
  register_extended_taxonomy('event_type', [
    'event',
  ], [
    'exclusive' => true,
    'has_archive' => true,
    'hierarchical' => false,
    'meta_box' => 'dropdown',// 'radio', 'dropdown',  'simple' (supports multiple)
    'public' => true,// applies to “publicly_queryable”, “show_ui”, and “show_in_nav_menus”
    'query_var' => true,
    'required' => false,
    'show_admin_column' => true,
    'show_in_nav_menus' => false,// hide from “Add menu items” sidebar
    'show_in_quick_edit' => true,
    'show_in_rest' => true,
  ], [
    'singular' => 'Event Type',
    'plural' => 'Event Types',
    'slug' => 'event-type',
  ]);
});
