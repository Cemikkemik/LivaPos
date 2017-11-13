<script>
     tendooApp.filter( 'table_status', function(){
        return function( filter ) {
            var tableStatus     =   <?php echo json_encode( $this->config->item( 'gastro-table-status' ) );?>;
            return tableStatus[ filter ];
        }
    });
</script>