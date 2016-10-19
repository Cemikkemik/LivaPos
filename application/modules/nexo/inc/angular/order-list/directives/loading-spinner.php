<script>
tendooApp.directive( 'mySpinner', function(){
	return {
		template :	'<div ng-show="showSpinner" class="nexo-overlay" style="width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9); z-index: 5000; position: absolute; top: 0px; left: 0px;"><i class="fa fa-refresh fa-spin nexo-refresh-icon" style="color: rgb(0, 0, 0); font-size: 50px; position: absolute; top: 50%; left: 50%; margin-top: -25px; margin-left: -25px; width: 44px; height: 50px;"></i></div>'
	}
} );
</script>
<script>
tendooApp.directive( 'grandSpinner', function(){
	return {
		template :	'<div ng-show="showGrandSpinner" class="nexo-overlay" style="width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9); z-index: 5000; position: absolute; top: 0px; left: 0px;"><i class="fa fa-refresh fa-spin nexo-refresh-icon" style="color: rgb(0, 0, 0); font-size: 50px; position: absolute; top: 50%; left: 50%; margin-top: -25px; margin-left: -25px; width: 44px; height: 50px;"></i></div>'
	}
} );
</script>
<script>
tendooApp.directive( 'theSpinner', function(){
	return {
		link	:	function( scope, element, attrs ) {
			scope.namespace	=	attrs.namespace;
		},
		scope	:	{
			spinnerObj	:	'='
		},
		restrict	:	'E',
		template :	'<div ng-show="spinnerObj[ namespace ]" class="nexo-overlay" style="width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9); z-index: 5000; position: absolute; top: 0px; left: 0px;"><i class="fa fa-refresh fa-spin nexo-refresh-icon" style="color: rgb(0, 0, 0); font-size: 50px; position: absolute; top: 50%; left: 50%; margin-top: -25px; margin-left: -25px; width: 44px; height: 50px;"></i></div>'
	}
} );
</script>
<!-- -->