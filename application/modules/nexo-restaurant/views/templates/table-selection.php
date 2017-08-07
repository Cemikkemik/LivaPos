<div ng-show="selectedTable != false || <?php echo store_option( 'disable_area_rooms' ) == 'yes' ? 'true' : 'false';?>" class="col-lg-4 col-md-4 col-xs-5 col-sm-5" style="height:{{ wrapperHeight }}px">
    <div class="text-center">
        <h4><?php echo __( 'Table', 'nexo-restaurant' );?> : {{ selectedTable.TABLE_NAME }}</h4>
    </div>
    <hr style="margin:0px;">
    <div class="row">
        <div class="col-md-6">
            <h4><strong><?php echo __( 'Maximum Seats', 'nexo-restaurant' );?></strong> : {{ selectedTable.MAX_SEATS }}</h4>
            <h4><strong><?php echo __( 'Status', 'nexo-restaurant' );?></strong> : {{ selectedTable.STATUS | table_status }}</h4>
        </div>
        <div class="col-md-6">
            <h4 ng-show="selectedTable.STATUS == 'available'"><strong><?php echo __( 'Seat Used', 'nexo-restaurant' );?></strong> : {{ seatToUse }} <span class="label label-info" ng-show="seatToUse > selectedTable.MAX_SEATS"><?php echo __( 'Limited to : ', 'nexo-restaurant' );?> {{ selectedTable.MAX_SEATS }}</span></h4>
            <h4 ng-show="selectedTable.STATUS == 'in_use'"><strong><?php echo __( 'Seat Used', 'nexo-restaurant' );?></strong> : {{ selectedTable.CURRENT_SEATS_USED }}</h4>
        </div>
    </div>
    <hr style="margin:10px 0;">
    <div ng-show="selectedTable.STATUS != 'out_of_use'">
        <!-- <div class="form-group" ng-show="selectedTable.STATUS == 'available'">
          <label for=""><?php echo __( 'Reservation duration time', 'nexo-restaurant' );?></label>
          <select type="text" class="form-control" id="" placeholder="">
              <option ng-repeat="pattern in reservationPattern" value="{{ pattern }}">{{ pattern }} <?php echo __( 'Minute(s)', 'nexo-restaurant' );?></option>
          </select>
          <p class="help-block"><?php echo __( 'This table will be set as reserved during the amount of time selected.', 'nexo-restaurant' );?></p>
        </div> -->
        <keyboard ng-show="selectedTable.STATUS != 'in_use' && selectedTable != false" input_name="used_seat" keyinput="keyboardInput" hide-side-keys="hideSideKeys" hide-button="hideButton"/>
    </div>
    
    <div class="alert alert-info" ng-show="selectedTable == false">
    <strong><?php _e( 'Info !', 'nexo-restaurant' );?></strong> <?php echo __( 'You must select a table to choose the seat used', 'nexo-restaurant' );?>.
    </div>

    <div class="btn-group btn-group-lg">
        <button class="btn btn-primary" ng-show="true == false"></button>

        <!-- <button ng-show="selectedTable.STATUS == 'available'" ng-click="setUsedSeat( selectedTable )" type="button" class="btn btn-primary"><?php echo __( 'Seats in use', 'nexo-restaurant' );?></button> -->
        <!-- <button ng-show="selectedTable.STATUS == 'available'" ng-click="setAsReserved( selectedTable )" type="button" class="btn btn-warning"><?php echo __( 'Set as reserved', 'nexo-restaurant' );?></button> -->
        <button ng-show="selectedTable.STATUS == 'in_use'" ng-click="setAvailable( selectedTable )" type="button" class="btn btn-success"><?php echo __( 'Set as available', 'nexo-restaurant' );?></button>
        <!-- <button ng-show="selectedTable.STATUS == 'reserved'" ng-click="cancelReservation( selectedTable )" type="button" class="btn btn-warning"><?php echo __( 'Cancel reservation', 'nexo-restaurant' );?></button> -->

        <button ng-hide="isAreaRoomsDisabled" ng-click="cancelTableSelection()" type="button" class="btn btn-default"><?php echo __( 'Show Areas', 'nexo-restaurant' );?></button>

        <button class="btn btn-primary" ng-show="true == false"></button>
    </div>
