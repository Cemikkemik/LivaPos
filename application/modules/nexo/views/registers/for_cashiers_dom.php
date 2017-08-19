<div class="row registers_list">
    
</div>
<script type="text/javascript">
"use strict";

var NexoRegisters	=	new function(){
	/**
	 * Init
	**/
	
	this.init		=	function(){
		this.loadRegisters();
	};
	
	/**
	 * Bind Action
	**/
	
	this.bindAction	=	function(){
		$( '.open_register' ).bind( 'click', function(){
			var $this	=	$( this );
			$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'register_status' ) );?>/' + $( this ).data( 'item-id' ), {
				success		:	function( data ){
					// Somebody is logged in
					if( data[0].STATUS == 'opened' ) {
						if( data[0].USED_BY != '<?php echo User::id();?>' ) {
							// Display confirm box to logout current user and login
							bootbox.alert( '<?php echo _s( 'Impossible d\'accéder à une caisse en cours d\'utilisation. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );
						} else {
							bootbox.alert( '<?php echo _s( 'Vous allez être redirigé vers la caisse...', 'nexo' );?>' );	
							// Document Location
						}
					} else if( data[0].STATUS == 'locked' ) {
						bootbox.alert( '<?php echo _s( 'Impossible d\'accéder à une caisse verrouillée. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );

					} else if( data[0].STATUS == 'closed' ) {
						var dom		=	'<h3 class="modal-title"><?php echo _s( 'Ouverture de la caisse', 'nexo' );?></h3><hr style="margin:10px 0px;">';
			
							dom		+=	'<p><?php echo tendoo_info( sprintf( _s( '%s, vous vous préparez à ouvrir une caisse. Veuillez spécifier le montant initiale de la caisse', 'nexo' ), User::pseudo() ) );?></p>' + 
										'<div class="input-group">' +
											'<span class="input-group-addon" id="basic-addon1"><?php echo _s( 'Solde d\'ouverture de la caisse', 'nexo' );?></span>' +
											'<input type="text" class="form-control open_balance" placeholder="<?php echo _s( 'Montant', 'nexo' );?>" aria-describedby="basic-addon1">' +
										'</div>';
						
						bootbox.confirm( dom, function( action ) {
							if( action ) {
								$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'open_register' ) );?>/' + $this.data( 'item-id' ), {
									dataType	:	'json',
									type		:	'POST',
									data		:	_.object( [ 'date', 'balance', 'used_by' ], [ '<?php echo date_now();?>', $( '.open_balance' ).val(), '<?php echo User::id();?>' ]),
									success: function( data ){
										bootbox.alert( '<?php echo _s( 'La caisse a été ouverte. Veuillez patientez...', 'nexo' );?>' );
										document.location	=	'<?php echo site_url( array( 'dashboard', 'nexo', 'registers', '__use' ) );?>/' + $this.data( 'item-id');
									}
								});
							}
						});
						
						// Set custom width
						$( '.modal-title' ).closest( '.modal-dialog' ).css({
							'width'		:	'60%'
						})
					}
					
				},
				dataType	:	"json",
				error		:	function(){
					bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant l\'ouverture de la caisse.', 'nexo' );?>' );
				}
			})
			
			return false;
		});
				
		$( '.close_register' ).bind( 'click', function(){
			var $this	=	$( this );
			$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'register_status' ) );?>/' + $( this ).data( 'item-id' ), {
				success		:	function( data ){
					// Somebody is logged in
					if( data[0].STATUS == 'opened' ) {
						
						if( data[0].USED_BY != '<?php echo User::id();?>'  ) {
							bootbox.alert( '<?php echo _s( 'Vous ne pouvez pas fermer cette caisse. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );
							return;
						}
						
						var dom		=	'<h3 class="modal-title"><?php echo _s( 'Fermeture de la caisse', 'nexo' );?></h3><hr style="margin:10px 0px;">';
			
							dom		+=	'<p><?php echo tendoo_info( sprintf( _s( '%s, vous vous préparez à fermer une caisse. Veuillez spécifier le montant finale de la caisse', 'nexo' ), User::pseudo() ) );?></p>' + 
										'<div class="input-group">' +
											'<span class="input-group-addon" id="basic-addon1"><?php echo _s( 'Solde de fermeture de la caisse', 'nexo' );?></span>' +
											'<input type="text" class="form-control open_balance" placeholder="<?php echo _s( 'Montant', 'nexo' );?>" aria-describedby="basic-addon1">' +
										'</div>';
						
						bootbox.confirm( dom, function( action ) {
							if( action == true ) {
								$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'close_register' ) );?>/' + $this.data( 'item-id' ), {
									dataType	:	'json',
									type		:	'POST',
									data		:	_.object( [ 'date', 'balance', 'used_by' ], [ '<?php echo date_now();?>', $( '.open_balance' ).val(), '<?php echo User::id();?>' ]),
									success: function( data ){
										bootbox.alert( '<?php echo _s( 'La caisse a été fermée. Veuillez patientez...', 'nexo' );?>' );
										document.location	=	'<?php echo current_url();?>';
									}
								});
							}
						});
						
						// Set custom width
						$( '.modal-title' ).closest( '.modal-dialog' ).css({
							'width'		:	'60%'		
						})
						
					} else if( data[0].STATUS == 'locked' ) {
						
						bootbox.alert( '<?php echo _s( 'Impossible de fermer une caisse verrouillée. Si le problème persiste, contactez l\'administrateur.', 'nexo' );?>' );

					} else if( data[0].STATUS == 'closed' ) {
						
						bootbox.alert( '<?php echo _s( 'Cette caisse est déjà fermée.', 'nexo' );?>' );
						
					}
					
				},
				dataType	:	"json",
				error		:	function(){
					bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant l\'ouverture de la caisse.', 'nexo' );?>' );
				}
			})
			
			return false;
		});
	}
	
	/**
	 * Load Rgisters
	**/
	
	this.loadRegisters	=	function(){
		$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'registers' ) );?>', {
			success		:	function( data ){
				if( ! _.isEmpty( data ) ) {					
					_.each( data, function( value, key ){
						
						if( value.STATUS 	==	'opened' ) {
							var OpenRegisters	=	'';	
							var CloseRegisters	=	'<a href="#" class="btn btn-warning close_register" role="button" data-item-id="' + value.ID + '"><i class="fa fa-lock"></i> <?php echo _s( 'Fermer', 'nexo' );?></a>';
							var EnterRegisters	=	'<a href="<?php echo site_url( array( 'dashboard', 'nexo', 'registers', '__use' ) );?>/' + value.ID + '" class="btn btn-info" role="button" data-item-id="' + value.ID + '"><i class="fa fa-sign-in"></i> <?php echo _s( 'Utiliser', 'nexo' );?></a>';
							
						} else if( value.STATUS == 'closed' ) {
							var OpenRegisters	=	'<a href="#" class="btn btn-success open_register" role="button" data-item-id="' + value.ID + '"><i class="fa fa-unlock"></i> <?php echo _s( 'Ouvrir', 'nexo' );?></a>';
							var CloseRegisters	=	'';
							var EnterRegisters	=	'';
						} else if( value.STATUS == 'locked' ){
							var OpenRegisters	=	'<a href="#" class="btn btn-success disabled"><i class="fa fa-remove"></i> <?php echo _s( 'Caisse Fermée', 'nexo' );?></a>';
							var CloseRegisters	=	'';
							var EnterRegisters	=	'';
						}
						
						var dom		=	
						'<div class="col-sm-5 col-md-3">' +
							'<div class="thumbnail">' +	
								// '<img alt="100%x200" data-src="" data-holder-rendered="true" style="height: 150px; width: 100%; display: block;">' +
								'<div class="caption">' +
									'<h3 style="margin:0">' + value.NAME + '</h3>' +
									'<p>' + value.DESCRIPTION + '</p>' +
									'<div class="btn-group btn-group-justified" role="group" aria-label="...">' +
										OpenRegisters +
										CloseRegisters +
										EnterRegisters +
									'</div>' +
								'</div>' +
							'</div>' +
						'</div>';
						
						$( '.registers_list' ).append( dom );	
					});
					
					NexoRegisters.bindAction();
				}
			},
			error		:	function(){
				bootbox.alert( '<?php echo _s( 'Une ereur s\'est produite durant le chargement des caisses', 'nexo' );?>' );
			}
		});
	}
	
};
$( document ).ready(function(e) {
    NexoRegisters.init();
});
</script>
