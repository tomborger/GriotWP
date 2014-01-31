<?php

/**
 * The main class for the plugin.
 * 
 * Sets up WordPress structures for working with records and exposes data for
 * Angular through wp_localize_script. 
 *
 * @since 0.0.1
 */
class MIA_Author{

	/**
	 * Register Object custom post type.
	 *
	 * @since 0.0.1
	 */
	function register_object_cpt() {

		$labels = array(
			'name'                => _x( 'Objects', 'Post Type General Name', 'mia-author' ),
			'singular_name'       => _x( 'Object', 'Post Type Singular Name', 'mia-author' ),
			'menu_name'           => _x( 'Objects', 'Post Type Menu Name', 'mia-author' ),
			'parent_item_colon'   => __( 'Parent Object:', 'mia-author' ),
			'all_items'           => __( 'All Objects', 'mia-author' ),
			'view_item'           => __( 'View Object', 'mia-author' ),
			'add_new_item'        => __( 'Add New Object', 'mia-author' ),
			'add_new'             => __( 'Add New', 'mia-author' ),
			'edit_item'           => __( 'Edit Object', 'mia-author' ),
			'update_item'         => __( 'Update Object', 'mia-author' ),
			'search_items'        => __( 'Search Objects', 'mia-author' ),
			'not_found'           => __( 'Not found', 'mia-author' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'mia-author' ),
		);

		$args = array(
			'label'               => __( 'object', 'mia-author' ),
			'description'         => __( 'Represents a primary record in the application.', 'mia-author' ),
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
			'name'                => _x( 'Stories', 'Post Type General Name', 'mia-author' ),
			'singular_name'       => _x( 'Story', 'Post Type Singular Name', 'mia-author' ),
			'menu_name'           => _x( 'Stories', 'Post Type Menu Name', 'mia-author' ),
			'parent_item_colon'   => __( 'Parent Story:', 'mia-author' ),
			'all_items'           => __( 'All Stories', 'mia-author' ),
			'view_item'           => __( 'View Story', 'mia-author' ),
			'add_new_item'        => __( 'Add New Story', 'mia-author' ),
			'add_new'             => __( 'Add New', 'mia-author' ),
			'edit_item'           => __( 'Edit Story', 'mia-author' ),
			'update_item'         => __( 'Update Story', 'mia-author' ),
			'search_items'        => __( 'Search Stories', 'mia-author' ),
			'not_found'           => __( 'Not found', 'mia-author' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'mia-author' ),
		);

		$args = array(
			'label'               => __( 'story', 'mia-author' ),
			'description'         => __( 'Represents secondary media related to a primary record in the application.', 'mia-author' ),
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

		// Our styles
		wp_enqueue_style(
			'miaAuthor',
			plugins_url( 'css/author.css', __FILE__ ),
			false
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
			'miaAuthor',
			plugins_url( 'js/author.js', __FILE__ ),
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
		$miaAuthorData = array(

			'recordType'  => $screen_id,
			'templateUrl' => $this->templates[ $screen_id ],
			'title'       => $post->post_title,
			'data'        => $post->post_content,

		);

		// Print to page
		wp_localize_script(
			'miaAuthor',
			'miaAuthorData',
			$miaAuthorData
		);

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

		// If this page is managed by the plugin, enqueue scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );

	}

}