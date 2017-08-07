<?php
global $Options;
$this->config->load( 'rest' );
use Carbon\Carbon;

$table_total            =   $this->events->apply_filters( 'np_profit_lost_report_tfoot_row', array(
    'total'             =>    array(
      'text'          =>    __( 'Total', 'nexo_premium' ),
      'class'         =>    '',
      'attr'          =>    '',
      'colspan'       =>    2
    ),
    'total_qte'         =>    array(
      'text'          =>    '{{ totalQuantity }}',
      'class'         =>    'text-right',
      'attr'          =>    '',
      'colspan'       =>    0
    ),
    'total_gsle'        =>    array(
      'text'          =>    '{{ totalGrossSalePrice | moneyFormat }}',
      'class'         =>    'text-right',
      'attr'          =>    '',
      'colspan'       =>    0
    ),
    'total_pdiscount'   =>    array(
      'text'          =>    '{{ totalPercentDiscount | moneyFormat }}',
      'class'         =>    'text-right',
      'attr'          =>    '',
      'colspan'       =>    0
    ),
    'total_fdiscount'   =>    array(
      'text'          =>    '{{ totalFixedDiscount | moneyFormat }}',
      'class'         =>    'text-right',
      'attr'          =>    '',
      'colspan'       =>    0
    ),
    'total_sales'       =>    array(
      'text'          =>    '{{ totalSales | moneyFormat }}',
      'class'         =>    'text-right',
      'attr'          =>    '',
      'colspan'       =>    0
    ),
    'total_purchase'    =>    array(
      'text'          =>    '{{ totalPurchasePrice | moneyFormat }}',
      'class'         =>    'text-right',
      'attr'          =>    '',
      'colspan'       =>    0
    ),
    'total_income'      =>    array(
      'text'          =>    '{{ totalIncome | moneyFormat }}',
      'class'         =>    'text-right',
      'attr'          =>    'ng-class="{ warning : totalIncome < 0, success : totalIncome > 0, default : totalIncome == 0}"',
      'colspan'       =>    0
    )
) );

$table_head             =   $this->events->apply_filters( 'np_profit_lost_report_thead_row', array(
    'date'              =>  array(
      'text'          =>  _s( 'Date', 'nexo_premium' ),
      'class'         =>  '',
      'width'         =>  190
    ),
    'design'            =>  array(
      'text'          =>  _s( 'Nom du produit', 'nexo_premium' ),
      'class'         =>  '',
      'width'         =>  300
    ),
    'qte'               =>  array(
      'text'          =>  _s( 'Quantité', 'nexo_premium' ),
      'class'         =>  'text-right',
      'width'         =>  70
    ),
    'gros_sale'         =>  array(
      'text'            =>  _s( 'Prix de vente brut', 'nexo_premium' ),
      'class'           =>  'text-right',
      'width'           =>  180
    ),
    'discount_p'        =>  array(
      'text'          =>  _s( 'Remise (%)', 'nexo_premium' ),
      'class'         =>  'text-right',
      'width'         =>  250
    ),
    'discount_f'        =>  array(
      'text'          =>  _s( 'Remise', 'nexo_premium' ),
      'class'         =>  'text-right',
      'width'         =>  120
    ),
    'net_sale'          =>  array(
      'text'          =>  _s( 'Prix de vente net', 'nexo_premium' ),
      'class'         =>  'text-right',
      'width'         =>  200
    ),
    'buy_price'         =>  array(
      'text'          =>  _s( 'Prix d\'achat', 'nexo_premium' ),
      'class'         =>  'text-right',
      'width'         =>  200
    ),
    'profit'            =>  array(
      'text'          =>  _s( 'Bénéfice', 'nexo_premium' ),
      'class'         =>  'text-right',
      'width'         =>  150
    )
) );

