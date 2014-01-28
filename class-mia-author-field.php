<?php

/**
 * A single field definition.
 * 
 * Once encoded as JSON, provides Angular with data to render a single field
 * in the admin area.
 *
 * @since 0.0.1
 */
class MIA_Author_Field{

	/**
	 * The internal name of the field. Required.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $name = '';


	/**
	 * The HTML to render the field itself. Required.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $html = '';


	/**
	 * The human-readable title of the field. 
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $title = '';


	/**
	 * The record type (i.e. object, view, story, page) to which the field should 
	 * be appended.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $record_type = 'object';


	/**
	 * The order in which the field should be rendered.
	 *
	 * @since 0.0.1
	 * @var int
	 */
	public $order = 1;


	/**
	 * True if the field is enabled.
	 *
	 * @since 0.0.1
	 * @var bool
	 */
	public $enabled = true;


	/**
	 * The slug of the collection to which the field belongs.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $collection;


	/**
	 * Retrieve the field's name property.
	 * 
	 * @since 0.0.1
	 * @return string The name of the field.
	 */
	function get_name() {

		return $this->name;

	}


	/**
	 * Retrieve the field's html property.
	 * 
	 * @since 0.0.1
	 * @return string The HTML content of the field.
	 */
	function get_html() {

		return $this->html;

	}


	/**
	 * Define the collection the name belongs to.
	 * 
	 * @since 0.0.1
	 * @param string $name The name of the collection.
	 */
	function set_collection( $name ) {

		$this->collection = $name;

	}


	/**
	 * Set up field
	 * 
	 * @since 0.0.1
	 */
	function __construct( $name, $html, $title = null, $record_type = null, $order = null, $enabled = null ) {

		if( is_string( $name ) ) {
			$this->name = $name;
		}

		if( is_string( $html ) ) {
			$this->html = $html;
		}

		if( is_string( $title ) ) {
			$this->title = $title;
		}

		if( is_string( $record_type ) ) {
			$this->record_type = $record_type;
		}

		if( is_int( $order ) ) {
			$this->order = $order;
		}

		if( is_bool( $enabled ) ) {
			$this->enabled = $enabled;
		}

	}

}