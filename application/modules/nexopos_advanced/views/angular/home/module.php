define([ 'config.routes', 'home.controller' ], function( routes, controller ){
    var app         =   angular.module( 'tendooApp', [] );
        // console.log( routes );
        // app.config( routes );
        app.controller( 'test', controller );
    // 'ngRoute', 'ngRessource'
})
