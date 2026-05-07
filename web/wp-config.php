<?php
/**
 * WordPress configuration entry point.
 *
 * All project config lives in /config:
 *   config/application.php              shared settings (DB, URLs, salts, defines)
 *   config/environments/development.php local + Pantheon dev/multidev
 *   config/environments/staging.php     Pantheon test
 *   config/environments/production.php  Pantheon live
 *
 * web/wp/wp-load.php sets ABSPATH = web/wp/ before requiring this file, so the
 * fallback below only runs if wp-config.php is loaded outside the normal chain.
 */

require_once dirname(__DIR__) . '/config/application.php';

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/wp/');
}

require_once ABSPATH . 'wp-settings.php';
