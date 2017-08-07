<?php
global $Options;
$this->load->config( 'rest' );
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
      'width'         =>  220
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
      'width'         =>  180
    ),
    'discount_f'        =>  array(
      'text'          =>  _s( 'Remise', 'nexo_premium' ),
      'class'         =>  'text-right',
      'width'         =>  120
    ),
    'net_sale'          =>  array(
      'text'          =>  _s( 'Prix de vente net', 'nexo_premium' ),
      'class'         =>  'text-right',
      'width'         =>  180
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
      'text'          =>  '{{ item.DESIGN }}',
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
<div ng-controller="profitAndLosses" ng-cloak>
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

    <table class="table table-bordered table-striped box report_box">
        <thead>
            <tr style="font-weight:600">
                <?php foreach( $table_head as $row ):?>
                    <td width="<?php echo @$row[ 'width' ]; ?>" class="<?php echo @$row[ 'class' ]; ?>">
                      <?php echo @$row[ 'text' ] ?>
                    </td>
                <?php endforeach;?>

            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="item in items | orderBy : 'DATE_CREATION' : false">
                <?php foreach( $table_column as $row ):?>
                    <td class="<?php echo @$row[ 'class' ];?>" <?php echo @$row[ 'attr' ];?>>
                      <?php echo @$row[ 'text' ];?>
                    </td>
                <?php endforeach;?>
            </tr>
            <tr ng-show="items.length == 0" class="hidden-print">
                <td colspan="{{ columnNbr }}" class="text-center">
                    <?php echo __( 'Aucun résultat à afficher. Veuillez choisir un interval de temps différent.', 'nexo_premium' ); ?>
                </td>
            </tr>
            <tr ng-show="items.length > 0">
                <?php foreach( $table_total as $row ):?>
                    <td class="<?php echo @$row[ 'class' ]; ?>" <?php echo @$row[ 'attr' ]; ?> colspan="<?php echo @$row[ 'colspan' ] ?>">
                      <?php echo @$row[ 'text' ];?>
                    </td>
                <?php endforeach;?>
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
</div>
