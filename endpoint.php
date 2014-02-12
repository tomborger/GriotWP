<?php

global $wpdb;

$query_vars = get_query_var( 'griot' );
$query_arr = explode( '/', $query_vars );

// No extra endpoints; return all data
if( 0 == count( $query_arr ) ) {

	$request = 'all';

}

// Malformed
else if( ! in_array( $query_arr[0], array( 'objects', 'stories' ) ) ) {

	$request = 'all';

}

// 'objects' or 'stories'
else if( 1 == count( $query_arr ) ) {

	$request = $query_arr[0];

} 

// ID
else if( is_numeric( $query_arr[1] ) ) {

	$request = $query_arr[1];

}

else {

	$request = 'all';

}

echo "<h1>" . $request . "</h1>";

switch( $request ) {

	case 'all':

		$posts = $wpdb->get_results( "SELECT post_type, post_content FROM $wpdb->posts WHERE post_status = 'publish' AND ( post_type = 'object' OR post_type = 'story' )", OBJECT );

		$output = array(
			'objects' => array(),
			'stories' => array(),
		);

		foreach( $posts as $post ) {

			if( 'object' == $post->post_type ) {
				$output['objects'][] = json_decode( $post->post_content, true );
			}

			if( 'story' == $post->post_type ) {
				$output['stories'][] = json_decode( $post->post_content, true );
			}

		}

		echo json_encode( $output );

		break;

	case 'objects':
	case 'stories':

		$post_type = $request == 'objects' ? 'object' : 'story';

		$posts = $wpdb->get_col( 
			$wpdb->prepare(
				"SELECT post_content FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = %s",
				$post_type
		 )
		);

		$output = array();

		foreach( $posts as $post_content ) {

			$output[] = json_decode( $post_content );

		}

		echo json_encode( $output );

		break;

	default:

		$output = $wpdb->get_var( 
			$wpdb->prepare( 
				"SELECT post_content FROM $wpdb->posts WHERE post_status = 'publish' AND ID = %d",
				$request 
			)
		);

		echo $output;

		break;

}