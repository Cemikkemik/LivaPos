<?php
global $Options;
$this->load->config( 'rest' );
?>
<div ng-controller="expensesListing">
    <div class="row hidden-print">
        <div class="col-lg-5 col-md-5 col-sm-5">
            <div class="input-group">
                <span class="input-group-addon"><?php echo __( 'Date de départ', 'nexo_premium' );?></span>
                <input ng-model="startDate" type="text" class="form-control start_date" placeholder="">

            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-5">
            <div class="input-group">
                <span class="input-group-addon"><?php echo __( 'Date de fin', 'nexo_premium' );?></span>
                <input ng-model="endDate" type="text" class="form-control end_date" placeholder="">
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2">
            <div class="btn-group btn-group-md">
                <button type="button" name="name"class="btn btn-default" ng-click="getExpenses()">
                    <i class="fa fa-refresh"></i>
                    <?php echo __( 'Charger', 'nexo_premium' );?>
                </button>
                <button type="button"  print-item=".content-wrapper" name="name"class="btn btn-default" ng-click="printReport()">
                    <i class="fa fa-print"></i>
                    <?php echo __( 'Imprimer', 'nexo_premium' );?>
                </button>
            </div>
        </div>
    </div>
    <br>
    <script type="text/javascript">
    $('.start_date').datepicker({
        format: 'mm/dd/yyyy',
        startDate: '-3d'
    });
    $('.end_date').datepicker({
        format: 'mm/dd/yyyy',
        startDate: '-3d'
    });
    </script>
    <table class="table table-bordered table-striped box">
        <thead>
            <tr>
                <td width="100">
                    <?php echo __( 'Dates', 'nexo_premium' ); ?>
                </td>
                <td width="100">
                    <?php echo __( 'Références', 'nexo_premium' ); ?>
                </td>
                <td width="400">
                    <?php echo __( 'Désignation', 'nexo_premium' ); ?>
                </td>
                <td width="200">
                    <?php echo __( 'Montant', 'nexo_premium' ); ?>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="expense in expenses">
                <td>
                    {{ expense.DATE_CREATION }}
                </td>
                <td>
                    {{ expense.REF }}
                </td>
                <td>
                    {{ expense.INTITULE }}
                </td>
                <td  class="text-right">
                    {{ expense.MONTANT | moneyFormat }}
                </td>
            </tr>
            <tr ng-show="expenses.length == 0" class="hidden-print">
                <td colspan="4" class="text-center">
                    <?php echo __( 'Aucun résultat disponible. Veuillez choisir un intervalle de temps différent.', 'nexo_premium' ); ?>
                </td>
            </tr>
            <tr ng-show="expenses.length > 0">
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td class="text-right">
                    {{ total | moneyFormat }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
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
