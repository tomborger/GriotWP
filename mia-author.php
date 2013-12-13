<?php

/**
 * Plugin Name: MIA Author
 * Description: Author gives you the post types and fields necessary to populate the MIA's open-source presentation software (name TBD).
 * Version: 0.0.1
 * Author: Minneapolis Institute of Arts
 * Author URI: http://new.artsmia.org
 * License: GPL2
 */

/*  Copyright 2013 MINNEAPOLIS INSTITUTE OF ARTS

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Grab utility functions
include( 'util.php' );

// Main MIA Author class
include( 'class-mia-author.php' );

// MIA Author Field class
include( 'class-mia-author-field.php' );

new MIA_Author();