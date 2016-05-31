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
		var ctx	 = $("#chart_div");
		var data = {
		labels: <?php echo json_encode($Dates);?>,
		datasets: [
				{
					label: '<?php echo addslashes(__('Ventes ces 7 dernières jours', 'nexo'));?>',
					fillColor: "rgba(220,220,220,0.2)",
					backgroundColor: '#00c0ef',
					strokeColor: "rgba(220,220,220,1)",
					pointColor: "rgba(220,220,220,1)",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "rgba(220,220,220,1)",
					data: <?php echo json_encode($NbrOrder);?>,
					backgroundColor: [
						"#FF6384",
						"#4BC0C0",
						"#FFCE56",
						"#E7E9ED",
						"#36A2EB",
						"#3E7EDD",
						"#F9C181"
					],
				}				
			]
		};
		var myBarChart = new Chart(ctx, {
			type: 'bar',
			data: data
		});    
    });
</script>
<canvas id="chart_div" width="400" height="400" style="width:100%;"></canvas>