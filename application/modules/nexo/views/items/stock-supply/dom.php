<div class="row" ng-controller="stockSupplyingCTRL">
    <div class="col-md-12" ng-cloak>
        <form ng-submit="fetchItem()">
            <div class="input-group input-group-lg">
                <span class="input-group-addon"><?php echo __( 'Produit', 'nexo' );?></span>
                <input type="text" class="form-control barcode-search" 
                    ng-model="fields.barcode" placeholder="<?php echo __( 'Code barre, nom du produit ou sku', 'nexo' );?>"
                    ng-model-options="{ debounce: 1000 }"
                >
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-default"><?php echo __( 'Rechercher', 'nexo' );?></button>
                </div>
            </div>
        </form>
        
        <br>
        <div class="callout callout-danger" ng-if="status == 404 && item.length == 0">
            <h4><?php echo __( 'Produit introuvable', 'nexo' );?></h4>
            <p><?php echo __( 'Le produit que vous recherchez n\'est pas disponible. Veuillez spécifier d\'autres termes de recherche.', 'nexo' );?></p>
        </div>
        <div class="callout callout-danger" ng-if="form.$valid == false && form.$submitted == true">
            <h4><?php echo __( 'Impossible de soumettre les données', 'nexo' );?></h4>

            <p><?php echo __( 'Ce formulaire contient une ou plusieurs erreurs.', 'nexo' );?></p>
        </div>
        <div class="callout callout-success" ng-if="status == 202">
            <h4><?php echo __( 'Opération effectuée', 'nexo' );?></h4>
            <p><?php echo __( 'La mise à jour du stock s\'est déroulé correctement.', 'nexo' );?></p>
        </div>
        <div class="callout callout-info" ng-if="( barcode.length < 3 || barcode == null ) && item.length == 0">
            <p><?php echo __( 'Veuillez spécifier au moins 3 lettres/chiffres.', 'nexo' );?></p>
        </div>
    </div>
    <div class="col-md-12" ng-cloak>
        <div class="box box-widget widget-user-2" ng-if="item.length > 1">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header">
                <!-- /.widget-user-image -->
                <h3 class="text-center"><?php echo __( 'Quel produit souhaitez vous approvisionner ?', 'nexo' );?></h3>
            </div>
            <div class="box-footer no-padding">
                <ul class="nav nav-stacked">
                    <li ng-click="loadItem( _item, 'sku-barcode' )" ng-repeat="_item in item"><a href="#">{{ _item.DESIGN }}<span class="pull-right badge bg-default">{{ _item.QUANTITE_RESTANTE }}</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-12" ng-cloak ng-show="item.length == 1">
        
        <div class="row">
            <form name="form" novalidate>
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ item[0].DESIGN }}</h3>
                        </div>
                        <div class="box-body">
                            
                            <div class="form-group" ng-class="{ 'has-error' : ( ( form.item_qte.$error.required && form.$submitted ) || ( form.item_qte.$touched && form.item_qte.$error.required ) ) }">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{ selectedOperation.text }} 
                                        <span class="fa fa-caret-down"></span></button>
                                        <ul class="dropdown-menu">
                                            <li ng-repeat="action in actions" ng-click="selectAction( action )"><a href="javascript:void(0)">{{ action.text }}</a></li>
                                        </ul>
                                    </div>
                                    <!-- /btn-group -->
                                    <input type="text" name="item_qte" class="form-control" ng-model="fields.item_qte" integer required placeholder="<?php echo __( 'Quantité', 'nexo' );?>">
                                </div>
                                <p class="help-block" ng-show="form.$submitted || form.provider.$touched">
                                    <span ng-show="form.item_qte.$error.required"><?php echo __( 'Vous devez spécifier une quantité.', 'nexo' );?></span>
                                    <span style="color: red;display:block;" ng-show="selectedOperation.namespace == false"><?php echo __( 'Vous devez choisir le type de l\'opération', 'nexo' );?></span>
                                </p>
                            </div>

                            <div class="form-group" ng-if="selectedOperation.namespace == 'supply'" ng-class="{ 'has-error' : ( ( form.shipping.$error.required && form.$submitted ) || ( form.shipping.$touched && form.shipping.$error.required ) ) }">
                                <label><?php echo __( 'Livraison', 'nexo' );?></label>
                                <select required name="shipping" ng-options="item as item.TITRE for item in shippings track by item.ID" ng-model="fields.shipping" class="form-control"></select>
                                <p class="help-block" ng-show="form.$submitted || form.shipping.$touched">
                                    <span ng-show="form.shipping.$error.required"><?php echo __( 'Vous devez choisir une livraison', 'nexo' );?></span>
                                </p>
                            </div>
                            
                            <div class="form-group" ng-if="selectedOperation.namespace == 'supply'" ng-class="{ 'has-error' : ( ( form.provider.$error.required && form.$submitted ) || ( form.provider.$touched && form.provider.$error.required ) ) }">
                                <label><?php echo __( 'Choisir le fournisseur', 'nexo' );?></label>
                                <select required name="provider" ng-options="item as item.NOM for item in providers track by item.ID" ng-model="fields.provider" class="form-control"></select>
                                <p class="help-block" ng-show="form.$submitted || form.provider.$touched">
                                    <span ng-show="form.provider.$error.required"><?php echo __( 'Vous devez choisir un fournisseur', 'nexo' );?></span>
                                </p>
                            </div>

                            <div class="form-group" ng-if="selectedOperation.namespace == 'supply'" ng-class="{ 'has-error' : ( ( form.unit_price.$error.required && form.$submitted ) || ( form.unit_price.$touched && form.unit_price.$error.required ) ) }">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo __( 'Prix d\'achat unitaire', 'nexo' );?></span>
                                    <input valid-number required name="unit_price" ng-model="fields.unit_price" type="text" class="form-control" placeholder="<?php echo __( 'Définir un prix', 'nexo' );?>">
                                </div>
                                <p class="help-block" ng-show="form.$submitted || form.unit_price.$touched">
                                    <span ng-show="form.unit_price.$error.integer"><?php echo __( 'Vous devez définir une valeur numérique', 'nexo' );?></span>
                                    <span ng-show="form.unit_price.$error.required"><?php echo __( 'Vous devez définir une quantité', 'nexo' );?></span>
                                </p>
                            </div>

                            <div class="form-group" style="margin-bottom:0px">
                                <label><?php echo __( 'Détails', 'nexo' );?></label>
                                <textarea ng-model="fields.description" class="form-control" rows="3" placeholder="<?php echo __( 'Fournissez des détails à cette opération', 'nexo' );?>"></textarea>
                            </div>
                        </div>
                        <div class="box-footer">
                            <input ng-click="submitSupply()" type="submit" class="submitSupply btn btn-primary" value="<?php echo __( 'Valider l\'opération', 'nexo' );?>"/>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo __( 'Historique d\'approvisionnement', 'nexo' );?></h3>
                    </div>
                    <table class="box-body table table-striped">
                        <thead>
                            <tr>
                                <td class="text-left"><?php echo __( 'Auteur', 'nexo' );?></td>
                                <td class="text-right"><?php echo __( 'Quantité', 'nexo' );?></td>
                                <td class="text-right"><?php echo __( 'Type', 'nexo' );?></td>
                                <td class="text-center"><?php echo __( 'Date', 'nexo' );?></td>
                                <td class="text-right"></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="text-center" colspan="5" ng-show="history.length == 0"><?php echo __( 'Aucune historique pour ce produit', 'nexo' );?></td></tr>
                            <tr ng-repeat="entry in history" ng-class="{ 'success' : testType( entry ), 'warning' : testType( entry ) == false }">
                                <td class="text-left">{{ entry.author }}</td>
                                <td class="text-right">{{ testType( entry ) ? '+' : '-' }} {{ entry.quantity }}</td>
                                <td class="text-right">{{ convertType( entry.type ) }}</td>
                                <td class="text-right">{{ entry.date | date }}</td>
                                <td class="text-right">
                                    <a href="<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'produits', 'supply']);?>/{{ entry.codebar }}/edit/{{ entry.id }}" class="btn btn-sm btn-default">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><?php echo __( 'Quantité restante', 'nexo' );?></td>
                                <td class="text-right">{{ item[0].QUANTITE_RESTANTE }}</td>
                                <td></td>
                                <td></td>
                                <td class="text-right"><a type="button" class="btn btn-primary btn-sm" ng-href="<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'produits', 'supply' ] );?>/{{ item[0].CODEBAR }}"><i class="fa fa-eye"></i> <?php echo __( 'Plus de détails', 'nexo' );?></a></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>