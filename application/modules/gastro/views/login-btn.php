<?php
global $Options;
$module         =   Modules::get( 'gastro' );
?>
<a href="<?php echo tendoo_config( 'nexo', 'store_url' );?>api/auth?
host=<?php echo $_SERVER[ 'SERVER_NAME' ];?>
&ip=<?php echo $_SERVER[ 'SERVER_ADDR' ];?>
&app_name=<?php echo 'nexo'; //$module[ 'application' ][ 'namespace' ];?>
&app_version=<?php echo $module[ 'application' ][ 'version' ];?>
&gcp_proxy=<?php echo @$Options[ store_prefix() . 'printer_gpc_proxy' ];?>
&envato_licence=<?php echo @$Options[ store_prefix() . 'restaurant_envato_licence' ];?>
&request_uri=<?php echo urlencode( site_url([ 'dashboard', store_slug(), 'gastro', 'callback' ]) );?>" 
class="btn btn-primary"><?php echo __( 'Connect to Google', 'gastro' );?></a>