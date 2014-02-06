<?php

/**
 * Plugin Name: Griot
 * Description: Provides a back end for populating the MIA's open-source iPad presentation software (name TBD).
 * Version: 0.0.1
 * Author: Minneapolis Institute of Arts
 * Text Domain: griot
 * Author URI: http://new.artsmia.org
 * License: TBD
 */

/**
 * Main Griot class.
 */
include( 'class-griot.php' );

// Config
// NOTE: Template array key must match post type
$templates = array(
	'object'  =>   plugins_url( 'templates/object.html', __FILE__ ),
	'story'   =>   plugins_url( 'templates/story.html', __FILE__ )
);
$Griot = new Griot( $templates );