<?php

require dirname(__DIR__, 2) . '/vendor/autoload.php';

// Satisfy the ABSPATH guard at the top of each mu-plugin file.
if (!defined('ABSPATH')) {
    define('ABSPATH', sys_get_temp_dir() . '/wordpress/');
}

// Require the classes under test here. mu-plugin code is not autoloaded at
// runtime (WordPress loads it directly), so unit tests must load it
// explicitly. Guard with file_exists() so the bootstrap keeps working while
// files are added incrementally, e.g.:
//
// $inc = dirname(__DIR__, 2) . '/web/wp-content/mu-plugins/my-plugin/inc/';
// foreach (['ClassUnderTest.php'] as $file) {
//     if (file_exists($inc . $file)) {
//         require_once $inc . $file;
//     }
// }
