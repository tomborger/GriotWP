<?php

/**
 * The main class for the plugin.
 * 
 * Sets up WordPress structures for working with records and exposes data for
 * Angular through wp_localize_script. 
 *
 * @since 0.0.1
 */
class Griot{


	/**
	 * Flush rewrite rules. Called on activation and deactivation.
	 *
	 * @since 0.0.1
	 */
	function flush_rewrite_rules() {
		flush_rewrite_rules();
	}


	/**
	 * Register Object custom post type.
	 *
	 * @since 0.0.1
	 */
	function register_object_cpt() {

		$labels = array(
			'name'                => _x( 'Objects', 'Post Type General Name', 'griot' ),
			'singular_name'       => _x( 'Object', 'Post Type Singular Name', 'griot' ),
			'menu_name'           => _x( 'Objects', 'Post Type Menu Name', 'griot' ),
			'parent_item_colon'   => __( 'Parent Object:', 'griot' ),
			'all_items'           => __( 'All Objects', 'griot' ),
			'view_item'           => __( 'View Object', 'griot' ),
			'add_new_item'        => __( 'Add New Object', 'griot' ),
			'add_new'             => __( 'Add New', 'griot' ),
			'edit_item'           => __( 'Edit Object', 'griot' ),
			'update_item'         => __( 'Update Object', 'griot' ),
			'search_items'        => __( 'Search Objects', 'griot' ),
			'not_found'           => __( 'Not found', 'griot' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'griot' ),
		);

		$rewrite = array(
			'slug'                => _x( 'objects', 'URL Slug', 'griot' ),
			'with_front'          => false,
			'feeds'               => true,
			'pages'               => false,
		);

		$args = array(
			'label'               => __( 'object', 'griot' ),
			'description'         => __( 'Represents a primary record in the application.', 'griot' ),
			'labels'              => $labels,
			'supports'            => array( 'title', ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'						=> 'dashicons-format-image',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'rewrite'             => $rewrite,
		);

		register_post_type( 'object', $args );

	}

	/**
	 * Register Story custom post type.
	 *
	 * @since 0.0.1
	 */
	function register_story_cpt() {

		$labels = array(
			'name'                => _x( 'Stories', 'Post Type General Name', 'griot' ),
			'singular_name'       => _x( 'Story', 'Post Type Singular Name', 'griot' ),
			'menu_name'           => _x( 'Stories', 'Post Type Menu Name', 'griot' ),
			'parent_item_colon'   => __( 'Parent Story:', 'griot' ),
			'all_items'           => __( 'All Stories', 'griot' ),
			'view_item'           => __( 'View Story', 'griot' ),
			'add_new_item'        => __( 'Add New Story', 'griot' ),
			'add_new'             => __( 'Add New', 'griot' ),
			'edit_item'           => __( 'Edit Story', 'griot' ),
			'update_item'         => __( 'Update Story', 'griot' ),
			'search_items'        => __( 'Search Stories', 'griot' ),
			'not_found'           => __( 'Not found', 'griot' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'griot' ),
		);

		$rewrite = array(
			'slug'                => _x( 'stories', 'URL Slug', 'griot' ),
			'with_front'          => false,
			'feeds'               => true,
			'pages'               => false,
		);

		$args = array(
			'label'               => __( 'story', 'griot' ),
			'description'         => __( 'Represents secondary media related to a primary record in the application.', 'griot' ),
			'labels'              => $labels,
			'supports'            => array( 'title', ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'						=> 'dashicons-book',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'rewrite'             => $rewrite,
		);

		register_post_type( 'story', $args );

	}


	/**
	 * Define endpoints for retrieving JSON.
	 *
	 * @since 0.0.1
	 */
	function register_endpoints() {

		add_rewrite_endpoint( 'griot', EP_ROOT );

	}


	/**
	 * Redirect requests to endpoint
	 *
	 * @since 0.0.1
	 */
	function redirect_endpoints() {

		global $wp_query;

		if( ! isset( $wp_query->query_vars['griot'] ) ) {
			return;
		}

		include( 'endpoint.php' );

		exit;

	}


	/**
	 * Build a directory of stories and objects for use in connection fields.
	 *
	 * @since 0.0.1
	 */
	function build_directory() {

		global $wpdb;

		$objects_query = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'object' AND post_status = 'publish'";

		$objects = $wpdb->get_results( $objects_query, ARRAY_A );

		$stories_query = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'story' AND post_status = 'publish'";

		$stories = $wpdb->get_results( $stories_query, ARRAY_A );

		$directory = array(
			'object' => $objects,
			'story' => $stories,
		);

		update_option( 'griot_directory', $directory );

	}


	/**
	 * Enqueue vendor and plugin scripts.
	 *
	 * @since 0.0.1
	 */
	function enqueue_scripts_and_styles() { 

		// Return early if we're not on an object or story edit page.
		$screen = get_current_screen();

		$ok_screen_ids = array( 'object', 'story', );

		if( ! in_array( $screen->id, $ok_screen_ids ) ) {

			return;

		}

		// Angular scripts
		wp_enqueue_script( 
			'angular', 
			plugins_url( 'components/angular/angular.min.js', __FILE__ ), 
			false, 
			null,
			true
		);

		// CKEditor
		// Required by WYSIWYG fields
		wp_enqueue_script(
			'ckeditor',
			plugins_url( 'components/ckeditor/ckeditor.js', __FILE__ ),
			false
		);
		wp_enqueue_script(
			'ckeditor_adapter',
			plugins_url( 'components/ckeditor/adapters/jquery.js', __FILE__ ),
			array( 'ckeditor' ),
			null,
			true
		);

		// Swiper
		// Required by repeater fields
		wp_enqueue_style(
			'swiper',
			plugins_url( 'components/swiper/idangerous.swiper.css', __FILE__ ),
			false
		);
		wp_enqueue_script(
			'swiper',
			plugins_url( 'components/swiper/idangerous.swiper.js', __FILE__ ),
			false,
			null,
			true
		);

		// Leaflet
		// Required by zoomer fields
		wp_enqueue_style(
			'leaflet',
			plugins_url( 'components/leafletnew/leaflet.css', __FILE__  ),
			false
		);
		wp_enqueue_script(
			'leaflet',
			plugins_url( 'components/leafletnew/leaflet.js', __FILE__  ),
			false,
			null,
			true
		);

		// Leaflet Draw
		// Required by zoomer fields
		wp_enqueue_style(
			'leaflet_draw',
			plugins_url( 'components/leaflet.draw/leaflet.draw.css', __FILE__  ),
			false
		);
		wp_enqueue_script(
			'leaflet_draw',
			plugins_url( 'components/leaflet.draw/leaflet.draw.js', __FILE__  ),
			array( 'leaflet' ),
			null,
			true
		);

		// jQuery Actual
		// Required by zoomer fields
		wp_enqueue_script( 
			'jquery_actual',
			plugins_url( 'components/jquery.actual/jquery.actual.min.js', __FILE__ ),
			array( 'jquery' ),
			null,
			true
		);

		// Flat Image Zoom
		// Required by zoomer fields
		wp_enqueue_script(
			'flat_image_zoom',
			plugins_url( 'components/flat_image_zoom/flat_image_zoom.js', __FILE__ ),
			array( 
				'jquery', 
				'leaflet', 
				'leaflet_draw', 
				'jquery_actual', 
				'underscore',
			),
			null,
			true
		);

		// Griot
		wp_enqueue_style(
			'griot',
			plugins_url( 'css/griot.css', __FILE__ ),
			false
		);
		wp_enqueue_script(
			'griot',
			plugins_url( 'js/griot.js', __FILE__ ),
			'angular',
			null,
			true
		);
		wp_enqueue_script( 
			'griot-main',
			plugins_url( 'js/controllers/main.js', __FILE__ ),
			'griot',
			null,
			true
		);
		wp_enqueue_script( 
			'griot-modelchain',
			plugins_url( 'js/services/modelchain.js', __FILE__ ),
			'griot',
			null,
			true
		);
		wp_enqueue_script( 
			'griot-get-title',
			plugins_url( 'js/filters/get-title.js', __FILE__ ),
			'griot',
			null,
			true
		);
		wp_enqueue_script( 
			'griot-annotatedimage',
			plugins_url( 'js/directives/annotatedimage.js', __FILE__ ),
			'griot',
			null,
			true
		);
		wp_enqueue_script( 
			'griot-annotations',
			plugins_url( 'js/directives/annotations.js', __FILE__ ),
			'griot',
			null,
			true
		);
		wp_enqueue_script( 
			'griot-ckeditor',
			plugins_url( 'js/directives/ckeditor.js', __FILE__ ),
			'griot',
			null,
			true
		);
		wp_enqueue_script( 
			'griot-field',
			plugins_url( 'js/directives/field.js', __FILE__ ),
			'griot',
			null,
			true
		);
		wp_enqueue_script( 
			'griot-fieldset',
			plugins_url( 'js/directives/fieldset.js', __FILE__ ),
			'griot',
			null,
			true
		);
		wp_enqueue_script( 
			'griot-repeater-fields',
			plugins_url( 'js/directives/griot-repeater-fields.js', __FILE__ ),
			'griot',
			null,
			true
		);
		wp_enqueue_script( 
			'griot-repeater',
			plugins_url( 'js/directives/repeater.js', __FILE__ ),
			'griot',
			null,
			true
		);
		wp_enqueue_script( 
			'griot-image',
			plugins_url( 'js/directives/imagepicker.js', __FILE__ ),
			'griot',
			null,
			true
		);

		// Add WordPress media manager
		wp_enqueue_media();
		wp_enqueue_script( 'custom-header' );

		// Print application data
		$this->print_data( $screen->id );

	}


	/**
	 * Expose record data and template URL to application
	 * 
	 * @since 0.0.1
	 */
	function print_data( $screen_id ) {

		// Grab $post variable
		global $post;

		// Construct data for application
		$griotData = array(

			'recordType'  => $screen_id,
			'templateUrl' => $this->templates[ $screen_id ],
			'title'       => $post->post_title,
			'data'        => $post->post_content,
			'directory'		=> get_option( 'griot_directory' ),
			'imageSrc'    => get_option( 'griot_image_src', 'external' ),
			'imageList'   => get_option( 'griot_image_list', array(
				'http://new.artsmia.org/wp-content/uploads/2013/08/women_in_craft_and_design-e1375730308453-314x231.jpg',
				'http://new.artsmia.org/wp-content/uploads/2013/07/sacred-305x231.jpg',
				'http://new.artsmia.org/wp-content/uploads/2013/09/matisse-e1382128933785-346x231.jpg',
				'http://new.artsmia.org/wp-content/uploads/2013/08/weinstein-e1381497453626-385x229.jpg'
			) )

		);

		// Print to page
		wp_localize_script(
			'griot',
			'griotData',
			$griotData
		);

	}


	/**
	 * Register metabox for connections
	 *
	 * @since 0.0.1
	 */
	function register_connections_metabox() {

		// Return early if we're not on an object page.
		$screen = get_current_screen();

		if( $screen->id != 'object' ) {

			return;

		}

		// Add meta box
		add_meta_box(
			'griot-connections',
			 __( 'Related Stories', 'griot' ),
			array( $this, 'connections_metabox_template' ),
			'object',
			'side'
		);

	}


	/**
	 * Callback that prints Angular template to connections metabox.
	 *
	 * @since 0.0.1
	 */
	function connections_metabox_template() {

		echo "<field name='connections' type='connection' />";

	}


	/**
	 * Set up plugin
	 * 
	 * @since 0.0.1
	 * @param bool $load_default If true, load default fields from plugin.
	 */
	function __construct( $templates ) {

		// Set up on activation
		register_activation_hook( __FILE__, array( $this, 'flush_rewrite_rules' ) );
		register_activation_hook( __FILE__, array( $this, 'build_directory' ) );

		// Clean up on deactivation
		register_deactivation_hook( __FILE__, array( $this, 'flush_rewrite_rules' ) );


		// Register templates
		$this->templates = $templates;

		// Register Object and Story post types
		add_action( 'init', array( $this, 'register_object_cpt' ) );
		add_action( 'init', array( $this, 'register_story_cpt' ) );

		// Register endpoint
		add_action( 'init', array( $this, 'register_endpoints' ) );
		add_action( 'template_redirect', array( $this, 'redirect_endpoints' ) );

		// Rebuild directory when post structure changes
		add_action( 'save_post', array( $this, 'build_directory' ) );
		add_action( 'trash_post', array( $this, 'build_directory' ) );
		add_action( 'delete_post', array( $this, 'build_directory' ) );

		// If this page is managed by the plugin, enqueue scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );

		// Add connections metabox
		add_action( 'add_meta_boxes', array( $this, 'register_connections_metabox' ) );
 
	}

}