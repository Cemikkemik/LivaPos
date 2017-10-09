<div ng-controller="itemHistoryCTRL">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">
            <?php echo __( 'Historique d\'activité sur le produit', 'nexo' );?>
            </h3>
            <span class="box-tools">
                <button print-item=".supply-history" class="btn btn-primary btn-sm"><i class="fa fa-print"></i></button>
            </span>
        </div>
        <div class="box-body no-padding supply-history">
            <style>
            @media print {
                table {
                    font-size: 0.8em;
                }
            }
            </style>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <td width="300"><?php echo __( 'Nom du produit', 'nexo' );?></td>
                        <td width="150"><?php echo __( 'Opération', 'nexo' );?></td>
                        <td class="text-right" width="50"><?php echo __( 'Avant', 'nexo' );?></td>
                        <td class="text-right" width="50"><?php echo __( 'Quantité', 'nexo' );?></td>
                        <td class="text-right" width="50"><?php echo __( 'Après', 'nexo' );?></td>
                        <td class="text-right" width="100"><?php echo __( 'Valeur', 'nexo' );?></td>
                        <td class="text-right" width="150"><?php echo __( 'Total', 'nexo' );?></td>
                        <td class="text-right" width="100"><?php echo __( 'Par', 'nexo' );?></td>
                        <td class="text-right" width="150"><?php echo __( 'Effectué', 'nexo' );?></td>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-hide="item.quantity == 0" ng-repeat="item in items track by $index" class="{{ operationClassName( item ) }}">
                        <td>{{ item.name }}</td>
                        <td>{{ operationName( item.type ) }} </td>
                        <td class="text-right">{{ item.before_quantity }} </td>
                        <td class="text-right">{{ testOperation( item ) }} {{ item.quantity }} </td>
                        <td class="text-right"> = {{ item.after_quantity }} </td>
                        <td class="text-right">{{ item.price | moneyFormat }}</td>
                        <td class="text-right">{{ item.total_price | moneyFormat }} </td>
                        <td class="text-right">{{ item.author_name }} </td>
                        <td class="text-right">{{ item.date }} </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"> <!-- {{ total.unit_price.plus - total.unit_price.minus | moneyFormat }} --></td>
                        <td class="text-right">{{ total.quantity.plus - total.quantity.minus }}</td>
                        <td></td>
                        <td class="text-right">{{ total.total_price.plus - total.total_price.minus | moneyFormat }}</td>
                        <td class="text-right"></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div ng-show="totalPage > 0">
        <ul class="pagination">
            <li ng-class="{ 'active' : currentPage == page }" ng-repeat="page in [] | range:totalPage"><a href="javascript:void(0)" ng-click="loadHistory( page )">{{ page + 1 }}</a></li>
        </ul>
    </div>
</div>
