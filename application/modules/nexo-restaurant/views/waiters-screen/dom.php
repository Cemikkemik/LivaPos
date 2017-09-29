<div class="row" ng-controller="waiterScreenCTRL">
     <div class="col-md-4 col-lg-3 col-sm-6 col-xs-12" ng-repeat="( code, order ) in orders track by $index">
          <div class="box box-widget widget-user-2">
               <!-- Add the bg color to the header using any of the bg-* classes -->
               <div class="widget-user-header bg-primary">
                    <h5>
                         <?php echo sprintf( __( 'Order : %s', 'nexo-restaurant' ), '{{ code }}' );?>
                    </h5>
                    <small>{{ testRestaurantType( order.restaurant_type ) }} &mdash; {{ testOrderType( order.type ) }}</small>
               </div>
               <div class="box-footer no-padding">
                    <ul class="nav nav-stacked">
                         <!-- <span class="pull-right badge bg-blue">31</span> -->
                         <li ng-repeat="item in order.items"><a href="#">{{ item.NAME }} </a></li>
                         <li ng-if="order.items.length == 0"><?php echo __( 'No item available', 'nexo-restaurant' );?></li>
                         <li style="padding:10px 10px">
                              <button ng-click="collectOrder( order )" class="btn btn-sm btn-primary">
                                   <?php echo __( 'Collect', 'nexo-restaurant' );?>
                              </button>
                         </li>
                    </ul>
                    
               </div>
          </div>
     </div>
     <div class="col-md-12" ng-show="ordersLength( orders ) == 0">
          <?php echo tendoo_info( __( 'There is no ready order for the meantime.', 'nexo-restaurant' ) );?>
     </div>
</div>