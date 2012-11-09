<?php
/*
 * Basic RDF export of all BRANCH articles. See the example.rdf file for an example of the schema.
 *
 */

// Basic plugin support from Wordpress
require_once( ABSPATH . "wp-includes/pluggable.php" );

// Do the actual export and download here
if( isset( $_GET['rdfdownload'] ) ) {
   rdf_export( );
   exit;
}

function rdf_export( ) {

   // Generate filename site.rdf.YYYY-MM-DD.xml
   $sitename = sanitize_key( get_bloginfo( 'name' ) );
   if ( ! empty($sitename) ) $sitename .= '.';
   $filename = $sitename . 'rdf.' . date( 'Y-m-d' ) . '.xml';

   // HTTP stuff
   header( 'Content-Description: File Transfer' );
   header( 'Content-Disposition: attachment; filename=' . $filename );
   header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

   /* Standard header... */
   rdf_header_contents( );

   // Construct query args (all ps_articles) and issue the query...
   $qargs = array( 'post_status' => 'publish',
                   'post_type' => 'ps_articles',
                   'posts_per_page' => -1 );
   $query= new WP_Query( $qargs );

   // deal with the results, if any
   if ( $query->have_posts( ) ) {
      $all_posts = $query->get_posts( );
      foreach( $all_posts as $my_post ) {
         rdf_object_contents( $my_post );
      }
   }

   // Docs say this is necessary
   wp_reset_postdata( );

   // Standard footer...
   rdf_footer_contents( );
}

function rdf_object_contents( $my_post ) {
  
   // indent used to set the XML indent for nice human readable XML. Set to 0 to disable this and save a few bytes 
   $indent = 1;
   rdf_open_object_tag( $my_post, $indent );

   $indent = 2;

   // just step through all the tags needed...
   rdf_archive_contents( $my_post, $indent );
   rdf_federation_contents( $my_post, $indent );
   rdf_see_also_contents( $my_post, $indent );
   rdf_title_contents( $my_post, $indent );
   rdf_author_contents( $my_post, $indent );
   rdf_editor_contents( $my_post, $indent );
   rdf_publisher_contents( $my_post, $indent );
   rdf_translator_contents( $my_post, $indent );
   rdf_dc_date_contents( $my_post, $indent );
   rdf_genre_contents( $my_post, $indent );
   rdf_discipline_contents( $my_post, $indent );
   rdf_type_contents( $my_post, $indent );
   rdf_text_contents( $my_post, $indent );
   rdf_thumbnail_contents( $my_post, $indent );

   $indent = 1;
   rdf_close_object_tag( $indent );
}

function rdf_open_object_tag( $my_post, $indent ) {
   $tag = "branch:object";
   $embed = "rdf:about=" . "\"" . rdf_permalink( $my_post ) . "\"";
   rdf_open_tag( $tag, $indent, $embed );
   rdf_newline( );
}

function rdf_close_object_tag( $indent ) {
   $tag = "branch:object";
   rdf_close_tag( $tag, $indent );
}

function rdf_archive_contents( $my_post, $indent ) {
   $tag = "collex:archive";
   rdf_open_tag( $tag, $indent );
   echo "branch";
   rdf_close_tag( $tag, 0 );
}

function rdf_federation_contents( $my_post, $indent ) {
   $tag = "collex:federation";
   rdf_open_tag( $tag, $indent );
   echo "NINES";
   rdf_close_tag( $tag, 0 );
}

function rdf_see_also_contents( $my_post, $indent ) {
   $tag = "rdfs:seeAlso";
   $embed = "rdf:resource=" . "\"" . rdf_permalink( $my_post ) . "\"";
   rdf_open_tag( $tag, $indent, $embed );
   rdf_close_tag( $tag, 0 );
}

function rdf_title_contents( $my_post, $indent ) {
   $tag = "dc:title";
   rdf_open_tag( $tag, $indent );
   echo rdf_remove_title_tags( $my_post->post_title );
   rdf_close_tag( $tag, 0 );
}

// We pull the author as the first token from the title; this is the convention used by BRANCH. It can contain many parts and we need to
// format it as last name, all other names
function rdf_author_contents( $my_post, $indent ) {
   $tag = "role:AUT";
   rdf_open_tag( $tag, $indent );
   $arr = preg_split( "/,/", $my_post->post_title );
   $fullname_arr = preg_split( "/ /", $arr[ 0 ] );
   $lastname = $fullname_arr[ count( $fullname_arr ) - 1 ];
   $restofname = join( " ", array_slice( $fullname_arr, 0, -1, true ) );
   $author = $lastname . ", " . $restofname;
   echo $author;
   rdf_close_tag( $tag, 0 );
}

function rdf_editor_contents( $my_post, $indent ) {
   $tag = "role:EDT";
   rdf_open_tag( $tag, $indent );
   $editor = get_the_author_meta( "last_name", $my_post->post_author ) . ", " . get_the_author_meta( "first_name", $my_post->post_author );
   echo $editor;
   rdf_close_tag( $tag, 0 );
}

function rdf_publisher_contents( $my_post, $indent ) {
   $tag = "role:PBL";
   rdf_open_tag( $tag, $indent );
   echo "RaVoN";
   rdf_close_tag( $tag, 0 );
}

