<?php
use Carbon\Carbon;
?>
<div class="box box-solid" data-meta-namespace="<?php echo store_prefix() ;?>sale_type_new">
    <div class="box-header ui-sortable-handle" style="cursor: move;"> <i class="fa fa-money"></i>
        <h3 class="box-title">
            <?php _e('Variétés des achats', 'nexo');?>
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn bg-purple-active btn-sm" data-widget="collapse"><i class="fa fa-minus"></i> </button>
            <button type="button" class="btn bg-purple-active btn-sm" data-refresh-widget="nexo_sales_types"><i class="fa fa-refresh"></i> </button>
        </div>
    </div>
    <div class="box-body border-radius-none">
        <canvas id="canvas" height="300"></canvas>
    </div>
    <!-- /.box-body -->
</div>
<script>
var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
<?php
$startOfWeek    =    Carbon::parse(date_now())->startOfWeek()->subDay();
$endOfWeek        =    Carbon::parse(date_now())->endOfWeek()->subDay();
?>
var startOfWeek	=	'<?php echo $startOfWeek->toDateString();?>';
var endOfWeek	=	'<?php echo $endOfWeek->toDateString();?>';

var randomScalingFactor = function() {
	return Math.round(Math.random() * 100);
	//return 0;
};
var randomColorFactor = function() {
	return Math.round(Math.random() * 255);
};
var randomColor = function(opacity) {
	return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.3') + ')';
};

var NexoSalesLines;

var config = {
	type: 'line',
	data: {
        labels: [ "<?php echo _s('Dimanche', 'nexo');?>", "<?php echo _s('Lundi', 'nexo');?>", "<?php echo _s('Mardi', 'nexo');?>", "<?php echo _s('Mercredi', 'nexo');?>", "<?php echo _s('Jeudi', 'nexo');?>", "<?php echo _s('Vendredi', 'nexo');?>", "<?php echo _s('Samedi', 'nexo');?>"],
		datasets: []
	},
	options: {
		title: {
            display: true,
            text: '<?php echo sprintf(_s('Ventes réalisées du %s au %s', 'nexo'), nexo_date_format( $startOfWeek->toDateString() ), nexo_date_format( $endOfWeek->toDateString() ) );?>'
        },
		responsive: true,
		tooltips: {
			mode: 'label'
		},
		hover: {
			mode: 'dataset'
		},
		scales: {
			xAxes: [{
				display: true,
				scaleLabel: {
					show: true,
					labelString: 'Month'
				}
			}],
			yAxes: [{
				display: true,
				scaleLabel: {
					show: true,
					labelString: 'Value'
				}
			}],
			scaleStartValue : 0
		}
	}
};

var NexoSalesStats	=	new function(){
	this.load		=	function( action ){
		var get_params	=	action == 'refresh' ? '?refresh=true' : '?load=cache';
		var post_data	=	_.object( [ 'start', 'end' ], [ startOfWeek, endOfWeek ] );
		var order_types	=	$.parseJSON( '<?php echo json_encode($this->config->item('nexo_order_types'));?>' );
		var colors		=	{
			nexo_order_advance	:	{
				borderColor			:	'rgb(70, 195, 208)',
				backgroundColor 	: 	'rgba(70, 195, 208, 0.2)'
			},
			nexo_order_comptant	:	{
				borderColor			:	'rgb(142, 208, 70)',
				backgroundColor 	: 	'rgba(142, 208, 70, 0.2)'
			},
			nexo_order_devis	:	{
				borderColor			:	'rgb(216, 89, 89)',
				backgroundColor 	: 	'rgba(216, 89, 89,0.2)'
			},
			nexo_order_partialy_refunded		:	{
				borderColor			:	'rgb(216, 207, 86)',
				backgroundColor 	: 	'rgba(216, 207, 86,0.2)'
			},
			nexo_order_refunded		:	{
				borderColor			:	'rgb(116, 107, 50)',
				backgroundColor 	: 	'rgba(216, 207, 86,0.2)'
			}
		}

		$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'widget_sales_stats' ));?>' + get_params + '<?php echo store_get_param( '&' );?>', {
			type	:	'POST',
			data	:	post_data,
			success	:	function( data ) {

				var i 	=	0;

				NexoSalesLines.data.datasets	=	[]; // Reset

				_.each( data, function( value, key ) {

					NexoSalesLines.data.datasets.push({
						data	:	_.toArray( value ),
						label	: 	_.propertyOf( order_types )( key ),
						fill	: 	true,
						borderDash: [10, 10],
						borderColor	:	_.propertyOf( colors )( key ).borderColor,
						backgroundColor : _.propertyOf( colors )( key ).backgroundColor,
					});
					NexoSalesLines.update();
					i++;
				});
			}
		});
	}
}

$(document).ready(function(e) {
    var ctx = document.getElementById("canvas").getContext("2d");
	NexoSalesLines = new Chart(ctx, config);

	$( '[data-refresh-widget="nexo_sales_types"]' ).bind( 'click', function(){
		NexoSalesStats.load( 'refresh' );
	});

	NexoSalesStats.load();
});
</script>
