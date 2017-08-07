HTML.query( '.group-content' )
.add( 'span.input-group-addon' )
.textContent	=	'<?php echo _s( 'Intitulé de la commande', 'nexo' );?>';

HTML.query( '.group-content' )
.add( 'input.form-control' )
.each( 'ng-model', 'orderName' )
.each( 'placeholder', '<?php echo _s( 'Nom de la commande', 'nexo' );?>' )
