<?php 
$this->load->helper( 'text' );
global $Options;
?>
<div class="row">
    <?php if( $stores ):?>
    <?php foreach( $stores as $store ):?>
    	<?php
		$register_enabled	=	@$Options[ 'store_' . $store[ 'ID' ] . '_nexo_enable_registers' ];
		?>
    <div class="col-lg-3 col-sm-4">
        <div class="box box-widget widget-user"> 
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-black" style="background: url('<?php echo upload_url() . 'stores/' . $store[ 'IMAGE' ];?>') center center no-repeat;height:150px;">
                <h3 class="widget-user-username" style="background:rgba(0,0,0,0.5);display:inline-block;padding:5px;"><?php echo xss_clean( $store[ 'NAME' ] );?></h3><br />
                
                <?php if( strlen( $store[ 'DESCRIPTION' ] ) > 0 ):?>
                <h5 class="widget-user-desc" style="background:rgba(0,0,0,0.5);display:inline-block;padding:5px;width;100%"><?php echo xss_clean( $store[ 'DESCRIPTION' ] );?></h5>
                <?php endif;?>
            </div>
            <!-- <div class="widget-user-image"> <img class="img-circle" src="<?php echo module_url( 'nexo' ) . '/images/store.png';?>" alt="User Avatar"> </div>-->
            <div class="box-footer" style="padding:0;">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="description-block">
                        <?php
						if( ( User::in_group( 'shop_cashier' ) || User::in_group( 'shop_tester' ) ) ) {
							if( $register_enabled == 'oui' ) {
								$store_url	=	site_url( 
									array( 'dashboard', 'stores', $store[ 'ID' ], 'nexo', 'registers', 'lists' ) 
								);
							} else {
								$store_url	=	site_url( 
									array( 'dashboard', 'stores', $store[ 'ID' ], 'nexo', 'registers', '__use', 'default' ) 
								);
							}
						} else {
							$store_url	=	site_url( array( 'dashboard', 'stores', $store[ 'ID' ] ) );
						}						
						?>
                        <a href="<?php echo $store_url;?>" class="btn btn-lg btn-primary"><i class="fa fa-sign-in"></i> <?php _e( 'Entrer', 'nexo' );?></a>
						</div>
                        <!-- /.description-block --> 
                    </div>
                    <!-- /.col --> 
                </div>
                <!-- /.row --> 
            </div>
        </div>
    </div>
    <?php endforeach;?>
    <?php endif;?>
</div>
