<?php
use Carbon\Carbon;

?>
<div class="box box-solid" data-meta-namespace="<?php echo store_prefix() ;?>nexo_sales_income">
<div class="box-header ui-sortable-handle" style="cursor: move;">
  <i class="fa fa-money"></i>

  <h3 class="box-title"><?php _e('Chiffre d\'affaire', 'nexo');?></h3>

  <div class="box-tools pull-right">
    <button type="button" class="btn bg-blue btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
    </button>
    <button type="button" class="btn bg-blue btn-sm" data-widget-refresh="income"><i class="fa fa-refresh"></i>
    </button>
  </div>
</div>
<div class="box-body border-radius-none">
    <canvas id="income_canvas" width="400" height="400"></canvas>         
</div>
</div>
<script>
var ctx = document.getElementById("income_canvas");
<?php
$startOfWeek    =    Carbon::parse(date_now())->startOfWeek()->subDay();
$endOfWeek        =    Carbon::parse(date_now())->endOfWeek()->subDay();
?>
var startOfWeek	=	'<?php echo $startOfWeek->toDateString();?>';
var endOfWeek	=	'<?php echo $endOfWeek->toDateString();?>';

Chart.defaults.global.defaultFontColor	=	'#666';
Chart.defaults.global.defaultFontSize	=	15;

var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [ "<?php echo _s('Dimanche', 'nexo');?>", "<?php echo _s('Lundi', 'nexo');?>", "<?php echo _s('Mardi', 'nexo');?>", "<?php echo _s('Mercredi', 'nexo');?>", "<?php echo _s('Jeudi', 'nexo');?>", "<?php echo _s('Vendredi', 'nexo');?>", "<?php echo _s('Samedi', 'nexo');?>"],
        datasets: [{
            label: '<?php echo _s('Chiffre d\'affaire', 'nexo');?>',
            data: [0, 0, 0, 0, 0, 0, 0],
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)',
				'rgba(55, 159, 100, 0.8)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
				'rgba(55, 159, 100, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
		title: {
            display: true,
            text: '<?php echo sprintf(_s('Ventes réalisées du %s au %s', 'nexo'), nexo_date_format( $startOfWeek->toDateString() ), nexo_date_format( $endOfWeek->toDateString() ) );?>'
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                },
				gridLines: {
					color: '#CCC'
				},
            }],
			xAxes: [{
				display: true,
				gridLines: {
					color: '#DDD'
				}
			}]
        },
		responsive : true
    }
});

var NexoIncomeWidget	=	new function(){
	this.load			=	function( action ) {
		var get_params	=	action == 'refresh' ? '?refresh=true' : '?load=cache';
		var post_data	=	_.object( [ 'start', 'end' ], [ startOfWeek, endOfWeek ] );
		$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'widget_income' ));?>' + get_params + '<?php echo store_get_param( '&' );?>', {
			type		:	'POST',
			data		:	post_data,
			success		: 	function( data ){				
				myChart.data.datasets[0].data	=	data;
				myChart.update();
				console.log( myChart.tooltip );
				myChart.tooltip._options.tooltips.callbacks.label	=	function( value ) {
					value.yLabel	=	NexoAPI.DisplayMoney( value.yLabel );
					return value.yLabel;
				}
			},
			dataType	:	"json"
		});
	}
}

$( document ).ready(function(e) {
	NexoIncomeWidget.load();
	
    $( '[data-widget-refresh="income"]' ).bind( 'click', function(){
		NexoIncomeWidget.load( 'refresh' );
	});
});
</script>
          