<script>
tendooApp.controller( 'dashboardIndexController', 
    [ '$scope', '$queue', '$http', '$interpolate', '$compile',
    function( $scope, $queue, $http, $interpolate, $compile ) {
    $scope.widgets          =   [];

    // Widget column 0
    $scope.widgets[0]       =   [<?php echo json_encode( ( array ) get_option(
        $this->events->apply_filters( 'column_0_widgets', 'column_0_widgets' )
    ) );?>];

    // widget column 1
    $scope.widgets[1]       =   [<?php echo json_encode( ( array ) get_option(
        $this->events->apply_filters( 'column_1_widgets', 'column_1_widgets' )
    ) );?>];

    // widget column 2
    $scope.widgets[2]       =   [<?php echo json_encode( ( array ) get_option(
        $this->events->apply_filters( 'column_2_widgets', 'column_2_widgets' )
    ) );?>];

    $scope.requests         =   [];

    $scope.debug            =   1

    $scope.runRequest       =   ( requests, runSoFar = 0 ) => {
        runSoFar++
        $http.get( '/foo/bar' ).then( ( result ) => {
            if( runSoFar < requests.length ) {
                $scope.runRequest( requests, runSoFar );
            }
        }, ( error ) => {
            if( runSoFar < requests.length ) {
                $scope.runRequest( requests, runSoFar );
            }
        });

        $scope.debug++;
    }

    $scope.runRequest( $scope.widgets[0] );

    $scope.sortableOptions      =   {
        connectWith     :   '.widgets-container',
        placeholder     :   'widget-shadow col-md-12',
        // items           :   '.widget-item',
        handle          :   '> .widget-body .widget-handler',
        start           :   function( e ) {
            $( '.widget-shadow' ).append( '<div class="widget-holder" style="margin-bottom:20px"></div>' );
            var height     =   $( e.toElement ).closest( '.box' ).height();
            $( '.widget-holder' ).css({ height });
        },
        end             :   function(){
            console.log( 'Should Update Widgets' );
        }
    }
}]);
</script>