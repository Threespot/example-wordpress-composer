<?php
/**
 * Development environment overrides.
 *
 * Applies to local dev, Pantheon's `dev` env, and any multidev branch — i.e.
 * anywhere that isn't `test` or `live`.
 */

use Env\Env;
use Roots\WPConfig\Config;

Config::define('WP_DEBUG', Env::get('WP_DEBUG') ?? true);
Config::define('WP_DEBUG_LOG', true);
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('SCRIPT_DEBUG', Env::get('SCRIPT_DEBUG') ?? false);
Config::define('SAVEQUERIES', false);

if (Env::get('IS_LOCAL')) {
    Config::define('IS_LOCAL', true);
}
