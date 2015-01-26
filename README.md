All in One SEO Populate Keywords - by Webspec Design (v1.4.0)
==================

__New in v1.4.0:__ Capability added to view keywords page and exclude metabox. By default, this is only available to admin users. Use the following to show it to other roles:

```$role = get_role('editor');
$role->add_cap('can_populate_keys');```

Use
------------------

- Find the settings under "Tools"
- Pressing "Populate" from here will push a random selection of keywords entered (between 5-9) to the post_meta of all qualifying post types (posts and pages by default) that do not have the "Exclude" option checked. The keywords are also saved to the database at this time
- When saving or creating a post, if "Exclude" is not checked and the All in One Meta Keywords box is empty, keywords will be automatically populated from those entered in the settings

Adding Custom Post Types
------------------

By default, this plugin affects the post types 'Posts' and 'Pages' __Deprecated__: Use the `ai1_seo_populate_keywords_valid_post_types` filter to add fields, like so:

	//DEPRECATED
     add_filter('ai1_seo_populate_keywords_valid_post_types', 'my_types_function');

     function my_fields_function($types) {
          $types[] = 'my_post_type';
          return $types;
     }

__New in v1.3.0:__ Under Settings > Writing, you can use checkboxes to pick valid custom post types. If this has never been saved, it will have checked posts, pages, and any post types you added via the above filter in versions < 1.3.0. Upon saving, those values will be saved to the database. When determining valid post types, the plugin will merge the database values and the filter values.


TODO:
------------------

- AJAX Save/Populate