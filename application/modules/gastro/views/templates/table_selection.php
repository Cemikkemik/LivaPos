<div ng-show="selectedTable != false || <?php echo store_option( 'disable_area_rooms' ) == 'yes' ? 'true' : 'false';?>" class="col-lg-4 col-md-4 col-xs-5 col-sm-5" style="overflow-y:scroll;height:{{ wrapperHeight }}px">
    <div class="text-center">
        <h4>
            <?php echo __( 'Table', 'gastro' );?> : {{ selectedTable.TABLE_NAME }}</h4>
    </div>
    <hr style="margin:0px;">
    <div class="row">
        <div class="col-md-6">
            <h4><strong><?php echo __( 'Maximum Seats', 'gastro' );?></strong> : {{ selectedTable.MAX_SEATS }}</h4>
            <h4><strong><?php echo __( 'Status', 'gastro' );?></strong> : {{ selectedTable.STATUS | table_status }}</h4>
        </div>
        <div class="col-md-6">
            <h4 ng-show="selectedTable.STATUS == 'available'"><strong><?php echo __( 'Seat Used', 'gastro' );?></strong> : {{ seatToUse }} <span class="label label-info"
                    ng-show="seatToUse > selectedTable.MAX_SEATS"><?php echo __( 'Limited to : ', 'gastro' );?> {{ selectedTable.MAX_SEATS }}</span></h4>
            <h4 ng-show="selectedTable.STATUS == 'in_use'"><strong><?php echo __( 'Seat Used', 'gastro' );?></strong> : {{ selectedTable.CURRENT_SEATS_USED }}</h4>
        </div>
    </div>

    <div class="alert alert-info" ng-show="selectedTable == false">
        <strong><?php _e( 'Info !', 'gastro' );?></strong>
        <?php echo __( 'You must select a table to choose the seat used', 'gastro' );?>.
    </div>

    <div class="btn-group btn-group-justified">
        <!-- <div class="btn-group" role="group" ng-show="selectedTable.STATUS == 'in_use'">
            <button ng-click="newOrder()" class="btn btn-primary">
                <i class="fa fa-plus"></i>
                <span class="hidden-xs"><?php echo __( 'New Order', 'nexo' );?></span>
            </button>
        </div> -->
        <div class="btn-group" role="group" ng-show="selectedTable.STATUS == 'in_use' && sessionOrder.TYPE == 'nexo_order_comptant'" >
            <button ng-click="setAvailable( selectedTable )" type="button" class="btn btn-success"><i class="fa fa-hand-paper-o"></i> <span class="hidden-xs"><?php echo __( 'Available', 'gastro' );?></span></button>
        </div>
        <div class="btn-group" role="group" ng-hide="isAreaRoomsDisabled">
            <button  ng-click="cancelTableSelection()" type="button" class="btn btn-default"><i class="fa fa-reply"></i> <span class="hidden-xs"><?php echo __( 'Return', 'gastro' );?></span></button>
        </div>
        <div class="btn-group" role="group" ng-show="isTableSelected()">
            <button ng-click="showHistory = true"  class="btn btn-primary"><i class="fa fa-clock-o"></i> <span class="hidden-xs"><?php echo __( 'History' );?></span></button>
        </div>
        <div class="btn-group" role="group" ng-show="showHistory">
            <button ng-click="showHistory = false"  class="btn btn-default"><i class="fa fa-remove"></i><span class="hidden-xs"><?php echo __( 'Close' );?></span></button>
        </div>
    </div>
    <div ng-show="sessionOrder && sessionOrder.TYPE != 'nexo_order_comptant'">
    <br>
        <div class="alert alert-info">
            <strong><?php echo __( 'Info', 'gastro' );?></strong> <?php echo __( 'You can\'t set a table as free if the placed orders aren\'t paid', 'nexo-restauran' );?>
        </div>
    </div>
    <h4 class="text-center" ng-show="compareAmount( sessionOrder.SOMME_PERCU, '<', sessionOrder.TOTAL  )"><?php echo __( 'Options for current session : ', 'gastro' );?> {{ sessionOrder.CODE }}</h4>
    <div class="btn-group btn-group-justified" ng-show="historyHasLoaded">
        <a class="btn btn-success btn-sm" ng-click="openCheckout( sessionOrder )" ng-show="compareAmount( sessionOrder.SOMME_PERCU, '<', sessionOrder.TOTAL  )"><i class="fa fa-shopping-cart"></i> <?php echo __( 'Checkout', 'gastro' );?></a>
        <!-- Everytime and order is paid, make the table available -->
        <a class="btn btn-info btn-sm" ng-click="addNewItem( sessionOrder )" ng-show="sessionOrder.TYPE != 'nexo_order_comptant'"><i class="fa fa-plus"></i> <?php echo __( 'New Item', 'gastro' );?></a>
        <a class="btn btn-default btn-sm" ng-click="printReceipt( sessionOrder )"><i class="fa fa-print"></i> <?php echo __( 'Print', 'nexo-restaurant' );?></a>
        <!-- <a class="btn btn-default btn-sm" ng-click="moveOrder( sessionOrder )"><i class="fa fa-arrow"></i> <?php echo __( 'Move', 'nexo-restaurant' );?></a> -->
    </div>
    <hr style="margin:10px 0;">
    <div ng-show="selectedTable.STATUS != 'out_of_use'">
        <!-- <div class="form-group" ng-show="selectedTable.STATUS == 'available'">
          <label for=""><?php echo __( 'Reservation duration time', 'gastro' );?></label>
          <select type="text" class="form-control" id="" placeholder="">
              <option ng-repeat="pattern in reservationPattern" value="{{ pattern }}">{{ pattern }} <?php echo __( 'Minute(s)', 'gastro' );?></option>
          </select>
          <p class="help-block"><?php echo __( 'This table will be set as reserved during the amount of time selected.', 'gastro' );?></p>
        </div> -->
        <div ng-show="selectedTable.STATUS != 'in_use' && selectedTable != false">
            <h4 class="text-center" style="margin-bottom: 0px"><?php echo  __( 'How many people join the party ?', 'gastro' );?></h4>
            <keyboard input_name="used_seat" keyinput="keyboardInput"
                hide-side-keys="hideSideKeys" hide-button="hideButton" />
        </div>
    </div>

    
