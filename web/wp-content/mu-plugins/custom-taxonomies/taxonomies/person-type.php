<?php
// Custom taxonomy for people (e.g. staff, board of directors)
// Docs: https://github.com/johnbillion/extended-cpts/wiki/Registering-taxonomies
add_action('init', function () {
  register_extended_taxonomy('person_type', [
    'person',
  ], [
    'checked_ontop' => true,// useful when there are many terms
    'exclusive' => true,
    'has_archive' => true,
    'hierarchical' => false,
    'meta_box' => 'dropdown',// 'radio', 'dropdown',  'simple' (supports multiple)
    'public' => true,// applies to “publicly_queryable”, “show_ui”, and “show_in_nav_menus”
    'query_var' => true,
    'required' => true,
    'show_admin_column' => true,
    'show_in_nav_menus' => false,// hide from “Add menu items” sidebar
    'show_in_quick_edit' => true,
    'show_in_rest' => true,
  ], [
    'singular' => 'Person Type',
    'plural' => 'Person Types',
    'slug' => 'person-type',
  ]);
});
