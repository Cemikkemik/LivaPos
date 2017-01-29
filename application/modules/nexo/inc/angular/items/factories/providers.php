<script type="text/javascript">
    "use strict";
    tendooApp.factory( 'providers', [ 'rawToOptions', function( rawToOptions ){
        var data    =   {
            raw     :   <?php echo json_encode( $providers );?>
        };

        data.options    =   rawToOptions( data.raw, 'ID', 'NOM' );

        return data;
    }]);
</script>
