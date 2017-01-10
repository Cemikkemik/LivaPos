<script type="text/javascript">
    tendooApp.controller( 'tendooSpinner', [ '$scope', '$http', function( $scope, $http ){

        var color   =   angular.element( '.main-header>.navbar>.sidebar-toggle' ).css( 'color' );
        angular.element( 'md-progress-circular' ).find( 'svg > path' ).css({
            stroke  :   color
        });

        $scope.$on( 'httpPreConfig', function( e ) {
            alert( 'ok' );
        });
    }]);
</script>
<script type="text/javascript">
    $( document ).ready( function(){
        $( document ).ajaxComplete(function() {
    	  $( '[ng-controller="tendooSpinner"]' ).hide();
    	});
    	$( document ).ajaxError(function() {
    	  $( '[ng-controller="tendooSpinner"]' ).hide();
    	});
    	$( document ).ajaxSend(function() {
    	  $( '[ng-controller="tendooSpinner"]' ).show();
    	});
    });
</script>
