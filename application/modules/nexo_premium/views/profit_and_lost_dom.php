<?php
global $Options;
$this->load->config( 'rest' );
?>
<div ng-controller="profitAndLost">
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
                <button type="button" name="name"class="btn btn-default" ng-click="getSales()">
                    <i class="fa fa-refresh"></i>
                    <?php echo __( 'Charger', 'nexo_premium' );?>
                </button>
                <button type="button" print-item=".content-wrapper" name="name"class="btn btn-default" ng-click="printReport()">
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
    <table class="table table-bordered table-striped box report_box">
        <thead>
            <tr>
                <td width="400">
                    <?php _e( 'Détails du produit', 'nexo_premium' );?>
                </td>
                <td width="200">
                    <?php _e( 'Date', 'nexo_premium' );?>
                </td>
                <td width="200">
                    <?php echo __( 'Remise (%)', 'nexo_premium' ); ?>
                </td>
                <td width="200">
                    <?php echo __( 'Remise', 'nexo_premium' ); ?>
                </td>
                <td width="200">
                    <?php echo __( 'Quantité', 'nexo_premium' ); ?>
                </td>
                <td width="200">
                    <?php echo __( 'Ventes', 'nexo_premium' ); ?>
                </td>
                <td width="200">
                    <?php echo __( 'Coûts', 'nexo_premium' ); ?>
                </td>
                <td width="200">
                    <?php echo __( 'Bénéfice', 'nexo_premium' ); ?>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="item in items">
                <td>
                    {{ item.DESIGN }}
                </td>
                <td>
                    {{ item.DATE_CREATION }}
                </td>
                <td class="text-right">
                    {{ item.DISCOUNT_PERCENT }}
                </td>
                <td class="text-right">
                    {{ item.DISCOUNT_AMOUNT | moneyFormat }}
                </td>
                <td class="text-right">
                    {{ item.QUANTITE }}
                </td>
                <td class="text-right">
                    {{ item.PRIX * item.QUANTITE | moneyFormat }}
                </td>
                <td class="text-right">
                    {{ item.PRIX_DACHAT * item.QUANTITE | moneyFormat }}
                </td>
                <td class="text-right">
                    {{ ( item.PRIX - item.PRIX_DACHAT ) * item.QUANTITE | moneyFormat }}
                </td>
            </tr>
            <tr ng-show="items.length == 0" class="hidden-print">
                <td colspan="8" class="text-center">
                    <?php echo __( 'Aucun résultat à afficher. Veuillez choisir un interval de temps différent.', 'nexo' ); ?>
                </td>
            </tr>
            <tr ng-show="items.length > 0">
                <td>
                    <?php echo __( 'Total', 'nexo_premium' ); ?>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td class="text-right">
                    {{ totalFixedDiscount | moneyFormat }}
                </td>
                <td class="text-right">
                    {{ totalQuantity }}
                </td>
                <td class="text-right">
                    {{ totalSales | moneyFormat }}
                </td>
                <td class="text-right">
                    {{ totalCosts | moneyFormat }}
                </td>
                <td class="text-right">
                    {{ totalIncome | moneyFormat }}
                </td>
            </tr>
        </tbody>
    </table>
    <?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/filters/money-format.php' );?>
    <script type="text/javascript">
    tendooApp.controller( 'profitAndLost', [ '$http', '$scope', function( $http, $scope ) {

        $scope.$watch( 'items', function(){

            $scope.totalIncome                  =   0;
            $scope.totalCosts                   =   0;
            $scope.totalSales                   =   0;
            $scope.totalQuantity                =   0;
            $scope.totalFixedDiscount           =   0;
            // $scope.totalPercentageDiscount      =   0;

            _.each( $scope.items, function( value ){
                console.log( value.PRIX_DACHAT );
                $scope.totalCosts           +=  ( parseFloat( value.PRIX_DACHAT ) * parseInt( value.QUANTITE ) );
                $scope.totalIncome          +=  ( ( parseFloat( value.PRIX ) - parseFloat( value.PRIX_DACHAT ) ) * parseInt( value.QUANTITE ) );
                $scope.totalSales           +=  ( parseFloat( value.PRIX ) * parseInt( value.QUANTITE ) );
                $scope.totalQuantity        += ( parseInt( value.QUANTITE ) );
                $scope.totalFixedDiscount   +=  ( parseFloat( value.DISCOUNT_AMOUNT ) );
            })
        })

        /**
        *
        * get Sales
        *
        * @param string date start
        * @param string date end
        * @return void
        */

        $scope.getSales     =   function(){
            if( ! angular.isUndefined( $scope.startDate ) &&  ! angular.isUndefined( $scope.endDate ) ) {
                $http.post( '<?php echo site_url( array( 'rest', 'nexo', 'order_with_item' ) );?>' + '?<?php echo store_get_param( null );?>', {
                    'start_date'    : $scope.startDate,
                    'end_date'    : $scope.endDate
                },{
        			headers			:	{
        				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
        			}
        		}).then(function( returned ){
                    $scope.items        =   returned.data;
        		}, function(){
                    $scope.items        =   [];
                });
            } else {
                NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo_premium' );?>', '<?php echo _s( 'Vous devez sélectionner une date valide', 'nexo_premium' );?>' );
            }
        }

        /**
        *
        * Print Report
        *
        * @return void
        */

        $scope.printReport      =   function(){
            // alert( 'OK' );
        }
    }]);
    </script>
</div>
