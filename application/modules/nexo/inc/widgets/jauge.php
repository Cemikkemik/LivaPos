<div class="box box-solid" data-meta-namespace="nexo_jauge">
    <div class="box-header ui-sortable-handle" style="cursor: move;"> <i class="fa fa-money"></i>
        <h3 class="box-title">
            <?php _e( 'Epuisement du stock', 'nexo' );?>
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn bg-purple-active btn-sm" data-widget="collapse"><i class="fa fa-minus"></i> </button>
            <button type="button" class="btn bg-purple-active btn-sm" data-refresh="gauge"><i class="fa fa-refresh"></i> </button>
        </div>
    </div>
    <div class="box-body border-radius-none">
        <canvas id="myChart" width="400" height="400"></canvas>
        <p class="text-center"><?php _e( 'Ce graphisme affiche le pourcentage de stock restant', 'nexo' );?></p>
    </div>
    <!-- /.box-body -->
</div>

<script type="text/javascript">
$( document ).ready(function(e) {
var getGauge	=	function( __refresh ){
	return;
		$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'items_cached' ) );?>' + ( __refresh == true ? '?refresh=true' : '' ), {
			success		:	function( content ){
				if( typeof content == 'object' ) {
					var ctx = document.getElementById("myChart");
					var myChart = new Chart(ctx, {
						type: 'bar',
						data: {
							labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
							datasets: [{
								label: '# of Votes',
								data: [12, 19, 3, 5, 2, 3],
								backgroundColor: [
									'rgba(255, 99, 132, 0.2)',
									'rgba(54, 162, 235, 0.2)',
									'rgba(255, 206, 86, 0.2)',
									'rgba(75, 192, 192, 0.2)',
									'rgba(153, 102, 255, 0.2)',
									'rgba(255, 159, 64, 0.2)'
								],
								borderColor: [
									'rgba(255,99,132,1)',
									'rgba(54, 162, 235, 1)',
									'rgba(255, 206, 86, 1)',
									'rgba(75, 192, 192, 1)',
									'rgba(153, 102, 255, 1)',
									'rgba(255, 159, 64, 1)'
								],
								borderWidth: 1
							}]
						},
						options: {
							scales: {
								yAxes: [{
									ticks: {
										beginAtZero:true
									}
								}]
							}
						}
					});
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