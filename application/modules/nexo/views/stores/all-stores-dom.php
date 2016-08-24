<?php 
$this->load->helper( 'text' );
?>
<div class="row">
    <?php if( $stores ):?>
    <?php foreach( $stores as $store ):?>
    <div class="col-lg-3 col-sm-4">
        <div class="box box-widget widget-user"> 
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-black" style="background: url('<?php echo upload_url() . 'stores/' . $store[ 'IMAGE' ];?>') center center;">
                <h3 class="widget-user-username" style="background:rgba(0,0,0,0.5);float:left;padding:5px;"><?php echo xss_clean( $store[ 'NAME' ] );?></h3>
                <h5 class="widget-user-desc"><?php echo xss_clean( character_limiter( $store[ 'DESCRIPTION' ], 10 ) );?></h5>
            </div>
            <!-- <div class="widget-user-image"> <img class="img-circle" src="<?php echo module_url( 'nexo' ) . '/images/store.png';?>" alt="User Avatar"> </div>-->
            <div class="box-footer" style="padding:0;">
                <div class="row">
                    <div class="col-sm-6 border-right">
                        <div class="description-block">
                        	<a href="<?php echo site_url( array( 'dashboard', 'stores', $store[ 'ID' ] ) );?>" class="btn btn-lg btn-primary"><i class="fa fa-sign-in"></i> <?php _e( 'Entrer', 'nexo' );?></a>
						</div>
                        <!-- /.description-block --> 
                    </div>
                    <div class="col-sm-6">
                        <div class="description-block">
                        	<a href="#" class="btn btn-lg btn-primary"><?php _e( 'Accéder', 'nexo' );?></a>
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
