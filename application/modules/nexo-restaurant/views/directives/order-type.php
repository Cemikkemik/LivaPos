<script>
     tendooApp.directive( 'orderTypes', function(){
        return {
            templateUrl        :  '<?php echo site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'templates', 'order-types' ] );?>',
            restrict            :   'E',
            controller          :   [ '$scope', '$timeout', function( $scope, $timeout ) {
                $timeout( function(){
                    // angular.element( '.order-type-box .modal-dialog' ).css( 'width', '50%' );
                    angular.element( '.order-type-box .modal-body' ).css( 'padding-top', '0px' );
                    angular.element( '.order-type-box .modal-body' ).css( 'padding-bottom', '0px' );
                    angular.element( '.order-type-box .modal-body' ).css( 'padding-left', '0px' );
                    angular.element( '.order-type-box .modal-body' ).css( 'padding-right', '0px' );
                    angular.element( '.order-type-box .modal-body' ).css( 'overflow-x', 'hidden' );
                    angular.element( '.order-type-box .middle-content' ).css( 'padding', 0 );

                    $( '.order-type-box' ).removeClass( 'hidden' );
                    $( '.order-type-box .modal-dialog' ).css({
                        'top': function () {
                            return ( window.innerHeight / 2 ) - ( $( '.order-type-box .modal-dialog' ).height() / 2);
                        }
                    });

                    $( '.order-type-box' ).find( '.modal-footer' ).prepend( '<a href="<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'commandes', 'lists']);?>" class="btn btn-warning"><?php echo _s( 'Exit', 'nexo-restaurant' );?></a>' );

                    $( '.order-type-box [data-dismiss="modal"]' ).remove();
                    $( '.order-type-box [data-bb-handler="cancel"]' ).remove();
                    
                }, 100 );

                $scope.selectType       =   function( type ) {
                    _.each( $scope.types, function( _type ) {
                        _type.active       =   false;
                    });

                    type.active            =   true;
                    $scope.switchOrderType();
                }
            }]
        }
    })
</script>