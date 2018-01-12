tendooApp.controller( 'groupedItemCTRL', [ '$scope', '$http', function( $scope, $http ) {
    
    $scope.searchStatus         =   'not_found';
    $scope.grouped_items        =   [];
    $scope.categories           =   grouped_items.categories;
    $scope.barcodes             =   grouped_items.barcodes;
    $scope.form                 =   {};
    $scope.taxes                =   grouped_items.taxes;

    /**
     * searchitems
     * @param string field to search
     * @return void
     */
    $scope.searchItem       =   function( search_field ) {
        $scope.searchStatus     =   'searching';
        $http.post( grouped_items.api_url, {
            search      :   search_field
        }, {
            headers     :   {
                [ tendoo.rest.key ] : tendoo.rest.value
            }
        }).then( result => {
            let entries     =   result.data;
            if ( entries.length == 0 ) {
                $scope.searchStatus     =   'not_found';
            } else if( entries.length > 1 ) {
                $scope.searchStatus     =   'found';
                $scope.entries          =   entries;
            } else {
                $scope.addToGrouped( entries[0] );
            }
            $scope.search_string     =   '';
        })
    }

    /**
     * Add item to grouped items
     * @param object item
     * @return void
     */
    $scope.addToGrouped     =   function( item ) {
        let alreadyExists   =   false;
        $scope.grouped_items.forEach( ( _item ) => {
            if ( _item.barcode == item.CODEBAR ) {
                alreadyExists   =   true;
                _item.quantity++;
            }
        });

        if ( ! alreadyExists ) {
            $scope.grouped_items.push({
                name            :   item.DESIGN,
                sale_price      :   item.PRIX_DE_VENTE,
                quantity        :   1,
                type            :   item.TYPE,
                barcode         :   item.CODEBAR
            });
        }
    }

    /**
     * Get total for listed item
     * @param object of items
     * @return number 
     */
    $scope.getTotal     =   function( items ) {
        let total       =   0;
        if( items ) {
            items.forEach( item => {
                total       +=  ( parseFloat( item.sale_price ) * parseFloat( item.quantity ) );
            })
        }
        return total;
    }

    /**
     * increase quantity
     * @param int index
     * @return void
     */
    $scope.increase     =   function( index ) {
        $scope.grouped_items[ index ].quantity++;
    }

    /**
     * decrease quantity
     * @param int index
     * @return void
     */
    $scope.decrease     =   function( index ) {
        $scope.grouped_items[ index ].quantity--;
        if ( $scope.grouped_items[ index ].quantity == 0 ) {
            $scope.removeFromGroup( index );
        }
    }

    /**
     * getTotalQuantity
     * @param object of items
     * @return int
     */
    $scope.getTotalQuantity     =   function( items ) {
        let total   =   0;
        items.forEach( item => {
            total   +=  item.quantity;
        });
        return total;
    }

    /**
     * Grouped items
     * @param object of items
     * @return void
     */
    $scope.submitItem       =   function( items ) {
        if ( [ '', null, undefined ].indexOf( $scope.item_name ) != -1 ) {
            return NexoAPI.Notify().warning(
                grouped_items.text.warning,
                grouped_items.text.missing_item_name
            );
        }

        if ( $scope.grouped_items.length == 0 ) {
            return NexoAPI.Notify().warning(
                grouped_items.text.warning,
                grouped_items.text.missing_items
            );
        }

        let formHasError    =   false;
        _.mapObject( $scope.form, ( value, key ) => {
            console.log( value );
            if ( [ 'category_id', 'state', 'sku', 'sale_price' ].indexOf( key ) != -1 ) {
                if ( value == '' ) {
                    formHasError    =   true;
                }
            }
        });
        
        if ( formHasError ) {
            return NexoAPI.Notify().warning(
                grouped_items.text.warning,
                grouped_items.text.formHasError
            );
        }
        $http.post( grouped_items.post_item_url, {
            items,
            item_name   :   $scope.item_name,
            form        :   $scope.form
        }, {
            headers     :   {
                [ tendoo.rest.key ]     :   tendoo.rest.value
            }
        }).then( result => {
            $scope.grouped_items    =   [];
        })
    }

    /**
     * Remove From Group
     * @param int
     * @return void
     */
    $scope.removeFromGroup  =   function( index ) {
        $scope.grouped_items.splice( index, 1 );
    }
}])

tendooApp.directive('selectpicker', ['$parse', function ($parse) {
    return {
      restrict: 'A',
      link: function (scope, element, attrs) {
        element.selectpicker($parse(attrs.selectpicker)());
        element.selectpicker('refresh');
        
        scope.$watch(attrs.ngModel, function (newVal, oldVal) {
          scope.$parent[attrs.ngModel] = newVal;
          scope.$evalAsync(function () {
            if (!attrs.ngOptions || /track by/.test(attrs.ngOptions)) element.val(newVal);
            element.selectpicker('refresh');
          });
        });
        
        scope.$on('$destroy', function () {
          scope.$evalAsync(function () {
            element.selectpicker('destroy');
          });
        });
      }
    };
}]);