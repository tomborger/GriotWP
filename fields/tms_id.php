<?php

  add_filter( 'mia_author_fields', 'add_tms_id_field' );
  
  function add_tms_id_field( $fields ){

    $html = "<p>Instructions</p>
            <input type='text' name='Slug' value='Text here' />";

    $field = create_mia_author_field( 'TMS ID', $html, 'object', 'tms-id', 'Enter the TMS ID.' );

    $fields[] = $field;

    return $fields;

  }