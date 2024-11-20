<?php
/**
 * Plugin Name: Custom Post Types
 * Description: Implements all custom post types
 * Version: 1.0.0
 * Author: Threespot
 * Author URI: https://www.threespot.com/
 * License: MIT License
 */

// Gather all files from the post-types directory
$files = glob(__DIR__ . '/post-types/*.php');

if (function_exists('register_extended_post_type')) {
    // Require each file
    foreach ($files as $file) {
        require_once $file;
    }
}
