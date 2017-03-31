<?php
// Creating Index Cards
if( User::in_group( 'shop_manager' ) || User::in_group( 'master' ) || User::in_group( 'shop_tester' ) ) {
    ?>
<div class="content">
    <div class="row">
        <div class="col-md-3 global_stores_card">
            <!-- Info Boxes Style 2 -->
            <div class="info-box bg-purple"> <span class="info-box-icon"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content"> <span class="info-box-text">
                    <?php _e( 'Boutiques', 'nexo' );?>
                    </span> <span class="info-box-number store-number">00</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <span class="progress-description"></span> </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-md-3 global_cashiers_card">
            <!-- /.info-box -->
            <div class="info-box bg-yellow"> <span class="info-box-icon"><i class="fa fa-users"></i></span>
                <div class="info-box-content"> <span class="info-box-text">
                    <?php _e( 'Caissiers', 'nexo' );?>
                    </span> <span class="info-box-number cashiers">00</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <span class="progress-description"></span> </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-md-3 global_income_card">
            <!-- /.info-box -->
            <div class="info-box bg-green"> <span class="info-box-icon"><i class="fa fa-money"></i></span>
                <div class="info-box-content"> <span class="info-box-text">
                    <?php _e( 'Chiffre d\'affaire', 'nexo' );?>
                    </span> <span class="info-box-number global-income">0.00</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <span class="progress-description"></span> </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-md-3 global_expense_card">
            <!-- /.info-box -->
            <div class="info-box bg-red"> <span class="info-box-icon"><i class="fa fa-thumbs-down"></i></span>
                <div class="info-box-content"> <span class="info-box-text">
                    <?php _e( 'Dépenses', 'nexo' );?>
                    </span> <span class="info-box-number">0.00</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <span class="progress-description"></span> </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <?php echo $this->events->do_action( 'after_main_store_card' );?>
    </div>
</div>
<script type="text/javascript">
"use strict";

var MainStats		=	new function(){

	this._reset		=	function(){
		this.Stores		=	new Array;
		this.Income		=	0;
		this.Expenses	=	0;
	};

	this.load		=	function(){
		this._reset();

		$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'stores' ) );?>', {
			success	:	function( e ) {

				// console.log( e.length );

				if( ! _.isEmpty( e ) ) {
					MainStats.Stores	=	e;

					$( '.store-number' ).html( e.length );

					MainStats.LoadOtherStats();
				}
			},
			dataType	:	"json"
		});

	}

	/**
	 * Load
	**/

	this.LoadOtherStats	=	function(){
		// Load Income
		function load_data( type, _obj, whenFinished ) {

			if( type == 'income' ) {

				if( ! _.isEmpty( _obj ) ) {
					while( 	! _.isEmpty( _obj )	) {

						$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'order' ) );?>' + '?store_id=' + _obj[0].ID, {
							success		:	function( data ){

								if( ! _.isEmpty( data ) ) {

									_.each( data, function( value ){
										if( value.TYPE == 'nexo_order_comptant' ) {
											MainStats.Income	+=	parseFloat( value.TOTAL );
										}
									});
								}

								_obj	=	_obj.splice(1);

								load_data( 'income', _obj, whenFinished );
							},
							dataType	:	'json'
						});

						break;
					}
				} else {
					if( typeof whenFinished == 'function' ) {
						whenFinished();
					}
				}

			} else if( type == 'expense' ) {

				if( ! _.isEmpty( _obj ) ) {
					while( 	! _.isEmpty( _obj )	) {

						$.ajax( '<?php echo site_url( array( 'nexo_premium', 'expenses' ) );?>' + '?store_id=' + _obj[0].ID, {
							success		:	function( data ){

								if( ! _.isEmpty( data ) ) {

									_.each( data, function( value ){
										MainStats.Expenses	+=	parseInt( value.MONTANT );
									});
								}

								_obj	=	_obj.splice(1);

								load_data( 'expense', _obj, whenFinished );
							},
							dataType	:	'json'
						});

						break;
					}
				} else {
					if( typeof whenFinished == 'function' ) {
						whenFinished();
					}
				}
			}
		}

		load_data( 'income', _.values( this.Stores ), function(){
			$( '.global-income' ).html( NexoAPI.DisplayMoney( MainStats.Income ) );
		});

		load_data( 'expense', _.values( this.Stores ), function(){
			$( '.global_expense_card .info-box-number' ).html( NexoAPI.DisplayMoney( MainStats.Expenses ) );
		});

		$( '.global_cashiers_card .info-box-number' ).html( '<?php echo count( $this->auth->list_users( 'shop_cashier' ) );?>' );
	}
};

$(document).ready(function(e) {
    $( '.content-header' )
		.find( 'h1' )
		.append( ' <a href="javascript:void(0)" class="btn btn-sm btn-primary pull-right refresh_main_stats"><i class="fa fa-refresh"></i></a> ' );

	$( '.refresh_main_stats' ).bind( 'click', function(){
		MainStats.load();
	});

	MainStats.load();
});



</script>
<?php }
