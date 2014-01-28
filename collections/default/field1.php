<?php

// Define field HTML
$html = <<<EOD

	<div class='field'>
		<input type='text' name='field1'>
	</div>

EOD;

// Create field
$field1 = new MIA_Author_Field(
	'field1',
	$html,
	'The First Field',
	'object',
	1,
	true
);

// Register field
$result = $default_collection->register_field( $field1 );

if( is_wp_error( $result ) ) {

	echo 'Error: ' . $result->get_error_message();
	
}