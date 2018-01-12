<div id="grouped-items" ng-controller="groupedItemCTRL">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <div class="input-group input-group-lg">
                    <input type="text" ng-model="item_name" class="form-control" placeholder="<?php echo __( 'Nom du produit', 'nexo' );?>">
                    <span class="input-group-btn">
                        <button ng-click="submitItem( grouped_items )" type="button" class="btn btn-default">
                            <?php echo __( 'Enregistrer', 'nexo' );?>
                        </button>
                    </span>
                </div>
            </div>

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab_1" data-toggle="tab" aria-expanded="false"><?php echo __( 'Details', 'nexo' );?></a>
                    </li>
                    <li class="">
                        <a href="#tab_2" data-toggle="tab" aria-expanded="false"><?php echo __( 'Produits', 'nexo' );?> ({{ grouped_items.length}})</a>
                    </li>
                </ul>
                <div class="tab-content no-padding">
                    <div class="tab-pane active" id="tab_1"  style="padding: 10px">
                        
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?php echo __( 'UGS', 'nexo' );?>
                                </div>
                                <input type="text" ng-model="form.sku" class="form-control" placeholder="<?php echo __( 'UGS', 'nexo' );?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?php echo __( 'Prix de vente', 'nexo' );?>
                                </div>
                                <input type="text" ng-model="form.sale_price" class="form-control" placeholder="<?php echo __( 'Prix de vente', 'nexo' );?>">
                                <div class="input-group-addon">
                                    {{ getTotal( grouped_items ) | moneyFormat }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?php echo __( 'Categorie', 'nexo' );?>
                                </div>
                                <select type="text" selectpicker="{ liveSearch : true }" toggle-dropdown ng-model="form.category_id" class="form-control selectpicker category-dropdown" placeholder="<?php echo __( 'UGS', 'nexo' );?>">
                                    <option value="{{ category.ID }}" ng-repeat="category in categories">{{ category.NOM }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?php echo __( 'Type de Taxe', 'nexo' );?>
                                </div>
                                <select type="text" ng-model="form.tax_type" class="form-control" placeholder="<?php echo __( 'UGS', 'nexo' );?>">
                                    <option value="exclusive"><?php echo __( 'Excluse', 'nexo' );?></option>
                                    <option value="inclusive"><?php echo __( 'Incluse', 'nexo' );?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?php echo __( 'Taxe', 'nexo' );?>
                                </div>
                                <select type="text" ng-model="form.tax_id" class="form-control" placeholder="<?php echo __( 'UGS', 'nexo' );?>">
                                    <option value="{{ tax.ID }}" ng-repeat="tax in taxes">{{ tax.NAME }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?php echo __( 'Code Barre', 'nexo' );?>
                                </div>
                                <input type="text" ng-model="form.barcode" class="form-control" placeholder="<?php echo __( 'Code Barre', 'nexo' );?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?php echo __( 'Type de code barre', 'nexo' );?>
                                </div>
                                <select type="text" ng-model="form.barcode_type" class="form-control" placeholder="<?php echo __( 'UGS', 'nexo' );?>">
                                    <option ng-repeat="( k, v ) in barcodes" value="{{ k }}">{{ v }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td>
                                        <?php echo __( 'Nom', 'nexo' );?>
                                    </td>
                                    <td class="text-right" width="120">
                                        <?php echo __( 'Prix de vente', 'nexo' );?>
                                    </td>
                                    <td class="text-right" width="120">
                                        <?php echo __( 'Quantité', 'nexo' );?>
                                    </td>
                                    <td width="10"></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" ng-show="grouped_items.length == 0" colspan="4">
                                        <?php echo __( 'Aucun produit ajoutés', 'nexo' );?>
                                    </td>
                                </tr>
                                <tr ng-repeat="item in grouped_items">
                                    <td>{{ item.name }}</td>
                                    <td class="text-right">{{ item.sale_price | moneyFormat }}</td>
                                    <td>

                                        <div class="input-group input-group-sm">
                                            <span ng-click="decrease( $index )" class="input-group-btn">
                                                <button type="button" class="btn btn-default">-</button>
                                            </span>
                                            <input type="text" ng-model="item.quantity" class="form-control" id="exampleInputAmount">
                                            <span ng-click="increase( $index )" class="input-group-btn">
                                                <button type="button" class="btn btn-default">+</button>
                                            </span>
                                        </div>

                                    </td>
                                    <td>
                                        <button ng-click="removeFromGroup( $index )" class="btn btn-danger btn-sm">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="text-right">{{ getTotal( grouped_items ) | moneyFormat }}</td>
                                    <td class="text-right">{{ getTotalQuantity( grouped_items ) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.tab-content -->
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-lg">
                <input ng-model="search_string" type="text" class="form-control" placeholder="Search">
                <span class="input-group-btn">
                    <button ng-click="searchItem( search_string )" type="button" class="btn btn-default">
                        <?php echo __( 'Rechercher', 'nexo' );?>
                    </button>
                </span>
            </div>
            <br>
            <div class="box">
                <div class="box-header with-border">
                    <?php echo __( 'Produits retrouvés' );?>
                </div>
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <td>
                                    <?php echo __( 'Nom', 'nexo' );?>
                                </td>
                                <td class="text-right" width="150">
                                    <?php echo __( 'Code barre', 'nexo' );?>
                                </td>
                                <td class="text-right" width="120">
                                    <?php echo __( 'Prix de vente', 'nexo' );?>
                                </td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-show="searchStatus == 'not_found'">
                                <td class="text-center" colspan="4">
                                    <?php echo __( 'Aucun produit trouvé', 'nexo' );?>
                                </td>
                            </tr>
                            <tr ng-show="searchStatus == 'searching'">
                                <td class="text-center" colspan="4">
                                    <?php echo __( 'Recherche de produits..', 'nexo' );?>
                                </td>
                            </tr>
                            <tr ng-show="searchStatus == 'found'" ng-repeat="entry in entries">
                                <td>{{ entry.DESIGN }}</td>
                                <td class="text-right">{{ entry.CODEBAR }}</td>
                                <td class="text-right">{{ entry.PRIX_DE_VENTE | moneyFormat }}</td>
                                <td>
                                    <button ng-click="addToGrouped( entry )" class="btn-sm btn btn-primary">
                                        <?php echo __( 'Ajouter', 'nexo' );?>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>