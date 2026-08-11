<?php
/**
 * Project configuration.
 *
 * Loaded by web/wp-config.php. Per-environment overrides live in
 * config/environments/{development,staging,production}.php and are included
 * near the bottom of this file (after defaults, before Config::apply()).
 */

use Env\Env;
use Roots\WPConfig\Config;

$root_dir = dirname(__DIR__);

require_once $root_dir . '/vendor/autoload.php';

Env::$options = Env::USE_ENV_ARRAY | Env::CONVERT_BOOL | Env::CONVERT_NULL;

/**
 * On Pantheon, environment variables are injected automatically. Locally,
 * load them from .env (phpdotenv v5 API).
 */
if (!isset($_ENV['PANTHEON_ENVIRONMENT']) && file_exists($root_dir . '/.env')) {
    Dotenv\Dotenv::createImmutable($root_dir)
        ->load();
    Dotenv\Dotenv::createImmutable($root_dir)
        ->required(['DB_NAME', 'DB_USER', 'DB_HOST'])
        ->notEmpty();
}

/**
 * Map Pantheon's env name → a logical WP_ENV the config files key off of.
 *   live      → production  (locked down, no debug)
 *   test      → staging     (locked down, no debug)
 *   anything else (dev, multidev branch, local) → development
 */
$pantheon_env = $_ENV['PANTHEON_ENVIRONMENT'] ?? null;
$wp_env = match ($pantheon_env) {
    'live' => 'production',
    'test' => 'staging',
    default => 'development',
};
Config::define('WP_ENV', $wp_env);

/**
 * Database
 */
Config::define('DB_NAME', Env::get('DB_NAME'));
Config::define('DB_USER', Env::get('DB_USER'));
Config::define('DB_PASSWORD', Env::get('DB_PASSWORD') ?: '');

$db_host = Env::get('DB_HOST');
$db_port = Env::get('DB_PORT');
if ($db_port && !str_contains((string) $db_host, ':')) {
    $db_host .= ':' . $db_port;
}
Config::define('DB_HOST', $db_host);
Config::define('DB_CHARSET', 'utf8');
Config::define('DB_COLLATE', '');

$table_prefix = Env::get('DB_PREFIX') ?: 'wp_';

/**
 * URLs
 *
 * Pantheon's load balancer sets HTTP_USER_AGENT_HTTPS=ON for HTTPS requests;
 * we mirror that so generated URLs stay on the right scheme.
 */
$is_https = (($_SERVER['HTTP_USER_AGENT_HTTPS'] ?? '') === 'ON')
    || (($_SERVER['HTTPS'] ?? '') === 'on');
$scheme = $is_https ? 'https' : 'http';

if (isset($_SERVER['HTTP_HOST'])) {
    $home = $scheme . '://' . $_SERVER['HTTP_HOST'];
} else {
    // CLI / cron / wp-cli — fall back to WP_HOME from env.
    $home = rtrim((string) Env::get('WP_HOME'), '/');
}
Config::define('WP_HOME', $home);
Config::define('WP_SITEURL', $home . '/wp');

/**
 * Authentication keys and salts
 *
 * Pantheon manages these in the dashboard. Locally, generate fresh values at
 * https://api.wordpress.org/secret-key/1.1/salt/ and add them to .env.
 */
foreach ([
    'AUTH_KEY',
    'SECURE_AUTH_KEY',
    'LOGGED_IN_KEY',
    'NONCE_KEY',
    'AUTH_SALT',
    'SECURE_AUTH_SALT',
    'LOGGED_IN_SALT',
    'NONCE_SALT',
] as $salt) {
    Config::define($salt, Env::get($salt) ?: 'put your unique phrase here');
}

/**
 * Core hardening (applies to every environment)
 */
Config::define('DISALLOW_FILE_EDIT', true);
Config::define('FORCE_SSL_ADMIN', true);
Config::define('WP_POST_REVISIONS', 3);
Config::define('ACF_PRO_LICENSE', Env::get('ACF_PRO_LICENSE'));

