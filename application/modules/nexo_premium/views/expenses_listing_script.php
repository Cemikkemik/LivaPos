<?php
    global $Options;
    $this->config->load( 'rest' );
?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/filters/money-format.php' );?>
<script type="text/javascript">
tendooApp.controller( 'expensesListing', [ '$scope', '$http', function( $scope, $http ) {

    /**
    *
    * Get Expenses Listing
    *
    * @return void
    */

    $scope.getExpenses     =   function(){
        $scope.total        =   0;
        if( ! angular.isUndefined( $scope.startDate ) && ! angular.isUndefined( $scope.endDate ) ) {
            $http.post( '<?php echo site_url( array( 'rest', 'nexo', 'expenses_from_timeinterval' ) );?>' + '?<?php echo store_get_param( null );?>', {
                'start_date'    : $scope.startDate,
                'end_date'    : $scope.endDate
            },{
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then(function( returned ){
                _.each( returned.data, function( entry ) {
                    entry.DATE_CREATION     =   moment( entry.DATE_CREATION ).format( '<?php echo store_option( 'nexo_js_datetime_format', 'llll' );?>' )
                });
                
                $scope.expenses        =   returned.data;
            }, function(){
                $scope.expenses        =   [];
            });
        } else {
            NexoAPI.Notify().warning( '<?php echo __( 'Attention', 'nexo_premium' )?>', '<?php echo __( 'Vous devez définir un intervalle de temps précis pour avoir des résultats', 'nexo_premium' );?>');
        }
    }

    /**
     * When Scope Expenses change
    **/

    $scope.$watch( 'expenses', function(){
        $scope.total            =   0;
        _.each( $scope.expenses, function( value, key ) {
            $scope.total += parseFloat( value.MONTANT );
        })
    })
}]);
</script>
<script type="text/javascript">
$('.start_date').datepicker({
    format: 'mm/dd/yyyy'
});
$('.end_date').datepicker({
    format: 'mm/dd/yyyy'
});
</script>
