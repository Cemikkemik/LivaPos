<?php
! defined('APPPATH') ? die() : null;

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'type'        =>        'unwrapped',
    'namespace'    =>        'daily_advanced_report'
));

global $Options;

if (! $Cache->get($report_slug) || @$_GET[ 'refresh' ] == 'true') {
    ob_start();

$this->events->add_action('dashboard_header', function () { ?>
<script type="text/javascript" src="<?php echo module_url('nexo');?>/bower_components/moment/min/moment.min.js"></script>
<script type="text/javascript" src="<?php echo module_url('nexo');?>/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo module_url('nexo');?>/bower_components/underscore/underscore-min.js"></script>
<link rel="stylesheet" href="<?php echo module_url('nexo');?>/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
<?php });
    ?>

<div class="well well-sm">
    <h2 class="text-center"><?php echo @$Options[ 'site_name' ] ? $Options[ 'site_name' ] : __('Nom indisponible', 'nexo_premium');
    ?></h2>
    <h4 class="text-center"><?php echo sprintf(
        __('Rapport journalier détaillé <br> pour le %s', 'nexo_premium'),
        $CarbonReportDate->formatLocalized('%A %d %B %Y')
    );
    ?></h4>
    <?php
    $by            =    sprintf(__('Document imprimé par : %s', 'nexo_premium'), User::pseudo());
    ?>
    <p class="text-center"><?php echo $this->events->apply_filters('nexo_detailled_daily_report', $by);
    ?></p>
</div>
<div class="hidden-print"> <?php echo tendoo_info(__('Ensemble des activités effectuées par la caisse durant une période déterminée.', 'nexo_premium'));
    ?> </div>
<?php
/**
 * Disable Selecting Register when register option is disabled
 * @since 2.0.5
**/

if( ! in_array( @$Options[ 'nexo_enable_registers' ], array( null, 'non' ) ) ){
?>
<div class="input-group"> <span class="input-group-addon" id="basic-addon1"><?php echo _s( 'Selectionnez la caisse dont vous souhaitez afficher le rapport journalier', 'nexo_premium' );?></span>
    <select type="text" class="form-control" name="register_id">
        <option value="">
        <?php _e( 'Faites un choix', 'nexo_premium' );?>
        </option>
    </select>
</div>
<br />
<?php
} // End Filtering Register Options
?>

<table class="table table-bordered table-striped box">
    <thead>
        <tr>
            <td colspan="3"><?php echo __('Récapitulatif des recettes', 'nexo_premium');
    ?></td>
        </tr>
        <tr>
            <td><?php _e('Type de documents', 'nexo_premium');
    ?></td>
            <td><?php _e('Quantité', 'nexo_premium');
    ?></td>
            <td><?php echo sprintf(__('Chiffre d\'affaire collectif (%s)', 'nexo_premium'), @$Options[ 'nexo_currency' ]);
    ?></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php _e('Commandes Cash', 'nexo_premium');
    ?></td>
            <td id="cash_nbr"></td>
            <td id="cash_amount" class="text-right"></td>
        </tr>
        <tr>
            <td><?php _e('Commandes Avance', 'nexo_premium');
    ?></td>
            <td id="avance_nbr"></td>
            <td id="avance_amount" class="text-right"></td>
        </tr>
        <tr>
            <td><?php _e('Commandes Devis', 'nexo_premium');
    ?></td>
            <td id="devis_nbr"></td>
            <td id="devis_amount" class="text-right"></td>
        </tr>
        <?php if( @$Options[ 'nexo_enable_vat' ] == 'oui' ):?>
        <tr>
            <td id=""></td>
            <td><?php _e('Taxes collectées', 'nexo_premium');
    ?></td>
            <td id="collected_tax" class="text-right"></td>
        </tr>
        <?php endif;?>
        <?php if( @$Options[ 'nexo_enable_shadow_price' ] == 'yes' ):?>
        <tr>
            <td id=""></td>
            <td><?php _e('Bonus des prix fictifs', 'nexo_premium');
    ?></td>
            <!-- MARK -->
            <td id="shadow_price" class="text-right"></td>
        </tr>
        <?php endif;?>
        <tr>
            <td></td>
            <td><?php _e('Revenu journalier', 'nexo_premium');
    ?></td>
            <td id="cash_avance_amount_total" class="text-right"></td>
        </tr>
        <tr>
            <td></td>
            <td><?php _e('Revenu exigible', 'nexo_premium');
    ?></td>
            <td id="avance_amount_left_total" class="text-right"></td>
        </tr>
        <tr>
            <td></td>
            <td><?php _e('Total', 'nexo_premium');
    ?></td>
            <td id="daily_order_total_expected" class="text-right"></td>
        </tr>
    </tbody>
</table>

<!-- Récapitulatif des dépenses -->
<div class="hidden-print"> <?php echo tendoo_info(__('Récapitulatif des dépenses', 'nexo_premium'));
    ?> </div>
<table class="table table-bordered table-striped box">
    <thead>
        <tr>
            <td colspan="3"><?php _e('Récapitulatif des dépenses', 'nexo_premium');
    ?></td>
        </tr>
        <tr>
            <td><?php _e('Nom des documents', 'nexo_premium');
    ?></td>
            <td><?php echo sprintf(__('Montant (%s)', 'nexo_premium'), @$Options[ 'nexo_currency' ]);
    ?></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php _e('Remise + Rabais + Ristourne', 'nexo_premium');
    ?></td>
            <td id="rrr_total_amount" class="text-right"></td>
        </tr>
        <!-- Looper les fctures en dépenses -->
        <!-- Fin loop -->
        <tr id="before_total">
            <td><?php _e('Total dépense', 'nexo_premium');
    ?></td>
            <td id="charge_total_amount" class="text-right"></td>
        </tr>
    </tbody>
</table>

<!-- Bilan trésorerie -->
<div class="hidden-print before_final_table"> <?php echo tendoo_info(__('Bilan de la trésorerie<br><br>Chiffre d\'Affaire Net = ( Commandes Cash -  ( Remise + Rabais + Ristourne ) )', 'nexo_premium'));
    ?> </div>
<!-- Bilan trésorerie -->

<table class="table table-bordered table-striped box">
    <thead>
        <tr>
            <td><?php _e('Bilan tresorerie', 'nexo_premium');
    ?></td>
            <td><?php echo sprintf(__('Montant (%s)', 'nexo_premium'), @$Options[ 'nexo_currency' ]);
    ?></td>
        </tr>
    </thead>
    <tbody>
    	<?php

		/**
		 * If register option is enabled, initial balance row is shown
		 * @since 2.7.7
		**/

		if( @$Options[ 'nexo_enable_registers' ] == 'oui' ){
		?>
        <tr>
            <td><?php _e('Solde Initial', 'nexo_premium');
    ?></td>
            <td id="solde_initiale" class="text-right"></td>
        </tr>
        <?php
		}
		?>
        <tr>
            <td><?php _e('Recettes (+)', 'nexo_premium');?></td>
            <td id="recettes_total" class="text-right"></td>
        </tr>
        <tr>
            <td><?php _e('Dépenses (-)', 'nexo_premium'); ?></td>
            <td id="depenses_total" class="text-right"></td>
        </tr>
        <?php

		/**
		 * If register option is enabled, initial balance row is shown
		 * @since 2.7.7
		**/

		if( @$Options[ 'nexo_enable_registers' ] == 'oui' ){
		?>
        <tr>
            <td><?php _e('Solde Final', 'nexo_premium');?></td>
            <td id="solde_finale" class="text-right"></td>
        </tr>
        <?php
		}
		?>
        <tr>
            <td><?php _e('Chiffre d\'affaire net (*)', 'nexo_premium');?></td>
            <td id="ca_net" class="text-right"></td>
        </tr>
    </tbody>
</table>
<p><?php echo @$Options[ 'nexo_other_details' ];
    ?></p>
<script type="text/javascript">

"use strict";

var Nexo_Daily_Report	=	new function(){

	this.Orders			=	new Array();

	this.Reset			=	function(){
		this.ComptantNbr	=	0;
		this.AvanceNbr		=	0;
		this.DevisNbr		=	0;
		this.CashAvanceNbr	=	0;

		this.ComptantTotal	=	0;
		this.AvanceTotal	=	0;
		this.DevisTotal		=	0;
		this.CashAvanceTotal=	0;
		this.AvanceLeftTotal=	0;

		this.RRR_Total		=	0;
		this.Bills_Total	=	0;
		this.Global_Charges	=	0;

		this.RecettesTotales=	0;
		this.Depense_Totales=	0;

		// @since 2.7.5
		this.VAT_Total			=	0;
		this.ShadowPrice_Total	=	0;
		this.RealPrice_Total	=	0;
		this.Register_ID		=	null;

		this.CashAvanceTotal	=	0;
		this.CashAvanceNbr		=	0;
		this.AvanceLeftTotal	=	0;
		this.AvanceTotal		=	0;
		this.AvanceNbr			=	0;

		this.ComptantNbr		=	0;
		this.ComptantTotal		=	0;

		this.DevisNbr			=	0;
		this.DevisTotal			=	0;

		this.InitialBalance		=	0;
		this.ClosingBalance		=	0;

	}

	// @since 2.7.5
	this.LoadRegisters		=	function(){
		$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'registers', store_get_param( '?' ) ) );?>', {
			success			:	function( data ) {
				if( ! _.isEmpty( data ) ) {
					_.each( data, function( register, key ){
						$( '[name="register_id"]' ).append( '<option value="' + register.ID + '">' + register.NAME + '</option>' );
					})

					$( '[name="register_id"]' ).bind( 'change', function(){
						Nexo_Daily_Report.Reset();
						if( $( this ).val() != '' ) {
							Nexo_Daily_Report.Register_ID	=	$( this ).val();
							Nexo_Daily_Report.OrderReport();
						}
					});
				}
			},
			error			:	function(){
				bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant la récupération des caisses', 'nexo_premium' );?>' );
			}
		});
	}

	this.Init			=	function(){
		<?php
		/**
		 * When register is disabled, let load daily report directly
		 * @since 2.7.7
		**/
		if( in_array( @$Options[ 'nexo_enable_registers' ], array( null, 'non' ) ) ){
			?>
			this.Reset();
			this.Register_ID	=	0; // Since default register has "0" as id
			this.OrderReport();
			<?php
		} else {
			?>
			this.LoadRegisters();
			<?php
		}
		?>
	}
	this.OrderReport	=	function(){

		$.post(
			'<?php echo site_url(array( 'rest', 'nexo', 'order_by_dates' ));?>/nexo_order_comptant/' + this.Register_ID + '<?php echo store_get_param( '?' );?>',
			_.object(
				[ 'start', 'end' ],
				[ '<?php echo $CarbonReportDate->copy()->startOfDay();?>', '<?php echo $CarbonReportDate->copy()->endOfDay();?>' ]
			),
			function( orders ) {

				Nexo_Daily_Report.Orders	=	orders;
				var CashOrderCode			=	new Array;

				_.map( orders, function( value, key ) {

					Nexo_Daily_Report.RRR_Total			+=	( NexoAPI.ParseFloat( value.RABAIS ) + NexoAPI.ParseFloat( value.REMISE ) + NexoAPI.ParseFloat( value.RISTOURNE ) )

					if( _.indexOf( <?php echo json_encode( 
						$this->events->apply_filters( 'report_order_types', [ 'nexo_order_comptant' ] )
					);?>, value.TYPE ) != -1 ) {

						Nexo_Daily_Report.ComptantNbr++
						Nexo_Daily_Report.CashAvanceNbr++;
						Nexo_Daily_Report.ComptantTotal		+=	NexoAPI.ParseFloat( value.TOTAL );
						Nexo_Daily_Report.CashAvanceTotal 	+= 	NexoAPI.ParseFloat( value.TOTAL );
						CashOrderCode.push( value.CODE );

					} else if( value.TYPE == '<?php echo 'nexo_order_advance';?>' ) {

						Nexo_Daily_Report.AvanceNbr++
						Nexo_Daily_Report.CashAvanceNbr++;
						Nexo_Daily_Report.AvanceTotal		+=	NexoAPI.ParseFloat( value.SOMME_PERCU );
						Nexo_Daily_Report.CashAvanceTotal 	+= 	NexoAPI.ParseFloat( value.SOMME_PERCU );
						Nexo_Daily_Report.AvanceLeftTotal	+=	( NexoAPI.ParseFloat( value.TOTAL ) - NexoAPI.ParseFloat( value.SOMME_PERCU ) );

					} else if( value.TYPE == '<?php echo 'nexo_order_devis';?>' ) {

						Nexo_Daily_Report.DevisNbr++
						Nexo_Daily_Report.DevisTotal	+=	NexoAPI.ParseFloat( value.TOTAL );

					}

					// Sum VAT
					Nexo_Daily_Report.VAT_Total			+=	NexoAPI.ParseFloat( value.TVA );
				});

				// <!-- MARK -->
				$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'order_items_dual_item', store_get_param( '?' ) ) );?>', {
					dataType	:	'json',
					data		:	_.object( [ 'orders_code' ], [ CashOrderCode ] ),
					type		:	'POST',
					success		:	function( data ){

						_.each( data.order_items, function( _item, _key ){
							Nexo_Daily_Report.ShadowPrice_Total	+=	( NexoAPI.ParseFloat( _item.PRIX ) ); //  * parseInt( _item.QUANTITE )
						});

						_.each( data.items, function( _item, _key ){
							Nexo_Daily_Report.RealPrice_Total	+=	( NexoAPI.ParseFloat( _item.PRIX_DE_VENTE ) ); //  * parseInt( _item.QUANTITE )
						});

						Nexo_Daily_Report.BuildOutput();
					}
				});
				// <!-- MARK -->
			},
			'json'
		);
	}

	this.BuildOutput	=	function(){
		$( '#cash_nbr' ).html( this.ComptantNbr );
		$( '#avance_nbr' ).html( this.AvanceNbr );
		$( '#devis_nbr' ).html( this.DevisNbr );

		$( '#cash_amount' ).html( NexoAPI.DisplayMoney( this.ComptantTotal ) );
		$( '#avance_amount' ).html( NexoAPI.DisplayMoney( this.AvanceTotal ) );
		$( '#devis_amount' ).html( NexoAPI.DisplayMoney( this.DevisTotal ) );

		$( '#cash_avance_amount_total' ).html( NexoAPI.DisplayMoney( this.CashAvanceTotal ) );
		$( '#avance_amount_left_total' ).html( NexoAPI.DisplayMoney( this.AvanceLeftTotal ) );
		$( '#daily_order_total_expected' ).html( NexoAPI.DisplayMoney( this.AvanceLeftTotal + this.CashAvanceTotal ) );

		$( '#shadow_price' ).html( NexoAPI.DisplayMoney( Math.abs(
			NexoAPI.ParseFloat( Nexo_Daily_Report.ShadowPrice_Total - Nexo_Daily_Report.RealPrice_Total )
		) ) );

		this.GetCharges();
	}

	/**
	 * Get Charge
	 *
	**/

	this.GetCharges		=	function(){

		$( '#rrr_total_amount' ).html( NexoAPI.DisplayMoney( this.RRR_Total ) );
		// Temporarily
		$( '#charge_total_amount' ).html( NexoAPI.DisplayMoney( this.RRR_Total ) );

		$.post(
			'<?php echo site_url(array( 'nexo_bills', 'bills_by_date', store_get_param( '?' ) ));
    ?>',
			_.object( [ 'start', 'end' ], [ '<?php echo $CarbonReportDate->copy()->startOfDay();
    ?>', '<?php echo $CarbonReportDate->copy()->endOfDay();
    ?>' ] ),
			function( bills ) {
				_.map( bills, function( value, key ) {

					Nexo_Daily_Report.Bills_Total	+=	NexoAPI.ParseFloat( value.MONTANT );

					$( '#before_total' ).before( '<tr><td>' + value.INTITULE  + '</td><td class="text-right">' +  NexoAPI.DisplayMoney( NexoAPI.ParseFloat( value.MONTANT ) ) + '</td></tr>' );

				});

				Nexo_Daily_Report.Global_Charges	=	Nexo_Daily_Report.Bills_Total + Nexo_Daily_Report.RRR_Total;
				// Set global Charge
				$( '#charge_total_amount' ).html( NexoAPI.DisplayMoney( Nexo_Daily_Report.Global_Charges ) );

				<?php

				/**
				 * If register option is disabled, fetch activities is not required
				 * @since 2.7.7
				**/

				if( @$Options[ 'nexo_enable_registers' ] == 'oui' ):

				?>
				$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'register_activities_by_timerange' ) );?>/' + Nexo_Daily_Report.Register_ID + '<?php echo store_get_param( '?' );?>', {
					success		:	function( data ){
						console.log( data );

						var firstOpening	=	null;
						var lastClosing		=	null;

						if( ! _.isEmpty( data ) ) {
							_.each( data, function( activity, key ) {
								// First inital opening
								if( activity.TYPE == 'opening' && firstOpening == null ) {
									firstOpening	=	activity;
								}
								if( activity.TYPE	==	'closing' ) {
									lastClosing		=	activity;
								}
							});

							if( firstOpening != null ) {
								Nexo_Daily_Report.InitialBalance		=	NexoAPI.ParseFloat( firstOpening.BALANCE );
							}
							if( lastClosing	!= null ) {
								Nexo_Daily_Report.ClosingBalance		=	NexoAPI.ParseFloat( lastClosing.BALANCE );
							} else {
								$( '.register_notice' ).remove();
								$( '<div class="register_notice"><?php echo tendoo_warning( _s( 'Cette caisse n\'a pas été fermée en ce jour.', 'nexo_premium' ) );?></div>' ).insertAfter( '.before_final_table' );
							}

						} else {
							bootbox.alert( '<?php echo _s( 'Cette caisse n\'a pas été ouverte en ce jour', 'nexo_premium' );?>' );
						}
						Nexo_Daily_Report.Final_Results();
					},
					type		:	'POST',
					dataType	:	'json',
					data		:	_.object( [ 'start', 'end' ], [ '<?php echo $CarbonReportDate->copy()->startOfDay();?>', '<?php echo $CarbonReportDate->copy()->endOfDay();?>' ] )
				});
				<?php
				// End register activities
				else :

				/**
				 * If register is disabled, fetch directly Results
				 * @since 2.7.7
				**/
				?>

				Nexo_Daily_Report.Final_Results();

				<?php
				// End show results
				endif;
				?>
			}
		);
	}

	/**
	 * Final Result
	 *
	 * @return void
	**/

	this.Final_Results	=	function(){

		this.Recettes_Totales	=	( this.ComptantTotal + this.AvanceTotal ) + this.RRR_Total; // we add this.RRR_Total since it's has been excluded on order.TOTAL
		this.Depense_Totales	=	this.Bills_Total + this.RRR_Total;

		<?php
		/**
		 * If Resgister option is enabled
		 * @since 2.7.7
		**/
		if( @$Options[ 'nexo_enable_registers' ] == 'oui' ):
		?>

		$( '#solde_initiale' ).html( NexoAPI.DisplayMoney( this.InitialBalance ) );
		$( '#solde_finale' ).html( NexoAPI.DisplayMoney( this.ClosingBalance ) );

		<?php
		// End Register Options
		endif;
		?>

		$( '#recettes_total' ).html( NexoAPI.DisplayMoney( this.Recettes_Totales ) );
		$( '#depenses_total' ).html( NexoAPI.DisplayMoney( this.Depense_Totales ) );

		$( '#collected_tax' ).html( NexoAPI.DisplayMoney( this.VAT_Total ) );
		$( '#ca_net' ).html( NexoAPI.DisplayMoney( this.Recettes_Totales - this.Depense_Totales ) );
	};
};

$( document ).ready(function(e) {
    Nexo_Daily_Report.Init();
});
</script>
<?php

}

// save cache
if (! $Cache->get($report_slug) || @$_GET[ 'refresh' ] == 'true') {
    $Content    =    ob_get_clean();
    $Cache->save($report_slug, $Content, 999999999); // long time
} else {
    $Content    =    $Cache->get($report_slug);
}

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $Content
), 'daily_advanced_report', 1);

$this->Gui->output();
