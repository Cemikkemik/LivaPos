<div class="row">
    <div class="col-lg-12">
        <div class="btn-group btn-group-lg btn-group-justified" role="group" aria-label="...">
            <a href="<?php echo site_url( array( 'dashboard', 'nexo', 'commandes', 'lists', 'v2_checkout?order_type=dinein' ) );?>" class="btn btn-default">
            	<i class="fa fa-cutlery"></i> 
				<?php _e( 'Dine In', 'nexo_restaurant' );?>
			</a>
            <a href="<?php echo site_url( array( 'dashboard', 'nexo', 'commandes', 'lists', 'v2_checkout?order_type=takeaway' ) );?>" class="btn btn-default">
            	<i class="fa fa-sign-out"></i>
				<?php _e( 'Take Away', 'nexo_restaurant' );?>
			</a>
            <a href="<?php echo site_url( array( 'dashboard', 'nexo', 'commandes', 'lists', 'v2_checkout?order_type=delivery' ) );?>" class="btn btn-default">
            	<i class="fa fa-truck"></i> 
				<?php _e( 'Delivery', 'nexo_restaurant' );?>
			</a>
        </div>
    </div>
</div>
