<script>
     tendooApp.filter( 'table_status', function(){
        return function( filter ) {
            var tableStatus     =   <?php echo json_encode( $this->config->item( 'nexo-restaurant-table-status' ) );?>;
            return tableStatus[ filter ];
        }
    });
</script>