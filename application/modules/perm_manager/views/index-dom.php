<div class="row" ng-controller="permManagerController">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body no-padding table-responsive">
                <table class="table">
                    <thead>
                         <tr class="header-details">
                              <th>
                                   <div class="input-group input-group-sm">
                                        <input class="form-control" ng-model="search" placeholder="<?php echo __( 'Search Permission', 'perm_manager' );?>">
                                        <span class="input-group-btn">
                                             <button ng-click="savePermissions()" type="button" class="btn btn-default"><?php echo __( 'Save Permissions', 'nexo' );?></button>
                                        </span>
                                   </div>
                              </th>
                              <th ng-repeat="group in groups">
                                   <label ng-click="checkStatus( group.name, permissions_bulk[ group.name ] )">
                                        <input ng-model="permissions_bulk[ group.name ]" type="checkbox"> 
                                        {{ group.definition }}
                                   </label>
                              </th>
                         </tr>
                    </thead>
                    <tbody>
                         <tr ng-repeat="permission in permissions | filter:search">
                              <td>{{ permission.definition }}</td>
                              <td ng-repeat="group in groups" class="details">
                                   <label>
                                        <input value="{{ permission.name }}" ng-model="permissions_data[ group.name ][ permission.name ]" type="checkbox">
                                        <span style="color:#a7a7a7">{{ group.definition }}</span>
                                   </label>
                              </td>
                         </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>
.details span {
     display:none;
}
.details:hover span {
     display:inline-block;
}
</style>