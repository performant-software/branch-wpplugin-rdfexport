<?php
/*
 *
 *
 */

class PsRDFExport {

public function __construct( $params ) {

   add_action( 'admin_menu', array( $this, 'rdfexport_add_settings_page' ) );
   register_uninstall_hook( __FILE__, array( $this, 'uninstall_rdfexport' ) );
}

public function uninstall_rdfexport( ) {
   delete_option( 'ps-rdfexport-slug' );
}

public function rdfexport_add_settings_page( ) {
   add_submenu_page( 'tools.php', 'Export to RDF', 'Export to RDF', 'manage_options', 'ps-rdfexport-slug', array( $this, 'rdfexport_page' ) );
}

public function rdfexport_page( ) {
?>
<div class="wrap">
<form method="get" id="rdfexport_form">
  <input type="hidden" name="rdfdownload" value="true">
  <h2>Export to RDF</h2>
  <p>When you click the button below WordPress will create an XML file for you to save to your computer.</p>
  <p>This format is called RDF and is designed to be imported into the NINES system.</p>
  <p class="submit">
     <input type="submit" name="submit" class="button-secondary" value="Export to RDF" />
  </p>
</form>
</div>
<?php
}
}

require_once( 'ps-rdfexport.php' );

?>