</div>
<?php if( store_option( 'disable_area_rooms' ) != 'yes' ) :?>
<div ng-show="selectedTable === false" class="col-lg-4 col-md-4 col-sm-5 col-xs-5 bootstrap-tab-menu" style="height:{{ wrapperHeight }}px;border-left:solid 1px #EEE;">
    <div class="text-center">
        <h4>
            <?php echo __( 'Select an Area', 'gastro' );?>
        </h4>
    </div>
    <hr style="margin:0px;">
    <div class="list-group">
        <a ng-class="{ 'active' : area.active }" ng-click="loadTables( area )" ng-repeat="area in areas track by $index" class="text-left list-group-item"
            href="javascript:void(0)" style="border-left:0px solid transparent;margin: 0px; border-radius: 0px; border-width: 0px 0px 1px 1px; border-style: solid; border-bottom-color: rgb(222, 222, 222); line-height: 30px;border-left: solid 0px;">{{ area.NAME }}</a>
        <a ng-show="areas.length == 0" class="text-left list-group-item" href="javascript:void(0)" style="margin: 0px; border-radius: 0px; border-width: 0px 0px 1px 1px; border-style: solid; border-bottom-color: rgb(222, 222, 222); line-height: 30px;border-left: solid 0px;">
            <?php echo __( 'No Areas available', 'gastro' );?>
        </a>
    </div>
    <the-spinner spinner-obj="spinner" namespace="areas" />
</div>
<?php endif;?>
<div ng-show="! showHistory" class="col-lg-8 col-md-8 col-sm-7 col-xs-7" style="background:#f5f5f5;height:{{ wrapperHeight }}px;border-left:solid 1px #EEE;overflow-y:scroll">
    <div class="text-center">
        <h4>
            <?php echo __( 'Select a table', 'gastro' );?>
        </h4>
    </div>
    <hr style="margin:0px;">
    <div class="row">
        <br>
        <!-- table-animation {{ getTableColorStatus( table ) }} -->
        <!-- ng-dblclick="showHistory = true" -->
        <div class="col-md-3 col-sm-6 col-xs-6 text-center"  ng-click="selectTable( table )" ng-repeat="table in tables track by $index">
            <div class="box" ng-class="{ 'table-selected' : table.active }" style="padding:10px 0">
                <img ng-src="<?php echo module_url( 'gastro' ) . '/img/';?>table-{{ ( table.STATUS == 'in_use' ? 'busy-' : '' ) + table.MAX_SEATS }}.png"
                    style="width:90px" alt="">
                <p class="text-center">{{ table.TABLE_NAME == null ? table.NAME : table.TABLE_NAME }}</p>
                <p ng-show="table.STATUS == 'in_use'" class="timer">{{ getTimer( table.SINCE ) }}</p>
                <p ng-show="table.STATUS != 'in_use'" class="timer">--:--:--</p>
            </div>
        </div>
        <div class="col-md-12" ng-show="tables.length == 0">
            <?php echo tendoo_info( __( 'There is not table available. At least one table is needed to use that feature.', 'nexo' ) );?>
        </div>
    </div>
    <the-spinner spinner-obj="spinner" namespace="tables" />
