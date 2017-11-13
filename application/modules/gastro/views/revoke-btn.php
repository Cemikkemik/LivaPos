<?php
global $Options;
?>
<a 
    
href="<?php echo tendoo_config( 'nexo', 'store_url' );?>api/google-revoke?app_code=<?php echo @$Options[ store_prefix() . 'nexopos_app_code' ];?>
&request_uri=<?php echo urlencode( site_url([ 'dashboard', store_slug(), 'gastro', 'revoke' ] ) . '?app_code=' . @$Options[ store_prefix() . 'nexopos_app_code' ] );?>"
class="btn btn-danger" onclick="return confirm('<?php echo __( 'Would you like to revoke the connection ?', 'gastro' );?>' )"><?php echo __( 'Revoke Connection', 'gastro' );?></a>