<div class="box box-solid bg-purple-gradient" data-meta-namespace="nexo_jauge">
    <div class="box-header ui-sortable-handle" style="cursor: move;"> <i class="fa fa-money"></i>
        <h3 class="box-title">
            <?php _e( 'Epuisement du stock', 'nexo' );?>
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn bg-purple-active btn-sm" data-widget="collapse"><i class="fa fa-minus"></i> </button>
            <button type="button" class="btn bg-purple-active btn-sm" data-refresh="gauge"><i class="fa fa-refresh"></i> </button>
        </div>
    </div>
    <div class="box-body border-radius-none" style="height:300px;">
        <svg id="fillgauge1" width="97%" height="250"></svg>
        <p class="text-center"><?php _e( 'Ce graphisme affiche le pourcentage de stock restant', 'nexo' );?></p>
    </div>
    <!-- /.box-body -->
</div>
<?php
use Carbon\Carbon;

$Cron    =    new Nexo_Cron;

$StartDate    =    Carbon::parse(date_now())->subDays(7)->toDateString();
$EndDate    =    Carbon::parse(date_now())->toDateString();

$Stats    =    $Cron->get_stats($StartDate, $EndDate);
$NbrOrder    =    array();
foreach ($Stats as $stat) {
    $NbrOrder[]        =    $stat[ 'order_nbr' ];
}
$Dates    =    array_keys($Stats);
foreach ($Dates as &$Date) {
    $Date    =    Carbon::parse($Date);
    $Date    =    $Date->toFormattedDateString();
}
?>
<script type="text/javascript">
$( document ).ready(function(e) {
var config1 = liquidFillGaugeDefaultSettings();
    config1.circleColor = "#FFFFFF";
    config1.textColor = "#201d5f";
    config1.waveTextColor = "#605ca8";
    config1.waveColor = "#FFFFFF";
    config1.circleThickness = 0.1;
	config1.waveHeight = 0.1;
    config1.waveCount = 1;
    config1.textVertPosition = 0.2;
    config1.waveAnimateTime = 3000; 
var gauge1 	= loadLiquidFillGauge("fillgauge1", 0, config1);

var getGauge	=	function( __refresh ){
		$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'items_cached' ) );?>' + ( __refresh == true ? '?refresh=true' : '' ), {
			success		:	function( content ){
				if( typeof content == 'object' ) {
					var initial_stock	=	0;
					var left_stock		=	0;
					
					_.each( content, function( value, key ) {
						initial_stock	=	parseInt( value.QUANTITY );
						left_stock		=	parseInt( value.QUANTITE_RESTANTE );
					});
					
					var pourcentage		=	Math.floor( ( left_stock * 100 ) / initial_stock );
					
					gauge1.update( pourcentage );
				}
			},
			
			error		:	function(){
				tendoo.notify.warning( '<?php echo _s( 'Erreur', 'nexo' );?>', '<?php echo _s( 'Une erreur s\'est produite durant la récupération des données sur le serveur.', 'nexo' );?>' );
			}
		});
	}
	getGauge();
	
	$( '[data-refresh="gauge"]' ).bind( 'click', function(){
		getGauge( true );
	});
});
</script>