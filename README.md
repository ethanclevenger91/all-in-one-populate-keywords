All in One SEO Populate Keywords - by Webspec Design
==================

Use
------------------

- Find the settings under "Tools"
- Pressing "Populate" from here will push a random selection of keywords entered (between 5-9) to the post_meta of all qualifying post types (posts and pages by default) that do not have the "Exclude" option checked. The keywords are also saved to the database at this time
- When saving or creating a post, if "Exclude" is not checked and the All in One Meta Keywords box is empty, keywords will be automatically populated from those entered in the settings

Adding Custom Post Types
------------------

By default, this plugin affects the post types 'Posts' and 'Pages' Use the `ai1_seo_populate_keywords_valid_post_types` filter to add fields, like so:


     add_filter('ai1_seo_populate_keywords_valid_post_types', 'my_types_function');

     function my_fields_function($types) {
          $types[] = 'my_post_type';
          return $types;
     }

TODO:
------------------

-AJAX Save/Populate
-Save option for post types