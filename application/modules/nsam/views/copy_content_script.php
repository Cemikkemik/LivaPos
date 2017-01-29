<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript">
"use strict";

$( document ).ready( function(){
    $( 'button[type="submit"]' ).bind( 'click', function(){
        var elements    =   $( 'select[multiple]' ).val();
        var store_id    =   $( '[name="store_id"]' ).val();

        if( _.keys( elements ).length == 0 || store_id == '' ) {
            NexoAPI.Notify().warning( '<?php echo _s( 'Warning', 'nsam' );?>','<?php echo _s( 'You must select a store and/or select a content to copy.', 'nsam' );?>' );
            return false;
        }

        NexoAPI.Bootbox().confirm( '<?php echo _s( 'Copying content from other store, will erase all content from this store (to avoid conflict). Would you confirm your action ?', 'nsam' );?>', function( action ){
            if( action ) {
                $.ajax({
                    url     :   '<?php echo site_url( array( 'rest', 'nsam', 'copy' ) );?>?store_id=<?php echo get_store_id() == null ? 0 : get_store_id();?>',
                    data    :   {
                        store_id    :   store_id,
                        elements    :   elements
                    },
                    method  :   'POST',
                    success :   function(){
                        NexoAPI.Notify().success( '<?php echo _s( 'Success', 'nsam' );?>', '<?php echo _s( 'The selected item has been copied.', 'nsam' );?>' );
                    },
                    error   :   function(){
                        NexoAPI.Notify().warning( '<?php echo _s( 'An error occured', 'nsam' );?>', '<?php echo _s( 'An error occured.', 'nsam' );?>' );
                    }
                });
            }
        });
    });
});
</script>
