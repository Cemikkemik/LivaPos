     
  <div class="box box-widget widget-user-2" data-meta-namespace="nexo_profile">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header bg-<?php echo $this->users->get_meta( 'theme-skin' ) ? str_replace( 'skin-', '', $this->users->get_meta( 'theme-skin' ) ) : 'primary';?>">
      <div class="widget-user-image">
        <img class="img-circle" src="<?php echo User::get_gravatar_url();?>" alt="User Avatar">
      </div>
      <!-- /.widget-user-image -->
      <h3 class="widget-user-username"><?php echo User::pseudo();?></h3>
      <h5 class="widget-user-desc">
	  <?php 
	  $Groups	=	 Group::get();
	  echo $Groups[0]->definition;
	  ?>
		</h5>
    </div>
    <div class="box-footer no-padding">
      <ul class="nav nav-stacked">
        <li><a href="#"><?php _e( 'Ventes réalisées', 'nexo' );?> <span class="pull-right badge bg-blue">31</span></a></li>
        <li><a href="#"><?php _e( 'Date d\'inscription', 'nexo' );?> <span class="pull-right badge bg-aqua">5</span></a></li>
      </ul>
    </div>
  </div>