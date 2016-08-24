<div class="row">
    <div class="col-lg-12">
        <div class="btn-group btn-group-lg btn-group-justified" role="group" aria-label="...">
            <a href="<?php echo site_url( array( 'sign-out' ) );?>" class="btn btn-warning">
            	<i class="fa fa-power-off"></i> 
				<?php _e( 'Log out', 'nexo_restaurant' );?>
			</a>
            <a href="<?php echo site_url( array( 'dashboard', 'users', 'profile' ) );?>" class="btn btn-primary">
            	<i class="fa fa-cogs"></i>
				<?php _e( 'Account Settings', 'nexo_restaurant' );?>
			</a>
        </div>
    </div>
</div>
