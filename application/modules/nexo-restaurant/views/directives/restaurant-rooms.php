<script>
     tendooApp.directive( 'restaurantRooms', function(){
        return {
            templateUrl        :  '<?php echo site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'templates', 'table-selection' ] );?>',
            restrict            :   'E'
        }
    });
</script>