/**
 * Custom content directory (we keep WP core in /wp, content in /wp-content).
 */
Config::define('WP_CONTENT_DIR', $root_dir . '/web/wp-content');
Config::define('WP_CONTENT_URL', $home . '/wp-content');

/**
 * Pantheon writes its temp files into the binding's /tmp.
 */
if (defined('PANTHEON_BINDING')) {
    Config::define('WP_TEMP_DIR', sprintf('/srv/bindings/%s/tmp', PANTHEON_BINDING));
}

/**
 * Admin Columns Pro plugin license
 */
Config::define('ACP_LICENCE', Env::get('ACP_LICENCE'));

/**
 * SearchWP plugin license
 */
Config::define('SEARCHWP_LICENSE_KEY', Env::get('SEARCHWP_LICENSE_KEY'));

/**
 * Object Cache Pro config
 * https://docs.pantheon.io/object-cache/wordpress
 * This configuration applies to both local and Pantheon environments
 */
$ocp_settings = [
	'token' => Env::get('OCP_LICENSE') ?: null,
	'host' => Env::get('CACHE_HOST') ?: '127.0.0.1',
	'port' => Env::get('CACHE_PORT') ?: 6379,
	'database' => Env::get('CACHE_DB') ?: 0,
	'password' => Env::get('CACHE_PASSWORD') ?: null,
	'maxttl' => 86400,
	'timeout' => 2.0,
	'read_timeout' => 2.0,
	'retry_interval' => 100,
	'split_alloptions' => true,
	'prefetch' => true,
	'debug' => false,
	'save_commands' => false,
	'analytics' => [
		'enabled' => true,
		'persist' => false,
		'retention' => 3600, // 1 hour
		'footnote' => true,
	],
	'prefix' => "ocppantheon", // This prefix can be changed. Setting a prefix helps avoid conflict when switching from other plugins like wp-redis.
	'serializer' => 'igbinary',
	'compression' => 'zstd',
	'async_flush' => true,
	'strict' => true,
];

// Load Object Cache Pro token from Pantheon secrets.json if available.
if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
	$secrets_file = rtrim(Env::get('HOME') ?: ($_SERVER['HOME'] ?? ''), '/') . '/files/private/secrets.json';
	if ($secrets_file && file_exists($secrets_file)) {
		$secrets = json_decode(file_get_contents($secrets_file), true);
		if (is_array($secrets) && !empty($secrets['OCP_LICENSE'])) {
			$ocp_settings['token'] = $secrets['OCP_LICENSE'];
		}
	}
}

// Object Cache Pro config for lando
// https://docs.pantheon.io/object-cache/wordpress#local-configuration-with-lando
if (isset($_ENV['LANDO']) && $_ENV['LANDO'] === 'ON') {
	$ocp_settings['serializer'] = 'php';
	$ocp_settings['compression'] = 'none';

	// Try to get token from auth.json (one directory up from web/)
	$auth_json_path = $root_dir . '/auth.json';

	if (file_exists($auth_json_path)) {
		$auth_json = json_decode(file_get_contents($auth_json_path), true); // true = return as array
		if (isset($auth_json['http-basic']['objectcache.pro']['password'])) {
			$ocp_settings['token'] = $auth_json['http-basic']['objectcache.pro']['password'];
		}
	}
}

Config::define('WP_REDIS_CONFIG', $ocp_settings);

/**
 * Disable Object Cache Pro when Redis isn't provisioned, e.g. on brand-new
 * Pantheon sites before `terminus redis:enable` has been run. Without this,
 * the object-cache.php drop-in tries to reach the 127.0.0.1 fallback above
 * and every request (including `wp core install`) fails with
 * "Error establishing a Redis connection".
 */
if (isset($_ENV['PANTHEON_ENVIRONMENT']) && !Env::get('CACHE_HOST')) {
	Config::define('WP_REDIS_DISABLED', true);
}


/**
 * Per-environment overrides.
 */
$env_config = __DIR__ . "/environments/{$wp_env}.php";
if (file_exists($env_config)) {
    require_once $env_config;
}

Config::apply();
