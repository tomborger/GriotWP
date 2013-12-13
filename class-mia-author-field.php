<?php

class MIA_Author_Field{

  public $name, $html, $slug, $helper;

  public function __construct( $name, $html, $slug, $helper ){

    $this->name = $name;
    $this->html = $html;
    $this->slug = $slug;
    $this->helper = $helper;

  }
  
}