<?php

/**
 * Plugin Name: MIA Author
 * Description: Author gives you the post types and fields necessary to populate the MIA's open-source presentation software (name TBD).
 * Version: 0.0.1
 * Author: Minneapolis Institute of Arts
 * Author URI: http://new.artsmia.org
 * License: TBD
 */

// Main MIA Author class
include( 'class-mia-author.php' );

// Collection class
include( 'class-mia-author-collection.php' );

// Fields class
include( 'class-mia-author-field.php' );

new MIA_Author();