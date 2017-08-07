<div class="row" ng-controller="watchRestaurantCTRL" ng-cloak="">
    <div class="col-md-4" ng-repeat="( column_index, column ) in columns">
        <div class="box" ng-repeat="( order_index, order ) in column track by $index" ng-hide="order.TYPE == 'nexo_order_takeaway_ready' || order.TYPE == 'nexo_order_dinein_ready' || order.TYPE == 'nexo_order_delivery_ready'">
            <div class="box-header with-border">
                <strong>#{{ order.CODE }}</strong> &mdash;
                <span ng-if="order.REAL_TYPE =='dinein'">
                    <strong><?php echo __( 'Dine In', 'nexo-restaurant' );?></strong>
                </span>
                <span ng-if="order.REAL_TYPE == 'takeaway'">
                    <strong><?php echo __( 'Take Away', 'nexo-restaurant' );?></strong>
                </span>
                <span ng-if="order.REAL_TYPE == 'delivery'">
                    <strong><?php echo __( 'Delivery', 'nexo-restaurant' );?></strong>
                </span>
                (<span am-time-ago="order.DATE_CREATION  | amParse: 'YYYY-MM-DD HH:mm:ss' "></span>)
                <span class="pull-right">
                    <span class="order-status">{{ getOrderStatus( order ) }}</span>
                </span>
            </div>
            <div ng-if="order.REAL_TYPE == 'dinein'" class="box-header with-border text-center">
                <?php if( store_option( 'disable_meal_feature' ) != 'yes' ):?>
                <strong><?php echo __( 'Room', 'nexo-restaurant' );?></strong> : {{ order.ROOM_NAME }} >
                <strong><?php echo __( 'Area', 'nexo-restaurant' );?></strong> : {{ order.AREA_NAME }} >
                <?php endif;?>
                <strong><?php echo __( 'Table', 'nexo-restaurant' );?></strong> : {{ order.TABLE_NAME }}
            </div>
            <div class="box-body">
                <div class="row" ng-hide="order.TYPE == 'nexo_order_takeaway_ready' || order.TYPE == 'nexo_order_dinein_ready' || order.TYPE == 'nexo_order_delivery_ready'">
                    <div class="{{ order.meals.length > 1 ? 'col-md-6' : 'col-md-12' }}" ng-repeat="( meal_code, meal ) in order.meals track by $index">
                        <ul class="list-group" style="margin-bottom:0">
                            <!-- ng-if="categoryCheck( item )"  -->
                            <!-- " -->
                            <li class="list-group-item" ng-hide="meal_code == 'null'">
                                <strong><?php echo __( 'Meal', 'nexo-restaurant' );?></strong> : {{ meal_code }}
                            </li>
                            <li  ng-repeat="( food_index, food ) in meal track by $index" ng-click="selectItem( food )" class="info list-group-item {{ food.active == true ? 'active' : '' }}" >
                                <span class="badge">{{ food.FOOD_STATUS }}</span>
                                {{ food.DESIGN == null ? food.NAME : food.DESIGN }} {{ food.MEAL }} (x{{ food.QTE_ADDED }}) <br> 
                                <p class="restaurant-note" ng-show="modifier.default == 1" ng-repeat="modifier in food.MODIFIERS track by $index">
                                    + {{ modifier.name }}
                                </p>
                                <p class="restaurant-note" ng-show="food.FOOD_NOTE != null && food.FOOD_NOTE != ''"><strong><?php echo __( 'Note', 'nexo-restaurant' );?></strong>: {{ food.FOOD_NOTE }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div ng-if="orders.length == 0" class="list-group">
                    <a href="#" class="list-group-item warning"><?php echo __( 'No order for this kitchen', 'nexo-restaurant' );?></a>
                </div>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-md-7">
                        <div class="btn-group">
                            <div class="btn-group ">
                                <button ng-click="selectAllItems( order )" class="btn btn-default btn-sm"><?php echo __( 'Select All', 'nexo-restaurant' );?></button>
                            </div>
                            <div class="btn-group ">
                                <button ng-click="unselectAllItems( order )" class="btn btn-default btn-sm"><?php echo __( 'Unselect All', 'nexo-restaurant' );?></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="btn-group btn-group-justified">

                            <div
                                ng-show="ifAllSelectedItemsIs( 'not_ready', order )"
                                ng-click="changeFoodState( order, 'in_preparation' )"
                                class="btn-group ">
                                <button class="btn btn-default"><?php echo __( 'Cook', 'nexo-restaurant' );?></button>
                            </div>

                            <div
                                ng-show="ifAllSelectedItemsIs( 'in_preparation', order )" 
                                ng-click="changeFoodState( order, 'ready' )"
                                class="btn-group ">
                                <button class="btn btn-default"><?php echo __( 'Ready', 'nexo-restaurant' );?></button>
                            </div>

                            <!--<div ng-show="ifAllSelectedItemsIs( 'in_preparation', order )" ng-click="changeFoodState( order, 'issue' )" class="btn-group ">
                                <button class="btn btn-warning"><?php echo __( 'Issue', 'nexo-restaurant' );?></button>
                            </div>-->

                            <!--<div ng-show="ifAllSelectedItemsIs( 'not_ready', order )" ng-click="changeFoodState( order, 'denied' )" class="btn-group ">
                                <button class="btn btn-danger"><?php echo __( 'Unavailable', 'nexo-restaurant' );?></button>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12" ng-show="columns.length == 0">
        <?php echo tendoo_info( __( 'No order has been placed for this kitchen', 'nexo-restaurant' ) );?>
    </div>
</div>
<style media="screen">
    .restaurant-note {
        border: solid 1px #d8d8d8;
        border-radius: 10px;
        padding: 5px 10px;
        margin: 5px 0;
        background: #F2F2F2;
    }

    .active .restaurant-note{
        color : #333;
    }

    .order-status {
        padding: 0 20px;
        text-align: center;
        border: solid 1px #1d5d7b;
        border-radius: 10px;
        background: #abe4ff;
        font-weight: 600;
    }
</style>
