<?php
/**
 * Quicksilver: purge Cloudflare after Pantheon cache operations.
 *
 * Attached (pantheon.yml) to the AFTER stage of clear_cache, deploy, and
 * sync_code, so it only runs once Pantheon's own cache work has completed —
 * the ordering (Pantheon first, Cloudflare second) is structural.
 *
 * Runs without WordPress. Credentials come from Pantheon Secrets
 * (cloudflare_zone_id / cloudflare_api_token) with env-var fallback.
 */

$purge_environments = ['live'];

$environment = $_ENV['PANTHEON_ENVIRONMENT'] ?? getenv('PANTHEON_ENVIRONMENT');
if (!in_array($environment, $purge_environments, true)) {
    echo "Cloudflare purge skipped: environment '{$environment}' is not in [" . implode(', ', $purge_environments) . "].\n";
    return;
}

$zone_id = null;
$token = null;
if (function_exists('pantheon_get_secret')) {
    $zone_id = pantheon_get_secret('cloudflare_zone_id');
    $token = pantheon_get_secret('cloudflare_api_token');
}
$zone_id = $zone_id ?: getenv('CLOUDFLARE_ZONE_ID');
$token = $token ?: getenv('CLOUDFLARE_API_TOKEN');

if (!$zone_id || !$token) {
    echo "Cloudflare purge skipped: credentials not configured.\n";
    return;
}

$ch = curl_init("https://api.cloudflare.com/client/v4/zones/{$zone_id}/purge_cache");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$token}",
        'Content-Type: application/json',
    ],
    CURLOPT_POSTFIELDS => json_encode(['purge_everything' => true]),
]);
$body = curl_exec($ch);
$code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
curl_close($ch);

if ($code >= 200 && $code < 300) {
    echo "Cloudflare cache purged (purge_everything).\n";
} else {
    // Echo, don't throw — a CF failure must not fail the deploy workflow.
    echo "Cloudflare purge FAILED: HTTP {$code} {$body}\n";
}
