<?php
/**
 * Plugin Name: Threespot WP Base Config (Loader)
 * Description: Loads the threespot/wp-base-config package's MU-plugin modules.
 * Version: 0.1.0
 * Author: Threespot
 *
 * Composer's autoloader (loaded by config/application.php) already registered
 * the package's helper/public-API function files before WP runs mu-plugins.
 * This file only fires the MU-plugin module registrations.
 *
 * Written as a copy rather than a symlink because PHP resolves __DIR__ through
 * symlinks to the real path, which would break the package's internal
 * dirname(__DIR__, 3) path resolution.
 */

if (!defined('ABSPATH')) {
    exit;
}

$threespot_wp_base_config_bootstrap = dirname(__DIR__, 3) . '/vendor/threespot/wp-base-config/bootstrap.php';

if (file_exists($threespot_wp_base_config_bootstrap)) {
    require_once $threespot_wp_base_config_bootstrap;
}

unset($threespot_wp_base_config_bootstrap);
