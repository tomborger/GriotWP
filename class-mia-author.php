<?php

// Main MIA Author Class
class MIA_Author{

  public $config = array(
    'field_include_dir' => 'fields/',
    'field_list' => array(
      'tms_id',
      'second_field',
    ),
  );

  public function __construct(){

    add_action( 'init', array( $this, 'register_object_post_type' ) );
    add_action( 'init', array( $this, 'register_story_post_type' ) );
    add_action( 'add_meta_boxes', array( $this, 'register_author_metabox' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

  }

  // Register object post type
  public function register_object_post_type() {

    $labels = array(
      'name'                => _x( 'Objects', 'Post Type General Name', 'mia_author' ),
      'singular_name'       => _x( 'Object', 'Post Type Singular Name', 'mia_author' ),
      'menu_name'           => __( 'Objects', 'mia_author' ),
      'parent_item_colon'   => __( 'Parent Object:', 'mia_author' ),
      'all_items'           => __( 'All Objects', 'mia_author' ),
      'view_item'           => __( 'View Object', 'mia_author' ),
      'add_new_item'        => __( 'Add New Object', 'mia_author' ),
      'add_new'             => __( 'New Object', 'mia_author' ),
      'edit_item'           => __( 'Edit Object', 'mia_author' ),
      'update_item'         => __( 'Update Object', 'mia_author' ),
      'search_items'        => __( 'Search objects', 'mia_author' ),
      'not_found'           => __( 'No objects found', 'mia_author' ),
      'not_found_in_trash'  => __( 'No objects found in Trash', 'mia_author' ),
    );
    $args = array(
      'labels'              => $labels,
      'supports'            => array( 'title', 'revisions', ),
      'hierarchical'        => false,
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_nav_menus'   => false,
      'show_in_admin_bar'   => true,
      'menu_position'       => 5,
      'menu_icon'           => '',
      'can_export'          => true,
      'has_archive'         => false,
      'exclude_from_search' => true,
      'publicly_queryable'  => false,
      'rewrite'             => false,
      'capability_type'     => 'post',
    );
    register_post_type( 'author_object', $args );

  }

  // Register story post type
  public function register_story_post_type() {

    $labels = array(
      'name'                => _x( 'Stories', 'Post Type General Name', 'mia_author' ),
      'singular_name'       => _x( 'Story', 'Post Type Singular Name', 'mia_author' ),
      'menu_name'           => __( 'Stories', 'mia_author' ),
      'parent_item_colon'   => __( 'Parent Story:', 'mia_author' ),
      'all_items'           => __( 'All Stories', 'mia_author' ),
      'view_item'           => __( 'View Story', 'mia_author' ),
      'add_new_item'        => __( 'Add New Story', 'mia_author' ),
      'add_new'             => __( 'New Story', 'mia_author' ),
      'edit_item'           => __( 'Edit Story', 'mia_author' ),
      'update_item'         => __( 'Update Story', 'mia_author' ),
      'search_items'        => __( 'Search stories', 'mia_author' ),
      'not_found'           => __( 'No stories found', 'mia_author' ),
      'not_found_in_trash'  => __( 'No stories found in Trash', 'mia_author' ),
    );
    $args = array(
      'labels'              => $labels,
      'supports'            => array( 'title', 'revisions', ),
      'hierarchical'        => false,
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_nav_menus'   => false,
      'show_in_admin_bar'   => true,
      'menu_position'       => 5,
      'menu_icon'           => '',
      'can_export'          => true,
      'has_archive'         => false,
      'exclude_from_search' => true,
      'publicly_queryable'  => false,
      'rewrite'             => false,
      'capability_type'     => 'post',
    );
    
    register_post_type( 'author_story', $args );

  }

  // Register metabox for editing each post type
  public function register_author_metabox() {

    $post_types = array( 'author_object', 'author_story' );
    foreach( $post_types as $post_type ){

      $post_type_object = get_post_type_object( $post_type );
      $label = $post_type_object->labels->singular_name;

      add_meta_box( 'mia_author', $label . ' Content', array( $this, 'edit_metabox_content' ), $post_type, 'normal', 'high' );

    }

  }

  // Populate metabox with blank canvas for Angular
  public function edit_metabox_content(){

    echo "ANGULAR APP HERE (Helpful, I know)";

  }

  // Load Angular and our app
  public function enqueue_scripts(){

    if( $this->is_mia_author_edit_page() ){

      // Enqueue core
      wp_register_script( 'angular', plugins_url( 'js/vendor/angular.min.js', __FILE__ ), array(), null, true );
      wp_enqueue_script( 'mia_author', plugins_url( 'js/author/author.js', __FILE__ ), array( 'angular' ), null, true );

      // Print field definitions
      $this->print_field_definitions();
    }

  }

  public function print_field_definitions(){

    $path = $this->config['field_include_dir'];
    $files = $this->config['field_list'];

    $fields = array();

    foreach( $files as $file ){

      include( $path . $file . '.php' );

    }

    $fields = apply_filters( 'mia_author_fields', array() );

    wp_localize_script( 'mia_author', 'mia_author_fields', $fields );

  }

  public function is_mia_author_edit_page(){

    return true;

  }

}