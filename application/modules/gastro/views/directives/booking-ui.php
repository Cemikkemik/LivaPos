<script>
     tendooApp.directive( 'bookingUi', function(){
        return {
            templateUrl        :  '<?php echo site_url([ 'dashboard', store_slug(), 'gastro', 'templates', 'booking-ui' ] );?>',
            restrict            :   'E',
            controller          :   function( $scope ) {
                $scope.viewDate         =   '<?php echo date_now();?>';
                $scope.calendarView     =   'month';
                $scope.cellIsOpen       =   false;
            }
        }
    });
</script>