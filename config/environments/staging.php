<?php
/**
 * Staging environment overrides — Pantheon's `test` env.
 */

use Roots\WPConfig\Config;

// Hide deprecation notices emitted before WP boots (e.g. from autoloaded
// libraries). WordPress controls its own error_reporting after wp-settings.php.
error_reporting(E_ALL ^ E_DEPRECATED);

Config::define('WP_DEBUG', false);
Config::define('DISALLOW_FILE_MODS', true);
