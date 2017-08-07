<form class="form-inline">
    <div class='input-group date' id='datetimepicker6'>
    	<span class="input-group-addon"><?php _e('Date de départ', 'nexo_premium');?></span>
        <input type='text' class="form-control" name="start" value="<?php echo $start_date;?>" />
        <span class="input-group-addon"> <span class="glyphicon glyphicon-calendar"></span> </span>
	</div>
    <div class='input-group date' id='datetimepicker7'>
    	<span class="input-group-addon"><?php _e('Date de fin', 'nexo_premium');?></span>
        <input type='text' class="form-control" name="end" value="<?php echo $end_date;?>" />
        <span class="input-group-addon"> <span class="glyphicon glyphicon-calendar"></span> </span>
	</div>
    <input type="button" class="btn btn-primary trigger_fetch" value="<?php _e('Afficher les résultats', 'nexo_premium');?>" />
    <div class="input-group">
      <span class="input-group-btn">
        <button class="btn btn-default" type="button"><?php _e('Filter par', 'nexo_premium');?></button>
      </span>
      <select type="text" class="form-control" name="filtre">
      	<option value="by_sales"><?php _e('quantité', 'nexo_premium');?></option>
        <option value="by_cash"><?php _e('par chiffre d\'affaire', 'nexo_premium');?></option>
      </select>
    </div>
	
	<div class="checkbox">
		<label>
			<input type="checkbox" value="1" name="disable_cache">
			<?php echo __( 'Disable Cache', 'nexo_premium' );?>
		</label>
	</div>
	
</form>
<br />
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
      <li class="active">
      	<a href="#item_tab" data-toggle="tab" aria-expanded="true" data-url="<?php echo site_url(array( 'nexo_premium', 'best_items' ));?>"><?php _e('Produits', 'nexo_premium');?></a>
		</li>
      <li class=""><a href="#categories_tab" data-toggle="tab" aria-expanded="false" data-url="<?php echo site_url(array( 'nexo_premium', 'best_categories' ));?>"><?php _e('Catégories', 'nexo_premium');?></a></li>
      <li class=""><a href="#shippings_tab" data-toggle="tab" aria-expanded="false" data-url="<?php echo site_url(array( 'nexo_premium', 'best_shippings' ));?>"><?php _e('Livraisons', 'nexo_premium');?></a></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="item_tab">
      </div>
    </div>
    <!-- /.tab-content -->
  </div>
<script type="text/javascript">
"use strict";

