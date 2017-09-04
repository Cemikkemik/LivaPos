<div novalidate>
    <form name="form" novalidate ng-submit="submitForm()">
        <div class="form-group">
            <div class="input-group input-group-lg name-input-group">
                <input required type="text" name="name" ng-model="model[ 'name' ]" class="form-control"placeholder="<?php echo __( 'Customer Name', 'nexo' );?>">
                <span class="input-group-btn customer-save-btn">
                    <button type="submit" class="btn btn-default"><?php echo __( 'Enregistrer', 'nexo' );?></button>
                </span>
            </div>
            <p class="help-block" ng-show="( form.name.$error.required && form.name.$touched ) || ( form.name.$error.required && form.$submitted )">
                <?php echo __( 'Vous devez fournir un nom au client.', 'nexo' );?>
            </p>
        </div>
        <br>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li ng-click="enableTab( tab )" ng-repeat="tab in tabs" ng-class="{ active : tab.active }"><a href="javascript:void(0)">{{ tab.name }}</a></li>
            </ul>
            <div class="tab-content">
                <div ng-repeat="tab in tabs" ng-class="{ active : tab.active }" class="tab-pane">
                    <customers-form tab="tab"></customers-form>
                </div>
            </div>
        </div>
    </form>
</div>