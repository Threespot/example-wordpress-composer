<?php
/**
 * Plugin Name: Disable Polylang Language Cookie
 * Description: Prevents Polylang from setting the pll_language cookie. With directory-based
 *              URLs the cookie is unnecessary, and its Set-Cookie header prevents Pantheon’s
 *              Varnish edge cache from caching responses.
 */

define('PLL_COOKIE', false);
