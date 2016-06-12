<div class="box box-solid bg-green-gradient" data-meta-namespace="nexo_sales_income">
            <div class="box-header ui-sortable-handle" style="cursor: move;">
              <i class="fa fa-money"></i>

              <h3 class="box-title"><?php _e( 'Chiffre d\'affaire', 'nexo' );?></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn bg-green btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn bg-green btn-sm" data-widget="remove"><i class="fa fa-refresh"></i>
                </button>
              </div>
            </div>
            <div class="box-body border-radius-none">
            	<canvas id="nexo_sales"></canvas>              
            </div>
            <!-- /.box-body -->
            <div class="box-footer no-border" style="display: block;">
              <div class="row">
                <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                  <div style="display:inline;width:60px;height:60px;"><canvas width="60" height="60"></canvas><input type="text" class="knob" data-readonly="true" value="20" data-width="60" data-height="60" data-fgcolor="#39CCCC" readonly="readonly" style="width: 34px; height: 20px; position: absolute; vertical-align: middle; margin-top: 20px; margin-left: -47px; border: 0px; font-style: normal; font-variant: normal; font-weight: bold; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial; text-align: center; color: rgb(57, 204, 204); padding: 0px; -webkit-appearance: none; background: none;"></div>

                  <div class="knob-label">Mail-Orders</div>
                </div>
                <!-- ./col -->
                <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                  <div style="display:inline;width:60px;height:60px;"><canvas width="60" height="60"></canvas><input type="text" class="knob" data-readonly="true" value="50" data-width="60" data-height="60" data-fgcolor="#39CCCC" readonly="readonly" style="width: 34px; height: 20px; position: absolute; vertical-align: middle; margin-top: 20px; margin-left: -47px; border: 0px; font-style: normal; font-variant: normal; font-weight: bold; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial; text-align: center; color: rgb(57, 204, 204); padding: 0px; -webkit-appearance: none; background: none;"></div>

                  <div class="knob-label">Online</div>
                </div>
                <!-- ./col -->
                <div class="col-xs-4 text-center">
                  <div style="display:inline;width:60px;height:60px;"><canvas width="60" height="60"></canvas><input type="text" class="knob" data-readonly="true" value="30" data-width="60" data-height="60" data-fgcolor="#39CCCC" readonly="readonly" style="width: 34px; height: 20px; position: absolute; vertical-align: middle; margin-top: 20px; margin-left: -47px; border: 0px; font-style: normal; font-variant: normal; font-weight: bold; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial; text-align: center; color: rgb(57, 204, 204); padding: 0px; -webkit-appearance: none; background: none;"></div>

                  <div class="knob-label">In-Store</div>
                </div>
                <!-- ./col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.box-footer -->
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
		var ctx	 = $("#nexo_sales");
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
			type: 'line',
			data: data
		});    
    });
</script>