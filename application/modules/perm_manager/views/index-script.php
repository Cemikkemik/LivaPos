<?php global $Options;?>
<script>
tendooApp.controller( 'permManagerController', [ '$scope', '$http', function( $scope, $http ){
     $scope.permissions            =    <?php echo json_encode( $permissions );?>;
     $scope.groups                 =    <?php echo json_encode( $groups );?>;
     $scope.groups_permissions     =    <?php echo json_encode( $groups_permissions );?>;
     $scope.permissions_data       =    new Object;
     $scope.permissions_bulk       =    new Object;

     angular.forEach( $scope.groups, ( group ) => {
          angular.forEach( $scope.permissions, ( permission ) => {
               
               if( typeof $scope.permissions_data[ group.name ] == 'undefined' ) {
                    $scope.permissions_data[ group.name ]        =    new Object;
               }

               if( _.indexOf( $scope.groups_permissions[ group.name ], permission.name ) != -1 ) {
                    $scope.permissions_data[ group.name ][ permission.name ]    =   true; 
               } else {
                    $scope.permissions_data[ group.name ][ permission.name ]    =   false; 
               }
          });
     });

     $scope.checkStatus            =    function( column, checkboxStatus ) {
          // console.log( $scope.permissions_data[ column ], checkboxStatus )
          angular.forEach( $scope.permissions_data[ column ], function( perm, index ) {
               $scope.permissions_data[ column ][ index ]   =    checkboxStatus;
          });
     }

     $scope.savePermissions        =    function(){
          $http.post( '<?php echo api_url([ 'permissions', 'save' ]);?>', {
               'permissions'  :    $scope.permissions_data
          }, {
               headers	:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
               }
          }).then( ( result ) => {
               $.notify({
                   title    :   '<?php echo _s( 'Successful', 'perm_manager' );?>',
                   message  :   '<?php echo _s( 'The Permissions has been updated.', 'perm_manager' );?>'
               })
          })
     }
}])

// tendooApp.directive('input', function($timeout) {
//      return {
//           link: function(scope, element, attrs) {
//                return $timeout(function() {
//                     return $(element).iCheck({
//                          checkboxClass: 'icheckbox_square-blue',
//                          radioClass: 'iradio_square-blue',
//                          increaseArea: '20%' // optional
//                     });
//                });
//           }
//      };
// })
</script>