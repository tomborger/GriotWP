<?php

// Create Collection
$default_collection = new MIA_Author_Collection(
	'default',
	'Default Collection',
	'Minneapolis Institute of Arts',
	'The standard collection of fields for adding content to the Mia\'s open-source iPad presentation software (name TBD).'
);

/**
 * Include field definitions (also registers each with collection)
 */
include( 'field1.php' );
include( 'field2.php' );
include( 'field3.php' );

// Register Collection
$result = $this->register_collection( $default_collection );

if( is_wp_error( $result ) ) {

	echo $result->get_error_message();

}