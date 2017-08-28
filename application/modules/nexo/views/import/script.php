<?php
global $Options;
$columns                    =   array(
    'DESIGN'                =>  __( 'Nom du produit', 'nexo' ),
    'DESCRIPTION'           =>  __( 'Description du produit', 'nexo' ),
    'SKU'                   =>  __( 'Unite de Gestion de Stock', 'nexo' ),
    'CODEBAR'               =>  __( 'Code barre', 'nexo' ),
    'QUANTITY'              =>  __( 'Quantite', 'nexo' ),
    'PRIX_DE_VENTE'         =>  __( 'Prix de vente', 'nexo' ),
    'PRIX_DACHAT'           =>  __( 'Prix d\'achat', 'nexo' ),
    'REF_SHIPPING'          =>  __( 'Collection', 'nexo' ),
    'REF_CATEGORIE'         =>  __( 'Categorie', 'nexo' ),
    'REF_RAYON'             =>  __( 'Rayon', 'nexo' ),
    'TAILLE'                =>  __( 'Taille', 'nexo' ),
    'DESCRIPTION'           =>  __( 'Description', 'nexo' ),
);
?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/services/textHelper.php' );?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/services/csv.php' );?>
<script type="text/javascript">
    "use strict";

    tendooApp.directive('fileReader', function() {
      return {
        scope: {
          fileReader:"=",
          fileExtension : "="
        },
        link: function(scope, element) {
          $(element).on('change', function(changeEvent) {
              var value = element.val();
              var ext   = value.substring(value.lastIndexOf('.') + 1).toLowerCase();
              if( ext != 'csv' && ext != 'xls' ){
                  NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Ce type de fichier n\'est pas autorisé.', 'nexo' );?>')
                  element.val( '' );
                  return false;
              }
              var files = changeEvent.target.files;
            if (files.length) {
              var r = new FileReader();
              r.onload = function(e) {
                  var contents = e.target.result;
                  scope.$apply(function () {
                    scope.fileReader    =   contents;
                    scope.fileExtension =   ext;
                  });
              };

              r.readAsText(files[0]);
            }
          });
        }
      };
    });

    tendooApp.controller( 'importCSVController', [ '$scope', '$http', 'textHelper', 'csv', function( $scope, $http, textHelper, csv ) {

        $scope.columns_data         =   new Array;
        $scope.csvArray             =   new Array;
        $scope.autoSku              =   false;

        $scope.fileContent          =  '';
        $scope.ext                  =   '';
        $scope.columns              =   <?php echo json_encode( $columns );?>;
        $scope.notices              =   new Array;
        $scope.addToCurrentItems    =   false;
        $scope.refresh              =   'true';
        $scope.overwrite            =   'true';
    

        $scope.$watch( 'fileContent', function(){
            if( $scope.fileContent != '' ) {
                $scope.csvArray     =   csv.toArray( $scope.fileContent );
            }
        });

        /**
         *  Create Items
         *  @param
         *  @return
        **/

        $scope.createItems  =   function( data, collection_categories_ids ) {
            let shipping        =   _.values( collection_categories_ids.shippings )[0];
            _.each( data.items, function( item, key ) {
                var category_id     =   false;

                if( isNaN( item.REF_CATEGORIE ) ) {
                    category_id     =   _.propertyOf( collection_categories_ids.categories )( textHelper.toUrl( item.REF_CATEGORIE, '_', true ) );
                }

                data.items[key].REF_CATEGORIE   =  category_id ? category_id : 1 ;
                // Use defined category or

                if( $scope.enableForSale && angular.isUndefined( data.items[ key ].STATUS ) ) {
                    data.items[key].STATUS      =   1;
                }

                if( $scope.enableStockManagement && angular.isUndefined( data.items[ key ].STOCK_ENABLED ) ) {
                    data.items[key].STOCK_ENABLED      =   1;
                }

                if( $scope.enablePhysical && angular.isUndefined( data.items[ key ].TYPE ) ) {
                    data.items[key].TYPE      =   1;
                }

                if( $scope.remainingQteMatchesQte && angular.isUndefined( data.items[ key ].QUANTITE_RESTANTE ) ) {
                    data.items[key].QUANTITE_RESTANTE      =   data.items[key].QUANTITY;
                }

            });

            $http.post( '<?php echo site_url( array( 'rest', 'nexo', 'create_bulk_items', store_get_param('?') ) );?>',{
                'items'     :   data.items,
                'author'    :   <?php echo User::id();?>,
                'date'      :   tendoo.now(),
                'refresh'   :   $scope.addToCurrentItems ? 'true' : 'false',
                'overwrite' :   $scope.overwrite,
                'shipping_id'   :   shipping
            },{
    			headers			:	{
    				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
    			}
    		}).then( function( returned ) {

                $scope.notices.push({
                    msg     :   '<?php echo _s( 'Importation Terminée', 'nexo' );?>',
                    type    :   'info'
                });

            }, function(){
                $scope.notices.push({
                    msg     :   '<?php echo _s( 'Une erreur s\'est produite', 'nexo' );?>',
                    type    :   'warning'
                });
            });
        }

        /**
         *  Create Shipping and Categories
         *  @param
         *  @return
        **/

        $scope.createShippingAndCategories  =   function( data ){
            $http.post( '<?php echo site_url( array( 'rest', 'nexo', 'create_shipping_categories', store_get_param('?') ) );?>',{
                'shippings'  :  data.shippings,
                'cats'      :   data.categories,
                'default_shipping_title'    :   '<?php echo _s( 'Collection Importée', 'nexo' );?>',
                'author'        :   <?php echo User::id();?>,
                'date'      :   tendoo.now(),
                'default_cat_title'    :   '<?php echo _s( 'Categorie Importée', 'nexo' );?>',
                'refresh' :     $scope.addToCurrentItems ? 'true' : 'false',
                'overwrite'     :   $scope
            },{
    			headers			:	{
    				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
    			}
    		}).then( function( returned ) {

                $scope.notices.push({
                    msg     :   '<?php echo _s( 'Création des éléments', 'nexo' );?>',
                    type    :   'info'
                });

                $scope.createItems( data, returned.data );

            }, function(){
                $scope.notices.push({
                    msg     :   '<?php echo _s( 'Une erreur s\'est produite', 'nexo' );?>',
                    type    :   'warning'
                });
            });
        }

        /**
         *  Check Options
         *  @param string option namespace
         *  @return void
        **/

        $scope.checkOptions         =   function( column_id ) {
            var valueToSearch       =   _.propertyOf( $scope.columns_data )( column_id );
            var occurence           =   0;
            _.each( $scope.columns_data, function( value, key ) {
                if( key != column_id && valueToSearch == value) {
                    $scope.columns_data[ column_id ] = '';
                    NexoAPI.Notify().warning( '<?php echo _s( 'Option en cours d\'utilisation', 'nexo' );?>', '<?php echo _s( 'Vous ne pouvez pas choisir cette option car elle est déjà en cours d\'utilisation par la colonne : ', 'nexo' );?>' + key );
                }
            });
        }

        /**
         *  Submit CSV
         *  @return void
        **/

        $scope.submitCSV            =   function(){

            if( $scope.fileContent.length == 0 ) {
                return false;
            }

            if( _.indexOf( $scope.columns_data, 'DESIGN' ) == -1 ) {
                NexoAPI.Notify().warning( '<?php echo _s( 'Champ necessaire absent', 'nexo' );?>', '<?php echo _s( 'Vous devez au moins choisir l\'option "nom du produit" pour l\'un des champs disponible', 'nexo' );?>' );
                return;
            }

            if( _.indexOf( $scope.columns_data, 'SKU' ) == -1 && $scope.autoSku ) {
                NexoAPI.Notify().warning( '<?php echo _s( 'Champ necessaire absent', 'nexo' );?>', '<?php echo _s( 'Vous devez au moins choisir l\'option "Unité de gestion de stock" pour l\'un des champs disponible', 'nexo' );?>' );
                return;
            }

            // if( _.indexOf( $scope.columns_data, 'barcode' ) == -1 ) {
            //     NexoAPI.Notify().warning( '<?php echo _s( 'Champ necessaire absent', 'nexo' );?>', '<?php echo _s( 'Vous devez au moins choisir l\'option "Code barre" pour l\'un des champs disponible', 'nexo' );?>' );
            //     return;
            // }

            $scope.notices      =   new Array;

            $scope.notices.push({
                msg     :   '<?php echo _s( 'Analyse du fichier en cours...', 'nexo' );?>',
                type    :   'info'
            });

            $http.post( '<?php echo site_url( array( 'rest', 'nexo', 'import_csv', store_get_param('?') ) );?>',{
                'cols'  :   $scope.columns_data,
                'csv'   :   $scope.fileContent,
                'ext'   :   $scope.ext
            },{
    			headers			:	{
    				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
    			}
    		}).then( function( returned ) {
                $scope.notices.push({
                    msg     :   '<?php echo _s( 'Création des collections et catégories...', 'nexo' );?>',
                    type    :   'info'
                });

                $scope.createShippingAndCategories( returned.data );

            }, function(){
                $scope.notices.push({
                    msg     :   '<?php echo _s( 'Une erreur s\'est produite', 'nexo' );?>',
                    type    :   'warning'
                });
            });
        }
    }]);
</script>
