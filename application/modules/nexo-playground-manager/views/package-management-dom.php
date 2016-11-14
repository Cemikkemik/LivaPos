<?php
global $Options;
$this->load->config( 'rest' );
?>

<div ng-controller="nrPackage">
    <div class="row">
        <div class="col-lg-3" style="height:{{ contentHeight }}px;overflow-y:scroll;">
            <div class="input-group">
                <input type="text" ng-model="searchOrder" class="form-control" placeholder="<?php echo _s( 'Search order', 'nexo-playground-manager' );?>" aria-describedby="basic-addon1">
                <span class="input-group-btn">
                <button class="btn btn-default" ng-click="getOrders( 'search' )" type="button"><?php echo _s( 'Search', 'nexo-playground-manager' );?></button>
                <button class="btn btn-default" ng-click="reset()" type="button"><i class="fa fa-times"></i></button>
                </span> </div>
            <br />
            <div class="list-group" >
            	<a ng-repeat="order in orders" href="#" class="list-group-item" ng-click="editOrder( order.CODE )" ng-class="{ active : order.active }">{{ order.ID | fillZero: 4 }}</a> 
			</div>
        </div>
        <div class="col-lg-5"><h4 class="text-center"><?php _e( 'Package Details', 'nexo-playground-manager' );?></h4>
        	<div class="row">
            	<div class="col-lg-6">
                	<p><strong><?php echo __( 'Total Time:', 'nexo-playground-manager' );?></strong> {{ orders[ selectedOrderIndex ].overallTime | secondsToDateTime | date : 'HH:mm:ss' }}</p>
                    <p><strong><?php echo __( 'Bought Date:', 'nexo-playground-manager' );?></strong> {{ orders[ selectedOrderIndex ].DATE_CREATION }}</p>

                </div>
                <div class="col-lg-6">
                	<p><strong><?php echo __( 'Customer :', 'nexo-playground-manager' );?></strong> {{ orders[ selectedOrderIndex ].NOM }}</p>
                    <p><strong><?php echo __( 'Elapsed Time :', 'nexo-playground-manager' );?></strong> {{ orders[ selectedOrderIndex ].USED_SECONDS | secondsToDateTime | date : 'HH:mm:ss' }}</p>
                </div>
 	   			<div class="col-lg-12">
                	<h3 class="text-center">{{ orders[ selectedOrderIndex ].remainingTime | secondsToDateTime | date : 'HH:mm:ss' }}</h3>
                    <p class="text-center">
                        <small><?php echo __( 'Remaining Time', 'nexo-playground-manager' );?></small><br />
                    </p>
                </div>
                <div class="col-lg-12">
                	<button ng-disabled="buttonDisabled" class="btn btn-{{ buttonClass }} btn-block" ng-click="runTimer()">{{ buttonText }}</button>
                </div>


            </div>
        </div>
        <div class="col-lg-4" style="height:{{ contentHeight }}px;overflow-y:scroll;">
        	<h4 class="text-center"><?php _e( 'Package Time', 'nexo-playground-manager' );?></h4>
            <div class="list-group" >
            	<a ng-repeat="item in orderDetails" href="#" class="list-group-item">{{ item.DESIGN }} &mdash; {{ item.NP_TIME | secondsToDateTime | date : 'HH:mm:ss' }} ( {{ item.QUANTITE }} <?php echo __( 'Time(s)', 'nexo-playground-manager' );?> )</a>
			</div>
        	<the-spinner spinner-obj="spinner" namespace="order_details"></the-spinner>
        </div>

    </div>
</div>