</div>
<div ng-show="showHistory" class="historyContainer col-lg-8 col-md-8 col-sm-7 col-xs-7" style="background:#f5f5f5;height:{{ wrapperHeight }}px;border-left:solid 1px #EEE;overflow-y:scroll">
    <div class="text-center">
        <h4>
            <?php echo __( 'Table Order History', 'gastro' );?>
        </h4>
    </div>
    <hr style="margin:0px 0px;">
    <div ng-show="isEmptyObject( sessions )">
        <br>
        <?php echo tendoo_info( __( 'There is not order history for this table for the moment.' ) );?>
    </div>
    <div class="row" ng-repeat="(code, session) in sessions">
        <div class="col-md-12">
            <h4><?php echo sprintf( __( 'From <strong>%s</strong> to <strong>%s</strong>' ), '{{ session.starts }}', '{{ checkDate( session.ends ) }}' );?></h4>
        </div>
        <div class="col-md-12">
            <div class="row grid">
                <div class="col-md-12" ng-repeat="order in session.orders">
                    <div class="box">
                        <div class="box-header with-border">
                            <span class="label label-success" ng-show="order.TYPE == 'nexo_order_comptant'">
                                <i class="fa fa-money"></i> 
                                <?php echo __( 'Paid', 'gastro' );?> 
                            </span>
                            <span class="label label-warning" ng-show="order.TYPE != 'nexo_order_comptant'">
                                <i class="fa fa-money"></i>  
                                <?php echo __( 'Unpaid', 'gastro' );?>
                            </span>
                            <span style="margin-left:10px">{{ order.CODE }} </span>
                            <span class="pull-right">{{ order.RESTAURANT_ORDER_STATUS | capitalize }} - {{ order.WAITER_NAME | capitalize }}</span>
                        </div>
                        <div class="box-body" style="padding:0px 10px">
                            <div class="row">
                                <div class="col-md-6" ng-repeat="item in order.items">
                                    <ul class="products-list product-list-in-box">
                                        <li class="item" >
                                            <div class="product-img">
                                                <img ng-src="<?php echo get_store_upload_url() . '/items-images/';?>{{ item.APERCU }}" alt="{{ item.DESIGN }}">
                                            </div>
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">
                                                    {{ item.DESIGN || item.NAME }} ( x {{ item.QTE_ADDED }})
                                                    <span class="label label-warning pull-right">{{ item.PRIX - getModifierPrices( item.metas.modifiers ) * item.QTE_ADDED | moneyFormat }}</span>
                                                </a>
                                                <br>
                                                <span class="modifier-class" ng-if="item.metas.modifiers" ng-init="item_modifiers = jsonParse( item.metas.modifiers )">
                                                    &mdash; {{ item_modifiers[0].name }} <span class="label label-warning pull-right">{{ item_modifiers[0].price | moneyFormat }}</span>
                                                </span>
                                                <span class="product-description">
                                                ( {{ item.metas.restaurant_food_status }} )
                                                </span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                        </div>
                        <div class="box-footer" ng-show="order.TYPE != 'nexo_order_comptant' && compareAmount( order.SOMME_PERCU, '<', order.TOTAL  )" ng-init="sessionOrder = order">
                            <a class="btn btn-success btn-sm" ng-click="openCheckout(order)" ng-show="compareAmount( order.SOMME_PERCU, '<', order.TOTAL  )"><i class="fa fa-shopping-cart"></i> <?php echo __( 'Checkout', 'gastro' );?></a>
                            <!-- Everytime and order is paid, make the table available -->
                            <a class="btn btn-info btn-sm" ng-click="addNewItem(order)" ng-show="order.TYPE != 'nexo_order_comptant'"><i class="fa fa-plus"></i> <?php echo __( 'New Item', 'gastro' );?></a>
                            <!-- <a class="btn btn-primary btn-sm" ng-click="setAsServed( order.REF_ORDER )" ng-show="order.RESTAURANT_ORDER_STATUS == 'ready'"><i class="fa fa-cutlery"></i>  <?php echo __( 'Serve', 'gastro' );?></a> -->
                            <a class="btn btn-default btn-sm" ng-click="printReceipt(order)"><i class="fa fa-print"></i> <?php echo __( 'Print', 'nexo-restaurant' );?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <the-spinner spinner-obj="spinner" namespace="tableHistory" />
</div>
<my-spinner></my-spinner>