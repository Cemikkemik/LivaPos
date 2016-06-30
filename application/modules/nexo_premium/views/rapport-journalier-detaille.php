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
    <h2 class="text-center"><?php echo @$Options[ 'site_name' ] ? $Options[ 'site_name' ] : __('Nom indisponible', 'nexo');
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
<div class="hidden-print">
    <?php echo tendoo_info(__('Ensemble des activités effectuées par la caisse durant une période déterminée.', 'nexo_premium'));
    ?>
</div>
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
        <tr>
            <td></td>
            <td><?php _e('Chiffre d\'affaire journalier (Commandes Cash + Commandes Avance)', 'nexo_premium');
    ?></td>
            <td id="cash_avance_amount_total" class="text-right"></td>
        </tr>
        <tr>
            <td></td>
            <td><?php _e('Chiffre d\'affaire à recouvrer (Créances)', 'nexo_premium');
    ?></td>
            <td id="avance_amount_left_total" class="text-right"></td>
        </tr>
    </tbody>
</table>

<!-- Récapitulatif des dépenses -->
<div class="hidden-print">
<?php echo tendoo_info(__('Récapitulatif des dépenses', 'nexo_premium'));
    ?>
</div>

<table class="table table-bordered table-striped box">
    <thead>
        <tr>
            <td colspan="3"><?php _e('Récapitulatif des dépenses', 'nexo_premium');
    ?></td>
        </tr>
        <tr>
            <td><?php _e('Désignation des documents', 'nexo_premium');
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
<div class="hidden-print">
<?php echo tendoo_info(__('Bilan de la trésorerie<br><br>Chiffre d\'Affaire Net = ( Commandes Cash -  ( Remise + Rabais + Ristourne ) )', 'nexo_premium'));
    ?>
</div>
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
    	<!--
        <tr>
            <td><?php _e('Solde Initial', 'nexo_premium');
    ?></td>
            <td id="solde_initiale" class="text-right"></td>
        </tr>
        -->
        <tr>
            <td><?php _e('Recettes (+)', 'nexo_premium');
    ?></td>
            <td id="recettes_total" class="text-right"></td>
        </tr>
        <tr>
            <td><?php _e('Dépenses (-)', 'nexo_premium');
    ?></td>
            <td id="depenses_total" class="text-right"></td>
        </tr>
        <!--
        <tr>
            <td><?php _e('Solde Final', 'nexo_premium');
    ?></td>
            <td id="depenses_total" class="text-right"></td>
        </tr>
        -->
        <tr>
            <td><?php _e('Chiffre d\'affaire net (*)', 'nexo_premium');
    ?></td>
            <td id="solde_final" class="text-right"></td>
        </tr>
        <!-- Commande doit - Remise -->
        <!--<tr>
            <td><?php _e('Chiffre d\'affaire net (*)', 'nexo_premium');
    ?></td>
            <td></td>
        </tr>-->
    </tbody>
</table>
<p><?php echo @$Options[ 'nexo_other_details' ];
    ?></p>
<script type="text/javascript">

"use strict";

var Nexo_Daily_Report	=	new function(){
	
	this.ComptantNbr	=	0;
	this.AvanceNbr		=	0;
	this.DevisNbr		=	0;
	this.CashAvanceNbr	=	0;
	
	this.ComptantTotal	=	0;
	this.AvanceTotal	=	0;
	this.DevisTotal		=	0;
	this.CashAvanceTotal=	0;
	this.AvanceLeftTotal=	0;
	
	this.Orders			=	new Array();
		
	this.RRR_Total		=	0;
	this.Bills_Total	=	0;
	this.Global_Charges	=	0;
	
	this.SoldInitiale	=	0;
	this.RecettesTotales=	0;
	this.Depense_Totales=	0;
	
	this.Init			=	function(){
		this.OrderReport();
	}
	this.OrderReport	=	function(){
		$.post( 
			'<?php echo site_url(array( 'nexo_orders', 'orders_by_date' ));
    ?>',
			_.object( [ 'start', 'end' ], [ '<?php echo $CarbonReportDate->copy()->startOfDay();
    ?>', '<?php echo $CarbonReportDate->copy()->endOfDay();
    ?>' ] ),
			function( orders ) {
				
				Nexo_Daily_Report.Orders	=	orders;
				
				_.map( orders, function( value, key ) {
					
					Nexo_Daily_Report.RRR_Total			+=	( NexoAPI.ParseFloat( value.RABAIS ) + NexoAPI.ParseFloat( value.REMISE ) + NexoAPI.ParseFloat( value.RISTOURNE ) )
					
					if( value.TYPE == '<?php echo 'nexo_order_comptant';
    ?>' ) {
						
						Nexo_Daily_Report.ComptantNbr++
						Nexo_Daily_Report.CashAvanceNbr++;
						Nexo_Daily_Report.ComptantTotal		+=	NexoAPI.ParseFloat( value.TOTAL );	
						Nexo_Daily_Report.CashAvanceTotal 	+= 	NexoAPI.ParseFloat( value.TOTAL );	
						console.log( NexoAPI.ParseFloat( value.TOTAL ) );
						
					} else if( value.TYPE == '<?php echo 'nexo_order_advance';
    ?>' ) {
						
						Nexo_Daily_Report.AvanceNbr++
						Nexo_Daily_Report.CashAvanceNbr++;
						Nexo_Daily_Report.AvanceTotal		+=	NexoAPI.ParseFloat( value.SOMME_PERCU );	
						Nexo_Daily_Report.CashAvanceTotal 	+= 	NexoAPI.ParseFloat( value.SOMME_PERCU );	
						Nexo_Daily_Report.AvanceLeftTotal	+=	( NexoAPI.ParseFloat( value.TOTAL ) - NexoAPI.ParseFloat( value.SOMME_PERCU ) );
						
					} else if( value.TYPE == '<?php echo 'nexo_order_devis';
    ?>' ) {
						
						Nexo_Daily_Report.DevisNbr++
						Nexo_Daily_Report.DevisTotal	+=	NexoAPI.ParseFloat( value.TOTAL );	
						
					}
				});
				
				Nexo_Daily_Report.BuildOutput();
				
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
			'<?php echo site_url(array( 'nexo_bills', 'bills_by_date' ));
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
				
				// Set global
				Nexo_Daily_Report.Final_Results();
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
		
		$( '#solde_initiale' ).html( NexoAPI.DisplayMoney( this.SoldInitiale ) );
		$( '#recettes_total' ).html( NexoAPI.DisplayMoney( this.Recettes_Totales ) );
		$( '#depenses_total' ).html( NexoAPI.DisplayMoney( this.Depense_Totales ) );
		$( '#solde_final' ).html( NexoAPI.DisplayMoney( this.Recettes_Totales - this.Depense_Totales ) );
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
