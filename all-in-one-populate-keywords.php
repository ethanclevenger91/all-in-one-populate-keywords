<?php
/*
Plugin Name: All in One SEO Populate Keywords
Plugin URI: http://www.webspecdesign.com
Description: Webspec Design
Version: 1.4.0
Author: Webspec Design
Author URI: http://www.webspecdesign.com
*/

class Ai1_Keywords_Populate {
	private static $option_key = 'ai1_seo_populate_keys';
	private static $exclude_meta_key = '_ai1_seo_populate_keys_exclude';
	private static $valid_post_types = array('post', 'page');
	private static $post_types_option_key = 'ai1_seo_populate_valid_post_types';
	private static $ai1_meta_keywords = '_aioseop_keywords';
	private static $ai1_keys_cap = 'can_populate_keys';
	function __construct() {
		if(is_admin()) {
			add_action('admin_menu', array($this, 'register_menu_page'));
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
			add_action('admin_notices', array($this, 'saved_notice'));
			add_action('init', array($this, 'ai1_populate_keywords_filters'));
			add_action('admin_init', array($this, 'check_for_ai1_seo'));
			add_action('add_meta_boxes', array($this, 'custom_meta_boxes'));
			add_action('save_post', array($this, 'save_post'));
			add_action('admin_init', array($this, 'ai1_seo_populate_keys_settings'));
			add_action('admin_init', array($this, 'assign_capabilities'));
		} else {
			//no actions to register on front end. improve performance hopefully
		}
	}

	function assign_capabilities() {
		$role = get_role('administrator');
		$role->add_cap(self::$ai1_keys_cap);
	}

	function check_for_ai1_seo() {
		if(!is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php')) {
			add_action('admin_notices', array($this, 'please_activate_ai1_base'));
		}
	}

	function please_activate_ai1_base() {
		echo '<div class="error">
	       		<p>You need to activate All in One SEO Pack for All in One SEO Keyword Populate to work</p>
	    	</div>';
	}

	//getters and setters
	function getOptionKey() {
		return self::$option_key;
	}

	function setOptionKey($newkey) {
		self::$option_key = $newkey;
	}

	function getValidPostTypes() {
		//return self::$valid_post_types;
		$valid_post_types = get_option(self::$post_types_option_key);
		if(!is_array($valid_post_types)) {
			$valid_post_types = self::$valid_post_types;
		}
		return $valid_post_types;
	}

	function setValidPostTypes($new_post_types) {
		self::$valid_post_types = $new_post_types;
	}

	//Filter for users to add custom post types to the posts that should be populated with meta keys
	//DEPRECATED
	function ai1_populate_keywords_filters() {
		$this->setValidPostTypes(apply_filters('ai1_seo_populate_keywords_valid_post_types', $this->getValidPostTypes()));
	}

	function register_menu_page() {
		add_submenu_page('tools.php', 'All-in-One SEO Populate Keywords', 'Populate Keywords', self::$ai1_keys_cap, 'ai1_populate_keywords', array($this, 'menu_page_html'));
	}

	function set_keys_option($keys) {
		$option_val='';
		foreach($keys as $keyword) {
			$option_val.=$keyword.'|';
		}
		update_option($this->getOptionKey(), $option_val);
		$this->push_keywords($keys);
	}

	function push_keywords($keys) {
		$posts = get_posts(array(
			'posts_per_page'=>-1,
			'post_type'=>$this->getValidPostTypes(),
			)
		);
		foreach($posts as $post) {
			if(get_post_meta(get_the_ID(), self::$exclude_meta_key, true) == 1) {
				continue;
			}
			$num = rand(5, 9);
			if(count($keys) < $num) {
				$num = count($keys);
			}
			if($num) {
				$use_these_keys = array_rand($keys, $num);
				$use_this_array = array();
				foreach($use_these_keys as $curr) {
					$use_this_array[] = $keys[$curr];
				}
				$keyword_string = implode(', ', $use_this_array);
				update_post_meta($post->ID, self::$ai1_meta_keywords, $keyword_string);
			}
		}
	}

	function new_post_add_keywords($post_id) {
		$keys = explode('|', get_option($this->getOptionKey()));
		array_pop($keys);
		$num = rand(5, 9);
		if(count($keys) < $num) {
			$num = count($keys);
		}
		if($num) { //only if num is greater than 0
			$use_these_keys = array_rand($keys, $num);
			$use_this_array = array();
			foreach($use_these_keys as $curr) {
				$use_this_array[] = $keys[$curr];
			}
			$keyword_string = implode(', ', $use_this_array);
			$_POST['aiosp_keywords'] = $keyword_string; //alter the post variable - when the Ai1SEO save_post fires later, it'll grab this value
		}
	}

