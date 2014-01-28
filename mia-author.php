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

/**
 * Class for collections of field definitions.
 */
include( 'class-mia-author-collection.php' );

/**
 * Class for individual field definitions.
 */
include( 'class-mia-author-field.php' );

$MIA_Author = new MIA_Author( true );