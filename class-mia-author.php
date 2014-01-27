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
	public $collections;


	/**
	 * Add a new collection to $collections.
	 * 
	 * @since 0.0.1
	 * 
	 * @param MIA_Author_Collection $collection The collection object to register. 
	 * @return bool Returns true on success, WP_Error on failure.
	 */
	function register_collection( $collection ){}


	/**
	 * Remove a collection from $collections.
	 * 
	 * @since 0.0.1
	 *
	 * @param string $name The name of the collection to remove.
	 * @return bool Returns true on success, WP_Error on failure.
	 */
	function unregister_collection( $name ){}


	/**
	 * List collections currently registered with the object.
	 * 
	 * @since 0.0.1
	 * 
	 * @return array Returns a copy of the $collections property
	 */
	function get_collections(){}


	/**
	 * Check to see if a collection is currently registered.
	 * 
	 * @since 0.0.1
	 * 
	 * @return bool Returns true if collection is registered, otherwise false.
	 */
	function collection_exists(){}


	/**
	 * Enqueue Angular and plugin scripts.
	 *
	 * @since 0.0.1
	 */
	function enqueue_scripts(){ 

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
	function print_fields(){

		// TODO: Prepare field data

		// Use wp_localize_scripts to print field data to page
		wp_localize_script(
			'miaAuthor',
			'miaAuthorFields',
			array( 'Prepared field data here' )
		);

	}


	/**
	 * Set up plugin
	 * 
	 * @since 0.0.1
	 * @param bool $load_default If true, load default fields from plugin.
	 */
	function __construct( $load_default = true ) {

		// Queue scripts and field data to be processed and printed at page load
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// If directed, include default field collection.
		if( $load_default ) {

			include( 'collections/default/load.php' );

		}

	}

}