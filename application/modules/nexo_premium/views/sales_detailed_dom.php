<?php
$this->load->config( 'rest' );
$this->load->config( 'nexo' );
global $Options;
?>
<div ng-controller="advancedSalesReportController">
    <form class="form-inline hidden-print">
        <div class='input-group date' id='datetimepicker6' ng-click="refreshValue()">
        	<span class="input-group-addon"><?php _e('Date de départ', 'nexo_premium');?></span>
            <input ng-change="refreshValue()" type='text' class="form-control" ng-model="startDate" value="" />
            <span class="input-group-addon"> <span class="glyphicon glyphicon-calendar"></span> </span>
    	</div>
        <div class='input-group date' id='datetimepicker7' ng-click="refreshValue()">
        	<span class="input-group-addon"><?php _e('Date de fin', 'nexo_premium');?></span>
            <input ng-change="refreshValue()" type='text' class="form-control" ng-model="endDate" value="" />
            <span class="input-group-addon"> <span class="glyphicon glyphicon-calendar"></span> </span>
    	</div>
        <div class="input-group">
          <span class="input-group-addon"><?php echo __( 'Choisir le format', 'nexo_premium' );?></span>
          <select type="text" class="form-control" placeholder="" ng-model="reportType">
              <option value="full"><?php echo __( 'Détaillé', 'nexo_premium' );?></option>
              <option value="small"><?php echo __( 'Condensé', 'nexo_premium' );?></option>
          </select>
        </div>
        <input type="button" class="btn btn-primary" ng-click="fetchDetailedReport()" value="<?php _e('Afficher les résultats', 'nexo_premium');?>" />
        <div class="input-group">
          <span class="input-group-btn">
            <button class="btn btn-default" print-item=".report-wrapper" type="button"><?php _e('Imprimer', 'nexo_premium');?></button>
          </span>

        </div>
    </form>
    <br>
    <div class="row report-wrapper">
        <div class="col-lg-6">
            <h4 class="visible-print text-center">
                <?php _e( 'Rapport des ventes détaillées', 'nexo_premium' );?>
            </h4>
            <hr class="visible-print">
            <table class="table table-bordered box" ng-show="reportType == 'full'" ng-class="{ 'hidden-print' : reportType == 'small' }">
                <thead>
                    <tr>
                        <td width="400"><?php _e( 'Entrée', 'nexo_premium' );?></td>
                        <td width="200"><?php echo _e( 'Montant', 'nexo_premium' );?></td>
                    </tr>
                </thead>
                <tbody ng-repeat="entry in entries">
                    <tr>
                        <td>
                            <strong>{{ entry.DATE }} &mdash; <?php echo $this->events->apply_filters( 'detailed_sale_report_entry_code', '{{ entry.CODE }}' );?></strong>
                        </td>
                        <td class="text-right">
                            <strong>{{ entry.TOTAL | moneyFormat }}</strong>
                        </td>
                    </tr>
                    <tr ng-show="entry.REMISE_TYPE == 'percentage'" class="info">
                        <td>
                            <?php echo __( 'Remise au pourcentage', 'nexo_premium' );?>
                        </td>
                        <td class="text-right">
                            <span class="pull-left">{{ entry.REMISE_PERCENT }}%</span>
                            <span class="pull-right">- {{ ( calculateTotal( entry.items ) * entry.REMISE_PERCENT ) / 100 | moneyFormat }}</span>
                        </td>
                    </tr>
                    <tr ng-show="entry.REMISE_TYPE == 'flat'" class="info">
                        <td>
                            <?php echo __( 'Remise fixe', 'nexo_premium' );?>
                        </td>
                        <td class="text-right">
                            <span class="text-left"> </span>
                            <span class="text-right">- {{ entry.REMISE | moneyFormat }}</span>
                        </td>
                    </tr>
                    <tr ng-repeat="item in entry.items">
                        <td>{{ item.QUANTITE }} X {{ item.DESIGN }}</td>
                        <td class="text-right"> {{ item.PRIX | moneyFormat }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr ng-show="isEmpty" class="hidden-print">
                        <td colspan="2" class="text-center"><?php _e( 'Il n\'y a rien à afficher aujourd\'hui, veuillez choisir un interval de temps différent.', 'nexo_premium' );?></td>
                    </tr>
                </tfoot>
            </table>

            <table class="table table-bordered table-striped box">
                <thead>
                    <tr>
                        <td><?php echo __( 'Statistiques des ventes', 'nexo_premium' );?></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="400"><strong>{{ checkPaidOrders( entries ).nbr }} <?php echo __( 'Commandes Comptant', 'nexo_premium' );?></strong></td>
                        <td width="200" class="text-right"><strong>{{ checkPaidOrders( entries ).total | moneyFormat }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered box table-striped">
                <thead>
                    <tr>
                        <td width="400"><?php echo __( 'Analyse des ventes', 'nexo_premium' );?></td>
                        <td width="200"></td>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="payment in paymentTypeDetails">
                        <td>{{ payment.nbr }} &mdash; {{ payment.name }}</td>
                        <td class="text-right">{{ payment.total | moneyFormat }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong><?php echo __( 'Total', 'nexo_premium' );?></strong></td>
                        <td class="text-right"><strong>{{ totalOrder( entries ) | moneyFormat }}</strong></td>
                    </tr>
                </tfoot>
            </table>

            <table class="table table-bordered table-striped box ">
                <thead>
                    <tr>
                        <td width="400"><?php echo __( 'Autres Statistiques', 'nexo_premium' );?></td>
                        <td width="100" class="text-right"><?php echo __( 'Total', 'nexo_premium' );?></td>
                        <td width="100" class="text-right"><?php echo __( 'Montant', 'nexo_premium' );?></td>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-hide="isEmpty">
                        <td><?php echo __( 'Moyenne des commandes', 'nexo_premium' );?></td>
                        <td class="text-right">{{ countOrders( entries ) }}</td>
                        <td class="text-right">{{ totalOrder( entries ) / countOrders( entries ) > 0 ? totalOrder( entries ) / countOrders( entries ) : 0  | moneyFormat }}</td>
                    </tr>
                    <tr ng-hide="isEmpty">
                        <td><?php echo __( 'Articles par commandes', 'nexo_premium' );?></td>
                        <td class="text-right">{{ rawData.length }}</td>
                        <td class="text-right">{{ rawData.length / countOrders( entries ) > 0 ? rawData.length / countOrders( entries ) : 0  | number : 3 }}</td>
                    </tr>
                    <tr ng-hide="isEmpty">
                        <td><?php echo __( 'Moyenne des prix par articles', 'nexo_premium' );?></td>
                        <td class="text-right">{{ rawData.length }}</td>
                        <td class="text-right">{{ totalOrder( entries ) / rawData.length > 0 ? totalOrder( entries ) / rawData.length : 0 | number : 3 }}</td>
                    </tr>

                    <tr ng-show="isEmpty" class="hidden-print">
                        <td colspan="3" class="text-center"><?php echo __( 'Aucune information à afficher. Veuillez choisir un interval de date correcte.', 'nexo_premium' );?></td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered table-striped box">
                <thead>
                    <tr>
                        <td width="400"><?php _e( 'Ventes par Staff', 'nexo_premium' );?></td>
                        <td width="200"></td>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-hide="isEmpty" ng-repeat="author in authors">
                        <td>{{ author.name }} ({{ author.nbr * 100 /  countOrders( entries )  }}%)</td>
                        <td class="text-right">{{ author.total | moneyFormat }}</td>
                    </tr>
                    <tr ng-show="isEmpty" class="hidden-print">
                        <td colspan="3" class="text-center"><?php echo __( 'Aucune information à afficher. Veuillez choisir un interval de date correcte.', 'nexo_premium' );?></td>
                    </tr>
                </tbody>
            </table>
            <style type="text/css">
            @media print {
                body table td {
                    font-size: 10px;
                }
            }
            </style>
        </div>
    </div>
</div>