function rdf_translator_contents( $my_post, $indent ) {
   $tag = "role:TRL";
   rdf_open_tag( $tag, $indent );
   rdf_close_tag( $tag, 0 );
}

function rdf_dc_date_contents( $my_post, $indent ) {
   $tag = "dc:date";
   rdf_open_tag( $tag, $indent );
   rdf_newline( );
   rdf_collex_date_contents( $my_post, $indent + 1 );
   rdf_close_tag( $tag, $indent );
}

function rdf_collex_date_contents( $my_post, $indent ) {
   $tag = "collex:date";
   rdf_open_tag( $tag, $indent );
   rdf_newline( );
   rdf_date_label_contents( $my_post, $indent + 1 );
   rdf_date_value_contents( $my_post, $indent + 1 );
   rdf_close_tag( $tag, $indent );
}

// date in human readable form; e.g June 19th 2001
function rdf_date_label_contents( $my_post, $indent ) {
   $tag = "rdfs:label";
   $datebits = preg_split( "/-/", $my_post->post_modified );
   $longdate = date( "F jS, Y", mktime( 0, 0, 0, intval( $datebits[ 1 ] ), intval( $datebits[ 2 ] ), intval( $datebits[ 0 ] ) ) );
   rdf_open_tag( $tag, $indent );
   echo $longdate;
   rdf_close_tag( $tag, 0 );
}

// Extract the date from the last modified timestamp; we do not need the time piece
function rdf_date_value_contents( $my_post, $indent ) {
   $tag = "rdf:value";
   $datebits = preg_split( "/ /", $my_post->post_modified );
   rdf_open_tag( $tag, $indent );
   echo $datebits[ 0 ];
   rdf_close_tag( $tag, 0 );
}

function rdf_genre_contents( $my_post, $indent ) {
   $tag = "collex:genre";
   rdf_open_tag( $tag, $indent );
   echo "Criticism";
   rdf_close_tag( $tag, 0 );
   rdf_open_tag( $tag, $indent );
   echo "Nonfiction";
   rdf_close_tag( $tag, 0 );
}

function rdf_discipline_contents( $my_post, $indent ) {
   $tag = "collex:discipline";
   rdf_open_tag( $tag, $indent );
   echo "History";
   rdf_close_tag( $tag, 0 );
}

function rdf_type_contents( $my_post, $indent ) {
   $tag = "dc:type";
   rdf_open_tag( $tag, $indent );
   echo "InteractiveResource";
   rdf_close_tag( $tag, 0 );
}

function rdf_text_contents( $my_post, $indent ) {
   $tag = "collex:text";
   $embed = "rdf:resource=" . "\"" . rdf_permalink( $my_post ) . "\"";
   rdf_open_tag( $tag, $indent, $embed );
   rdf_close_tag( $tag, 0 );
}

function rdf_thumbnail_contents( $my_post, $indent ) {
   $tag = "collex:thumbnail";

   $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $my_post->ID ), 'thumbnail' );
   $thumbURL = $thumb[ '0' ];
   if( empty( $thumbURL ) == false ) {
      $embed = "rdf:resource=" . "\"" . $thumbURL . "\"";
      rdf_open_tag( $tag, $indent, $embed );
      rdf_close_tag( $tag, 0 );
   }
}

function rdf_header_contents( ) {
   echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
   $tag = "rdf:RDF";
   $embed = "xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n" .
"         xmlns:rdfs=\"http://www.w3.org/2000/01/rdf-schema#\"\n" .
"         xmlns:role=\"http://www.loc.gov/loc.terms/relators/\"\n" .
"         xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n" .
"         xmlns:dcterms=\"http://purl.org/dc/terms/\"\n" .
"         xmlns:collex=\"http://www.collex.org/schema#\"\n" .
"         xmlns:branch=\"http://www.collex.org/fakeschema#\"";
   rdf_open_tag( $tag, 0, $embed );
   rdf_newline( );
}

function rdf_footer_contents( ) {
   $tag = "rdf:RDF";
   rdf_close_tag( $tag, 0 );
}

function rdf_open_tag( $name, $indent, $embed = "" ) {
  $indent_str = str_repeat( "   ", $indent );
  if( empty( $embed ) ) {
     echo "$indent_str<$name>";
  } else {
     echo "$indent_str<$name $embed>";
  }
}

function rdf_close_tag( $name, $indent ) {
  $indent_str = str_repeat( "   ", $indent );
  echo "$indent_str</$name>";
  rdf_newline( );
}

// I cannot get the Wordpress permalink methods to work so create them here. Don't know if this is the right thing to do.
function rdf_permalink( $my_post ) {
   $permalink = site_url( ) . "?ps_articles=" . $my_post->post_name;
   return( $permalink );
}

// Generate a newline for nice human readable XML; comment out to disable and save a few bytes
function rdf_newline( ) {
   echo "\n";
}

// BRANCH titles can contain HTML tags; <em>, for example; just strip them out
function rdf_remove_title_tags( $title ) {
   return( strip_tags( $title ) );
}

?>
