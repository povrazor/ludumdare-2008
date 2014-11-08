<?php
defined('ABSPATH') or die("No.");
/*
Plugin Name: LDJam
Plugin URI: http://ludumdare.com/
Description: Ludum Dare Game Jam Website
Version: 0.1
Author: Mike Kasprzak
Author URI: http://www.sykhronics.com
License: TBD
*/

require_once "wp_functions.php";
require_once "ld_functions.php";

//$ldvar = NULL;

function shortcode_ldjam( $atts ) {
	//if ( $ldvar === NULL ) { $ldvar = ld_get_vars(); }
	return "I am very important";	
}
add_shortcode( 'ldjam', 'shortcode_ldjam' );


function shortcode_ldjam_root( $atts ) {
	//if ( $ldvar === NULL ) { $ldvar = ld_get_vars(); }
	if ( ld_is_admin() ) {
		return "Thanks Chiefy";
	}
	else {
		return "";
	}
}
add_shortcode( 'ldjam-root', 'shortcode_ldjam_root' );


/* This goes in the theme, so a shortcode isn't possible */
function ldjam_show_bar() {
	//if ( $ldvar === NULL ) { $ldvar = ld_get_vars(); }
	return "On Now: <strong>I don't know. I'm testing here!</strong>";
}

?>