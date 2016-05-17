<?php
use Carbon\Carbon;

$Cron		=	new Nexo_Cron;

$StartDate	=	Carbon::parse( date_now() )->subDays( 7 )->toDateString();
$EndDate	=	Carbon::parse( date_now() )->toDateString();

$Stats		=	$Cron->get_stats( $StartDate, $EndDate );
$NbrOrder	=	array();

foreach( $Stats as $stat ) {
	$NbrOrder[]		=	$stat[ 'chiffre_daffaire_net' ];
}
$Dates	=	array_keys( $Stats );
foreach( $Dates as &$Date ) {
	$Date	=	Carbon::parse( $Date );
	$Date	=	$Date->toFormattedDateString();
}
?>
<script type="text/javascript">
	$( document ).ready(function(e) {
		var ctx	 = $("#chart_div2");
		var data = {
		labels: <?php echo json_encode( $Dates );?>,
		datasets: [
				{
					label: '<?php echo addslashes( __( 'Chiffre d\'affaire ces 7 dernières jours', 'nexo' ) );?>',
					fillColor: "#F60",
					backgroundColor: '#00c0ef',
					strokeColor: "rgba(220,220,220,1)",
					pointColor: "rgba(220,220,220,1)",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "rgba(220,220,220,1)",
					data: <?php echo json_encode( $NbrOrder );?>
				}				
			]
		};
		
		new Chart(ctx, {
			data: data,
			type: 'polarArea'
		});
		
    });
</script>
<canvas id="chart_div2" width="400" height="400" style="width:100%;"></canvas>