	function menu_page_html() {
		if(isset($_POST['keywords'])) {
			$this->set_keys_option($_POST['keywords']);
		}
		echo '<div class="wrap">';
			echo '<h2>All in One SEO Populate Keywords by Webspec Design</h2>';
			echo '<form method="post">';
				echo '<div class="keywords-wrapper">';
					$keys = explode('|', get_option($this->getOptionKey()));
					array_pop($keys);
					foreach($keys as $key) {
						echo '<div class="pop-key-input-wrap">';
							echo '<label class="keyword-label">Keyword:</label>';
							echo '<input class="keyword-input" name="keywords[]" type="text" value="'.$key.'">';
							echo '<button type="button" class="remove-this-key button button-secondary">Remove This Key</button>';
						echo '</div>';
					}
				echo '</div>';
				echo '<div class="bottom-button-wrap">';
					echo '<button type="button" class="button button-secondary" onclick="addInput()">Add Another Keyword</button>';
					echo '<button type="button" class="button button-secondary" onclick="removeAllKeys()">Remove All Keywords</button>';
					echo '<input class="ai1-keywords-submit button button-primary" type="submit" value="Populate Keywords">';
				echo '</div>';
			echo '</form>';
		echo '</div>';
	}

	function saved_notice() {
		if(isset($_POST['keywords'])) {
			echo '<div class="updated">
	       		<p>Keywords Populated!</p>
	    	</div>';
	    }
	}

	function enqueue_admin_scripts($hook) {
		if('tools_page_ai1_populate_keywords' != $hook && 'options-writing.php' != $hook) return;
		wp_enqueue_script('ai1_populate_keywords_script', plugin_dir_url(__FILE__).'scripts/scripts.js');
		wp_enqueue_style('ai1_populate_keywords_styles', plugin_dir_url(__FILE__).'styles/style.css');
	}
	
	function custom_meta_boxes() {
		if(current_user_can(self::$ai1_keys_cap)) {
			foreach($this->getValidPostTypes() as $type) {	
				add_meta_box(
					'ai1_seo_populate_keys_exclude', 'All-in-One SEO Populate Keywords', array($this, 'details_meta_content'), $type, 'side', 'low'
					);
			}
		}
	}

	function details_meta_content($post) {
		wp_nonce_field('ai1_seo_populate_keys_meta', 'ai1_seo_populate_keys_meta_nonce');
		echo '<table>';
			echo '<tr><td><label>Exclude this post? </label></td>';
			echo '<td><input type="checkbox" id="ai1_seo_populate_keys_exclude" name="ai1_seo_populate_keys_exclude"';
			if(get_post_meta($post->ID, self::$exclude_meta_key, true)) {
				echo ' checked';
			}
			echo '></td></tr>';
		echo '</table>';
	}
	function save_post($post_id) {
		if ( ! isset( $_POST['ai1_seo_populate_keys_meta_nonce'] ) )
			return $post_id;
		$nonce = $_POST['ai1_seo_populate_keys_meta_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'ai1_seo_populate_keys_meta' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
		update_post_meta($post_id, self::$exclude_meta_key, isset($_POST['ai1_seo_populate_keys_exclude']));
		//if is not set to exclude and doesn't already have ai1 meta keywords and is in the array of valid post types
		if(get_post_meta($post_id, self::$exclude_meta_key, true) == '' && get_post_meta($post_id, self::$ai1_meta_keywords, true) == '' && in_array($_POST['post_type'], $this->getValidPostTypes())) {
			$this->new_post_add_keywords($post_id);
		}
	}

	function ai1_seo_populate_keys_settings() {
		add_settings_section('ai1_populate_keywords_settings_section', 'All-in-One SEO Populate Keywords', array($this, 'ai1_populate_keys_settings_html'), 'writing');
		add_settings_field(self::$post_types_option_key, 'Valid Post Types', array($this, 'valid_post_types_html'), 'writing', 'ai1_populate_keywords_settings_section');
		register_setting('writing', self::$post_types_option_key);
	}

	function ai1_populate_keys_settings_html() {
		//nothing here for now
	}

	function valid_post_types_html() {
		echo '<p>Post types that will receive keywords</p>';
		$pts = get_post_types(array('public'=>true), 'objects');
		$selected_pts = get_option(self::$post_types_option_key);
		if(!is_array($selected_pts)) $selected_pts = array();
		$selected_pts = array_unique(array_merge($selected_pts, $this->getValidPostTypes()));
		echo '<table id="ai1_pop_keys_settings_table">';
		foreach($pts as $pt) {
			echo '<tr><td><input type="checkbox" id="'.self::$post_types_option_key.'['.$pt->name.']" value="'.$pt->name.'" name="'.self::$post_types_option_key.'[]"';
			if(in_array($pt->name, $selected_pts)) {
				echo ' checked';
			}
			echo '></td>';
			echo '<td><label for="'.self::$post_types_option_key.'['.$pt->name.']">'.$pt->labels->name.'</label></td></tr>';
		}
		echo '</table>';
	}
}

$Ai1_Keywords_Populate = new Ai1_Keywords_Populate();
?>