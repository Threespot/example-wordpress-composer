<?php
/**
 * Register custom query vars
 *
 * @link https://developer.wordpress.org/reference/hooks/query_vars/
 * @link https://www.smashingmagazine.com/2016/03/advanced-wordpress-search-with-wp_query/
 */
add_filter('query_vars', function($vars) {
  // FIXME: List custom taxonomies below that should support query vars
  // Note: We also need to update the list in /custom-taxonomies/query-vars/actions.php
  $taxonomies = [
    'event_type',
    'person_type',
    'topic',
  ];

  foreach ($taxonomies as $taxonomy) {
    $vars[] = $taxonomy;
  }

  return $vars;
});
