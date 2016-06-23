<?php
! defined('APPPATH') ? die() : null;

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'col_id'    =>    1,
    'namespace'    =>    'restore',
    'gui_saver'    =>    true,
    'custom'    =>    array(
        'action'    =>    ''
    )
));

ob_start();

echo tendoo_warning(__('La restauration supprime le contenu actuel et le remplace par celui du fichier de sauvegarde. Vous devez envisager de sauvegarder l\'état actuel de la boutique avant de faire une restauration. Seuls les fichiers au format ".zip" sont acceptés.', 'nexo_premium'));

$error        =    $this->upload->display_errors('<span>', '</span>');
if (isset($_FILES[ 'restore_file' ]) && ! empty($erro)) {
    echo tendoo_error($error);
}
?>
<form method="post" enctype="multipart/form-data">
<div class="input-group">
  <span class="input-group-addon" id="basic-addon1"><?php _e('Prefixe à utiliser', 'nexo_premium');?></span>
  <input type="text" name="db_prefix" class="form-control" placeholder="<?php _e('Prefixe à remplacer', 'nexo_premium');?>" aria-describedby="basic-addon1">
</div>
<br />
<div class="input-group">	
  <input type="file" name="restore_file" class="form-control" placeholder="<?php _e('Veuillez envoyer le fichier de restauration', 'nexo_premium');?>" aria-describedby="basic-addon2">
  <span class="input-group-btn">
    <button class="btn btn-default" type="submit"><?php _e('Restaurer', 'nexo_premium');?></button>
  </span>
</div>
</form>
<?php
if (isset($queries_nbr)) {
    ?>
<br />
<div class="progress active query_status">
    <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
        <span class="sr-only">0% Complete (success)</span>
    </div>
</div>
<script type="text/javascript">
"use strict";

var totalQueries	=	<?php echo $queries_nbr;
    ?>;
var currentIndex	=	1;
var RunQueries		=	function(){
	$.ajax( '<?php echo site_url(array( 'nexo_premium', 'run_restore_query' ));
    ?>' + '/' + currentIndex + '/<?php echo $table_prefix;
    ?>', {
		success		:	function( value ) {
			if( currentIndex < ( totalQueries - 1 ) ) {
				var Percent		=	Math.ceil( currentIndex * 100 / totalQueries );
				$( '.query_status' ).find( '.progress-bar' ).attr( 'aria-valuenow', Percent );
				$( '.query_status' ).find( '.progress-bar' ).width( Percent + '%' );
				currentIndex++;
				RunQueries();
			} else if( currentIndex < totalQueries ) {

				$( '.query_status' ).find( '.progress-bar' ).removeClass( 'progress-bar-primary' );
				$( '.query_status' ).find( '.progress-bar' ).addClass( 'progress-bar-success' );
				
				var Percent		=	Math.ceil( currentIndex * 100 / totalQueries );
				
				$( '.query_status' ).find( '.progress-bar' ).attr( 'aria-valuenow', Percent );
				$( '.query_status' ).find( '.progress-bar' ).width( Percent + '%' );
				currentIndex++;
				RunQueries();
				
				setTimeout( function(){
					$( '.query_status' ).find( '.progress-bar' ).attr( 'aria-valuenow', 0 );
					$( '.query_status' ).find( '.progress-bar' ).width( 0 + '%' );
					$( '.query_status' ).after( '<div class="restore_notice"><?php echo addslashes($this->lang->line('restore_successful'));
    ?></p>' ); 
				}, 2000 );
			}
		}
	});
}
if( totalQueries > currentIndex ) {
	// First Run
	RunQueries();
}

</script>
    <?php

}

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    ob_get_clean()
), 'restore', 1);

$this->Gui->output();
