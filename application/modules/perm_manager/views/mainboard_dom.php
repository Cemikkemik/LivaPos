<div class="row" ng-controller="permManagerController">
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    <?php echo __( 'Permissions', 'perm_manager' );?>
                </div>
                <div class="box-tools">
                    <button class="btn btn-danger pull-right btn-sm" type="button" ng-click="bulkDelete()"> <?php echo __('Supprimer les rôles sélectionnés','perm_manager'); ?> </button>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"> <?php echo __( 'Utilisateur', 'perm_manager' );?> </span>
                        <select
                        class="form-control"
                        ng-model = "selectedUser"
                        ng-change = "changeSelectedRole()"
                        >
                            <option ng-repeat="role in roles" value="{{ role.name }}">{{ role.definition }}</option>
                        </select>
                    </div>
                </div>
                <div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th><?php echo __('Permission','perm_manager'); ?></th>
                                <th><?php echo __('Action','perm_manager'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="permission in selectedRole.permissions" >
                            
                                <td><input type="checkbox" ng-model ="permission.checked" ng-checked ="permission.checked" value="{{ permission.perm_name }}"></td>
                                <td>{{ permission.perm_desc }}</td>
                                <td><button class="btn btn-sm btn-default" type="button" ng-click="delete( permission.perm_name,  selectedRole.id )"> <?php echo __('Supprimer','perm_manager'); ?> </button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!--<uib-tabset active="tab_active">
                    <uib-tab index="role.name" ng-repeat="role in roles" heading="{{role.name}}">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><?php echo __('Permission','perm_manager'); ?></th>
                                    <th><?php echo __('Action','perm_manager'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="permission in role.permissions" >
                                    <td><input type="checkbox" ng-model ="permission.checked" ng-checked ="permission.checked" value="{{ permission.perm_name }}"></td>
                                    <td><h4>{{ permission.perm_desc }}</h4></td>
                                    <td><button class="btn" type="button" ng-click="delete(permission.perm_name,  role.name)"> <?php echo __('Delete','perm_manager'); ?> </button></td>
                                </tr>
                            </tbody>
                        </table>
                    </uib-tab>
                </uib-tabset>-->
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    <?php echo __( 'Ajouter une permission', 'perm_manager' );?>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"> <?php echo __( 'Rôle', 'perm_manager' );?> </span>
                        <select
                            class="form-control"
                            ng-model="add['group']"
                            >
                            <option ng-repeat="option in roles" value="{{ option.id }}">{{ option.definition }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"> <?php echo __( 'Permission', 'perm_manager' );?> </span>
                        <select
                        class="form-control"
                        ng-model="add['permission']"
                        >
                            <option ng-repeat="option in permissions" value="{{ option.id }}">{{ option.definition }}</option>
                        </select>
                    </div>
                </div>          

            </div>
            <div class="box-footer">
                <button class="btn btn-info" type="button" ng-click="addPermission()"><?php echo __('Ajouter','perm_manager'); ?></button>
            </div>
        </div>
    </div>
</div>
