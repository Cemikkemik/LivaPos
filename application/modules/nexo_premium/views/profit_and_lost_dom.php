<?php
global $Options;
$this->load->config( 'rest' );
use Carbon\Carbon;
?>
<div ng-controller="profitAndLost">
    <div class="row hidden-print">
        <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="input-group">
                <span class="input-group-addon"><?php echo __( 'Date de départ', 'nexo_premium' );?></span>
                <input ng-model="startDate" type="text" class="form-control start_date" placeholder="">

            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="input-group">
                <span class="input-group-addon"><?php echo __( 'Date de fin', 'nexo_premium' );?></span>
                <input ng-model="endDate" type="text" class="form-control end_date" placeholder="">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="btn-group btn-group-md">
                <button type="button" name="name"class="btn btn-default" ng-click="getSales()">
                    <i class="fa fa-refresh"></i>
                    <?php echo __( 'Charger', 'nexo_premium' );?>
                </button>
                <button type="button" print-item=".content-wrapper" name="name"class="btn btn-default" ng-click="printReport()">
                    <i class="fa fa-print"></i>
                    <?php echo __( 'Imprimer', 'nexo_premium' );?>
                </button>
                <button type="button" name="name"class="btn btn-default" ng-click="doExportCSV()">
                    <i class="fa fa-file"></i>
                    <?php echo __( 'Exporter CSV', 'nexo_premium' );?>
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
            <tr style="font-weight:600">
                <td width="250">
                    <?php _e( 'Date', 'nexo_premium' );?>
                </td>
                <td width="300">
                    <?php _e( 'Détails du produit', 'nexo_premium' );?>
                </td>

                <td width="50" class="text-right">
                    <?php echo __( 'Quantité', 'nexo_premium' ); ?>
                </td>
                <td width="200" class="text-right">
                    <?php echo __( 'Prix de vente Brut', 'nexo_premium' ); ?>
                </td>
                <td width="180" class="text-right">
                    <?php echo __( 'Remise (%)', 'nexo_premium' ); ?>
                </td>
                <td width="200" class="text-right">
                    <?php echo __( 'Remise', 'nexo_premium' ); ?>
                </td>
                <td width="200" class="text-right">
                    <?php echo __( 'Prix de vente Net', 'nexo_premium' ); ?>
                </td>
                <td width="200" class="text-right">
                    <?php _e( 'Prix d\'achat', 'nexo_premium' );?>
                </td>
                <td width="200" class="text-right">
                    <?php echo __( 'Bénéfice', 'nexo_premium' ); ?>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="item in items | orderBy : 'DATE_CREATION' : false">
                <td>
                    {{ item.DATE_CREATION | date: 'medium' }}
                </td>
                <td>
                    {{ item.DESIGN }}
                </td>
                <td class="text-right">
                    {{ item.QUANTITE }}
                </td>
                <td class="text-right info">
                    {{ item.PRIX * item.QUANTITE | moneyFormat }}
                </td>
                <td class="text-right warning">
                    {{ showPercentage( cartPercentage( item ) ) }} {{ showPercentage( item.DISCOUNT_PERCENT, '+' ) }} : {{ ( calculateCartPercentage( item ) + calculateItemPercentage( item ) ) * item.QUANTITE | moneyFormat }}
                </td>
                <td class="text-right warning">
                    {{ showFixedItemUniqueDiscount( item ) + showFixedCartUniqueDiscount( item ) | moneyFormat }}
                </td>
                <!--  - ( ( item.PRIX * item.DISCOUNT_PERCENT ) / 100 ) ) -->
                <td class="text-right info">
                    {{ calculateNetSellingPrice( item ) | moneyFormat }}
                </td>
                <td class="text-right info">
                    {{ item.PRIX_DACHAT * item.QUANTITE | moneyFormat }}
                </td>
                <td class="text-right" ng-class="{ 'danger' : calculateProfit( item ) < 0, 'success' : calculateProfit( item ) > 0, 'default' : calculateProfit( item ) == 0}">
                    {{ calculateProfit( item )| moneyFormat }}
                </td>
            </tr>
            <tr ng-show="items.length == 0" class="hidden-print">
                <td colspan="8" class="text-center">
                    <?php echo __( 'Aucun résultat à afficher. Veuillez choisir un interval de temps différent.', 'nexo_premium' ); ?>
                </td>
            </tr>
            <tr ng-show="items.length > 0">
                <td>
                    <strong><?php echo __( 'Total', 'nexo_premium' ); ?></strong>
                </td>
                <td>
                </td>
                <td class="text-right">
                    {{ totalQuantity }}
                </td>
                <td class="text-right">
                    {{ totalGrossSalePrice | moneyFormat }}
                </td>

                <td class="text-right">
                    {{ totalPercentDiscount | moneyFormat }}
                </td>
                <td class="text-right">
                    {{ totalFixedDiscount | moneyFormat }}
                </td>
                <td class="text-right">
                    {{ totalSales | moneyFormat }}
                </td>
                <td class="text-right">
                    {{ totalPurchasePrice | moneyFormat }}
                </td>
                <td class="text-right" ng-class="{ warning : totalIncome < 0}">
                    {{ totalIncome | moneyFormat }}
                </td>
            </tr>
        </tbody>
    </table>
    <style media="print">
    @media print{
        table {
            font-size: 12px;
        }
        h1 {
            font-size: 16px;
            text-align: center;
        }
    }
    </style>
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
            $scope.totalGrossSalePrice          =   0;
            $scope.totalPercentDiscount         =   0;
            $scope.totalPurchasePrice           =   0;

            _.each( $scope.items, function( value ){
                // Prix de vente Net
                $scope.totalPurchasePrice   +=  ( parseFloat( value.PRIX_DACHAT ) * parseInt( value.QUANTITE ) );

                // Benefice Net
                $scope.totalIncome          +=  $scope.calculateProfit( value );

                $scope.totalSales           +=  $scope.calculateNetSellingPrice( value );

                // Total Percent discount
                $scope.totalPercentDiscount +=  $scope.calculatePercentage( value );

                // Total Quantite
                $scope.totalQuantity        +=  ( parseInt( value.QUANTITE ) );

                //
                $scope.totalFixedDiscount   +=  $scope.showFixedDiscount( value );

                // Prix de vente Brut
                $scope.totalGrossSalePrice   += ( parseFloat( value.PRIX_DE_VENTE ) * parseFloat( value.QUANTITE ) );
            })
        });

        /**
        *
        * Calculate Percentage
        *
        * @param object item object
        * @return int/float
        */

        $scope.calculatePercentage              =   function( item ){
            var item_percentage                 =   Math.abs(item.DISCOUNT_PERCENT * item.PRIX_DE_VENTE) / 100;

            var priceAfterDiscount              =   parseFloat( item.PRIX_DE_VENTE ) - item_percentage;
            var cartValue                       =   $scope.cartValue( item, true );
            var general_percentage_value        =   0;

            if( item.REMISE_TYPE == 'percentage' ) {
                var general_percentage_value        =   ( priceAfterDiscount * parseFloat( item.REMISE_PERCENT ) ) / 100;
            }

            // console.log( item_percentage + general_percentage_value );

            return ( item_percentage + general_percentage_value ); //
        }

        /**
        *
        * Calculate Cart percentage
        *
        * @param
        * @return
        */

        $scope.calculateCartPercentage      =   function( item ){

            var priceAfterDiscount              =   parseFloat( item.PRIX_DE_VENTE ) - (
                $scope.calculateItemPercentage( item ) + $scope.showFixedItemUniqueDiscount( item )
            );

            var general_percentage_value        =   0;

            if( item.REMISE_TYPE == 'percentage' ) {
                var general_percentage_value        =   ( priceAfterDiscount * parseFloat( item.REMISE_PERCENT ) ) / 100;
            }

            // console.log( item_percentage + general_percentage_value );

            return general_percentage_value; //
        }

        /**
        *
        * Calculate Item Percentage
        *
        * @param
        * @return
        */

        $scope.calculateItemPercentage      =   function( item ){
            var item_percentage                 =   Math.abs(item.DISCOUNT_PERCENT * item.PRIX_DE_VENTE) / 100;

            return item_percentage;
        }

        /**
        *
        * Calculate Profit
        *
        * @param object item
        * @return int/float
        */

        $scope.calculateProfit          =   function( item ) {
            return $scope.calculateNetSellingPrice( item ) - ( item.PRIX_DACHAT * item.QUANTITE );
        }

        /**
        *
        * Calculate Net Setling Price
        *
        * @param object item object
        * @return int/float
        */

        $scope.calculateNetSellingPrice         =   function( item ){
            return (
                (
                    parseFloat( item.PRIX ) -
                    (
                        $scope.showFixedItemUniqueDiscount( item ) +
                        $scope.calculateItemPercentage( item )
                    )
                )
                * parseFloat( item.QUANTITE )
            ) - ( $scope.calculateCartPercentage( item ) + $scope.showFixedCartUniqueDiscount( item ) );
        }

        /**
        *
        * get Cart Percentage
        *
        * @param object
        * @return int/float
        */

        $scope.cartPercentage           =   function( item ){
            if( item.REMISE_TYPE == 'percentage' ) {
                return item.REMISE_PERCENT;
            }
            return 0;
        }

        /**
        *
        * Cart value
        *
        * @param object item
        * @param bool calculate inline discount
        * @return int/float
        */

        $scope.cartValue            =   function( item, inlineDiscount ){
            var cartValue                       =   ( parseFloat( item.TOTAL )
            // Valeur réelle du panier
            + ( parseFloat( item.REMISE ) ) ) // + parseFloat( item.RABAIS ) + parseFloat( item.RISTOURNE )
            // Restauration de la TVA
            - parseFloat( item.TVA );

            if( inlineDiscount === true ) {
                // Exclure aussi les remises effectués sur les produits
                if( item.DISCOUNT_TYPE == 'percentage' && item.DISCOUNT_PERCENT != '0' ) {
                    cartValue       +=  ( parseInt( item.PRIX_DE_VENTE ) * parseInt( item.DISCOUNT_PERCENT ) ) / 100;
                } else  { // in this case for fixed discount on item
                    cartValue       +=  parseInt( item.DISCOUNT_AMOUNT );
                }
            }

            return cartValue;
        }

        /**
        *
        * Cart Fixed Discount
        *
        * @return void
        */

        $scope.cartFixedDiscount        =   function(){
            if( item.REMISE_TYPE == 'flat' ) {
                return item.REMISE;
            }
            return 0;
        }

        /**
        *
        * Do Export to CSV
        *
        * @param
        * @return
        */

        $scope.doExportCSV          =   function() {
            if( angular.isDefined( $scope.items ) ) {
                if( $scope.items.length > 0 ) {
                    var     data           =   new Array;

                    _.each( $scope.items, function( value ){
                        var obj             =   new Object;

                        // Item name
                        obj[ '<?php echo _s( 'Date', 'nexo_premium' );?>']  =   value.DATE_CREATION;

                        // Item name
                        obj[ '<?php echo _s( 'Nom du produit', 'nexo_premium' );?>']  =   value.DESIGN;

                        // Total Quantite
                        obj[ '<?php echo _s( 'Quantité', 'nexo_premium' );?>']   =  ( parseInt( value.QUANTITE ) );

                        // Prix de vente Brut
                        obj[ '<?php echo _s( 'Ventes Brutes', 'nexo_premium' );?>']   = ( parseFloat( value.PRIX_DE_VENTE ) * parseFloat( value.QUANTITE ) );

                        // Total Percent discount
                        obj[ '<?php echo _s( 'Remises (%)', 'nexo_premium' );?>']   =    $scope.calculatePercentage( value );

                        // Remise fixes
                        obj[ '<?php echo _s( 'Remises Fixes', 'nexo_premium' );?>']   =  parseFloat( value.DISCOUNT_AMOUNT );

                        // Prix de vente Net
                        obj[ '<?php echo _s( 'Ventes Nettes', 'nexo_premium' );?>']   =  $scope.calculateNetSellingPrice( value );

                        // Prix d'achat
                        obj[ '<?php echo _s( 'Prix d\'achat', 'nexo_premium' );?>']  =   ( parseFloat( value.PRIX_DACHAT ) * parseInt( value.QUANTITE ) );

                        // Benefice Net
                        obj[ '<?php echo _s( 'Revenus', 'nexo_premium' );?>']   =  $scope.calculateProfit( value );

                        data.push( obj );
                    });

                    $scope.exportToCSV( data, '<?php echo _s( 'Rapport des revenus et des pertes', 'nexo_premium' );?>', true );
                    return;
                }
            }

            NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo_premium' );?>', '<?php echo _s( 'Aucune données disponible pour l\'impression. Veuillez afficher des résultats en premier', 'nexo_premium' );?>');
        }

        /**
        *
        * export to CSV
        *
        * @param  object/json json data
        * @param string title
        * @param bool label
        * @return string
        */

        $scope.exportToCSV     =   function (JSONData, ReportTitle, ShowLabel) {
            //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
            var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;

            var CSV = '';
            //Set Report title in first row or line

            CSV += ReportTitle + '\r\n\n';

            //This condition will generate the Label/Header
            if (ShowLabel) {
                var row = "";

                //This loop will extract the label from 1st index of on array
                for (var index in arrData[0]) {

                    //Now convert each value to string and comma-seprated
                    row += index + ',';
                }

                row = row.slice(0, -1);

                //append Label row with line break
                CSV += row + '\r\n';
            }

            //1st loop is to extract each row
            for (var i = 0; i < arrData.length; i++) {
                var row = "";

                //2nd loop will extract each column and convert it in string comma-seprated
                for (var index in arrData[i]) {
                    row += '"' + arrData[i][index] + '",';
                }

                row.slice(0, row.length - 1);

                //add a line break after each row
                CSV += row + '\r\n';
            }

            if (CSV == '') {
                alert("Invalid data");
                return;
            }

            //Generate a file name
            var fileName = "";
            //this will remove the blank-spaces from the title and replace it with an underscore
            fileName += ReportTitle.replace(/ /g,"_");

            //Initialize file format you want csv or xls
            var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);

            // Now the little tricky part.
            // you can use either>> window.open(uri);
            // but this will not work in some browsers
            // or you will not get the correct file extension

            //this trick will generate a temp <a /> tag
            var link = document.createElement("a");
            link.href = uri;

            //set the visibility hidden so it will not effect on your web-layout
            link.style = "visibility:hidden";
            link.download = fileName + ".csv";

            //this part will append the anchor tag and remove it after automatic click
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }


        /**
        *
        * get Sales
        *
        * @param string date start
        * @param string date end
        * @return void
        */

        $scope.getSales     =   function(){
            if( ! angular.isUndefined( $scope.startDate ) &&  ! angular.isUndefined( $scope.endDate ) || true ) {
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

        $scope.startDate        =   '<?php echo Carbon::parse( date_now() )->startOfDay()->toDateTimeString();?>';
        $scope.endDate          =   '<?php echo Carbon::parse( date_now() )->endOfDay()->toDateTimeString();?>';
        $scope.getSales();

        /**
        *
        * Is Float
        *
        * @param value
        * @return bool
        */

        $scope.isFloat          =   function(n){
            return Number(n) === n && n % 1 !== 0;
        }

        /**
        *
        * Is Float
        *
        * @param value
        * @return bool
        */

        $scope.isInt            =   function (n){
            return Number(n) === n && n % 1 === 0;
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

        /**
        *
        * show percentage
        *
        * @param int/float
        * @return string
        */

        $scope.showPercentage       =   function( value, before ){
            if( parseFloat( value ) > 0 ) {
                value           =   $scope.isFloat( value ) ? NexoAPI.Format( value ) : value;
                return before ? before + value + '%' : value + '%';
            }
            return '';
        }

        /**
        *
        * Show Fixed Discounted
        *
        * @param object item
        * @return int/float
        */

        $scope.showFixedDiscount        =   function( item ) {
            var general_percentage_value    =   0;

            if( item.REMISE_TYPE == 'flat' ) {
                var percent         =       ( parseFloat( item.REMISE ) * 100 ) / $scope.cartValue( item );
                general_percentage_value        =   ( ( parseFloat( item.PRIX_DE_VENTE ) * parseInt( item.QUANTITE ) ) * percent ) / 100;
            }

            return Math.abs( parseFloat( item.DISCOUNT_AMOUNT ) + general_percentage_value ); // parseFloat( item.REMISE )
        }

        /**
        *
        * Show Fixed unique discount
        *
        * @return int/float
        */

        $scope.showFixedCartUniqueDiscount        =   function( item ) {
            var general_percentage_value    =   0;

            if( item.REMISE_TYPE == 'flat' ) {
                var percent         =       ( parseFloat( item.REMISE ) * 100 ) / $scope.cartValue( item );
                general_percentage_value        =   ( ( parseFloat( item.PRIX_DE_VENTE ) ) * percent ) / 100;
            }

            return general_percentage_value; // parseFloat( item.REMISE )
        }

        /**
        *
        * showFixed Item Discount
        *
        * @return int/float
        */

        $scope.showFixedItemUniqueDiscount        =   function( item ) {
            return parseFloat( item.DISCOUNT_AMOUNT ); // parseFloat( item.REMISE )
        }
    }]);
    </script>
</div>
