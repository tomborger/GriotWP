<?php

/**
 * The main class for the plugin.
 * 
 * Provides getters and setters for collections as well as functions to set
 * up plugin and print field definitions to javascript.
 *
 * @since 0.0.1
 */
class MIA_Author{

	/**
	 * Array of field collections registered with the object.
	 * 
	 * @since 0.0.1
	 * @var array
	 */
	public $collections = array();


	/**
	 * Add a new collection to $collections.
	 * 
	 * @since 0.0.1
	 * 
	 * @param MIA_Author_Collection $collection The collection object to register. 
	 * @return bool|WP_Error Returns true on success, WP_Error on failure.
	 */
	function register_collection( $collection ){

		// Bug out if this is not an MIA Author Collection object
		if( ! $collection instanceof MIA_Author_Collection ) {

			return new WP_Error( 'invalid_type', __( 'Tried to register a collection that was not a collection object.', 'mia-author' ) );

		}

		// Bug out if collection is incomplete; i.e. lacks a name or title
		if( ! $collection->name ){

			return new WP_Error( 'incomplete', __( 'Tried to register a collection with no name.', 'mia-author' ) );

		}
		if( ! $collection->title ){

			return new WP_Error( 'incomplete', __( 'Tried to register a collection with no title.', 'mia-author' ) );

		}

		// Add collection to array
		$this->collections[] = $collection;

		// Return 
		if( in_array( $collection, $this->collections ) ) {

			return true;

		} else {

			return new WP_Error( 'unknown_error', __( 'The collection could not be registered; an unknown error occurred.', 'mia-author' ) );

		}

	}


	/**
	 * Remove a collection from $collections.
	 * 
	 * @since 0.0.1
	 *
	 * @param string $name The name of the collection to remove.
	 * @return bool Returns true on success, WP_Error on failure.
	 */
	function unregister_collection( $name ) {}


	/**
	 * List collections currently registered with the object.
	 * 
	 * @since 0.0.1
	 * 
	 * @return array Returns a copy of the $collections property
	 */
	function get_collections() {}


	/**
	 * Check to see if a collection is currently registered.
	 * 
	 * @since 0.0.1
	 * 
	 * @return bool Returns true if collection is registered, otherwise false.
	 */
	function collection_exists() {}


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
			'supports'            => array( 'title', 'editor', ),
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
			'supports'            => array( 'title', 'editor', ),
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
	 * Enqueue Angular and plugin scripts.
	 *
	 * @since 0.0.1
	 */
	function enqueue_scripts() { 

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

		// Prepare and print field data
		$this->print_fields();

	}


	/**
	 * Print all registered fields to javascript.
	 *
	 * @since 0.0.1
	 */
	function print_fields() {

		// TODO: Apply settings overrides to fields

		// Copy all registered and enabled fields into one array
		$fields = array();

		foreach( $this->collections as $collection ) {

			if( $collection->enabled ) {

				foreach( $collection->fields as $field ) {

					if( $field->enabled ) {

						$fields[] = $field;

					}

				}

			}

		}

		// Use wp_localize_scripts to print field data to page
		wp_localize_script(
			'miaAuthor',
			'miaAuthorFields',
			$fields
		);

	}


	/**
	 * Set up plugin
	 * 
	 * @since 0.0.1
	 * @param bool $load_default If true, load default fields from plugin.
	 */
	function __construct( $load_default = true ) {

		// Register Object and Story post types
		add_action( 'init', array( $this, 'register_object_cpt' ) );
		add_action( 'init', array( $this, 'register_story_cpt' ) );

		// Queue scripts and field data to be processed and printed at page load
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// If directed, include default field collection.
		if( $load_default ) {

			include( 'collections/default/load.php' );

		}

	}

}