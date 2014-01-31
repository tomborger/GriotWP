<?php

/**
 * Plugin Name: MIA Author
 * Description: Provides a back end for populating the MIA's open-source iPad presentation software (name TBD).
 * Version: 0.0.2
 * Author: Minneapolis Institute of Arts
 * Text Domain: mia-author
 * Author URI: http://new.artsmia.org
 * License: TBD
 */

/**
 * Main MIA Author class.
 */
include( 'class-mia-author.php' );

// Config
// NOTE: Template array key must match post type
$templates = array(
	'object'  =>   plugins_url( 'templates/object.html', __FILE__ ),
	'story'   =>   plugins_url( 'templates/story.html', __FILE__ )
);
$MIA_Author = new MIA_Author( $templates );