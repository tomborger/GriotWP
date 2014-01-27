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
	 * The internal name of the field.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $name = '';


	/**
	 * The human-readable title of the field.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $title = '';


	/**
	 * The record type (i.e. object or story) to which the field should be 
	 * appended.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $record_type = 'object';


	/**
	 * The HTML to render the field itself.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $html = '';


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
	function __construct( $name, $title, $record_type, $html, $order = 1, $enabled = true ) {

		if( ! empty( $name ) ) {
			$this->name = $name;
		}

		if( ! empty( $title ) ) {
			$this->title = $title;
		}

		if( ! empty( $record_type ) ) {
			$this->record_type = $record_type;
		}

		if( ! empty( $html ) ) {
			$this->html = $html;
		}

		if( isset( $order ) ) {
			$this->order = $order;
		}

		if( isset( $enabled ) ) {
			$this->enabled = $enabled;
		}

	}

}