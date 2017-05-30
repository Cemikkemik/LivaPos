<?php
    global $Options;
    $this->config->load( 'rest' )
?>
<form enctype="multipart/form-data" method="POST" ng-controller="importCSVController">
<?php echo tendoo_info( __( 'Vous avez la possibilité ici de personnaliser les différentes colonnes prise en charge durant l\'importation. Si vous ne souhaitez pas utiliser une colonne, vous pouvez la laisser vide.', 'nexo' ) . '<br>' . __( 'Chaque option de colonne ne peut être utilisé qu\'une seule fois.', 'nexo' ) );?>
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name();
?>" value="<?php echo $this->security->get_csrf_hash();
?>">
<?php
$columns                    =   array(
    'DESIGN'                =>  __( 'Nom du produit', 'nexo' ),
    'DESCRIPTION'           =>  __( 'Description du produit', 'nexo' ),
    'SKU'                   =>  __( 'Unite de Gestion de Stock', 'nexo' ),
    'CODEBAR'               =>  __( 'Code barre', 'nexo' ),
    'QUANTITY'              =>  __( 'Quantite', 'nexo' ),
    'PRIX_DE_VENTE'         =>  __( 'Prix de vente', 'nexo' ),
    'PRIX_DACHAT'           =>  __( 'Prix d\'achat', 'nexo' ),
    'REF_SHIPPING'          =>  __( 'Collection', 'nexo' ),
    'REF_CATEGORIE'         =>  __( 'Categorie', 'nexo' ),
    'REF_RAYON'             =>  __( 'Rayon', 'nexo' ),
    'TAILLE'                =>  __( 'Taille', 'nexo' ),
    'DESCRIPTION'           =>  __( 'Description', 'nexo' ),
);?>
<div class="row" ng-if="csvArray[0].length > 0">
    <div class="col-md-3" ng-repeat="( csvKey, csvVal ) in csvArray[0]">
        <div class="input-group">
            <span class="input-group-addon">{{ csvVal }} - {{ csvKey }}</span>
            <select type="text" class="form-control" ng-model="columns_data[ csvKey ]" placeholder="" ng-options="k as v for (k, v) in columns" ng-change="checkOptions( csvKey )">
                <option value=""><?php _e( 'Veuillez Choisir une option', 'nexo' );?>
            </select>
        </div>
    </div>
    <br>
</div>
<div class="row">
    <div class="col-md-6">
        <h3><?php _e( 'Valeurs Par défaut', 'nexo' );?></h3>
        <div class="checkbox">
          <label>
            <input type="checkbox" ng-model="enableForSale">
            <?php echo __( 'Tous les produits sont disponibles pour la vente par défaut', 'nexo' );?>
          </label>
        </div>
        <div class="checkbox">
          <label>
            <input type="checkbox" ng-model="enablePhysical">
            <?php echo __( 'Tous les produits sont "physique" par défaut', 'nexo' );?>
          </label>
        </div>
        <div class="checkbox">
          <label>
            <input type="checkbox" ng-model="enableStockManagement">
            <?php echo __( 'Tous les produits ont la gestion de stock activé par défaut', 'nexo' );?>
          </label>
        </div>
        <div class="checkbox">
          <label>
            <input type="checkbox" ng-model="remainingQteMatchesQte">
            <?php echo __( 'La quantité restante est égale à la quantité disponible par défaut', 'nexo' );?>
          </label>
        </div>

        <div class="checkbox">
          <label>
            <input type="checkbox" ng-model="addToCurrentItems">
            <?php echo __( 'Ajouter les fichiers importés au stock actuel.', 'nexo' );?>
          </label>
        </div>
        <div class="checkbox" ng-if="addToCurrentItems">
          <label>
            <input type="radio" ng-model="overwrite" name="treat_duplicate" value="false">
            <?php echo __( 'Ne pas écraser les produits avec le même SKU/Code barre.', 'nexo' );?>
          </label>
        </div>
        <div class="checkbox" ng-if="addToCurrentItems">
          <label>
            <input type="radio" ng-model="overwrite" name="treat_duplicate" value="true">
            <?php echo __( 'Mettre à jour les produits avec le même SKU/Code barre.', 'nexo' );?>
          </label>
        </div>
    </div>
    <div class="col-md-6">
        <h3><?php echo __( 'Fichier à importer', 'nexo' );?></h3>
        <fieldset class="form-group">
            <label for="exampleInputFile"><?php _e( 'Envoyer un fichier', 'nexo' );?></label>
            <input type="file" class="form-control-file" name="csv_file" file-reader="fileContent" file-extension="ext">
            <small class="text-muted"><?php _e( 'Veuillez choisir le fichier que vous souhaitez utiliser pour importer massivement les articles.', 'nexo' );?></small>
        </fieldset>
    </div>
</div>

<button type="submit" class="btn btn-primary" ng-class="{ 'disabled' : fileContent.length == 0 }" ng-click="submitCSV()"><?php echo __( 'Importer', 'nexo' );?></button>
<br>
<br>
<div ng-show="notices.length > 0">
    <p ng-repeat="notice in notices">{{ notice.msg }}</p>
</div>
</form>