</div>
<?php if( store_option( 'disable_area_rooms' ) != 'yes' ) :?>
<div ng-show="selectedTable === false" class="col-lg-2 col-md-2 col-sm-3 col-xs-3 bootstrap-tab-menu">
    <div class="text-center">
        <h4><?php echo __( 'Select a Room', 'nexo-restaurant' );?></h4>
    </div>
    <hr style="margin:0px;">
    <div class="list-group">
        <a ng-class="{ 'active' : room.active }" ng-click="loadRoomAreas( room )" ng-repeat="room in rooms" class="text-left list-group-item" href="javascript:void(0)" style="margin: 0px; border-radius: 0px; border-width: 0px 0px 1px 1px; border-style: solid; border-bottom-color: rgb(222, 222, 222); line-height: 30px;border-left: solid 0px;">{{ room.NAME }}</a>
        <a ng-show="rooms.length == 0" class="text-left list-group-item" href="javascript:void(0)" style="margin: 0px; border-radius: 0px; border-width: 0px 0px 1px 1px; border-style: solid; border-bottom-color: rgb(222, 222, 222); line-height: 30px;border-left: solid 0px;"><?php echo __( 'No Rooms available', 'nexo-restaurant' );?></a>
    </div>
</div>
<div ng-show="selectedTable === false" class="col-lg-2 col-md-2 col-sm-3 col-xs-3 bootstrap-tab-menu" style="height:{{ wrapperHeight }}px;border-left:solid 1px #EEE;">
    <div class="text-center">
        <h4><?php echo __( 'Select an Area', 'nexo-restaurant' );?></h4>
    </div>
    <hr style="margin:0px;">
    <div class="list-group">
        <a ng-class="{ 'active' : area.active }" ng-click="loadTables( area )" ng-repeat="area in areas" class="text-left list-group-item" href="javascript:void(0)" style="border-left:0px solid transparent;margin: 0px; border-radius: 0px; border-width: 0px 0px 1px 1px; border-style: solid; border-bottom-color: rgb(222, 222, 222); line-height: 30px;border-left: solid 0px;">{{ area.AREA_NAME }}</a>
        <a ng-show="areas.length == 0" class="text-left list-group-item" href="javascript:void(0)" style="margin: 0px; border-radius: 0px; border-width: 0px 0px 1px 1px; border-style: solid; border-bottom-color: rgb(222, 222, 222); line-height: 30px;border-left: solid 0px;"><?php echo __( 'No Areas available', 'nexo-restaurant' );?></a>
    </div>
    <the-spinner spinner-obj="spinner" namespace="areas"/>
</div>
<?php endif;?>
<div class="col-lg-8 col-md-8 col-sm-7 col-xs-7" style="height:{{ wrapperHeight }}px;border-left:solid 1px #EEE;overflow-y:scroll">
    <div class="text-center">
        <h4><?php echo __( 'Select a table', 'nexo-restaurant' );?></h4>
    </div>
    <hr style="margin:0px;">
    <div class="row">
        <br>
        <div class="col-md-3 text-center table-animation {{ getTableColorStatus( table ) }}" ng-click="selectTable( table )" ng-repeat="table in tables">
            <div class="">
                <img ng-src="<?php echo module_url( 'nexo-restaurant' ) . '/img/';?>table-{{ ( table.STATUS == 'in_use' ? 'busy-' : '' ) + table.MAX_SEATS }}.png" style="width:90px" alt="">
                <p class="text-center">{{ table.TABLE_NAME == null ? table.NAME : table.TABLE_NAME }}</p>
                <p ng-show="table.STATUS == 'in_use'" class="timer">{{ getTimer( table.SINCE ) }}</p>
                <p ng-show="table.STATUS != 'in_use'" class="timer">--:--:--</p>
            </div>
        </div>
    </div>
    <the-spinner spinner-obj="spinner" namespace="tables"/>
</div>
<my-spinner/>
