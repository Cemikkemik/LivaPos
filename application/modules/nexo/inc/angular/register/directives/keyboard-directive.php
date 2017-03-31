<?php
global $Options;
 ?>
<script>
tendooApp.directive( 'keyboard', function(){

	HTML.body.add( 'angular-cache' );



	HTML.query( 'angular-cache' )
	.add( 'div.row.keyboard-separator-wrapper>div.col-md-9' )
	.add( 'div.keyboard-wrapper.row' )
	.each( 'style', 'padding:15px 0;' );

	for( var i = 7; i <= 9; i++ ) {
		HTML.query( '.keyboard-wrapper' )
		.add( 'div.col-lg-4.col-sm-4.col-xs-4>button.btn.btn-default.btn-block.input-' + i )
		.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
		.each( 'ng-click', 'keyinput( ' + i + ', inputName )' )
		.textContent	=	i;
	}



	for( var i = 4; i <= 6; i++ ) {
		HTML.query( '.keyboard-wrapper' )
		.add( 'div.col-lg-4.col-sm-4.col-xs-4>button.btn.btn-default.btn-block.input-' + i )
		.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
		.each( 'ng-click', 'keyinput( ' + i + ', inputName )' )
		.textContent	=	i;
	}

	for( var i = 1; i <= 3; i++ ) {
		HTML.query( '.keyboard-wrapper' )
		.add( 'div.col-lg-4.col-sm-4.col-xs-4>button.btn.btn-default.btn-block.input-' + i )
		.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
		.each( 'ng-click', 'keyinput( ' + i + ', inputName )' )
		.textContent	=	i;
	}

    HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-4.col-sm-4.col-xs-4.clear-long>button.btn.btn-default.btn-block.input-clear' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( "clear", inputName )' )
	.textContent	=	'C';

    HTML.query( '.keyboard-wrapper .clear-long' )
    .each( 'ng-show', 'hideButton[ "dot" ]' );

	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-2.col-sm-4.col-xs-4.clear-small>button.btn.btn-default.btn-block.input-clear' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( "clear", inputName )' )
    .each( 'ng-hide', 'hideButton[ "dot" ]' )
	.textContent	=	'C';

    HTML.query( '.keyboard-wrapper .clear-small' )
    .each( 'ng-hide', 'hideButton[ "dot" ]' );

	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-2.col-sm-4.col-xs-4.dot-button>button.btn.btn-default.btn-block.input-dot' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( ".", inputName )' )
	.textContent	=	'.';

    HTML.query( '.keyboard-wrapper .dot-button' )
    .each( 'ng-hide', 'hideButton[ "dot" ]' )
    .each( 'ng-hide', 'hideButton[ "dot" ]' );

	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-4.col-sm-4.col-xs-4>button.btn.btn-default.btn-block.input-dot' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( 0, inputName )' )
	.textContent	=	'0';

	HTML.query( '.keyboard-wrapper' )
	.add( 'div.col-lg-4.col-sm-12.col-xs-12>button.btn.btn-default.btn-block.input-back' )
	.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
	.each( 'ng-click', 'keyinput( "back", inputName )' )
	.textContent	=	'←';

	HTML.query( '.keyboard-separator-wrapper' )
	.add( 'div.col-md-3.right-side-keyboard>div.row' )
	.each( 'style', 'padding:15px 0;' )
    .each( 'ng-hide', 'hideSideKeys' )

	<?php
	$keyShortcuts 		=	@$Options[ store_prefix() . 'keyshortcuts' ];
	$allKeys 			=	explode( '|', $keyShortcuts );
	if( $allKeys ) {
        if( ! empty( $allKeys[0] ) ) {
            foreach( $allKeys as $key ) {
    			?>
    			HTML.query( '.right-side-keyboard .row' )
    			.add( 'div.col-lg-12.col-sm-12.col-xs-12>button.btn.btn-info.btn-block.input-' + <?php echo $key;?> )
    			.each( 'style', 'margin-bottom:15px;line-height:30px;font-size:24px;font-weight:800' )
    			.each( 'ng-click', 'keyinput( ' + <?php echo  $key;?> + ', inputName, true )' )
    			.textContent	=	<?php echo  $key;?>;
    			<?php
    		}
        }
	}
	 ?>

	var payBoxHTML		=	angular.element( 'angular-cache' ).html();

	angular.element( 'angular-cache' ).remove();

	return {
		restrict	:	'E',
		scope		:	{
			keyinput	:	'=',
            hideSideKeys    :   '=',
            hideButton      :   '='
		},
		link		:	function( scope, element, attrs ) {
			scope.inputName					=	attrs.inputName
		},
		template 	:	payBoxHTML
	}
} );
</script>
