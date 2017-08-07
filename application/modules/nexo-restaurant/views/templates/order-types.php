<div class="container-fluid">
    <div class="row">
        <div class="col-md-6" ng-repeat="type in types" style="padding-top:15px">
            <div class="order-type {{ type.active == true ? 'selected' : '' }}" ng-click="selectType( type )">
                <img ng-src="<?php echo module_url( 'nexo-restaurant' ) . '/img/';?>{{ type.namespace + '.png' }}" alt="{{ type.text }}">
                <p><strong>{{ type.text }}</strong></p>
            </div>
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
    width: 60%;
    display: inline-block;
}
.selected {
    background: #f7f7f7;
    border: solid 1px #47b8fb;
}
</style>