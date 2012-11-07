<?php
/*
Plugin Name: PS RDFExport
Plugin URI: http://www.performantsoftware.com/wordpress/plugins/rdfexport/
Description: This plugin provides a post export into the RDF form expected by NINES
Version: 1.0.0
Author: Dave Goldstein
Author URI: 
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

require_once( 'ps-rdfexport.php' );

global $exporter;
$exporter = new psRDFExport( );
?>
