<div class="container-fluid" style="padding-top:15px">
    <div class="row">
        <div class="col-md-6" ng-repeat="order in orders track by $index">
            <ul class="media-list">
                <li ng-class="{ 'active-ready-order' : order.active }" class="media order-list" ng-click="selectOrder( order )" style="padding: 10px;">
                    <div class="media-left"> 
                        <a href="#"> 
                        <img alt="64x64" class="media-object" data-src="holder.js/64x64" src="data:image/png;base64,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" data-holder-rendered="true" style="width: 64px; height: 64px;"> </a> 
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">{{ order.TITLE }}</h4>
                        <span>
                            <strong><?php echo __( 'Items', 'gastro' );?></strong> : {{ order.items.length }}
                            | <strong><?php echo __( 'Placed', 'gastro' );?></strong> : {{ timeSpan( order ) }}
                            | <strong><?php echo __( 'Code', 'gastro' );?></strong> : {{ order.CODE }}
                        </span>
                    </div>
                </li>
                
            </ul>
        </div>
        <div class="col-md-12">
            <div ng-show="orders.length == 0">
                <?php echo tendoo_info( __( 'No order are ready right now', 'gastro' ) );?>
            </div>
        </div>
    </div>
</div>
<style>
.active-ready-order {
    background: #f4f7ff;
    box-shadow: 0px 0px 0px 4px #3c8dbc;
}
.order-list:hover {
    cursor: pointer;
    background:#f4f7ff;
}
</style>