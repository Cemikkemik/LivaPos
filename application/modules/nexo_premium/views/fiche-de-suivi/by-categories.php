<div class="input-group"> <span class="input-group-btn">
  <button class="btn btn-default trigger-shipping-selection" type="button">
  <?php _e('Choisir l\'arrivage', 'nexo_premium');?>
  </button>
  </span>
  <select name="select_shipping" class="form-control">
    <?php if ($shippings):?>
    <?php foreach ($shippings as $ship):?>
    <option value="<?php echo @$ship[ 'ID' ];?>"><?php echo $ship[ 'TITRE' ];?></option>
    <?php endforeach;?>
    <?php else:?>
    <option>
    <?php _e('Aucun arrivage disponible', 'nexo_premium');?>
    </option>
    <?php endif;?>
  </select>
</div>
<br>
<?php echo tendoo_info(__('Ce tableau vous permet de suivre les approvisionnements et les sorties d\'une collection, tout en vous permettant de voir les quantités restantes dans le stock général.', 'nexo_premium'));?>
<?php echo tendoo_warning(__('Les stocks affichés ici, ne sont que les stocks des produits dont les catégories sont situées au fond de la hierarchie des catégories. Les produits sauvegardés dans des catégories intermédiaires seront ignorés.', 'nexo_premium'));?>
<div class="table-responsive">
<table class="table-bordered table table-striped FicheDeSuiviParCategory">
    <thead>
      <tr>
        <td colspan="7"><?php _e('Fiche de suivi de stock', 'nexo_premium');?></td>
      </tr>
      <tr>
        <td width="300"><?php _e('Categories', 'nexo_premium');?></td>
        <td width="100"><?php _e('Entrées', 'nexo_premium');?></td>
        <td width="150"><?php _e( 'Valeur', 'nexo_premium' );?></td>
        <td width="100"><?php _e('Sorties', 'nexo_premium');?></td>
        <td width="150"><?php _e( 'Valeur', 'nexo_premium' );?></td>
        <td width="100"><?php _e('Stock Final', 'nexo_premium');?></td>
        <td width="150"><?php _e( 'Valeur', 'nexo_premium' );?></td>
      </tr>
    </thead>
    <tbody class="fiche-suivi-table">
    </tbody>
    <tfoot>
      <tr class="info">
        <td width="300"><?php _e('Total', 'nexo_premium');?></td>
        <td class="text-right" width="100"></td>
        <td money-format class="text-right" width="150"></td>
        <td class="text-right" width="100"></td>
        <td money-format class="text-right" width="150"></td>
        <td class="text-right" width="100"></td>
        <td money-format class="text-right" width="150"></td>
      </tr>
		</tfoot>
  </table>
</div>
<script>
'use strict';

