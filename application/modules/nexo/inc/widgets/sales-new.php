<?php
use Carbon\Carbon;
?>
<!-- bg-<?php echo $this->users->get_meta( 'theme-skin' ) ? str_replace( 'skin-', '', $this->users->get_meta( 'theme-skin' ) ) : 'primary';?> -->
<div class="box box-solid bg-blue" data-meta-namespace="nexo_sales_new">
    <div class="box-header ui-sortable-handle" style="cursor: move;"> <i class="fa fa-money"></i>
        <h3 class="box-title">
            <?php _e( 'Melleurs articles', 'nexo' );?>
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn bg-blue-active btn-sm" data-widget="collapse"><i class="fa fa-minus"></i> </button>
            <button type="button" class="btn bg-blue-active btn-sm" data-reload-widget="sale_new"><i class="fa fa-refresh"></i> </button>
        </div>
    </div>
    <div class="box-body border-radius-none" style="height:300px;">
        <div id="new_sales" class="chart"></div>
        <div class="text-center"><?php _e( 'Meilleurs produits ces 7 derniers jours', 'nexo' );?></div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer no-border">
        <div class="row">
        	<br />
            <div class="col-xs-6 text-center" style="border-right: 1px solid #f4f4f4">
                <input type="text" class="knob" value="45" data-width="90" data-height="90" data-fgColor="#3c8dbc" data-readonly="true">
                <div class="knob-label"><h4><?php _e( 'Stock restant (%)', 'nexo' );?></h4></div>
            </div>
            <!-- ./col -->
            <div class="col-xs-6 text-center" style="border-right: 1px solid #f4f4f4">
                <input type="text" class="knob" value="30" data-width="90" data-height="90" data-fgColor="#3c8dbc" data-readonly="true">
                <div class="knob-label"><h4>New Visitors</h4></div>
            </div>
            <!-- ./col --> 
        </div>
        <!-- /.row --> 
    </div>
    <!-- /.box-footer --> 
</div>
<?php 
$Cache		=	new CI_Cache( array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_') );
$this->load->config( 'nexo' );

if( ! $Cache->get( 'sales_new_widget_item_sales' ) ) {
	
} 
?>
<script type="text/javascript">
"use strict";
var Nexo_Sales_Widget		=	new function(){
	this.load				=	function( arg ){
		var colors				=	[ '#02B3E7', '#CFD3D6', '#736D79', '#776068', '#EB0D42', '#FFEC62', '#04374E' ];
		var refresh_it			=	arg == 'refresh' ? '?refresh=true' : '';
		var start_date			=	'<?php echo Carbon::parse( date_now() )->subDays( 7 )->startOfDay()->toDateTimeString();?>';
		var end_date			=	'<?php echo Carbon::parse( date_now() )->endOfDay()->toDateTimeString();?>';
		var limit				=	7;
		var post_data			=	_.object( [ 'start_date', 'end_date', 'limit' ], [ start_date, end_date, limit ] );
		$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'item_sales' ) );?>' + refresh_it, {
			data		:	post_data,
			type		:	'POST',
			dataType	:	"json",
			success		:	function( data ) {
				$( '#new_sales' ).fadeOut( 500, function(){
					
					$( this ).remove();
					$( '.pieTip' ).remove();
					
					$( '[data-meta-namespace="nexo_sales_new"]' ).find( '.box-body' ).prepend( '<div id="new_sales" class="chart"></div>' );
					
					var _i			=	0;
					var ItemsObject	=	new Object;
					_.each( data, function( value, key ) {
						if( typeof ItemsObject[ value.CODEBAR ] == 'undefined' ) {
							ItemsObject[ value.CODEBAR ] 	=	{
								value			:			0,
								title			:			value.DESIGN,
								color			:			colors[ _i ]
							};
						}
						
						ItemsObject[ value.CODEBAR ].value	+=	parseInt( value.QUANTITE );
						_i++;
					});
					
					$("#new_sales").drawPieChart( _.toArray( ItemsObject ) );
				});
			}
		});
	}
}

$(function(){
	Nexo_Sales_Widget.load();
	$( '[data-reload-widget="sale_new"]').bind( 'click', function(){
		Nexo_Sales_Widget.load( 'refresh' )
	});
});
</script>