var Nexo_Best_Of		=	new function(){
	this.fetch_report	=	function( url, output_wrapper, filtre ) {
		// Use previous url
		if( typeof url != 'undefined' ) {
			this.last_url	=	url;
		}

		var disable_cache = $( '[name="disable_cache"]' ).val() == "1" ? '&disable_cache=true' : '';

		$.ajax( this.last_url + '<?php echo store_get_param( '?' );?>' + disable_cache, {
			beforeSend	:	function() {
				$( output_wrapper ).html( '<h3 style="margin:10px 0px" class="text-center"><?php echo addslashes(__('Chargement...', 'nexo_premium'));?></h3>' );
			},
			success		:	function( content ){
				Nexo_Best_Of.DisplayChart( content, output_wrapper, filtre );
			},
			type		: 'POST',
			data		:	_.object( [ 'start', 'end' ], [ $( '[name=start]' ).val(), $( '[name=end]' ).val() ] ),
			error		: function() {
				$( output_wrapper ).html( '<h3 style="margin:10px 0px" class="text-center"><?php echo addslashes(__('Une erreur s\'est produite durant le chargement ! Veuillez recommencer', 'nexo_premium'));?></h3>' );
			}
		});
	}

	/**
	 * Random Hex Color
	 *
	**/

	this.RandHex			=	function(){
		var colors		=	[ '#346b90', '#57aefc', '#001c32', '#939c5b', '#9c5b93', '#5b939c', '#9c645b', '#645b9c', '#5b9c64', '#a2cc61', '#f49ac2', '#bc8dbf', '#8882be', '#7ea7d8', '#6ecff6', '#7bcdc8', '#82ca9d', '#c4df9b', '#fff79a', '#fdc68a', '#f7977a', '#f6989d', '#c6b7b7', '#4f4f4f', '#636363', '#213380', '#384474', '#4f5568', '#66655c', '#7d7650', '#948644', '#ab9638', '#c2a72c', '#d9b720', '#0a238c', '#f0c814', '#b92f1c', '#22e0e0', '#404040', '#480a0d', '#e7e009', '#d5eb79' ];
		var i= Math.floor(Math.random()*colors.length);
		if( i in colors ){
			return colors.splice(i, 1)[0];
		}
		return colors[i];
	};

	/**
	 * Display Chart
	 *
	**/

	this.DisplayChart	=	function( data, output_wrapper, filtre ) {

		$( output_wrapper ).html( '<canvas id="chart_wrapper"></canvas>' );

		var chartLabels		=	_.mapObject( data.dates_between_borders, function( val, key ){
			return moment( val ).format("MMMM DD YYYY");
		});

		var SoldChart		=	new Array;

		// console.log( data.items_sales );

		_.each( data.items_sales, function( value, sale_date ) {
			_.each( value, function( _item, _key ) {

				if( typeof SoldChart[ _item.ITEM_ID ] == 'undefined' ) {
					SoldChart[ _item.ITEM_ID ]			=	new Object;
				}

				SoldChart[ _item.ITEM_ID ].label			=	_item.DESIGN;
				SoldChart[ _item.ITEM_ID ].borderColor		=	Nexo_Best_Of.RandHex();

				if( typeof SoldChart[ _item.ITEM_ID ].data == 'undefined' ) {
					SoldChart[ _item.ITEM_ID ].data			=	new Array;
				}

				_.each( data.dates_between_borders, function( date, date_key ) {
					if( date == moment( _item.SOLD_DATE ).format( 'YYYY-MM-DD' ) ) {
						if( typeof SoldChart[ _item.ITEM_ID ].data[ date_key ] == 'undefined' ) {
							SoldChart[ _item.ITEM_ID ].data[ date_key ]	=	0;
						}

						if( filtre == 'by_sales' ) {
							SoldChart[ _item.ITEM_ID ].data[ date_key ]	+=	parseInt( _item.QUANTITE_UNIQUE_VENDUE );
						} else if( filtre == 'by_cash' ) {
                            SoldChart[ _item.ITEM_ID ].data[ date_key ]	+=
                            ( NexoAPI.ParseFloat( _item.PRIX ) * parseInt( _item.QUANTITE_UNIQUE_VENDUE ) );
						}
					}
				});
			});
		});

		var Charts		=	_.values( SoldChart );

		for( var x = 0; x < Charts.length; x++ ) {
			for( var i = 0; i < data.dates_between_borders.length ;i++ ) {
				if( typeof Charts[x].data[ i ] == 'undefined' ) {
					Charts[x].data[ i ]	=	0;
				}
			}
		}

		var CTX				=	document.getElementById( "chart_wrapper" );
		var myChart 		= 	new Chart( CTX, {
			type: 'line',
			data: {
				labels		: _.values( chartLabels ),
				datasets	: Charts
			},
			options: {
				scales: {
					height	:	5
				}
			}
		});

		// Unexpected shake bug fix
		$( '.chartjs-hidden-iframe' ).remove();
	};
}

$( document ).ready(function(e) {
	$( '.nav-tabs-custom .nav li a' ).bind( 'click', function(){
		Nexo_Best_Of.fetch_report( $( this ).data( 'url' ), $( '.tab-content .tab-pane' ), $( '[name="filtre"]' ).val() );
	});

	$( '.nav-tabs-custom .nav li a' ).eq(0).trigger( 'click' );

	$( '.trigger_fetch' ).bind( 'click', function(){
		Nexo_Best_Of.fetch_report( void(0), $( '.tab-content .tab-pane' ), $( '[name="filtre"]' ).val() );
	});

	$( '[name="filtre"]' ).change( function(){
		$( '.trigger_fetch' ).trigger( 'click' );
	});
});

// Date Picker
$(function () {
	$('#datetimepicker6').datetimepicker({
		format	:	'YYYY-MM-DD'
	});
	$('#datetimepicker7').datetimepicker({
		useCurrent: false, //Important! See issue #1075
		format	:	'YYYY-MM-DD'
	});
	$("#datetimepicker6").on("dp.change", function (e) {
		$('#datetimepicker7').data("DateTimePicker").minDate(e.date);
	});
	$("#datetimepicker7").on("dp.change", function (e) {
		$('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
	});
});
</script>
