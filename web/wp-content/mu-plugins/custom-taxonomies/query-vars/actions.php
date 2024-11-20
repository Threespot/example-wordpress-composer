<?php

/**
 * Build a custom query based on several conditions
 * The pre_get_posts action gives developers access to the $query object by reference
 * any changes you make to $query are made directly to the original object - no return value is requested
 *
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts
 *
 */
add_action('pre_get_posts', function ($query)
{
    if (
      // Don’t alter the admin queries
      is_admin() ||
      // Only alter the main query
      !$query->is_main_query() ||
      // Only on search and archive pages
      !($query->is_search() || $query->is_archive())
    ) {
        return;
    }

    $tax_query = [];

    // List custom taxonomies that should support query vars
    // Note: We also need to update the list in /custom-taxonomies/query-vars/filters.php
    $taxonomies = [
      'event_type',
      'person_type',
      'topic',
    ];

    // Add custom taxonomy term support
    foreach ($taxonomies as $taxonomy) {
      $query_var = get_query_var($taxonomy);
      if (!empty($query_var)) {
          array_push($tax_query, [
              'taxonomy' => $taxonomy,
              'field' => 'slug',
              'terms' => $query_var,
              'operator' => 'IN',// 'IN', 'NOT IN', 'AND', 'EXISTS' and 'NOT EXISTS'
              'include_children' => false,
          ]);
      }
    }

    if (count($tax_query) > 1) {
        $tax_query['relation'] = 'AND';
    }

    if (count($tax_query) > 0) {
        $query->set('tax_query', $tax_query);
    }

    // Order events by date
    if ($query->get('post_type') == 'event' && empty($query->get('orderby'))) {
        $query->set('order', 'DESC');
        $query->set('orderby', 'date');
    }

}, 1);
