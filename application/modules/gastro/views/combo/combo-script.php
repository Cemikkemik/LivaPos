<?php
global $Options;
$this->load->config( 'rest' );
?>
<script>
    tendooApp.directive( 'modifiers', function() {
        return {
            restrict            :   'E',
            template            :   <?php echo json_encode( $this->load->module_view( 'gastro', 'modifiers.dom', null, true ) );?>,
            controller          :      [ '$scope', '$attrs', '$http', '$compile', '$rootScope', function( $scope, $attrs, $http, $compile, $rootScope ){
                // override confirm button
                $( '[data-bb-handler="confirm"]' ).hide();
                $( '.modal-footer' ).append( $compile( `
                <a type="button" class="btn btn-success btn-lg ng-scope" ng-click="addModifier()"><?php echo __( 'Add Modifier', 'gastro' );?></a>
                ` )( $scope ) );    
                $( '.modal' ).css({
                    background  :   'rgba(136, 136, 136, 0.56)'
                });    

                // reset modifiers
                $scope.modifiers            =       [];

                /**
                 * Get Unique ID
                 * @param void
                 * @return void
                **/
                
                $scope.get_unique_id 	= function(){
                    let uniqueId
                    let debug       =   0;
                    
                    do {
                        uniqueId   =   Math.random().toString(36).substring(7);
                        debug++;
                        if( debug == 1000 ) {
                            return;
                        }
                    } while( _.indexOf( $scope.savedIDS, uniqueId ) != -1 );

                    return uniqueId;
                }

                /**
                 * Add Modifiers
                **/

                $scope.addModifier          =   function() {
                    let atLeastOneSelected  =   false;
                    let modifiersPrice       =   0;
                    let modifiersArray      =   [];

                    _.each( $scope.modifiers, ( modifier ) => {
                        if( parseInt( modifier.default ) == 1 ) {
                            atLeastOneSelected  =   true;
                            modifiersPrice  +=   parseFloat( modifier.price );
                            modifiersArray.push( modifier );
                        }
                    });

                    if( parseInt( $scope.modifiers[0].group_forced ) == 1 && atLeastOneSelected == false ) {
                        return NexoAPI.Toast()( '<?php echo _s( 'You must select at least one modifier.', 'gastro' );?>' );
                    }

                    if( ! $scope.currentItem.modifiersArray ) {
                        $scope.currentItem.modifiersArray   =   [];
                        $scope.currentItem.modifiersPrice   =   0;
                    }
                    
                    if( modifiersArray.length > 0 ){
                        $scope.currentItem.modifiersArray       =   _.union( 
                            $scope.currentItem.modifiersArray,
                            modifiersArray 
                        );
                    }

                    $scope.currentItem.modifiersPrice       +=   modifiersPrice;

                    if( $attrs.modifierGroupsLength > parseInt( $attrs.modifierIndex ) + 1 ) {
                        return $( '[data-bb-handler="confirm"]' ).trigger( 'click' );
                    } 
                    
                    // add new item with his modifier
                    let item                =   $scope.currentItem
                    // item.DESIGN         +=  modifiersLabels;
                    item.PRIX_DE_VENTE      =  parseFloat( item.PRIX_DE_VENTE ) + $scope.currentItem.modifiersPrice;
                    item.PRIX_DE_VENTE_TTC  =  parseFloat( item.PRIX_DE_VENTE_TTC ) + $scope.currentItem.modifiersPrice;
                    item.INLINE             =   true; // this item become inline since it's should be singular
                    item.CODEBAR            =   $scope.get_unique_id() + '-barcode-' + item.CODEBAR; // it definitely has to be 
                    // item.QTE_ADDED          =   1;
                    
                    // if meta is not set, then we'll set a default value
                    if( typeof item.metas == 'undefined' ) {
                        item.metas      =   new Object;
                    }

                    item.metas.modifiers    =   $scope.currentItem.modifiersArray;

                    // $scope.modifiers
                    // loop modifier price
                    v2Checkout.addToCart({
                        item, 
                        index       :   $attrs.index,
                        increase    :   false
                    });
                    // v2Checkout.addOnCart([item], $attrs.barcode, $attrs.qte, $attrs.increase == 'true' ? true : false );
                    $( '[data-bb-handler="confirm"]' ).trigger( 'click' );
                }

                $scope.modifiers            =   [];
                $scope.get_modifiers        =   function( group_id ) {
                    $http.get(
                        '<?php echo site_url( array( 'api', 'gastro', 'modifiers', 'by-group' ) );?>' + '/' +
                        group_id + '?<?php echo store_get_param( null );?>',
                    {
                        headers			:	{
                            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                        }
                    }).then(function( response ){
                        $scope.modifiers    =   response.data;
                        $scope.group_name   =   $scope.modifiers[0].group_name;

                        $( '#modifiers-' + $attrs.modifierIndex ).closest( '.modal-dialog' ).find( '.modal-title' ).html(
                            '<?php echo __( 'Choose a modifier for : <strong>#s<strong>', 'gastro' );?>'.replace( '#s', $scope.group_name )
                        )
                    });
                }

                /**
                 * Select Modifier
                **/

                $scope.select       =   ( modifier ) => {

                    if( modifier.default == '0' ) {
                        // check if modifier group allow multiple selection
                        if( modifier.group_multiselect == '0' ) {
                            // if the group doesn't allow multi select, just disable it
                            _.each( $scope.modifiers, ( _modifier ) => {
                                _modifier.default   =   '0';
                            });
                        }

                        modifier.default    =   '1';
                    } else {
                        modifier.default    =   '0';
                    }
                    
                }

                $scope.get_modifiers( $attrs.item );

                $rootScope.$on( 'close.modifierBox', ( scope, { ids, index, modifierIndex, increase, quantity, proceed, item } ) => {
                    delete v2Checkout.tempModifiedItem;
                });
            }]
        }
    });
</script>
<style>
.combo-item {
    display:block;
    font-size: 12px;
    border-bottom:solid 1px #EEE;
}
.modifiers-item {
    border: solid 1px #d2d2d2;
    height: 160px;
    margin-right: -1px;
    margin-bottom: -1px;
}

.modifiers-item:hover {
    box-shadow: inset 0px 0px 60px 0px #EEE;
    cursor: pointer;
}

.modifiers-item.active {
    box-shadow: inset 0px 0px 60px 0px #c1d3fd; 
}

.modifier-name {
    text-align: center;
    font-weight: 600;
    margin: 0;
}
.modifier-price {
    text-align: center;
    margin: 0;
}
.modifier-image {
    max-height: 90px;
    width: 100%;
    margin: 10px 0 5px;
    border-radius: 10px;
}

.modifiers-box {
  text-align: center;
  padding: 0!important;
}

.modifiers-box:before {
  content: '';
  display: inline-block;
  height: 100%;
  vertical-align: middle;
  margin-right: -4px;
}

.modifiers-box .modal-dialog {
  display: inline-block;
  text-align: left;
  vertical-align: middle;
}
</style>