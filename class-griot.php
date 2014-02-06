<?php

/**
 * The main class for the plugin.
 * 
 * Sets up WordPress structures for working with records and exposes data for
 * Angular through wp_localize_script. 
 *
 * @since 0.0.1
 */
class Griot{

	/**
	 * Register Object custom post type.
	 *
	 * @since 0.0.1
	 */
	function register_object_cpt() {

		$labels = array(
			'name'                => _x( 'Objects', 'Post Type General Name', 'griot' ),
			'singular_name'       => _x( 'Object', 'Post Type Singular Name', 'griot' ),
			'menu_name'           => _x( 'Objects', 'Post Type Menu Name', 'griot' ),
			'parent_item_colon'   => __( 'Parent Object:', 'griot' ),
			'all_items'           => __( 'All Objects', 'griot' ),
			'view_item'           => __( 'View Object', 'griot' ),
			'add_new_item'        => __( 'Add New Object', 'griot' ),
			'add_new'             => __( 'Add New', 'griot' ),
			'edit_item'           => __( 'Edit Object', 'griot' ),
			'update_item'         => __( 'Update Object', 'griot' ),
			'search_items'        => __( 'Search Objects', 'griot' ),
			'not_found'           => __( 'Not found', 'griot' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'griot' ),
		);

		$args = array(
			'label'               => __( 'object', 'griot' ),
			'description'         => __( 'Represents a primary record in the application.', 'griot' ),
			'labels'              => $labels,
			'supports'            => array( 'title', ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		register_post_type( 'object', $args );

	}

	/**
	 * Register Story custom post type.
	 *
	 * @since 0.0.1
	 */
	function register_story_cpt() {

		$labels = array(
			'name'                => _x( 'Stories', 'Post Type General Name', 'griot' ),
			'singular_name'       => _x( 'Story', 'Post Type Singular Name', 'griot' ),
			'menu_name'           => _x( 'Stories', 'Post Type Menu Name', 'griot' ),
			'parent_item_colon'   => __( 'Parent Story:', 'griot' ),
			'all_items'           => __( 'All Stories', 'griot' ),
			'view_item'           => __( 'View Story', 'griot' ),
			'add_new_item'        => __( 'Add New Story', 'griot' ),
			'add_new'             => __( 'Add New', 'griot' ),
			'edit_item'           => __( 'Edit Story', 'griot' ),
			'update_item'         => __( 'Update Story', 'griot' ),
			'search_items'        => __( 'Search Stories', 'griot' ),
			'not_found'           => __( 'Not found', 'griot' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'griot' ),
		);

		$args = array(
			'label'               => __( 'story', 'griot' ),
			'description'         => __( 'Represents secondary media related to a primary record in the application.', 'griot' ),
			'labels'              => $labels,
			'supports'            => array( 'title', ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		register_post_type( 'story', $args );

	}


	/**
	 * Check to see if directory of stories and objects has been saved in options
	 * and builds it if necessary.
	 *
	 * @since 0.0.1
	 */
	function check_directory() {

		if( ! get_option( 'griot_directory' ) ) {

			$this->rebuild_directory();

		}

	}


	/**
	 * Build a directory of stories and objects for use in connection fields.
	 *
	 * @since 0.0.1
	 */
	function rebuild_directory() {

		global $wpdb;

		$objects_query = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'object' AND post_status = 'publish'";

		$objects = $wpdb->get_results( $objects_query, ARRAY_A );

		$stories_query = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'story' AND post_status = 'publish'";

		$stories = $wpdb->get_results( $stories_query, ARRAY_A );

		$directory = array(
			'object' => $objects,
			'story' => $stories,
		);

		update_option( 'griot_directory', $directory );

	}


	/**
	 * Enqueue vendor and plugin scripts.
	 *
	 * @since 0.0.1
	 */
	function enqueue_scripts_and_styles() { 

		// Return early if we're not on an object or story edit page.
		$screen = get_current_screen();

		$ok_screen_ids = array( 'object', 'story', );

		if( ! in_array( $screen->id, $ok_screen_ids ) ) {

			return;

		}

		// Swiper styles
		wp_enqueue_style(
			'swiper',
			plugins_url( 'css/vendor/idangerous.swiper.css', __FILE__ ),
			false
		);

		// Our styles
		wp_enqueue_style(
			'griot',
			plugins_url( 'css/griot.css', __FILE__ ),
			false
		);

		// CKEditor
		wp_enqueue_script(
			'ckeditor',
			plugins_url( 'js/vendor/ckeditor/ckeditor.js', __FILE__ ),
			false
		);
		wp_enqueue_script(
			'ckeditor_adapter',
			plugins_url( 'js/vendor/ckeditor/adapters/jquery.js', __FILE__ ),
			array( 'ckeditor' ),
			null,
			true
		);

		// Swiper core
		wp_enqueue_script(
			'swiper',
			plugins_url( 'js/vendor/idangerous.swiper.min.js', __FILE__ ),
			false,
			null,
			true
		);

		// Angular core
		wp_enqueue_script( 
			'angular', 
			plugins_url( 'js/vendor/angular.min.js', __FILE__ ), 
			false, 
			null,
			false
		);

		// Our controller
		wp_enqueue_script(
			'griot',
			plugins_url( 'js/griot.js', __FILE__ ),
			false,
			null,
			true
		);

		// Print application data
		$this->print_data( $screen->id );

	}

	/**
	 * Expose record data and template URL to application
	 * 
	 * @since 0.0.1
	 */
	function print_data( $screen_id ) {

		// Grab $post variable
		global $post;

		// Construct data for application
		$griotData = array(

			'recordType'  => $screen_id,
			'templateUrl' => $this->templates[ $screen_id ],
			'title'       => $post->post_title,
			'data'        => $post->post_content,
			'directory'		=> get_option( 'griot_directory' ),

		);

		// Print to page
		wp_localize_script(
			'griot',
			'griotData',
			$griotData
		);

	}


	/**
	 * Register metabox for connections
	 *
	 * @since 0.0.1
	 */
	function register_connections_metabox() {

		// Return early if we're not on an object page.
		$screen = get_current_screen();

		if( $screen->id != 'object' ) {

			return;

		}

		// Add meta box
		add_meta_box(
			'griot-connections',
			 __( 'Related Stories', 'griot' ),
			array( $this, 'connections_metabox_template' ),
			'object',
			'side'
		);

	}


	/**
	 * Callback that prints Angular template to connections metabox.
	 *
	 * @since 0.0.1
	 */
	function connections_metabox_template() {

		echo "<field name='connections' type='connection' />";

	}


	/**
	 * Set up plugin
	 * 
	 * @since 0.0.1
	 * @param bool $load_default If true, load default fields from plugin.
	 */
	function __construct( $templates ) {

		// Register templates
		$this->templates = $templates;

		// Register Object and Story post types
		add_action( 'init', array( $this, 'register_object_cpt' ) );
		add_action( 'init', array( $this, 'register_story_cpt' ) );

		// Generate record directory in options if it doesn't exist
		$this->check_directory();

		// Rebuild directory when post structure changes
		add_action( 'save_post', array( $this, 'rebuild_directory' ) );
		add_action( 'trash_post', array( $this, 'rebuild_directory' ) );
		add_action( 'delete_post', array( $this, 'rebuild_directory' ) );

		// If this page is managed by the plugin, enqueue scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );

		// Add connections metabox
		add_action( 'add_meta_boxes', array( $this, 'register_connections_metabox' ) );
 
	}

}