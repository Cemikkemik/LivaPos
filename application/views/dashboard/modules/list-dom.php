<div class="row">
	<?php
	global $Options;
	$modules			=	Modules::get();
	$modules_status		=	$this->options->get( 'modules_status' );// get whether an update is available
	foreach( force_array( $modules ) as $_module )
	{
		if( isset( $_module[ 'application' ][ 'details' ][ 'namespace' ] ) )
		{
			$module_namespace		=	$_module[ 'application' ][ 'details' ][ 'namespace' ];
	?>
	<div class="col-lg-3">
   	<div class="box <?php echo ( riake( 'highlight', $_GET ) == $_module[ 'application' ][ 'details' ][ 'namespace' ] ) ? 'box-primary' : '' ;?> " id="#module-<?php echo $_module[ 'application' ][ 'details' ][ 'namespace' ];?>" <?php if( ! Modules::is_active( $module_namespace ) ):?> style="background:#F3F3F3;"<?php endif;?>>
      	<div class="box-header">
         	<h3 class="box-title"><?php echo isset( $_module[ 'application' ][ 'details' ][ 'name' ] ) ? $_module[ 'application' ][ 'details' ][ 'name' ] : __( 'Tendoo Extension' );?> &mdash; <?php echo 'v' . ( isset( $_module[ 'application' ][ 'details' ][ 'version' ] ) ? $_module[ 'application' ][ 'details' ][ 'version' ] : 0.1 );?></h3>
         </div>
         <div class="box-body" style="height:100px;"><?php echo isset( $_module[ 'application' ][ 'details' ][ 'description' ] ) ? $_module[ 'application' ][ 'details' ][ 'description' ] : '';?> </div>
         <div class="box-footer" <?php if( ! Modules::is_active( $module_namespace ) ):?> style="background:#F3F3F3;"<?php endif;?>>
             <div class="box-tools pull-right">
            <?php
				if( isset( $_module[ 'application' ][ 'details' ][ 'main' ] ) ){ // if the module has a main file, it can be activated
					if( ! Modules::is_active( $module_namespace ) )
					{
					?>
					  <a href="<?php echo site_url( array( 'dashboard' , 'modules' , 'enable' , $module_namespace ) );?>" class="btn btn-sm btn-default btn-box-tool" data-action="enable"><i style="font-size:20px;" class="fa fa-toggle-on"></i> Enable</a>
					<?php
					}
					else
					{
					?>
					  <a href="<?php echo site_url( array( 'dashboard' , 'modules' , 'disable' , $module_namespace ) );?>" class="btn btn-sm btn-default btn-box-tool" data-action="disable"><i style="font-size:20px;" class="fa fa-toggle-off"></i> Disable</a>
					<?php
					}
				}
				?>
              <a href="<?php echo site_url( array( 'dashboard' , 'modules' , 'remove' , $module_namespace ) );?>" class="btn btn-sm btn-default btn-box-tool" data-action="uninstall"><i style="font-size:20px;" class="fa fa-trash"></i> <?php _e( 'Remove' );?></a>
              
              <?php if( intval( riake( 'webdev_mode' , $Options ) ) == true ):?>
              <a href="<?php echo site_url( array( 'dashboard' , 'modules' , 'extract' , $module_namespace ) );?>" class="btn btn-sm btn-default btn-box-tool" data-action="extract"><i style="font-size:20px;" class="fa fa-file-zip-o"></i> <?php _e( 'Extract' );?></a>
              <?php endif;?>
              <?php
			  ?>
              <!-- <button class="btn btn-default btn-box-tool" data-action="update"><i style="font-size:20px;" class="fa fa-refresh"></i></button>-->
            </div>
         </div>
      </div>
   </div>
   <?php
		}
	}
	?>
</div>
<script>
$('[data-action="uninstall"]').bind('click', function(){
	if( confirm( '<?php _e( 'Do you really want to delete this module ?' );?>' ) ){
		return true;
	}
	return false;
});
</script>