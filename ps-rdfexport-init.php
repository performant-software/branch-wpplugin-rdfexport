<?php
/*
Plugin Name: PS RDFExport
Plugin URI: http://www.performantsoftware.com/wordpress/plugins/rdfexport/
Description: This plugin provides an export of all posts into the RDF format expected by NINES
Version: 1.0.0
Author: Dave Goldstein
Author URI: 
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

require_once( 'ps-rdfexport-bplate.php' );

global $rdfexporter;
$rdfexporter = new psRDFExport( );
?>
