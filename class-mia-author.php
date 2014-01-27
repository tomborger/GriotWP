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
	 * @return string|WP_Error Returns the collection's key in the $collections 
	 * array on success, WP_Error on failure.
	 */
	function register_collection( $collection ){

		// Bug out if this is not an MIA Author Collection object
		if( ! $collection instanceof MIA_Author_Collection ) {

			return new WP_Error( 'invalid_type', 'Tried to register a collection that was not a collection object.' );

		}

		// Retrieve name of collection
		$key = $collection->get_name();

		// Find free key if current key is taken
		$i = 1;
		while( array_key_exists( $key, $this->collections ) ){

			$i++;
			$key = $key . $i;

		}

		// Add collection to array
		$this->collections[ $key ] = $collection;

		// Return 
		if( isset( $this->collections[ $key ] ) ) {

			return $key;

		} else {

			return new WP_Error( 'unknown_error', 'The collection could not be registered; an unknown error occurred.' );

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