$table_column           =   $this->events->apply_filters( 'np_profit_lost_report_tbody_row', array(
    'date'              =>  array(
      'text'          =>  '{{ item.DATE_CREATION | date : "' . store_option( 'nexo_js_datetime_format', 'medium' ) . '" }}',
      'class'         =>  '',
      'attr'          =>  '',
      'csv_field'       =>  'item.DATE_CREATION'
    ),
    'design'            =>  array(
      'text'          =>  '{{ item.DESIGN.length == 0 ? item.NAME : item.DESIGN }}',
      'class'         =>  '',
      'attr'          =>  '',
      'csv_field'       =>  'item.DESIGN'
    ),
    'qte'               =>  array(
      'text'          =>  '{{ item.QUANTITE }}',
      'class'         =>  'text-right',
      'attr'          =>  '',
      'csv_field'       =>  'item.QUANTITE'
    ),
    'gros_sale'         =>  array(
      'text'          =>  '{{ item.PRIX * item.QUANTITE | moneyFormat }}',
      'class'         =>  'text-right info',
      'attr'          =>  '',
      'csv_field'       =>  'item.PRIX * item.QUANTITE'
    ),
    'discount_p'        =>  array(
      'text'          =>  '{{ showPercentage( cartPercentage( item ) ) }} {{ showPercentage( item.DISCOUNT_PERCENT, "+" ) }} {{ ( calculateCartPercentage( item ) + calculateItemPercentage( item ) ) * item.QUANTITE | moneyFormat }}',
      'class'         =>  'text-right warning',
      'attr'          =>  '',
      'csv_field'       =>  '( $scope.calculateCartPercentage( item ) + $scope.calculateItemPercentage( item ) ) * item.QUANTITE'
    ),
    'discount_f'        =>  array(
      'text'          =>  '{{ showFixedItemUniqueDiscount( item ) + showFixedCartUniqueDiscount( item ) | moneyFormat }}',
      'class'         =>  'text-right warning',
      'attr'          =>  '',
      'csv_field'       =>  '$scope.showFixedItemUniqueDiscount( item ) + $scope.showFixedCartUniqueDiscount( item )'
    ),
    'net_sale'          =>  array(
      'text'          =>  '{{ calculateNetSellingPrice( item ) | moneyFormat }}',
      'class'         =>  'text-right info',
      'attr'          =>  '',
      'csv_field'       =>  '$scope.calculateNetSellingPrice( item )'
    ),
    'buy_price'         =>  array(
      'text'          =>  '{{ item.PRIX_DACHAT * item.QUANTITE | moneyFormat }}',
      'class'         =>  'text-right info',
      'attr'          =>  '',
      'csv_field'       =>  'item.PRIX_DACHAT * item.QUANTITE'
    ),
    'profit'            =>  array(
      'text'          =>  '{{ calculateProfit( item )| moneyFormat }}',
      'class'         =>  'text-right info',
      'attr'          =>  'ng-class="{ \'danger\' : calculateProfit( item ) < 0, \'success\' : calculateProfit( item ) > 0, \'default\' : calculateProfit( item ) == 0}"',
      'csv_field'       =>  '$scope.calculateProfit( item )'
    )
) );
 ?>
<script type="text/javascript">
$('.start_date').datepicker({
    format: 'mm/dd/yyyy'
});
$('.end_date').datepicker({
    format: 'mm/dd/yyyy'
});
</script>
<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/filters/money-format.php' );?>
<script type="text/javascript">
tendooApp.controller( 'profitAndLosses', [ '$http', '$scope', function( $http, $scope ) {
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
            $scope.totalGrossSalePrice   += ( parseFloat( value.PRIX ) * parseFloat( value.QUANTITE ) );
        });

        // columnNbr
        $scope.columnNbr              = angular.element( 'thead td' ).length;
    });

    /**
    *
    * Calculate Percentage
    *
    * @param object item object
    * @return int/float
    */

    $scope.calculatePercentage              =   function( item ){
        var item_percentage                 =   Math.abs(item.DISCOUNT_PERCENT * item.PRIX ) / 100;

        var priceAfterDiscount              =   parseFloat( item.PRIX ) - item_percentage;
        var cartValue                       =   $scope.cartValue( item, true );
        var general_percentage_value        =   0;

        if( item.REMISE_TYPE == 'percentage' ) {
            var general_percentage_value        =   ( priceAfterDiscount * parseFloat( item.REMISE_PERCENT ) ) / 100;
        }

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

        var priceAfterDiscount              =   parseFloat( item.PRIX ) - (
            $scope.calculateItemPercentage( item ) + $scope.showFixedItemUniqueDiscount( item )
        );

        var general_percentage_value        =   0;

        if( item.REMISE_TYPE == 'percentage' ) {
            general_percentage_value        =   ( priceAfterDiscount * parseFloat( item.REMISE_PERCENT ) ) / 100;
        }

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

                _.each( $scope.items, function( item ){
                    var obj             =   new Object;

                    <?php foreach( $this->events->apply_filters( 'np_profit_lost_report_tbody_row', $table_column ) as $key => $row ):?>
                        obj[ '<?php echo $table_head[ $key ][ 'text' ];?>' ]     =   <?php echo $row[ 'csv_field' ];?>;
                    <?php endforeach;?>

                    data.push( obj );
                });

                $scope.exportToCSV( data, '<?php echo _s( 'Rapport des bénéfices et des pertes', 'nexo_premium' );?>', true );
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
                _.each( returned.data, function( entry ) {
                    entry.DATE_CREATION     =   moment( entry.DATE_CREATION ).format( '<?php echo store_option( 'nexo_js_datetime_format', 'llll' );?>' )
                });

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
