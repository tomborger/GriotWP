<?php

/**
 * A collection of fields for the user to choose from.
 * 
 * Provides getters and setters for collections as well as functions to set
 * up plugin and print field definitions to javascript.
 *
 * @since 0.0.1
 */
class MIA_Author_Collection{

	/**
	 * The internal name of the collection.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $name;


	/**
	 * The human-readable name of the collection.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $title;


	/**
	 * The author of the collection.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $author;


	/**
	 * User-facing description or instructions for the collection.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $description;


	/**
	 * Array of fields registered to the collection.
	 * 
	 * @since 0.0.1
	 * @var array
	 */
	public $fields;


	/**
	 * Add a new field to the collection.
	 * 
	 * @since 0.0.1
	 * 
	 * @param MIA_Author_Field $field The field object to register. 
	 * @return bool Returns true on success, WP_Error on failure.
	 */
	function register_field( $field ){}


	/**
	 * Remove a field from the collection.
	 * 
	 * @since 0.0.1
	 *
	 * @param string $name The name of the field to remove.
	 * @return bool Returns true on success, WP_Error on failure.
	 */
	function unregister_field( $name ){}


	/**
	 * List collections currently registered with the object.
	 * 
	 * @since 0.0.1
	 * 
	 * @return array Returns a copy of the $fields property
	 */
	function get_fields(){}


	/**
	 * Check to see if a field is currently registered.
	 * 
	 * @since 0.0.1
	 * 
	 * @return bool Returns true if field is registered, otherwise false.
	 */
	function field_exists(){}


	/**
	 * Set up collection
	 * 
	 * @since 0.0.1
	 */
	 function __construct(){}

}