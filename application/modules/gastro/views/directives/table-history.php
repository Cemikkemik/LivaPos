<script>
     tendooApp.directive( 'tableHistory', function(){
        return {
            templateUrl        :  '<?php echo site_url([ 'dashboard', store_slug(), 'gastro', 'templates', 'table_history' ] );?>',
            restrict            :   'E',
            controller          :   function( $scope ) {
                $scope.viewDate         =   '<?php echo date_now();?>';
                $scope.calendarView     =   'month';
                $scope.cellIsOpen       =   false;
            }
        }
    });
</script>