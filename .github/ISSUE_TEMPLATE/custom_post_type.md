---
name: Custom Post Type Spec
about: WordPress Custom Post Type Functional Specification
title: ''
labels: CPT
assignees: ''
---

## Functionality

- Briefly describe any functional requirements not captured by individual fields

#### Settings

<details><summary>CPT Settings</summary>

| label | | description |
| --- | --- | --- |
| description | | perhaps describe what your custom post type is used for? |
| labels | | assign labels built above |
| menu_icon | dashicons-post | image URL or Dashicon class name to use for icon. Custom image should be 20px by 20 pixel. [docs](https://developer.wordpress.org/resource/dashicons/#redo) |
| supports | editor, excerpt, thumbnail, revisions, author, comments, trackbacks, page-attributes, post-formats, custom-fields | add support for various available post editor features. Set to false to explicitly remove all features. [docs](https://developer.wordpress.org/reference/functions/add_post_type_support/#description) |
| taxonomies| | Comma separated list of supported taxonomies (by slug) |
| public| true | Whether or not posts of this type should be shown in the admin UI and is publicly queryable. |
| show_ui| true | Whether or not to generate a default UI for managing this post type. |
| show_in_menu| true | Whether to show this post type in the admin menu |
| show_in_admin_bar| true | Whether to make this post type available in the WordPress admin toolbar |
| show_in_nav_menus| true | Whether or not this post type is available for selection in navigation menus. |
| can_export| true | Can this post type be exported |
| has_archive| true | Whether or not the post type will have a post type archive URL. |
| hierarchical| false | Whether or not the post type can have parent-child relationships. |
| exclude_from_search| true | Whether or not to exclude posts with this post type from front end search results. |
| show_in_rest| true | Whether or not to show this post type data in the WP REST API. |
| publicly_queryable| true | Whether or not queries can be performed on the front end as part of parse_request() |
| capability_type| post | The post type to use for checking read, edit, and delete capabilities. A comma-separated second value can be used for plural version. |
| rewrite| false | Custom rewrite slug for use in URLs (overrides the post type slug). 'custom_rewrite_slug' => '' |

</details>

<details><summary>Extended CPT Settings</summary>

| label         | value | description                                 |
| ------------- | ----- | ------------------------------------------- |
| archive       | true  |                                             |
| admin_cols    |       | columns to show in the admin listing view   |
| admin_filters |       | filters available in the admin listing view |
| show_in_feed  | true  |                                             |

**Label Overrides**

| label    | value |
| -------- | ----- |
| singular | Post  |
| plural   | Posts |
| slug     | post  |

</details>

#### Custom Fields

_\* indicates required_

| Field | [Type](https://www.advancedcustomfields.com/resources/#field-types) | Notes |
| --- | --- | --- |
| \*custom_field | text | descibe any field value requirements such as: min/max character lengths, media requiremnts, relationship with other content |

## Wireframe

<details><summary>Details</summary>

[Wireframe](link_to_invision_app)

<img width="400" src="https://www.satyr.io/400x16:9"/>

</details>

## Design

[Comp](link_to_invision_app)

<img width="400" src="https://www.satyr.io/400x16:9"/>

### Related Issue(s)

- include a link to any related issue(s).
