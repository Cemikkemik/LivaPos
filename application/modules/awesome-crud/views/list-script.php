<script type="text/javascript">
    tendooApp.controller( 'awesomeCrudController', [ '$scope', '$compile', '$filter', '$resource', '$http', 'AwesomeCrudFactory', function(
        $scope, $compile, $filter, $resource, $http, AwesomeCrudFactory
    ) {
        $scope.showActions      =   <?php echo $awesome_crud->get( 'showActions' ) ? 'true' : 'false';?>;
        $scope.showCheckboxes   =   <?php echo $awesome_crud->get( 'showCheckboxes' ) ? 'true' : 'false';?>;
        $scope.checkboxToggle   =   false;
        $scope.loadStatus       =   'finished';
        $scope.entries          =   [];
        $scope.entriesList      =   [];
        $scope.actions          =   [{
            name            :   '<?php echo __( 'Edit', 'awesome-crud' );?>',
            callback        :   function( entry ) {
                document.location = '<?php echo $awesome_crud->get( 'baseUrl' ) . '/' . $awesome_crud->get( 'editSlug' );?>/' + entry.<?php echo $awesome_crud->get( 'primaryKey' );?>; 
            }
        },{
            name            :   '<?php echo __( 'Delete', 'awesome-crud' );?>',
            callback        :   function( entry ) {
                swal({
                    title: "<?php echo _s( 'Please confirm your action', 'awesome-crud' );?>",
                    text: "<?php echo _s( 'Would you like to delete this entry ?', 'awesome-crud' );?>",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true
                },
                function(){
                    AwesomeCrudFactory.delete({ id : entry[ '<?php echo $awesome_crud->get( 'primaryKey' );?>' ]}, function(){
                        $scope.load();
                    });
                });
            }
        }]

        $scope.$watch( 'checkboxToggle', function(){
            _.each( $scope.entries, function( entry ) {
                $scope.entriesList[ entry.<?php echo $awesome_crud->get( 'primaryKey' );?> ]    =   $scope.checkboxToggle;
            });
        })

        $scope.load             =   function(){
            $scope.loadStatus       =   'loading';
            $scope.entries          =   [];
            $scope.entries          =   AwesomeCrudFactory.query(function(){
                $scope.loadStatus   =   'finished';
            });
        }

        // Init
        $scope.load();
    }])

    $(document).ready(function(){
        $('input').iCheck({
            checkboxClass: 'icheckbox_minimal',
            radioClass: 'iradio_minimal',
            increaseArea: '20%' // optional
        });

        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        })
    });
</script>