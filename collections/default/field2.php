<?php

// Define field HTML
$html = <<<EOD

	<div class='field'>
		<input type='text' name='field2'>
	</div>

EOD;

// Create field
$field2 = new MIA_Author_Field(
	'field2',
	$html,
	'The Second Field',
	'object',
	2,
	true
);

// Register field
$result = $default_collection->register_field( $field2 );

if( is_wp_error( $result ) ) {

	echo 'Error: ' . $result->get_error_message();
	
}