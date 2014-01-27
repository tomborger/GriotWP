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
	public $name;


	/**
	 * The human-readable title of the field.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $title;


	/**
	 * The record type (i.e. object or story) to which the field should be 
	 * appended.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $record_type;


	/**
	 * The HTML to render the field itself.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $html;


	/**
	 * The order in which the field should be rendered.
	 *
	 * @since 0.0.1
	 * @var int
	 */
	public $order;


	/**
	 * True if the field is enabled.
	 *
	 * @since 0.0.1
	 * @var bool
	 */
	public $enabled;


	/**
	 * The slug of the collection to which the field belongs.
	 * 
	 * @since 0.0.1
	 * @var string
	 */
	public $collection;


	/**
	 * Set up field
	 * 
	 * @since 0.0.1
	 */
	 function __construct(){}

}