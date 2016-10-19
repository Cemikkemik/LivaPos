<script>
tendooApp.service( 'serviceKeyboardHandler', function(){
	this.tap		=	function( char, field ) {
		
		var	value	=	typeof angular.element( '.' + field ).val() == 'undefined' ? '' : angular.element( '.' + field ).val();
		
		if( char == 'clear' ) {
			angular.element( '.' + field ).val( '' );
		} else if( char == '.' ) {
			angular.element( '.' + field ).val( value + '.' );
		} else if( char == 'back' ) {
			angular.element( '.' + field ).val( value.substr( 0, value.length - 1 ) );
		} else if( typeof char == 'number' ) {
			angular.element( '.' + field ).val( value + '' + char );
		}		
	}
});
</script>