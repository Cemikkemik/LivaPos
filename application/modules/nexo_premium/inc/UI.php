<?php
! defined('APPPATH') ? die() : null;

/**
 * Nexo Premium UI
 *
 * @author Blair Jersyer
 * @version 1.0
**/

class Nexo_Premium_UI extends CI_Model
{
    public function Checkout_Script()
    {
        ?>
<script type="text/javascript">

	"use strict";

	NexoAPI.events.addFilter( 'nexo_customer_dom_popup', function( value ) {
		var dom	=	$.parseHTML( value );

		$( dom ).find( '.form-group' ).last().after(
			'<input type="checkbox" placeholder="<?php echo addslashes(__('Name', 'nexo_premium'));
        ?>" name="subcribe_to_mailchimp">'
		);

		return $( dom ).html();
	})
</script>
        <?php

    }
}
