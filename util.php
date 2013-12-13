<?php

/**
 * create_mia_author_field
 *
 * Allows future modifications of fields; must be called on the mia_author_fields action hook
 * 
 * @param string $name The title of the field in the UI
 * @param string $html The HTML to render for the field
 * @param string $post_type 'author_object' or 'author_story' (also accepts 'object' and 'story')
 * @param string $slug The name of the field in both JS and database
 * @param string $helper Instructions for the user
 */

function create_mia_author_field( $name, $html, $post_type, $slug, $helper ){

  global $post;

  // Bug out if user forgot a required parameter
  if( !isset( $name ) || !isset( $html ) || !isset( $post_type ) ){
    
    return;
  
  }

  // Correct shorthand
  if( 'object' == $post_type ){

    $post_type = 'author_object';

  }
  if( 'story' == $post_type ){

    $post_type = 'author_story';
    
  }

  // Return if post type is not for an MIA Author post type
  if( 'author_object' != $post_type && 'author_story' != $post_type ){

    return;

  }

  // Bug out if this request is for a different post type  
  if( isset($post) && $post_type != $post->post_type ){
    
    return;
  
  }

  // Define $helper if not set
  if( !isset( $helper ) ){

    $helper = '';

  }

  // Create slug if not defined and sanitize
  if( !isset( $slug ) ){

    $slug = sanitize_title( $name );

  } else {

    $slug = sanitize_title( $slug );

  }

  // Convert dashes to underscores
  $slug = str_replace('-', '_', $slug);

  // Build object
  $field = new MIA_Author_Field( $name, $html, $slug, $helper );

  return $field;

}