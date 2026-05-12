---
name: Custom Taxonomy Spec
about: WordPress Custom Taxonomy Specification
title: ''
labels: Taxonomy
assignees: ''
---

## Functionality

- Briefly describe any functional requirements not captured by individual fields

## Settings

| label | value | description |
| ----- | ----- | ----------- |
| object_types | [post, page, attachment, revision, nav_maenu_item, custom_css, customize_changeset, custom_post_type] | supported object types |
| public | true | |
| show_ui | true | |
| description | "" | Include a description of the taxonomy. |
| hierarchical | false | Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. |

<details><summary>Additional Wordpress Taxonomy Settings</summary>

| label | value | description |
| ----- | ----- | ----------- |
| label | | A plural descriptive name for the taxonomy marked for translation. |
| labels | [array()](https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments) | An array of labels for this taxonomy. By default tag labels are used for non-hierarchical types and category labels for hierarchical ones.| public | true | Whether a taxonomy is intended for use publicly either via the admin interface or by front-end users. |
| labels | [array()](https://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments) | An array of labels for this taxonomy. By default tag labels are used for non-hierarchical types and category labels for hierarchical ones.
| publicly_queryable | $public | Whether the taxonomy is publicly queryable. |
| show_ui | $public | Whether to generate a default UI for managing this taxonomy. |
| show_in_menu | $show_ui | Where to show the taxonomy in the admin menu. show_ui must be true. |
| show_in_nav_menus | $public | true makes this taxonomy available for selection in navigation menus. |
| show_in_rest |  | Whether to include the taxonomy in the REST API. You will need to set this to true in order to use the taxonomy in your gutenberg metablock. |
| rest_base | $taxonomy | To change the base url of REST API route. |
| rest_controller_class |  | REST API Controller class name. |
| show_tagcloud | $show_ui | Whether to allow the Tag Cloud widget to use this taxonomy. |
| show_in_quick_edit | $show_ui | Whether to show the taxonomy in the quick/bulk edit panel. |
| meta_box_cb | null | Provide a callback function name for the meta box display. |
| show_admin_column | false | Whether to allow automatic creation of taxonomy columns on associated post-types table. |
| update_count_callback |  | A function name that will be called when the count of an associated $object_type, such as post, is updated. Works much like a hook. |
| query_var | $taxonomy | False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". True is not seen as a valid entry and will result in 404 issues. |
| rewrite | true | Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks |
| capabilities | array() | An array of the capabilities for this taxonomy. |
| sort | | Whether this taxonomy should remember the order in which terms are added to objects. |
| _builtin | false | Whether this taxonomy is a native or "built-in" taxonomy. |


</details>

### Extended Taxonomy Settings

| label | value | description |
| ----- | ----- | ----------- |
| admin_cols | | columns to show in the admin listing view |
| allow_hierarchy | false | |
| checked_ontop | null | |
| dashboard_glance | true | Show this taxonomy in the 'At a Glance' dashboard widget |
| exclusive | false | |
| meta_box | [null, simple, radio, dropdown ] | 'radio' and 'dropdown' just allow exclusive choices (will overwrite the set choise). Simple has exclusive and multi options |
| required | false | |
 
### Custom Fields

These are not common for Taxonomies

_\* indicates required_

| Field          | [Type](https://www.advancedcustomfields.com/resources/#field-types) | notes |
| --- | --- | --- |
| \*custom_field | text | descibe any field value requirements such as: min/max character lengths, media requiremnts, relationship with other content |

## Related Issue(s)

- include a link to any related issue(s).
