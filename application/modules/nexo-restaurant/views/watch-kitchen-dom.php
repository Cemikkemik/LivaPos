<div class="row" ng-controller="watchRestaurantCTRL" ng-cloak="">
    <div class="col-md-9">
        <ul class="list-group" ng-repeat="( order_index, order ) in orders track by $index">
            <li href="#" class="list-group-item">
                <strong>#{{ order.CODE }}</strong> &mdash;
                <span ng-if="order.REAL_TYPE =='dine_in'">
                    <strong><?php echo __( 'Room', 'nexo-restaurant' );?></strong> : {{ order.ROOM_NAME }} >
                    <strong><?php echo __( 'Area', 'nexo-restaurant' );?></strong> : {{ order.AREA_NAME }} >
                    <strong><?php echo __( 'Table', 'nexo-restaurant' );?></strong> : {{ order.TABLE_NAME }}
                </span>
                <span ng-if="order.REAL_TYPE == 'take_away'">
                    <strong><?php echo __( 'Take Away', 'nexo-restaurant' );?></strong>
                </span>
                <div class="pull-right">
                    <p class="order-status">{{ getOrderStatus( order ) }}</p>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-md-6" ng-repeat="( meal_code, meal ) in order.meals track by $index">
                        <ul class="list-group">
                            <!-- ng-if="categoryCheck( item )"  -->
                            <!-- ng-show="order.TYPE != 'nexo_order_dine_ready'" -->
                            <li class="list-group-item"><strong><?php echo __( 'Meal', 'nexo-restaurant' );?></strong> : {{ meal_code }}</li>
                            <li  ng-repeat="( food_index, food ) in meal track by $index" ng-click="selectItem( food )" class="info list-group-item {{ food.active == true ? 'active' : '' }}" >
                                <span class="badge">{{ food.FOOD_STATUS }}</span>
                                &mdash; {{ food.DESIGN }} {{ food.MEAL }} (x{{ food.QTE_ADDED }}) <br> <p class="restaurant-note" ng-show="food.FOOD_NOTE != null && food.FOOD_NOTE != ''"><strong><?php echo __( 'Note', 'nexo-restaurant' );?></strong>: {{ food.FOOD_NOTE }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-md-5">
                        <div class="btn-group" ng-if="order.TYPE != 'nexo_order_dine_ready'">
                            <div class="btn-group btn-group-lg">
                                <button ng-click="selectAllItems( order_index )" class="btn btn-default btn-sm"><?php echo __( 'Select All', 'nexo-restaurant' );?></button>
                            </div>
                            <div class="btn-group btn-group-lg">
                                <button ng-click="unselectAllItems( order_index )" class="btn btn-default btn-sm"><?php echo __( 'Unselect All', 'nexo-restaurant' );?></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="btn-group btn-group-justified">

                            <div
                                ng-show="ifAllSelectedItemsIs( 'not_ready', order )"
                                ng-click="changeFoodState( order, 'in_preparation' )"
                                class="btn-group btn-group-lg">
                                <button class="btn btn-default"><?php echo __( 'Cook', 'nexo-restaurant' );?></button>
                            </div>

                            <div
                                ng-show="ifAllSelectedItemsIs( 'in_preparation', order )" 
                                ng-click="changeFoodState( order, 'ready' )"
                                class="btn-group btn-group-lg">
                                <button class="btn btn-default"><?php echo __( 'Ready', 'nexo-restaurant' );?></button>
                            </div>

                            <div ng-show="ifAllSelectedItemsIs( 'in_preparation', order )" ng-click="changeFoodState( order, 'issue' )" class="btn-group btn-group-lg">
                                <button class="btn btn-warning"><?php echo __( 'Issue', 'nexo-restaurant' );?></button>
                            </div>

                            <div ng-show="ifAllSelectedItemsIs( 'not_ready', order )" ng-click="changeFoodState( order, 'denied' )" class="btn-group btn-group-lg">
                                <button class="btn btn-danger"><?php echo __( 'Unavailable', 'nexo-restaurant' );?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div ng-if="orders.length == 0" class="list-group">
            <a href="#" class="list-group-item warning"><?php echo __( 'No order for this kitchen', 'nexo-restaurant' );?></a>
        </div>
    </div>
    <div class="col-md-3">
        <ul class="list-group">
            <li class="list-group-item"><?php echo __( 'Supported Categories', 'nexo-restaurant' );?></li>
            <li ng-repeat="category in categories track by $index" class="list-group-item">&mdash; {{ category.NOM }}</li>
        </ul>
        <div class="btn-group kitchen-buttons pull-right">
            <div class="btn-group btn-group-sm">
                <button ng-click="toggleFullScreen()" type="button" name="button" class="btn btn-primary">
                    <?php echo __( 'Toggle Full Screen', 'nexo-restaurant' );?>
                </button>
            </div>
        </div>
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
