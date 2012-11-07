<?php
/*
 *
 *
 */

class PsRDFExport {

public function __construct($params) {

   add_action( 'admin_menu', array( $this, 'rdfexport_add_settings_page' ) );
   register_uninstall_hook( __FILE__, array( $this, 'uninstall_rdfexport' ) );
}

public function uninstall_rdfexport( ) {
   delete_option( 'ps-rdfexport-slug' );
}

public function rdfexport_add_settings_page( ) {
   add_options_page( 'RDF Export', 'RDF Export', 'manage_options', 'ps-rdfexport-slug', array( $this, 'rdfexport_page' ) );
}

public function rdfexport_page( ) {
?>
<div class="wrap">
<form method="post" id="rdfexport_form" action="xxx">
  <h3>RDF Export</h3>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque commodo, nunc eu suscipit egestas, risus lectus pellentesque leo, ac pretium eros eros id turpis. Sed purus turpis, varius non tempor eget, tristique vitae risus. Sed dui enim, mattis vitae pulvinar vel, aliquam nec neque. Curabitur feugiat cursus arcu, vel molestie mi dignissim id. Nullam auctor tincidunt purus quis auctor.</p>
  <p class="submit">
     <input type="submit" name="submit" class="button-primary" value="Export to RDF" />
  </p>
</form>
</div>
<?php
}

}
?>
