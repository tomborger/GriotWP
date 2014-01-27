<?php

// Define field HTML
$html = <<<EOD

	<div class='field'>
		<input type='text' name='field3'>
	</div>

EOD;

// Create field
$field3 = new MIA_Author_Field(
	'field3',
	'The Third Field',
	'object',
	$html
);

// Register field
$result = $default_collection->register_field( $field3 );

if( is_wp_error( $result ) ) {

	echo 'Error: ' . $result->get_error_message();

}