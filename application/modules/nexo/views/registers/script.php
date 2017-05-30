<script type="text/javascript">
    "use strict";
    $( document ).ready(function(e) {
        $( '.close_register' ).bind( 'click', function(){
                var $this	=	$( this );
                $.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'register_status' ) );?>/' + $( this ).data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
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
                                    $.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'close_register' ) );?>/' + $this.data( 'item-id' ) + '?<?php echo store_get_param( null );?>', {
                                        dataType	:	'json',
                                        type		:	'POST',
                                        data		:	_.object( [ 'date', 'balance', 'used_by' ], [ '<?php echo date_now();?>', $( '.open_balance' ).val(), '<?php echo User::id();?>' ]),
                                        success: function( data ){
                                            bootbox.alert( '<?php echo _s( 'La caisse a été fermée. Veuillez patientez...', 'nexo' );?>' );
                                            <?php if( User::in_group( 'shop_cashier' ) ):?>
                                                document.location	=	'<?php echo site_url( array( 'dashboard', store_slug(), 'nexo', 'registers', 'for_cashiers?notice=register_has_been_closed' ) );?>';
                                            <?php else:?>
                                                document.location	=	'<?php echo site_url( array( 'dashboard', store_slug(), 'nexo', 'registers?notice=register_has_been_closed' ) );?>';
                                            <?php endif;?>
                                        }
                                    });
                                }
                            });

                            // Set custom width
                            $( '.modal-title' ).closest( '.modal-dialog' ).css({
                                'width'		:	'80%'
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
    });
</script>