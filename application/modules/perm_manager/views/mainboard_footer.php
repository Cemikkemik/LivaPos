<script type="text/javascript">

    <?php
        global $Options;
        $this->load->config( 'rest' );
    ?>
    
    tendooApp.factory( 'permissionsResource', function( $resource ) {
        return $resource(
            '<?php echo site_url( [ 'rest', 'perm_manager', 'permissions/id']); ?>',
            {
                id              :   '@_id'
            },{
                get : {
                    method : 'GET',
                    headers			:	{
                        '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                    }
                },
                save : {
                    method : 'POST',
                    headers : {
                        '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                    }
                },
                delete : {
                    method : 'DELETE',
                    headers : {
                        '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                    }
                }
            }
        );
    });

    tendooApp.factory( 'sharedAlert', [ 'SweetAlert', function( SweetAlert ){
        return new function(){

            /**
             *  Alert Text
             *  @param string message
             *  @return void
            **/

            this.alert      =   function( message ) {
                return SweetAlert.swal( message );
            }

            /**
             *  Confirmation
             *  @param string message
             *  @param function callback
             *  @return void
            **/

            this.confirm    =   function( message, callback ) {
                return SweetAlert.swal({
                    title                : "<?php echo __( 'Confirm your action', 'perm_manager' );?>",
                    text                 : message,
                    type                 : "warning",
                    showCancelButton     : true,
                    confirmButtonColor   : "#DD6B55",
                    confirmButtonText    : "Oui",
                    closeOnConfirm       : typeof callback == 'function'
                }, function( isConfirm ) {
                    callback( isConfirm );
                });
            }

            /**
             *  Alert Warning
             *  @param string message
             *  @return void
            **/

            this.warning            =   function( message ) {
                return SweetAlert.swal({
                    title                : "<?php echo __( 'Warning', 'perm_manager' );?>",
                    text                 : message,
                    type                 : "warning",
                    showCancelButton     : false,
                    confirmButtonColor   : "#DD6B55",
                    confirmButtonText    : "Ok",
                    closeOnConfirm       : true
                });
            }
        }
    }]);    

    tendooApp.controller( 'permManagerController', [ 
        '$scope', 
        '$http', 
        '$element', 
        'sharedAlert', 
        'permissionsResource',
        function ( 
            $scope, 
            $http, 
            $element, 
            sharedAlert,
            permissionsResource
        ){
        
        $scope.roles = {};
        $scope.permissions = {};
        $scope.add = {};
        
        /**
         * Load Data
         **/

         $scope.loadData = function(){
             $http.get( '<?php echo site_url( [ 'dashboard', 'perm_manager', 'get' ] );?>' ).then( function( returned ){
                $scope.roles = returned.data.roles;
                $scope.permissions = returned.data.permissions;
                if( angular.isUndefined( $scope.selectedUser ) ) {
                    $scope.selectedUser = $scope.roles[0].name;
                    $scope.selectedRole = $scope.roles[0];
                }                
             });
         }

         /**
          *  change Selected Role
          *  @param
          *  @return
         **/

         $scope.changeSelectedRole =  function(){
             _.each( $scope.roles, function( role ){
                 if( role.name == $scope.selectedUser ){
                     $scope.selectedRole = role;
                 }
             });
         }

         /**
          * Delete selected element 
          **/

         $scope.bulkDelete = function (){

             var bulkDel = [];
             
             _.each( $scope.roles, function( role ){
                _.each( role.permissions, function ( permission ){
                    if( permission.checked == true){
                       bulkDel.push( [permission.perm_name, role.name] );
                    }
                });
             });

             if ( bulkDel.length == 0){
                return sharedAlert.warning( '<?php echo _s( 'Select an item', 'perm_manager' );?>' );   
             } else {
                sharedAlert.confirm( '<?php echo _s( 'Would you like to delete all these elements?', 'perm_manager' );?>', function( action ) {
                    if( action ) {
                        permissionsResource.delete( {'entries[]' : bulkDel}, function( data ) {
                            $scope.loadData();
                            sharedAlert.alert( '<?php echo _s( 'All permissions has been deleted !', 'perm_manager' );?>' );
                        },function(){
                            sharedAlert.warning( '<?php echo _s(
                                'An error occured.',
                                'perm_manager'
                            );?>' );
                        });
                    }
                });
             }
         }

         /**
          * Delete one element 
          **/

         $scope.delete = function ( permission, group_id ){
             sharedAlert.confirm( '<?php echo _s( 'Would you like to delete this ?', 'perm_manager' );?>', function( action ) {
                if( action ) {
                    permissionsResource.delete({ permission, group_id }, function( data ) {
                        if( returned.data.status === 'success' ) {
                            sharedAlert.warning( '<?php echo _s( 'The permission has been delete.', 'perm_manager' );?>' );
                        }
                        $scope.loadData();
                    },function(){
                        sharedAlert.warning( '<?php echo _s(
                            'An error occured',
                            'perm_manager'
                        );?>' );
                    });
                }
            });
         }

         /**
          * Add a permission to a role
         **/

        $scope.addPermission = function (){
            permissionsResource.save( 
                $scope.add, 
                function(){
                    sharedAlert.warning( '<?php echo _s( 'The modification has been made', 'perm_manager' );?>' );
                    $scope.add[ 'permission' ]  =   '';
                    $scope.add[ 'group' ]       =   '';
                    $scope.loadData();
                }, 
                function( returned ){
                    if( returned.data.status === 'alreadyExists' ) {
                        sharedAlert.warning( '<?php echo _s( 'This role already has this permission !', 'perm_manager' );?>' );
                    }

                    if( returned.data.status === 'forbidden' || returned.status == 500 ) {
                        sharedAlert.warning( '<?php echo _s( 'An error occured during the operation.', 'perm_manager' );?>' );
                    }
                }
            )
        }
         $scope.loadData();
    }]);  

</script>