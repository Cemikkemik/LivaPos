dooo

<?php
global $PageNow, $Options;
if( $PageNow == 'nexo/registers/__use' ) :
?>
<script type="text/javascript">
    NexoAPI.events.addFilter( 'fetch_item', function( data ){
        var before  =   0;
        var after   =   0;
        <?php if( @$Options[ store_prefix() . 'delete_char_before' ] != null ){
            ?>
            before  =   <?php echo intval( ( int ) @$Options[ store_prefix() . 'delete_char_before' ] );?>;
            <?php
        } ?>
        <?php if( @$Options[ store_prefix() . 'delete_char_after' ] != null ){
            ?>
            after  =   <?php echo intval( ( int ) @$Options[ store_prefix() . 'delete_char_after' ] );?>;
            <?php
        } ?>

        // Retrait après
        data[0]     =   data[0].substr( 0, data[0].length - after );
        // Retrait Avant
        data[0]     =   data[0].substr( before, data[0].length );
        return data;
    });
</script>
<?php
endif;
