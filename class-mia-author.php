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
	 * Print all registered fields to javascript.
	 *
	 * @since 0.0.1
	 * 
	 * @return bool Returns true on success, WP_Error on failure.
	 */
	function print_fields(){}


	/**
	 * Set up plugin
	 * 
	 * @since 0.0.1
	 */
	 function __construct(){}

}