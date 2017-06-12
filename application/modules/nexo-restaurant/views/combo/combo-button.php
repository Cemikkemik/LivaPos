<div class="btn-group" role="group" ng-controller="comboCTRL">
    <button ng-if="isCombo && isEdit == false" type="button" class="btn btn-success btn-lg" ng-click="finish_combo()" style="margin-bottom:0px;"> 
        <i class="fa fa-plus"></i>
        <span class="hidden-xs"><?php _e('Finish Meal', 'nexo');?></span>
    </button>
    <button ng-if="isCombo && isEdit == true" type="button" class="btn btn-success btn-lg" ng-click="finish_combo()" style="margin-bottom:0px;"> 
        <i class="fa fa-plus"></i>
        <span class="hidden-xs"><?php _e('Edit Meal', 'nexo');?></span>
    </button>
    <button ng-if="! isCombo" type="button" class="btn btn-default btn-lg meal_button" ng-click="start_combo()" style="margin-bottom:0px;"> 
        <i class="fa fa-plus"></i>
        <span class="hidden-xs"><?php _e('Make Meal', 'nexo');?></span>
    </button>
</div>