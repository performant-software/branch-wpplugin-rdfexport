<?php
/*
 *
 *
 */

require_once( ABSPATH . "wp-includes/pluggable.php" );

if( isset( $_GET['rdfdownload'] ) ) {
   rdf_export( );
   exit;
}

function rdf_export( ) {

   $sitename = sanitize_key( get_bloginfo( 'name' ) );
   if ( ! empty($sitename) ) $sitename .= '.';
   $filename = $sitename . 'rdf.' . date( 'Y-m-d' ) . '.xml';

   header( 'Content-Description: File Transfer' );
   header( 'Content-Disposition: attachment; filename=' . $filename );
   header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

   rdf_header_contents( );

   /* global $my_post; */
   $qargs = array( 'post_type' => 'ps_articles',
                   'posts_per_page' => -1 );
   $query= new WP_Query( $qargs );
   if ( $query->have_posts( ) ) {
      $all_posts = $query->get_posts( );
      foreach( $all_posts as $my_post ) {
         rdf_object_contents( $my_post );
      }
   }

   wp_reset_postdata( );
   rdf_footer_contents( );
}

function process_post( $my_post ) {
?>
   <!-- main link to object URI (must remain stable) -->
   <branch:object rdf:about="http://www.branchcollective.org/?ps_articles=peter-logan-on-culture-edward-b-tylors-primitive-culture-1871">

      <!-- identify source archive by abbreviation - no punctuation, please! -->
      <collex:archive>branch</collex:archive>

      <!-- NINES, 18thConnect or MESA? -->
      <collex:federation>NINES</collex:federation>

      <!-- link to object URL -->
      <rdfs:seeAlso rdf:resource="http://www.branchcollective.org/?ps_articles=peter-logan-on-culture-edward-b-tylors-primitive-culture-1871"/>

      <!-- document title -->
      <dc:title>Peter Melville Logan, “On Culture: Edward B. Tylor’s Primitive Culture, 1871″</dc:title>

      <!-- roles: author, editor, publisher, translator -->
      <role:AUT>Logan, Peter Melville</role:AUT>
      <role:EDT>Felluga, Dino Franco</role:EDT>
      <role:PBL>RaVoN</role:PBL>
      <role:TRL/>

      <!-- for dates, if you have both computational and human readable dates, we want both -->
      <dc:date>
         <collex:date>
            <rdfs:label>January 14, 2011</rdfs:label>
            <rdf:value>2011-01-14</rdf:value>
         </collex:date>
      </dc:date>
      <!-- otherwise: <dc:date>2011</dc:date>-->

      <collex:genre>Criticism</collex:genre>
      <collex:genre>Nonfiction</collex:genre>
      <collex:discipline>History</collex:discipline>
      <dc:type>InteractiveResource</dc:type>

      <!-- URL for full text indexing -->
      <collex:text rdf:resource="http://www.branchcollective.org/?ps_articles=peter-logan-on-culture-edward-b-tylors-primitive-culture-1871"/>

      <!-- identifying thumbnail for the work? if not, choose default for project -->
      <collex:thumbnail rdf:resource="http://www.branchcollective.org/wp-content/uploads/2012/01/Edward_Burnett_Tylor.jpg"/>

   </branch:object>
<?php
}

function rdf_object_contents( $my_post ) {
   
   $indent = 1;
   rdf_open_object_tag( $my_post, $indent );

   $indent = 2;
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

function rdf_author_contents( $my_post, $indent ) {
   $tag = "role:AUT";
   rdf_open_tag( $tag, $indent );
   $arr = preg_split( "/,/", $my_post->post_title );
   $name = preg_split( "/ /", $arr[ 0 ] );
   $author = $name[ 1 ] . ", " . $name[ 0 ];
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
   rdf_colex_date_contents( $my_post, $indent + 1 );
   rdf_close_tag( $tag, $indent );
}

function rdf_colex_date_contents( $my_post, $indent ) {
   $tag = "colex:date";
   rdf_open_tag( $tag, $indent );
   rdf_newline( );
   /* rdf_date_label_contents( $my_post, $indent + 1 ); */
   rdf_date_value_contents( $my_post, $indent + 1 );
   rdf_close_tag( $tag, $indent );
}

function rdf_date_label_contents( $my_post, $indent ) {
   $tag = "rdfs:label";
   rdf_open_tag( $tag, $indent );
   rdf_close_tag( $tag, 0 );
}

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
   $tag = "colex:discipline";
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
   $tag = "rdf:RDF";
   rdf_open_tag( $tag, 0 );
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

function rdf_permalink( $my_post ) {
   $permalink = site_url( ) . "?ps_articles=" . $my_post->post_name;
   return( $permalink );
}

function rdf_newline( ) {
   echo "\n";
}

function rdf_remove_title_tags( $title ) {
   return( strip_tags( $title ) );
}

?>
