<?php

  add_filter( 'mia_author_fields', 'add_second_field' );
  
  function add_second_field( $fields ){

    $html = "<p>Instructions</p>
            <input type='text' name='Slug' value='Text here' />";

    $field = create_mia_author_field( 'Second Field', $html, 'object', 'tms-id', 'Enter the TMS ID.' );

    $fields[] = $field;

    return $fields;

  }