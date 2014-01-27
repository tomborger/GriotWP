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
	public $name = '';


	/**
	 * The human-readable name of the collection.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $title = '';


	/**
	 * The author of the collection.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $author = '';


	/**
	 * User-facing description or instructions for the collection.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $description = '';


	/**
	 * Array of fields registered to the collection.
	 * 
	 * @since 0.0.1
	 * @var array
	 */
	public $fields = array();

	/**
	 * Retrieve the collection's name property.
	 * 
	 * @since 0.0.1
	 * @return string The name of the collection.
	 */
	function get_name() {

		return $this->name;

	}


	/**
	 * Add a new field to the collection.
	 * 
	 * @since 0.0.1
	 * 
	 * @param MIA_Author_Field $field The field object to register. 
	 * @return string|WP_Error Returns the field's key in the $fields array on 
	 * success, WP_Error on failure.
	 */
	function register_field( $field ){

		// Bug out if this is not an MIA Author Field object
		if( ! $field instanceof MIA_Author_Field ) {

			return new WP_Error( 'invalid_type', 'Tried to register a field that was not a field object.' );

		}

		// Define collection name
		$field->set_collection( $this->name );

		// Retrieve name of field
		$key = $field->get_name();

		// Find free key if current key is taken
		$i = 1;
		while( array_key_exists( $key, $this->fields ) ){

			$i++;
			$key = $key . $i;

		}

		// Add field to collection
		$this->fields[ $key ] = $field;

		// Return 
		if( isset( $this->fields[ $key ] ) ) {

			return $key;

		} else {

			return new WP_Error( 'unknown_error', 'The field could not be registered; an unknown error occurred.' );

		}

	}


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
	 * Set up the collection.
	 * 
	 * @since 0.0.1
	 * @param string $name The name of the collection.
	 * @param string $title The human-readable title of the collection.
	 * @param string $author The author of the collection.
	 * @param string $description User-facing description or instructions.
	 */
	function __construct( $name, $title, $author, $description ) {

		if( ! empty( $name ) ) {
			$this->name = $name;
		}

		if( ! empty( $title ) ) {
			$this->title = $title;
		}

		if( ! empty( $author ) ) {
			$this->author = $author;
		}

		if( ! empty( $description ) ) {
			$this->description = $description;
		}

	}

}