var NexoFicheSuivi		=	new function(){

	/**
	 * Build Category List
	 *
	 * @return void
	**/

	this.BuildCategoryList	=	function( e ) {
		var i, object	=	e;
		for( i = 0; i < e.length ; i++ ){
			var _i;
			for( _i = 0; _i < e.length ; _i++ ) {
				if( parseInt( e[_i].ID ) == parseInt( e[i].PARENT_REF_ID ) ) {
					object	=	_.omit( object, _i );
				}
			}
		}

		var s		=	0, FinalArray	=	new Array, _s;

		function getParent( i ){
			var Parents		=	new Array;
			do {
				_.each( e, function( value, e ) {
					if( value.ID == i ){
						Parents.unshift( value );
						i	=	value.PARENT_REF_ID;
					};
				})
			} while( i != '0' );
			return Parents;
		}

		this.ParentsHierarchy	=	new Array;
		this.LatestCategories	=	object;

		_.each( object, function( value, key ) {
			var CurrentParent	=	getParent( value.PARENT_REF_ID );
				CurrentParent.push( value );
				NexoFicheSuivi.ParentsHierarchy.push( CurrentParent );
		});

		_.each( this.ParentsHierarchy, function( value, key ){
			var RowContent		=	'<tr>';
				RowContent		+=		'<td>' + _.last( value ).NOM + '</td>' +
										//'<td data-previous-stock cat-id="' + _.last( value ).ID + '"></td>' +
										'<td class="text-right" data-new-stock cat-id="' + _.last( value ).ID + '"></td>' +
										'<td class="text-right" data-new-value money-format cat-id="' + _.last( value ).ID + '"></td>' +
										'<td class="text-right" data-sold-stock cat-id="' + _.last( value ).ID + '"></td>' +
										'<td class="text-right" data-sold-value money-format cat-id="' + _.last( value ).ID + '"></td>' +
										'<td class="text-right" data-left-stock cat-id="' + _.last( value ).ID + '"></td>' +
										'<td class="text-right" data-left-value money-format cat-id="' + _.last( value ).ID + '"></td>';
				RowContent		+=	'</tr>';
			$( '.fiche-suivi-table' ).append( RowContent );
		});
	}

	/**
	 * Get Previous Stock content
	 *
	 * @param int collection id
	 * @return void
	**/

	this.LoadPreviousStock		=	function( shipping_id ){
		if( _.isObject( this.LatestCategories ) ) {
			$.ajax( '<?php echo site_url(array( 'nexo_premium', 'previous_stock' ));?>' + '/' + shipping_id + '<?php echo store_get_param( '?' );?>', {
				type 		:		'POST',
				dataType	:		'json',
				data		:		_.object( [ 'categories_id' ], [ this.LatestCategories ] ),
				success		:		function( content ) {
					NexoFicheSuivi.FillColStock( 'previous', content, shipping_id );
					NexoFicheSuivi.LoadCurrentStock( shipping_id );
				}
			});
		} else {
			alert( 'An error occured. Latest categories can\'t be retreived' );
		}
	};

	/**
	 * Fill stock col on table
	 *
	 * @param object
	 * @return void
	**/

	this.FillColStock			=	function( stock, products, shipping_id ) {

		var	ProductsStocks			=	new Array;
		var ProductsValues			=	new Array;

		//  Loop product and add it to an array with latest category id as key

		_.each( products, function( value, key ) {
			if( _.isUndefined( ProductsStocks[ value.REF_CATEGORIE ] ) ) {
				ProductsStocks[ value.REF_CATEGORIE ]	=	0;
				ProductsValues[ value.REF_CATEGORIE ]	=	0;
			}
			// according to case, we select old quantity, the new quantity, the sold quantity and the left one
			if( stock == 'previous' ) {
				ProductsStocks[ value.REF_CATEGORIE ]	+=	parseInt( value.QUANTITE_RESTANTE );
				ProductsValues[ value.REF_CATEGORIE ]	+=	( parseInt( value.QUANTITE_RESTANTE ) * parseFloat( value.PRIX_DE_VENTE ) );
			} else if( stock == 'new' ) {
				ProductsStocks[ value.REF_CATEGORIE ]	+=	( parseInt( value.QUANTITY ) - parseInt( value.DEFECTUEUX ) );
				ProductsValues[ value.REF_CATEGORIE ]	+=	( ( parseInt( value.QUANTITY ) - parseInt( value.DEFECTUEUX ) ) * parseFloat( value.PRIX_DE_VENTE ) );
			} else if( stock == 'sold' ) {
				ProductsStocks[ value.REF_CATEGORIE ]	+=	parseInt( value.QUANTITE_VENDU );
				ProductsValues[ value.REF_CATEGORIE ]	+=	( parseInt( value.QUANTITE_VENDU ) * parseFloat( value.PRIX_DE_VENTE ) )
			}
		});

		// If nothing is returned

		if( _.isEmpty( ProductsStocks ) ) {

			_.each( this.LatestCategories, function( value, key ) {
				ProductsStocks[ value.ID ]	 =	0;
			});

		}

		// Fills Row

		_.each( this.LatestCategories, function( value, key ) {

			$( '[data-' + stock + '-stock][cat-id="' + value.ID + '"]' ).html(
				_.isUndefined( ProductsStocks[ value.ID ] ) ? 0 : ProductsStocks[ value.ID ]
			);

			$( '[data-' + stock + '-value][cat-id="' + value.ID + '"]' ).html(
				_.isUndefined( ProductsValues[ value.ID ] ) ? 0 : ProductsValues[ value.ID ]
			);

		});
	}

	/**
	 * Load Current Stock
	 * @param Int shipping ID
	 * @return void
	**/

	this.LoadCurrentStock			=	function( shipping_id ){
		$.post( '<?php echo site_url(array( 'nexo_premium', 'current_stock' ));?>' + '/' + shipping_id + '<?php echo store_get_param( '?' );?>',
		_.object( [ 'categories_id' ], [ this.LatestCategories ] ),
		function( current_stock ){
			NexoFicheSuivi.FillColStock( 'new', current_stock, shipping_id );
			NexoFicheSuivi.FillColStock( 'sold', current_stock, shipping_id );
			NexoFicheSuivi.TotalRow();
			NexoFicheSuivi.TotalCols();
		});
	};

	/**
	 * Total on Row
	 * @return void
	**/

	this.TotalRow			=	function(){
		$( '[data-left-stock]' ).each(function(){
			// var PreviousTotal	=	parseInt( $( this ).siblings( '[data-previous-stock]' ).html() );
			var CurrentEntry	=	parseInt( $( this ).siblings( '[data-new-stock]' ).html() );
			var CurrentSold		=	parseInt( $( this ).siblings( '[data-sold-stock]' ).html() );
			// ( PreviousTotal );
			$( this ).html( CurrentEntry - CurrentSold );
		});

		$( '[data-left-value]' ).each(function(){
			// var PreviousTotal	=	parseInt( $( this ).siblings( '[data-previous-stock]' ).html() );
			var CurrentValueEntry	=	parseInt( $( this ).siblings( '[data-new-value]' ).html() );
			var CurrentValueSold	=	parseInt( $( this ).siblings( '[data-sold-value]' ).html() );
			// ( PreviousTotal );
			$( this ).html( CurrentValueEntry - CurrentValueSold );
		});
	};

	/**
	 * Total Cols
	 * @return void
	**/

	this.TotalCols			=	function(){
		var i;
		for( i = 1; i <= 6; i++ ) { // Seconds Cols
			var ColTotal		=	0;
			$( '.fiche-suivi-table tr' ).each( function(){
				ColTotal		+=	parseInt( $(this).find( 'td' ).eq(i).html() );
			} );

			if( typeof $( '.FicheDeSuiviParCategory tfoot tr td' ).eq(i).attr( 'money-format' ) == 'undefined' ) {
				$( '.FicheDeSuiviParCategory tfoot tr td' ).eq(i).html( ColTotal );
			} else {
				$( '.FicheDeSuiviParCategory tfoot tr td' ).eq(i).html( NexoAPI.DisplayMoney( ColTotal ) );
			}
		}

		// Add Price format everywhere

		for( i = 0; i <= 2; i++ ) { // Seconds Cols
			var ColTotal		=	0;
			$( '.fiche-suivi-table tr' ).each( function(){
				$(this).find( 'td[money-format]' ).eq(i).html( NexoAPI.DisplayMoney( $(this).find( 'td[money-format]' ).eq(i).html() ) );
			} );
		}
	};

};

$( document ).ready(function(e) {
    $.ajax( '<?php echo site_url(array( 'nexo_categories', 'get', store_get_param( '?' ) ));?>', {
		success		:	function( e ) {
			NexoFicheSuivi.BuildCategoryList( e );
		},
		dataType	:	"json"
	});
	$( '.trigger-shipping-selection' ).bind( 'click', function(){
		 NexoFicheSuivi.LoadPreviousStock( $( '[name="select_shipping"]' ).val() );
	});
});
</script>
