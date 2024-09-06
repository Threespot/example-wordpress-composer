<?php
/**
 * Plugin Name: Custom Taxonomies
 * Description: Implements all custom taxonomies
 * Version: 1.0.0
 * Author: Threespot
 * Author URI: https://www.threespot.com/
 * License: MIT License
 */

// Gather all files from the post-types directory
$files = glob(__DIR__ . '/taxonomies/*.php');
$files = array_merge($files, glob(__DIR__ . '/query-vars/*.php'));

if (function_exists('register_extended_taxonomy')) {
    // Require each file
    foreach ($files as $file) {
        require_once $file;
    }
}
