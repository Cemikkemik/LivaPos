var bower_url       =   "<?php echo js_url() . '../bower_components/';?>";
require.config({
    paths: {
        angular         :   bower_url + "/angular/angular.min",
        ngRoute         :   bower_url + "angular-route/angular-route.min",
        ngResource     :   bower_url + "angular-resource/angular-resource.min"
    },
    shim: {
        angular: {
            exports: 'angular'
        },
        ngRoute         :   {
            exports     :   'ngRoute',
            deps        :   [ 'angular' ]
        },
        ngResource     :   {
            exports     :   'ngResource',
            deps        :   [ 'angular' ]
        }
    }
});

require([
    'angular',
    'ngRoute',
    'ngResource',
    'home.module',
],
  function() {
      angular.bootstrap( document, ['tendooApp']);
  }
);
