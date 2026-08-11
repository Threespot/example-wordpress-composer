<?php
/**
 * Quicksilver: flush the object cache after a database clone.
 *
 * Attached (pantheon.yml) to the AFTER stage of clone_database, which fires on
 * the DESTINATION environment. Pantheon replaces MySQL but leaves the target's
 * Redis untouched, so every cached option keeps its pre-clone value. Anything
 * that then read-modify-writes one of those options persists the stale value
 * over the freshly cloned row — WP_Roles is the archetype, since it reads the
 * whole roles array, mutates one capability, and writes it back whole. That is
 * how the TablePress and Events Calendar capabilities were silently reverted on
 * live after a dev clone (2026-08-05).
 *
 * Deliberately NOT gated by environment — unlike the Cloudflare purge, any
 * environment can be a clone destination.
 *
 * Runs without WordPress. Connection details come from Pantheon's CACHE_*
 * environment variables, the same ones config/application.php feeds to Object
 * Cache Pro.
 */

$environment = $_ENV['PANTHEON_ENVIRONMENT'] ?? getenv('PANTHEON_ENVIRONMENT');
$from = $_POST['from_environment'] ?? 'unknown';

$host = $_ENV['CACHE_HOST'] ?? getenv('CACHE_HOST');
$port = (int) ($_ENV['CACHE_PORT'] ?? getenv('CACHE_PORT') ?: 6379);
$password = $_ENV['CACHE_PASSWORD'] ?? getenv('CACHE_PASSWORD');

if (!class_exists('Redis')) {
    echo "Object cache flush SKIPPED: the phpredis extension is not available.\n";
    return;
}

if (!$host) {
    echo "Object cache flush SKIPPED: CACHE_HOST is not set.\n";
    return;
}

try {
    $redis = new Redis();
    $redis->connect($host, $port, 2.0);
    if ($password) {
        $redis->auth($password);
    }
    // flushAll, not flushDb: after a clone every cached value is suspect, and
    // the environment's Redis instance is dedicated to this site.
    $redis->flushAll();
    echo "Object cache flushed on '{$environment}' after database clone from '{$from}'.\n";
} catch (Throwable $e) {
    // Echo, don't throw — a flush failure must not fail the clone workflow.
    // It must be loud, though: the site is in the stale-cache state until this
    // is run by hand, and the damage only shows up later.
    echo "Object cache flush FAILED on '{$environment}': " . $e->getMessage() . "\n";
    echo "ACTION REQUIRED before using the site: redis-cli -h {$host} -p {$port} -a <CACHE_PASSWORD> flushall\n";
}
