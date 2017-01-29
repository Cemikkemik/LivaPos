define( [],
function(){
    var config  =   function( $routeProvider, $locationProvider ) {
        $routeProvider.when( '/foo', {
            templateUrl     :   '<?php echo site_url([ 'dashboard', 'nexopos_advanced', 'foo' ] );?>'
        });

        $locationProvider.html5Mode( true );
    }

    config.$inject  =   [ '$routeProvider', '$locationProvider' ];

    return config;
})
