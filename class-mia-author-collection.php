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
	 * True if the collection is enabled.
	 * 
	 * @since 0.0.1
	 * @var bool
	 */
	public $enabled = true;


	/**
	 * Array of fields registered to the collection.
	 * 
	 * @since 0.0.1
	 * @var array
	 */
	public $fields = array();


	/**
	 * Add a new field to the collection.
	 * 
	 * @since 0.0.1
	 * 
	 * @param MIA_Author_Field $field The field object to register. 
	 * @return bool|WP_Error Returns true on success, WP_Error on failure.
	 */
	function register_field( $field ){

		// Bug out if this is not an MIA Author Field object
		if( ! $field instanceof MIA_Author_Field ) {

			return new WP_Error( 'invalid_type', __( 'Tried to register a field that was not a field object.', 'mia-author' ) );

		}

		// Bug out if field is incomplete; i.e. lacks a name or HTML content.
		if( ! $field->name ) {

			return new WP_Error( 'incomplete_field', __( 'Tried to register a field with no name.', 'mia-author' ) );

		}
		if( ! $field->html ) {

			return new WP_Error( 'incomplete_field', __( 'Tried to register a field with no HTML content.', 'mia-author' ) );

		}

		// Add collection name to field
		$field->set_collection( $this->name );

		// Add field to collection
		$this->fields[] = $field;

		// Return 
		if( in_array( $field, $this->fields ) ) {

			return true;

		} else {

			return new WP_Error( 'unknown_error', __( 'The field could not be registered; an unknown error occurred.', 'mia-author' ) );

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
	 * @param string $title The human-readable title of the collection.
	 * @param string $author The author of the collection.
	 * @param string $description User-facing description or instructions.
	 */
	function __construct( $name, $title, $author = null, $description = null, $enabled = null ) {

		if( is_string( $name ) ) {
			$this->name = $name;
		}

		if( is_string( $title ) ) {
			$this->title = $title;
		}

		if( is_string( $author ) ) {
			$this->author = $author;
		}

		if( is_string( $description ) ) {
			$this->description = $description;
		}

		if( is_bool( $enabled ) ) {
			$this->enabled = $enabled;
		}

	}

}