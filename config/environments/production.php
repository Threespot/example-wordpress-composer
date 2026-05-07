<?php
/**
 * Production environment overrides — Pantheon's `live` env.
 */

use Env\Env;
use Roots\WPConfig\Config;

// Hide deprecation notices emitted before WP boots. WordPress controls its
// own error_reporting after wp-settings.php.
error_reporting(E_ALL ^ E_DEPRECATED);

Config::define('WP_DEBUG', false);
Config::define('DISALLOW_FILE_MODS', true);

/**
 * Redirect to PRIMARY_DOMAIN if it's set (via Pantheon dashboard env vars or
 * terminus secrets) and the request didn't arrive on it. Skips on CLI so
 * wp-cli, cron, and Quicksilver hooks don't try to redirect.
 */
$primary_domain = Env::get('PRIMARY_DOMAIN');
if (
    $primary_domain
    && PHP_SAPI !== 'cli'
    && isset($_SERVER['HTTP_HOST'])
    && $_SERVER['HTTP_HOST'] !== $primary_domain
) {
    if (extension_loaded('newrelic')) {
        // phpcs:ignore PHPCompatibility.Extensions.RemovedExtensions.newrelicRemoved
        newrelic_name_transaction('redirect');
    }
    header('HTTP/1.0 301 Moved Permanently');
    header('Location: https://' . $primary_domain . $_SERVER['REQUEST_URI']);
    exit;
}
