<?php $this->config->load( 'nexo' );?>
<script>
tendooApp.service( '__windowSplash', function(){
	/// Display Splash
		this.showSplash			=	function(){
			if( $( '.nexo-overlay' ).length == 0 ) {
				$( 'body' ).append( '<div class="nexo-overlay"></div>');
				$( '.nexo-overlay').css({
					'width' : '100%',
					'height' : '100%',
					'background': 'rgba(0, 0, 0, 0.5)',
					'z-index'	: 5000,
					'position' : 'absolute',
					'top'	:	0,
					'left' : 0,
					'display' : 'none'
				}).fadeIn( 500 );
	
				$( '.nexo-overlay' ).append( '<i class="fa fa-refresh fa-spin nexo-refresh-icon" style="color:#FFF;font-size:50px;"></i>');
	
				$( '.nexo-refresh-icon' ).css({
					'position' : 'absolute',
					'top'	:	'50%',
					'left' : '50%',
					'margin-top' : '-25px',
					'margin-left' : '-25px',
					'width' : '44px',
					'height' : '50px'
				})			
			}
		}

		// Hide splash
		this.hideSplash			=	function(){
			$( '.nexo-overlay' ).fadeOut( 300, function(){
				$( this ).remove();
			} );
		}

		this.close				=	function(){
			$( '[data-bb-handler="cancel"]' ).trigger( 'click' );
		};
});
</script>