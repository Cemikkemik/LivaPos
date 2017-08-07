<?php
use Carbon\Carbon;

$Cache        =    new CI_Cache(array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_' . store_prefix() ));
$this->load->config('nexo');
?>
<!-- bg-<?php echo $this->users->get_meta('theme-skin') ? str_replace('skin-', '', $this->users->get_meta('theme-skin')) : 'primary';?> -->
<div class="box box-solid bg-blue" data-meta-namespace="<?php echo store_prefix() ;?>nexo_sales_new">
    <div class="box-header ui-sortable-handle" style="cursor: move;"> <i class="fa fa-money"></i>
        <h3 class="box-title">
            <?php _e('Meilleurs articles', 'nexo');?>
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn bg-blue-active btn-sm" data-widget="collapse"><i class="fa fa-minus"></i> </button>
            <button type="button" class="btn bg-blue-active btn-sm" data-reload-widget="sale_new"><i class="fa fa-refresh"></i> </button>
        </div>
    </div>
    <div class="box-body border-radius-none" style="height:300px;">
        <div id="new_sales" class="chart"></div>
        <div class="text-center"><?php _e('Meilleurs produits ces 7 derniers jours', 'nexo');?></div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer no-border">
        <div class="row">
        	<br />
            <?php
            // Get remaning stock
            if ($Cache->get('widget_sale_new_items')) {
                if (is_array($Cache->get('widget_sale_new_items'))) {
                    $items            =    $Cache->get('widget_sale_new_items');
                    $stock_initial    =    0;
                    $stock_remaning    =    0;
                    foreach ($items as $item) {
                        $stock_initial        =    intval($item->QUANTITY);
                        $stock_remaning        =    intval($item->QUANTITE_RESTANTE);
                        $stock_defectueux 	=    intval($item->DEFECTUEUX);
                    }
                                    
                    $percent            	=    floor(($stock_remaning * 100) / $stock_initial);
                    $percent_defectueux    	=    floor(($stock_defectueux * 100) / $stock_initial);
                }
            } else {
                $percent                	=    0;
                $percent_defectueux        	=    0;
            }
            ?>
            <div class="col-xs-6 text-center" style="border-right: 1px solid #f4f4f4">
                <input type="text" class="knob pourcentage_stock" value="<?php echo $percent;?>" data-width="90" data-height="90" data-fgColor="#3c8dbc" data-readonly="true">
                <div class="knob-label"><h4><?php _e('Stock restant (%)', 'nexo');?></h4></div>
            </div>
            <!-- ./col -->
            <div class="col-xs-6 text-center" style="border-right: 1px solid #f4f4f4">
                <input type="text" class="knob pourcentage_defectueux" value="<?php echo $percent_defectueux;?>" data-width="90" data-height="90" data-fgColor="#3c8dbc" data-readonly="true">
                <div class="knob-label"><h4><?php _e('Stock défectueux (%)', 'nexo');?></h4></div>
            </div>
            <!-- ./col --> 
        </div>
        <!-- /.row --> 
    </div>
    <!-- /.box-footer --> 
</div>
<?php 

if (! $Cache->get('widget_sale_new_best_items') || ! $Cache->get('widget_sale_new_items')) {
    ?>
<script type="text/javascript">
"use strict";
$(function(){
	// Nexo_Sales_Widget.load();
});
</script>
<?php

}
?>
<script type="text/javascript">
"use strict";
var Nexo_Sales_Widget		=	new function(){
	this.load				=	function( arg ){
		var colors				=	[ '#02B3E7', '#CFD3D6', '#736D79', '#776068', '#EB0D42', '#FFEC62', '#04374E' ];
		var refresh_it			=	arg == 'refresh' ? '?refresh=true' : '?load=cache';
		var start_date			=	'<?php echo nexo_date_format( Carbon::parse(date_now())->subDays(7)->startOfDay()->toDateTimeString() );?>';
		var end_date			=	'<?php echo nexo_date_format( Carbon::parse(date_now())->endOfDay()->toDateTimeString() );?>';
		var limit				=	7;
		var post_data			=	_.object( [ 'start_date', 'end_date', 'limit' ], [ start_date, end_date, limit ] );
		$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'widget_sale_new' ));?>' + refresh_it + '<?php echo store_get_param( '&' );?>', {
			data		:	post_data,
			type		:	'POST',
			dataType	:	"json",
			success		:	function( data ) {
				$( '#new_sales' ).fadeOut( 500, function(){
					
					$( this ).remove();
					$( '.pieTip' ).remove();
					
					$( '[data-meta-namespace="<?php echo store_prefix();?>nexo_sales_new"]' ).find( '.box-body' ).prepend( '<div id="new_sales" class="chart"></div>' );
					
					var _i			=	0;
					var ItemsObject	=	new Object;
					
					if( _.isObject( data.best_items ) ) {
						_.each( data.best_items, function( value, key ) {
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
					}
					
					$("#new_sales").drawPieChart( _.toArray( ItemsObject ) );

					// Left Stock
					if( _.isObject( data.items ) ) {
						var stock_total			=	0;
						var stock_restant		=	0;
						var stock_defectueux	=	0;
						
						_.each( data.items, function( value, key ) {
							stock_total			+=	parseInt( value.QUANTITY );
							stock_restant		+=	parseInt( value.QUANTITE_RESTANTE );
							stock_defectueux	+=	parseInt( value.DEFECTUEUX );
						});
						
						var percent				=	( stock_restant * 100 ) / stock_total;
						$( '.pourcentage_stock' ).val( Math.floor( percent ) );
						$( '.pourcentage_stock' ).trigger( 'change' );
						
						var percent_defectueux	=	( stock_defectueux * 100 ) / stock_total;
						$( '.pourcentage_defectueux' ).val( Math.floor( percent_defectueux ) );
						$( '.pourcentage_defectueux' ).trigger( 'change' );
					}
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