<script type="text/javascript">
    "use strict";
    tendooApp.directive( 'itemVariation', function(){
        return {
            template :  <?php echo json_encode( $this->load->module_view( 'nexo', 'items/parts',[], true ) );?>,
            controller  :   function( $scope, itemTypes, item, fields, providers, $rootScope ) {
                $scope.item         =   item;
                $scope.fields       =   fields;
                $scope.providers    =   providers;
                $scope.$broadcast   =   $rootScope.$broadcast;
                $scope.itemTypes    =   itemTypes;
            }
        }
    });
</script>
