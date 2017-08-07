<?php
! defined('APPPATH') ? die() : null;

global $Options;

$this->Gui->col_width(1, 4);

$this->Gui->add_meta(array(
    'namespace'        =>    'stats_ventes',
    'type'            =>    'unwrapped'
));

$this->events->add_filter('gui_page_title', function ($title) {
    return '<section class="content-header"><h1>' . strip_tags($title) . ' <span class="pull-right"><a class="btn btn-primary btn-sm" href="' . current_url() . '?refresh=true">' . __('Vider le cache', 'nexo_premium') . '</a> <a class="btn btn-default btn-sm" href="javascript:void(0)" print-item="#nexo-global-wrapper">' . __('Imprimer', 'nexo_premium') . '</a></span></h1></section>';
});

$this->events->add_action('dashboard_header', function () { ?>
<script type="text/javascript" src="<?php echo module_url('nexo');?>/bower_components/moment/min/moment.min.js"></script>
<script type="text/javascript" src="<?php echo module_url('nexo');?>/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo module_url('nexo');?>/bower_components/underscore/underscore-min.js"></script>
<link rel="stylesheet" href="<?php echo module_url('nexo');?>/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
<?php
});

if ($Cache->get($report_slug) == false || @$_GET[ 'refresh' ] == 'true') {
    // Start OB
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
			document.location =	'<?php echo site_url(array( 'dashboard', 'nexo_premium', 'Controller_Stats_Des_Ventes' ));
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
            __('Statistiques des ventes <br> pour %s', 'nexo_premium'),
            $CarbonReportDate->formatLocalized('%Y')
        );
    ?></h4>
    <?php
        $by            =    sprintf(__('Document imprimé par : %s', 'nexo_premium'), User::pseudo());
    ?>
    <p class="text-center"><?php echo $this->events->apply_filters('nexo_stats_des_ventes_report', $by);
    ?></p>
  </div>
  <div class="hidden-print"> <?php echo tendoo_info(__('Aperçu mensuel de l\'etat des ventes d\'articles selon la hierarchie des catégories. Tous les montants affichés sont brut.', 'nexo_premium'));
    ?> </div>
 <div class="table-responsive">
 		 <table class="table table-bordered table-striped box">
    <thead>
      <tr id="table_name">
        <td colspan="<?php echo count($Months) + 1 + intval($Categories_Depth);
    ?>"><?php _e('Statistique des ventes', 'nexo_premium');
    ?></td>
      </tr>
      <tr id="table-content">
        <?php

                // Counting Depth Length
                for ($i = 1; $i <= intval($Categories_Depth); $i++) {
                    echo '<td width="100">' . sprintf(__('Level %s', 'nexo_premium'), $i) . '</td>';
                }

    foreach ($Months as $Month) {
        ?>
        <td width="80" class="text-right"><?php echo $Month;
        ?></td>
        			<?php

    }
    ?>
        <td width="100" class="text-right"><strong>
          <?php _e('Total', 'nexo_premium');
    ?>
          </strong></td>
      </tr>
    </thead>
    <tbody id="bodyContent">
      <?php
        echo $this->Nexo_Misc->build_table($Categories_Hierarchy, $Categories_Depth, 1, '', count($Months) + 1);
    ?>
      <tr class="success">
        <?php for ($i = 0; $i < $Categories_Depth + count($Months) + 1; $i++):?>
        <?php if ($i == 0):?>
        <td><strong>
          <?php _e('Total', 'nexo_premium');
    ?>
          </strong></td>
        <?php else :?>
        <?php if ($i < $Categories_Depth):?>
        <td></td>
        <?php elseif (($i - $Categories_Depth) < count($Months)) :?>
        <td total-id="<?php echo $i - ($Categories_Depth - 1);
    ?>" class="text-right"></td>
        <?php else:?>
        <td global-total class="text-right"></td>
        <?php endif;
    ?>
        <?php endif;
    ?>
        <?php endfor;
    ?>
      </tr>
    </tbody>
  </table>
 </div>
  <hr />
  <p><?php echo @$Options[ 'nexo_other_details' ];
    ?></p>
  <br />
  <script type="text/javascript">

	"use strict";

	var NexoPremium_Sales_Statistics	=	new function(){

		this.CurrentDate				=	'<?php echo $CarbonReportDate->formatLocalized('%Y');
    ?>';
		this.LatestIds					=	new Array();
		this.Index						=	0;
		this.__TimeCalled				=	0;
		this.Nexo_Order_Avance			=	[ '<?php echo 'nexo_order_advance';
    ?>' ];
		this.Nexo_Order_Cash			=	<?php echo json_encode( $this->events->apply_filters( 'report_order_types', [ 'nexo_order_comptant' ] ) );?>;
		this.Nexo_Order_Devis			=	[ '<?php echo 'nexo_order_devis';
    ?>' ];
		this.CurrencyBefore				=	'<?php echo $this->Nexo_Misc->display_currency('before');
    ?>';
		this.CurrencyAfter				=	'<?php echo $this->Nexo_Misc->display_currency('after');
    ?>';


		/**
		 * Get Latest category Ids
		 *
		**/

		this.FetchLatestIds				=	function(){
			$( 'table tbody tr' ).each( function(){
				NexoPremium_Sales_Statistics.LatestIds.push( $( this ).find( '[data-id]' ).last().attr( 'data-id' ) );
			});

			// Fill table

			this.FillValue();
		};

		/**
		 * Init
		**/

		this.Init						=	function(){
			// FetchLatestIds
			this.FetchLatestIds();
		};

		/**
		 * Fill Value
		**/

		this.FillValue					=	function(){

			this.EntryLength	=	12; // 12 for the number of month in a year
			this.Reset();
			this.__FillTableValue();

		};

		/**
		 * __FillTableValue()
		 * Fetch ccontent from db
		**/

		this.__FillTableValue			=	function( depth ) {
			if( this.Index < this.EntryLength ) {
				// Looping IDS
				$.ajax( '<?php echo site_url(array( 'nexo_premium', 'sales_stats' ));
    ?>' + '/' + this.CurrentDate + '<?php echo store_get_param( '?' );?>', {
					beforeSend	:	function(){
						NexoPremium_Sales_Statistics.DisplayModal();
					},
					data		:	_.object( [ 'ids', 'month' ], [ this.LatestIds, this.Index + 1 ] ),
					type		:	'POST',
					dataType	:	'json',
					success		:	function( data ){
						NexoPremium_Sales_Statistics.TreatProducts( data );
						NexoPremium_Sales_Statistics.__FillTableValue();
						NexoPremium_Sales_Statistics.Index++;
					}
				});
			} else {
				this.ColSubTotal();
				this.RowSubTotal();
				this.GlobalTotal();
				this.CloseModal();
			}
		}

        /**
        *
        * cartValue
        *
        * @param
        * @return
        */

        this.cartValue          =   function( item, inlineDiscount ){
            var cartValue                       =   ( parseFloat( item.TOTAL )
            // Valeur réelle du panier
            + ( parseFloat( item.REMISE ) ) ) // + parseFloat( item.RABAIS ) + parseFloat( item.RISTOURNE )
            // Restauration de la TVA
            - parseFloat( item.TVA );

            if( inlineDiscount === true ) {
                // Exclure aussi les remises effectués sur les produits
                if( item.DISCOUNT_TYPE == 'percentage' && item.DISCOUNT_PERCENT != '0' ) {
                    cartValue       +=  ( parseInt( item.PRIX_DE_VENTE ) * parseInt( item.DISCOUNT_PERCENT ) ) / 100;
                } else  { // in this case for fixed discount on item
                    cartValue       +=  parseInt( item.DISCOUNT_AMOUNT );
                }
            }

            return cartValue;
        };

		/**
		 * Treat Product
		 * @param object
		**/

		this.TreatProducts		=	function( data ) {
			_.each( data, function( value, key ){
				var TotalCommandeCash	=	0;
				_.each( value, function( item, key ) {
					if( _.contains( NexoPremium_Sales_Statistics.Nexo_Order_Cash, item.TYPE_COMMANDE ) || 
							_.contains( NexoPremium_Sales_Statistics.Nexo_Order_Avance, item.TYPE_COMMANDE ) 
						) {
						// Exclure la TVA du Rapport
						// @since 2.9.6
						var $total   =   ( NexoAPI.ParseFloat( item.PRIX_TOTAL ) );
						if( item.REMISE_TYPE == 'percentage' ) {
							var $percentage =   (
									parseFloat( item.REMISE_PERCENT ) *
									parseFloat( item.PRIX_TOTAL )
							) / 100;
							$total      =   NexoAPI.ParseFloat( item.PRIX_TOTAL ) - $percentage;
						} else if( item.REMISE_TYPE == 'flat' ) {
							// exclude discount made on item
							var     percent     =   ( NexoAPI.ParseFloat( item.REMISE ) * 100 ) / NexoPremium_Sales_Statistics.cartValue( item, true );
							var     $valueOff      =   ( NexoAPI.ParseFloat( item.PRIX_DE_VENTE ) * percent ) / 100;

							$total      =   NexoAPI.ParseFloat( item.PRIX_TOTAL ) - $valueOff;
						}
						TotalCommandeCash	+=	$total;
					}
				});

				$( 'table tbody tr' ).eq( key - 1 ).find( '[month-id="' + NexoPremium_Sales_Statistics.Index + '"]' ).html(
					NexoPremium_Sales_Statistics.CurrencyBefore + ' ' +
					'<span class="amount" amount="' + TotalCommandeCash + '">' + NexoAPI.Format( TotalCommandeCash ) + '</span>' +
					NexoPremium_Sales_Statistics.CurrencyAfter + ' '
				);
			});
		}

		/**
		 * Do Calculate Sub Total
		**/

		this.RowSubTotal		=	function(){
			// Cal Sub total
			$( '[total-id]' ).each( function(){
				var StatVenteRowTotal	=	0;
				$( '[month-id="' + $( this ).attr( 'total-id' ) + '"]' ).each( function(){
					StatVenteRowTotal +=	NexoAPI.ParseFloat( $( this ).find( '.amount' ).attr( 'amount' ) );
				});
				$( this ).html(
					NexoPremium_Sales_Statistics.CurrencyBefore + ' ' +
					'<span class="amount" amount="' + StatVenteRowTotal + '">' + NexoAPI.Format( StatVenteRowTotal ) + '</span>' +
					NexoPremium_Sales_Statistics.CurrencyAfter + ' '
				);
			});
		}

		/**
		 * Col Total
		**/

		this.ColSubTotal		=	function(){
			// Cal Sub total
			$( '[col-total]' ).each( function(){
				var StatVenteColTotal	=	0;
				$( this ).siblings( '[month-id]' ).each( function(){
					StatVenteColTotal +=	NexoAPI.ParseFloat( $( this ).find( '.amount' ).attr( 'amount' ) );
				});
				$( this ).html(
					NexoPremium_Sales_Statistics.CurrencyBefore + ' ' +
					'<span class="amount" amount="' + StatVenteColTotal + '">' + NexoAPI.Format( StatVenteColTotal ) + '</span>' +
					NexoPremium_Sales_Statistics.CurrencyAfter + ' '
				);
			});
		}

		/**
		 * Global Total
		**/

		this.GlobalTotal		=	function(){
			var SalesStatsGlobal	=	0;
			$( '[global-total]' ).closest( 'tr' ).find('[total-id]' ).each( function(){
				SalesStatsGlobal +=	NexoAPI.ParseFloat( $( this ).find( '.amount' ).attr( 'amount' ) );
			});
			$( '[global-total]' ).html(
				NexoPremium_Sales_Statistics.CurrencyBefore + ' ' +
				'<span class="amount" amount="' + SalesStatsGlobal + '">' + NexoAPI.Format( SalesStatsGlobal ) + '</span>' +
				NexoPremium_Sales_Statistics.CurrencyAfter + ' '
			);
		};

		/**
		 * Cols Row Total
		**/

		this.SaleStatsRow		=	function(){

		};

		/**
		 * Reset the calendar
		**/

		this.Reset			=	function(){
			this.__TimeCalled	=	1;
			$( '.progress_level' ).html( 1 );
			$( '.progress-bar' ).css( 'width', '1%' ).data( 'aria-valuenow', 1 );
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
        NexoPremium_Sales_Statistics.Init();
    });

	</script>

  <!-- Button trigger modal -->
  <button type="button" class="btn btn-primary btn-lg launch_loading" data-toggle="modal" data-target="#myModal" style="display:none;"></button>
  <div class="modal fade hidden-print" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">
            <?php _e('Chargement en cours...', 'nexo_premium');
    ?>
          </h4>
        </div>
        <div class="modal-body">
          <div class="progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"> <span class="progress_level">0</span>% </div>
          </div>
        </div>
        <div class="modal-footer"> </div>
      </div>
    </div>
  </div>
</div>
<?php
    $Content    =    ob_get_clean();
}
// Fetch cache if exists
if ($Cache->get($report_slug) == false || @$_GET[ 'refresh' ] == 'true') {
    $Cache->save($report_slug, $Content, 999999999); // long time
} else {
    $Content    =    $Cache->get($report_slug);
}

$this->Gui->add_item(array(
    'type'        =>    'dom',
    'content'    =>    $Content
), 'stats_ventes', 1);

$this->Gui->output();
