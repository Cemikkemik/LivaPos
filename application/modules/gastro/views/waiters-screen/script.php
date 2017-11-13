<script>
tendooApp.controller( 'waiterScreenCTRL', [ '$scope', '$timeout', '$http', function( $scope, $timeout, $http ){
     $scope.timeout      =    5000;
     $scope.orders       =    {};
     $scope.getOrders    =    function( timeout = $scope.timeout ){
          $timeout( () => {
               $http.get( '<?php echo site_url([ 'rest', 'gastro', 'ready_orders', store_get_param( '?' ) ]);?>', {
                    headers			:	{
                         '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo get_option( 'rest_key' );?>'
                    }
               })
               .then( function( returned ){
                    // join order by code
                    $scope.orders            =    {};
                    returned.data.forEach( item => {
                         if( $scope.orders[ item.CODE ] == undefined ) {
                              $scope.orders[ item.CODE ]         =    {
                                   items     :    [],
                                   code      :    item.CODE,
                                   title     :    item.TITRE,
                                   id        :    item.ORDER_ID,
                                   type      :    item.TYPE,
                                   restaurant_type     :    item.RESTAURANT_ORDER_TYPE
                              };
                         }

                         $scope.orders[ item.CODE ].items.push( item );
                    });

                    $scope.getOrders();
               }, function(){
                    $scope.getOrders();
               });
          }, timeout );
     }

     /**
      * Test Order Type
      * @param string
      * @return string
     **/

     $scope.testOrderType     =    function( type ) {
          let string;
          switch( type ) {
               case 'nexo_order_devis'  :    string    =    '<?php echo __( 'Unpaid', 'gastro' );?>';break;
               case 'nexo_order_avance'  :    string   =    '<?php echo __( 'Partially Paid', 'gastro' );?>';break;
               case 'nexo_order_comptant'  :    string      =    '<?php echo __( 'Paid', 'gastro' );?>';break;
               default: string     =    '<?php echo __( 'Unknow Type', 'gastro' );?>'; break;
          }
          return string;
     }

     /**
      * Test Restaurant Order Type
      * @param string
      * @return string
     **/

     $scope.testRestaurantType     =    function( type ) {
          let string;
          switch( type ) {
               case 'dinein' : string = '<?php echo _s( 'Dine In', 'gastro' );?>'; break;
               case 'takeaway' : string = '<?php echo _s( 'Take Away', 'gastro' );?>'; break;
               case 'delivery' : string = '<?php echo _s( 'Delivery', 'gastro' );?>'; break;
               case 'Booking' : string = '<?php echo _s( 'Booking', 'gastro' );?>'; break;
               default: string = '<?php echo __( 'Unknow Type', 'gastro' );?>'; break;
          }
          return string;
     }

     /**
      * Get Order Length
      * @param object all orders
      * @return number
     **/

     $scope.ordersLength           =    function( orders ) {
          return _.keys( orders ).length;
     }

     /**
      *  collectOorder
      * @param object order
      * @return void
     **/

     $scope.collectOrder     =    function( order ){
          $http.post( '<?php echo site_url([ 'rest', 'gastro', 'order_collected', store_get_param( '?' ) ]);?>', {
               order_id       :    order.id
          },{
               headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo get_option( 'rest_key' );?>'
               }
          })
          .then( function( returned ){
               NexoAPI.Toast()( '<?php echo __( 'The order has been collected', 'gastro' );?>' );
               $scope.getOrders(0);
          }, function(){
               NexoAPI.Toast()( '<?php echo __( 'An issue occured during the process', 'gastro' );?>' );
          });
     }

     $scope.getOrders(0);
}]);
</script>