<?php
! defined('APPPATH') ? die() : null;

global $Options;

$this->events->add_filter('gui_page_title', function ($title) {
    return '<section class="content-header"><h1>' . strip_tags($title) . ' <span class="pull-right"><a class="btn btn-primary btn-sm" href="' . current_url() . '?refresh=true">' . __('Vider le cache', 'nexo_premium') . '</a> <a class="btn btn-default btn-sm" href="javascript:void(0)" print-item="#nexo-global-wrapper">' . __('Imprimer', 'nexo_premium') . '</a></span></h1></section>';
});

$this->events->add_action('dashboard_header', function () {
    ?>
<script type="text/javascript" src="<?php echo module_url('nexo');?>/bower_components/moment/min/moment.min.js"></script>
<script type="text/javascript" src="<?php echo module_url('nexo');?>/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo module_url('nexo');?>/bower_components/underscore/underscore-min.js"></script>
<link rel="stylesheet" href="<?php echo module_url('nexo');?>/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
	<?php
});

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'namespace'        =>     'flux_tresor',
    'type'            =>     'unwrapped'
));

if (! $Cache->get($report_slug) || @$_GET[ 'refresh' ] == 'true') {
    ob_start();

    $Months        =    array(
    __('Janvier', 'nexo_premium'),
    __('Février', 'nexo_premium'),
    __('Mars', 'nexo_premium'),
    __('Avril', 'nexo_premium'),
    __('Mai', 'nexo_premium'),
    __('Juin', 'nexo_premium'),
    __('Juillet', 'nexo_premium'),
    __('Aout', 'nexo_premium'),
    __('Septembre', 'nexo_premium'),
    __('Octobre', 'nexo_premium'),
    __('Novembre', 'nexo_premium'),
    __('Decembre', 'nexo_premium')
);

    $rows        =    array(
    'table-recettes-item table-recettes'    =>    __('Recettes(+)', 'nexo_premium'),
    // 'table-recettes-ac-item table-recettes-ac'	=>	__( 'Recettes cummulées', 'nexo_premium' ),
    // 'table-recettes-ac'	=>	__( 'Recettes commulées', 'nexo_premium' ),
    'table-depenses-item table-depenses'    =>    __('Dépenses(-)', 'nexo_premium'),
    // 'table-depenses-ac-item table-depenses-ac'	=>	__( 'Dépense cumulées', 'nexo_premium' ),
    'solde-finale-item solde-finale'        =>    __('Solde Final', 'nexo_premium')
);


/**
 * Daily Advanced Report
 *
 *
**/
?>
<form class="form-inline circonstrire">
  <div class="form-group">
    <label class="sr-only" for="report_date"><?php _e('Spécifier une date', 'nexo_premium');
    ?></label>
    <div class="input-group">
      <div class="input-group-addon"><?php _e('Spécifier une date', 'nexo_premium');
    ?></div>
      <input type="text" class="form-control" id="report_date" placeholder="<?php echo sprintf(__('Exemple : %s', 'nexo_premium'), $CarbonReportDate->formatLocalized('%Y'));
    ?>">
    </div>
  </div>
  <button type="submit" class="btn btn-primary circonstrire"><?php _e('Afficher le rapport', 'nexo_premium');
    ?></button>
</form>
<br />
<script type="text/javascript">

"use strict";

$( document ).ready(function(e) {
    $( '.circonstrire' ).bind( 'submit', function() {
		if( $( '#report_date' ).val() != '' ) {
			document.location =	'<?php echo site_url(array( 'dashboard', store_slug(), 'nexo_premium', 'Controller_Mouvement_Annuel_Tresorerie' ));
    ?>/' + $( '#report_date' ).val();
		} else {
			bootbox.alert( '<?php _e('Vous devez spécifier une date', 'nexo_premium');
    ?>' );
		}
		return false;
	});
});
</script>
<div id="nexo-global-wrapper">
    	<div class="well well-sm">
            <h2 class="text-center"><?php echo @$Options[ 'site_name' ] ? $Options[ 'site_name' ] : __('Nom indisponible', 'nexo_premium');
    ?></h2>

            <h4 class="text-center"><?php echo sprintf(
                __('Flux de trésorerie pour %s', 'nexo_premium'),
                $CarbonReportDate->formatLocalized('%Y')
            );
    ?></h4>
            <?php
            $by            =    sprintf(__('Document imprimé par : %s', 'nexo_premium'), User::pseudo());
    ?>
            <p class="text-center"><?php echo $this->events->apply_filters('nexo_detailled_daily_report', $by);
    ?></p>
        </div>
    	<div class="hidden-print">
	    	<?php echo tendoo_info(__('Flux de trésorerie des activités opérationnelles.', 'nexo_premium'));?>
        </div>
		<div class="table-responsive">
			<table class="table table-bordered table-striped box">
        	<thead>
            	<tr>
                	<td colspan="<?php echo count($Months) + 2;?>"><?php _e('Mouvement annuel de la trésorerie', 'nexo_premium');?></td>
                </tr>
                <tr>
                	<td width="80"><?php _e('Libellé / Mois', 'nexo_premium');?></td>
					<?php foreach ($Months as $key    =>    $Month):?>
                    	<?php $CarbonReportDate->month    =    $key + 1;?>
	                    <td width="90" class="month_thead"
                        	data-start-date="<?php echo $CarbonReportDate->copy()->startOfMonth();?>"
                            data-end-date="<?php echo $CarbonReportDate->copy()->endOfMonth();?>"
						><?php echo $Month;?></td>
					<?php endforeach;?>
                    <td width="80"><?php _e('Total', 'nexo_premium');?></td>
                </tr>
            </thead>
            <tbody id="tbody-wrapper">
            	<?php foreach ($rows as $class_name    =>    $row):?>
                <?php
                if ($class_name == "solde-finale-item solde-finale") {
                    $class = "success";
                } elseif ($class_name == "table-recettes-item table-recettes") {
                    $class = "info";
                } elseif ($class_name == "table-depenses-item table-depenses") {
                    $class = "warning";
                } else {
                    $class = "";
                } ?>
                <tr class="<?php echo $class;?>">
                	<td><?php echo $row;?></td>
                    <?php foreach ($Months as $key => $Month):?>
                    <td class="table-input-<?php echo $key + 1;?> <?php echo $class_name;?>-<?php echo $key + 1;?> text-right">0</td>
                    <?php endforeach;?>
                    <td class="total-<?php echo $class_name;?> text-right"></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
		</div>	
    	
        <hr />
        <p><?php echo @$Options[ 'nexo_other_details' ];?></p>
        <br />
    </div>
<script type="text/javascript">

"use strict";

var NexoCashFlow	=	new function(){

	this.Index				=	0;
	this.__TimeCalled		=	0;
	this.Nexo_Order_Avance	=	'<?php echo 'nexo_order_advance';
    ?>';
	this.Nexo_Order_Cash	=	<?php echo json_encode( $this->events->apply_filters( 'report_order_types', [ 'nexo_order_comptant' ] ) );?>;
	this.Nexo_Order_Devis	=	'<?php echo 'nexo_order_devis';
    ?>';
	this.CurrencyBefore		=	'<?php echo $this->Nexo_Misc->display_currency('before');
    ?>';
	this.CurrencyAfter		=	'<?php echo $this->Nexo_Misc->display_currency('after');
    ?>';

	/**
	 * Init class
	 *
	**/

	this.Init		=	function(){
		this.ShowStats();
	}

	/**
	 * Show Stats
	 *
	**/

	this.ShowStats	=	function(){

		this.EntryLength	=	$( '.month_thead' ).length;
		this.Reset();
		this.GetReport();

	};

	/**
	 * get report
	 *
	**/

	this.GetReport	=	function() {
		if( this.Index < this.EntryLength ) {
			this.DisplayModal();
			var	data	=	_.object( [ 'start', 'end' ], [ $( '.month_thead' ).eq( this.Index ).data( 'start-date' ), $( '.month_thead' ).eq( this.Index ).data( 'end-date' ) ] );

			$.post(
				'<?php echo site_url(array( 'nexo_premium', 'flux', store_get_param( '?' ) ));
    ?>',
				data,
				function( content ) {
					NexoCashFlow.Index++;
					NexoCashFlow.TreatReport( content, NexoCashFlow.Index );
					NexoCashFlow.GetReport();
				}
			);
		} else {
			NexoCashFlow.CalculRecettesAccumulees();
			NexoCashFlow.CalculDepenseAccumulees();
			NexoCashFlow.CalculSoldeFinal();
			NexoCashFlow.CalculLignes();
			this.CloseModal();
		}
	}

	/**
	 * Calcul des lignes
	 *
	**/

	this.CalculLignes		=	function(){
		// Calcul des depenses
		var TotalDepenses	=	0;
		$( '.table-depenses-item' ).each( function(){
			TotalDepenses	+=	NexoAPI.ParseFloat( $( this ).attr( 'table-amount' ) );
		});

		$( '.total-table-depenses-item' ).attr( 'table-amount', TotalDepenses ).html(
			NexoCashFlow.CurrencyBefore + ' ' +
			'<span class="amount" amount="' + ( TotalDepenses ) + '">' + NexoAPI.Format( TotalDepenses ) + '</span>' +
			NexoCashFlow.CurrencyAfter + ' '
		);

		// Calcul des recettes
		var TotalRecettes		=	0;
		var TotalRecetteIndex	=	0;
		$( '.table-recettes-item' ).each( function(){

			TotalRecettes	+=	(
				NexoAPI.ParseFloat( $( this ).attr( 'table-amount' ) )
			);

			TotalRecetteIndex++;
		});

		$( '.total-table-recettes-item' ).attr( 'table-amount', TotalRecettes ).html(
			NexoCashFlow.CurrencyBefore + ' ' +
			'<span class="amount" amount="' + ( TotalRecettes ) + '">' + NexoAPI.Format( TotalRecettes ) + '</span>' +
			NexoCashFlow.CurrencyAfter + ' '
		);

		/*// Calcul des recettes ac
		var TotalRecettesAc	=	0;
		$( '.table-recettes-ac-item' ).each( function(){
			TotalRecettesAc	+=	NexoAPI.ParseFloat( $( this ).attr( 'table-amount' ) );
		});
		$( '.total-table-recettes-ac-item' ).attr( 'table-amount', TotalRecettesAc ).html(
			NexoCashFlow.CurrencyBefore + ' ' +
			'<span class="amount" amount="' + ( TotalRecettesAc ) + '">' + NexoAPI.Format( TotalRecettesAc ) + '</span>' +
			NexoCashFlow.CurrencyAfter + ' '
		);*/

		// Calcul des depenses ac
		/*var TotalDepensesAc	=	0;
		$( '.table-depenses-ac-item' ).each( function(){
			TotalDepensesAc	+=	NexoAPI.ParseFloat( $( this ).attr( 'table-amount' ) );
		});
		$( '.total-table-depenses-ac-item' ).attr( 'table-amount', TotalDepensesAc ).html(
			NexoCashFlow.CurrencyBefore + ' ' +
			'<span class="amount" amount="' + ( TotalDepensesAc ) + '">' + NexoAPI.Format( TotalDepensesAc ) + '</span>' +
			NexoCashFlow.CurrencyAfter + ' '
		);*/

		// Calcul des soldes finales
		var TotalSoldeFinale	=	0;
		$( '.solde-finale-item' ).each( function(){
			TotalSoldeFinale	+=	NexoAPI.ParseFloat( $( this ).attr( 'table-amount' ) );
		});
		$( '.total-solde-finale-item' ).attr( 'table-amount', TotalSoldeFinale ).html(
			NexoCashFlow.CurrencyBefore + ' ' +
			'<span class="amount" amount="' + ( TotalSoldeFinale ) + '">' + NexoAPI.Format( TotalSoldeFinale ) + '</span>' +
			NexoCashFlow.CurrencyAfter + ' '
		);

	};

	/**
	 * Calcul solde final
	 *
	**/

	this.CalculSoldeFinal			=	function(){
		var NbrInput				=	$( '.table-recettes-item' ).length;
		var Index					=	0;
		var i;
		for( i = 0; i <= NbrInput; i++ ) {
			var Actifs	=			0;
			$( '.table-input-' + i ).each( function(){
				if( $( this ).hasClass( 'table-recettes-item' ) ) {
					Actifs		+=	NexoAPI.ParseFloat( $( this ).attr( 'table-amount' ) )
				}
			});

			var Passifs	=			0;
			$( '.table-input-' + i ).each( function(){
				if( $( this ).hasClass( 'table-depenses-item' ) ) {
					Passifs		+=	NexoAPI.ParseFloat( $( this ).attr( 'table-amount' ) )
				}
			});

			$( '.solde-finale-' + i ).attr( 'table-amount', NexoAPI.ParseFloat( Actifs - Passifs ) ).html(
				NexoCashFlow.CurrencyBefore + ' ' +
				'<span class="amount" amount="' + ( Actifs - Passifs ) + '">' + NexoAPI.Format( Actifs - Passifs ) + '</span>' +
				NexoCashFlow.CurrencyAfter + ' '
			);

		}
	};

	/**
	 * Calcul solde initial
	 *
	**/

	this.CalculRecettesAccumulees	=	function(){
		var index			=	1;
		var currentAmount	=	0;

		$( '.table-recettes-ac-item' ).each( function(){
			currentAmount	+=	NexoAPI.ParseFloat( $( '.table-recettes-' + index ).attr( 'table-amount' ) );
			$( this ).attr( 'table-amount', currentAmount ).html(
				NexoCashFlow.CurrencyBefore + ' ' +
				'<span class="amount" amount="' + currentAmount + '">' + NexoAPI.Format( currentAmount ) + '</span>' +
				NexoCashFlow.CurrencyAfter + ' '
			);
			index++;
		});
	}

	/**
	 * Calcul Depense accumulés
	 *
	**/

	this.CalculDepenseAccumulees	=	function(){
		var index			=	1;
		var currentAmount	=	0;

		$( '.table-depenses-ac-item' ).each( function(){
			currentAmount	+=	NexoAPI.ParseFloat( $( '.table-depenses-' + index ).attr( 'table-amount' ) );
			$( this ).attr( 'table-amount', currentAmount ).html(
				NexoCashFlow.CurrencyBefore + ' ' +
				'<span class="amount" amount="' + currentAmount + '">' + NexoAPI.Format( currentAmount ) + '</span>' +
				NexoCashFlow.CurrencyAfter + ' '
			);
			index++;
		});
	}

	/**
	 * Reset the calendar
	**/

	this.Reset			=	function(){
		this.__TimeCalled	=	1;
		$( '.progress_level' ).html( 1 );
		$( '.progress-bar' ).css( 'width', '1%' ).data( 'aria-valuenow', 1 );
	}

	/**
	 * Treat Report
	 *
	**/

	this.RRR_Total		=	0;
	this.AvanceTotal	=	0;
	this.CashTotal		=	0;
	this.BillsTotal		=	0;

	this.TreatReport	=	function( content, index ) {
		_.map( content.orders, function( value, index ) {

			var CurrentOrderRRR				=	( NexoAPI.ParseFloat( value.RISTOURNE ) + NexoAPI.ParseFloat( value.RABAIS ) + NexoAPI.ParseFloat( value.REMISE ) );
				NexoCashFlow.RRR_Total		+=	CurrentOrderRRR;

			if( _.indexOf( NexoCashFlow.Nexo_Order_Cash, value.TYPE ) != -1 ) {
				NexoCashFlow.CashTotal		+=	( NexoAPI.ParseFloat( value.TOTAL ) + NexoAPI.ParseFloat( CurrentOrderRRR ) );
			} else if( value.TYPE == NexoCashFlow.Nexo_Order_Avance ) {
				NexoCashFlow.AvanceTotal	+=	NexoAPI.ParseFloat( value.SOMME_PERCU );
			}
		});

		_.map( content.bills, function( value, index ) {
			NexoCashFlow.BillsTotal		+=	NexoAPI.ParseFloat( value.MONTANT );
		});

		$( '.table-recettes-' + index )
			.attr( 'table-amount', NexoAPI.ParseFloat( this.CashTotal + this.AvanceTotal ) )
			.html(
				this.CurrencyBefore + ' ' +
				'<span class="amount" amount="' + this.CashTotal + this.AvanceTotal + '">' + NexoAPI.Format( this.CashTotal + this.AvanceTotal ) + '</span>' +
				this.CurrencyAfter + ' '
			);

		$( '.table-depenses-' + index )
			.attr( 'table-amount', NexoAPI.ParseFloat( this.BillsTotal + this.RRR_Total ) )
			.html(
				this.CurrencyBefore + ' ' +
				'<span class="amount" amount="' + ( this.BillsTotal + this.RRR_Total ) +  '">' + NexoAPI.Format( this.BillsTotal + this.RRR_Total ) + '</span>' +
				this.CurrencyAfter + ' '
			);

		// Reset Totals
		this.RRR_Total		=	0;
		this.AvanceTotal	=	0;
		this.CashTotal		=	0;
		this.BillsTotal		=	0;
	}

	/**
	 * Display Modal
	**/

	this.DisplayModal	=	function(){
		if( ! $( '.launch_loading' ).data( 'clicked' ) ) {
			$( '.launch_loading' ).trigger( 'click' );
			$( '.launch_loading' ).data( 'clicked', true );
		} else {
			var Percent	=	( this.__TimeCalled / this.EntryLength ) * 100;
			this.SetPercent( Math.ceil( Percent ) );
		}
		this.__TimeCalled++;
	};

	/**
	 * Progress Bar
	**/

	this.SetPercent		=	function( percent ) {
		$( '.progress_level' ).html( percent );
		$( '.progress-bar' ).css( 'width', percent + '%' ).data( 'aria-valuenow', percent );
	}

	/**
	 * Close modal
	**/

	this.CloseModal		=	function(){
		$( '[data-dismiss="modal"]' ).trigger( 'click' );
	}
};

$( document ).ready(function(e) {
	NexoCashFlow.Init();
});

</script>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg launch_loading" data-toggle="modal" data-target="#myModal" style="display:none;"></button>

<div class="modal fade hidden-print" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php _e('Chargement en cours...', 'nexo_premium');
    ?></h4>
      </div>
      <div class="modal-body">
        <div class="progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
            <span class="progress_level">0</span>%
          </div>
        </div>
      </div>
      <div class="modal-footer">

      </div>
    </div>
  </div>
</div>
<?php

}

if (! $Cache->get($report_slug) || @$_GET[ 'refresh' ] == 'true') {
    $Content    =    ob_get_clean();
    $Cache->save($report_slug, $Content, 999999999); // long time
} else {
    $Content    =    $Cache->get($report_slug);
}

$this->Gui->add_item(array(
    'type'            =>     'dom',
    'content'        =>     $Content
), 'flux_tresor', 1);

$this->Gui->output();
