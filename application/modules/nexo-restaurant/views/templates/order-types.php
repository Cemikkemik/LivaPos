<div class="container-fluid">
    <div class="row" style="padding-top:15px">
        <div class="col-md-3 col-xs-4" ng-repeat="type in types">
            <div class="order-type {{ type.active == true ? 'selected' : '' }}" ng-click="selectType( type )">
                <img  ng-src="<?php echo module_url( 'nexo-restaurant' ) . '/img/';?>{{ type.namespace + '.png' }}" alt="{{ type.text }}">
                <p style="margin-top:15px"><strong>{{ type.text }}</strong></p>
            </div>
        </div>
        <div class="col-md-12" ng-show="types.length == 0">
            <?php echo tendoo_info( __( 'There is not order type enabled. Please contact the manager.', 'nexo-restaurant' ) );?>
        </div>
    </div>
</div>
<style>
.order-type {
    width: 100%;
    height: 150px;
    border: solid 1px #EEE;
    margin: 0 0 15px 0;
    border-radius: 11px;
    text-align: center;
    padding: 15px 0;
}
.order-type:hover {
    box-shadow: inset 0px 0px 60px 0px #a4d8fd;
    cursor: pointer;
}
.order-type img {
    width: 50%;
    display: inline-block;
}
.selected {
    background: #f7f7f7;
    border: solid 1px #47b8fb;
}
</style>