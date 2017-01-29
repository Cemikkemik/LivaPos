<script type="text/javascript">
    "use strict";
    tendooApp.config(function($routeProvider) {
       $routeProvider
        .when("/create/:type", {
            templateUrl : function( urlattr ){
                return '<?php echo site_url( [ 'dashboard', 'nexo', 'produits', 'template' ] );?>/' + urlattr.type
            }
        })
        .otherwise({
            templateUrl : '<?php echo site_url( [ 'dashboard', 'nexo', 'produits', 'template', 'main' ] );?>'
        });
    });
</script>
