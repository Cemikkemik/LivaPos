<script>
tendooApp.directive( 'keyboard', function(){
	
	HTML.body.add( 'angular-cache' );
	
	HTML.query( 'angular-cache' )
	.add( 'div.keyboard-wrapper.row' )
	.each( 'style', 'padding:15px 0;' );
	
	for( var i = 7; i <= 9; i++ ) {
		HTML.query( '.keyboard-wrapper' )
		.add( 'div.col-lg-4>button.btn.btn-default.btn-block.input-' + i )
		.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
		.each( 'ng-click', 'keyinput( ' + i + ', inputName )' )
		.textContent	=	i;
	}
	
	for( var i = 4; i <= 6; i++ ) {
		HTML.query( '.keyboard-wrapper' )
		.add( 'div.col-lg-4>button.btn.btn-default.btn-block.input-' + i )
		.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
		.each( 'ng-click', 'keyinput( ' + i + ', inputName )' )
		.textContent	=	i;
	}
	
	for( var i = 1; i <= 3; i++ ) {
		HTML.query( '.keyboard-wrapper' )
		.add( 'div.col-lg-4>button.btn.btn-default.btn-block.input-' + i )
		.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
		.each( 'ng-click', 'keyinput( ' + i + ', inputName )' )
		.textContent	=	i;
	}
	
	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-2>button.btn.btn-default.btn-block.input-clear' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( "clear", inputName )' )
	.textContent	=	'C';
	
	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-2>button.btn.btn-default.btn-block.input-dot' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( ".", inputName )' )
	.textContent	=	'.';
	
	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-4>button.btn.btn-default.btn-block.input-dot' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( 0, inputName )' )
	.textContent	=	'0';
	
	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-4>button.btn.btn-default.btn-block.input-back' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( "back", inputName )' )
	.textContent	=	'←';
	
	
	
	var payBoxHTML		=	angular.element( 'angular-cache' ).html();
	
	angular.element( 'angular-cache' ).remove();	
	
	return {
		restrict	:	'E',
		scope		:	{
			keyinput	:	'='
		},
		link		:	function( scope, element, attrs ) {
			scope.inputName					=	attrs.inputName
		},
		template 	:	payBoxHTML
	}
} );
</script>