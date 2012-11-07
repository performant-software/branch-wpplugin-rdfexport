<?php
/*
 *
 *
 */

if( isset( $_GET['rdfdownload'] ) ) {
   rdf_export( );
}

function rdf_export( ) {


   $sitename = sanitize_key( get_bloginfo( 'name' ) );
   if ( ! empty($sitename) ) $sitename .= '.';
   $filename = $sitename . 'rdf.' . date( 'Y-m-d' ) . '.xml';

   header( 'Content-Description: File Transfer' );
   header( 'Content-Disposition: attachment; filename=' . $filename );
   header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
?>
<!-- Set up namespaces: it isn't necessary to reference an actual XSD schema to validate the RDF-use the "xmlns" value only to establish a unique namespace-->
<rdf:RDF>
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
</rdf:RDF>
<?php
}

?>
