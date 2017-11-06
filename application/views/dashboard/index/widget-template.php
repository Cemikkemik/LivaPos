<div ng-repeat="widget in widget_data">
    <div class="box widget-body" ng-if="widget.wrapper">
        <div class="box-header with-border widget-handler">
            {{ widget.title }}
        </div>
        <div class="box-body" ng-bind-html="widget.template"></div>
    </div>
    <div ng-if="! widget.wrapper" class="widget-body">
        <div class="widget-handler" ng-bind-html="widget.template"></div>
    </div>
</div>