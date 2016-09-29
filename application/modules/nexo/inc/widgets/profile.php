<?php 
use Carbon\Carbon;

$this->load->helper('nexopos');
?>
<div class="box box-widget widget-user-2" data-meta-namespace="<?php echo store_prefix() ;?>nexo_profile"> 
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header bg-<?php echo $this->users->get_meta('theme-skin') ? str_replace('skin-', '', $this->users->get_meta('theme-skin')) : 'primary';?>">
        <div class="widget-user-image"> <img class="img-circle" src="<?php echo User::get_gravatar_url();?>" alt="User Avatar"> </div>
        <!-- /.widget-user-image -->
        <h3 class="widget-user-username"><?php echo User::pseudo();?></h3>
        <h5 class="widget-user-desc">
            <?php 
      $Groups    =     Group::get();
      echo $Groups[0]->definition;
      ?>
        </h5>
        <div class="box-tools pull-right">
            <button type="button" class="btn bg-primary btn-sm bg-blue-active" data-reload-widget="profile" style="position:absolute;top:10px;right:10px;background: rgba( 0, 0, 0, 0.3);"><i class="fa fa-refresh"></i> </button>
        </div>
    </div>
    <?php
    // Fetch from cache
    $Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_' . store_prefix() ));
    $Report        =    $Cache->get('profile_widget_cashier_sales_' . User::id());
    $this->load->model('Nexo_Misc');
    ?>
    <?php // echo intval( @$Report[ 'sales_numbers' ] );?>
    <div class="box-footer no-padding">
        <ul class="nav nav-stacked">
            <li><a href="javascript:void(0)">
                <?php _e('Ventes réalisées ce mois', 'nexo');?>
                <span class="pull-right sales_numbers_this_month"><?php echo intval(@$Report[ 'sales_numbers_this_month' ]);?></span></a></li>
            <!-- badge bg-blue  -->
            <li><a href="javascript:void(0)">
                <?php _e('Ventes générales', 'nexo');?>
                <span class="pull-right sales_numbers"><?php echo intval(@$Report[ 'sales_numbers' ]);?></span></a></li>
            <!-- badge bg-blue  -->
            <li> <a href="javascript:void(0)">
                <?php _e('Chiffre d\'affaire réalisé ce mois', 'nexo');?>
                <span class="pull-right sales_income_this_month"> <?php echo $this->Nexo_Misc->cmoney_format(__floatval(@$Report[ 'sales_income_this_month' ]));?> </span> </a> </li>
            <!-- badge bg-aqua  -->
            <li> <a href="javascript:void(0)">
                <?php _e('Chiffre d\'affaire général', 'nexo');?>
                <span class="pull-right sales_income"><?php echo $this->Nexo_Misc->cmoney_format(__floatval(@$Report[ 'sales_income' ]));?></span> </a> </li>
            <!-- badge bg-aqua  -->
        </ul>
    </div>
</div>
<?php if ($Report == false): ?>
<script type="text/javascript">
  "use strict";
	$( document ).ready(function(e) {
		Nexo_Profile_Widget.load();
	});
  </script>
<?php endif;?>
<script type="text/javascript">
  "use strict";
  var Nexo_Profile_Widget	=	new function(){
	  this.load				=	function( arg ) {
		  var refresh_it	=	arg == 'refresh' ? '?refresh=true' : '?load=cache';
		  // Start of Day is unused now.
		  var start_of_day		=	'<?php echo Carbon::parse(date_now())->subDays(7)->startOfDay()->toDateTimeString();?>';
		  var end_of_day		=	'<?php echo Carbon::parse(date_now())->endOfDay()->toDateTimeString();?>';
		  var cashier_id		=	<?php echo User::id();?>;
		  var post_data			=	_.object( [ 'start_of_day', 'end_of_day', 'cashier_id' ], [ start_of_day, end_of_day, cashier_id ] );
		  
		  $.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'cashier_sales' ));?>' + refresh_it + '<?php echo store_get_param( '&' );?>', {
			  beforeSend	:	function(){
				  $( '.sales_numbers' ).html( 0 );
				  $( '.sales_income' ).html( NexoAPI.DisplayMoney( 0 ) );
				  $( '.sales_numbers_this_month' ).html( 0 );
				  $( '.sales_income_this_month' ).html( NexoAPI.DisplayMoney( 0 ) );
			  },
			  success		:	function( data ){
				  if( _.isObject( data ) ){
					  $( '.sales_numbers' ).html( data.sales_numbers );
					  $( '.sales_income' ).html( NexoAPI.DisplayMoney( data.sales_income ) );
					  $( '.sales_numbers_this_month' ).html( data.sales_numbers_this_month );
					  $( '.sales_income_this_month' ).html( NexoAPI.DisplayMoney( data.sales_income_this_month ) );
				  }
			  },
			  type			:	'POST',
			  data			:	post_data,
			  dataType		:	'json'
		  });
	  }
  }
	$( document ).ready(function(e) {
		$( '[data-reload-widget="profile"]' ).bind( 'click', function(){
			Nexo_Profile_Widget.load( 'refresh' );
		});
	});
  </script>