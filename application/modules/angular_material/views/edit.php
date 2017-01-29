<?php $uri = $this->uri->segment_array(); ?>
<div layout="column" ng-controller="anguCrud" ng-cloak ng-init="loadEntry(<?php echo end( $uri );?>)" style="min-height:{{ crudHeight }}px">
    <div style="height:150px;background:url(<?php echo module_url( 'angular_material' ) . 'images/mb-bg-fb-0' . rand(1,4) . '.jpg';?>)">
    </div>
    <div style="height:auto;" layout="row">
        <div class="md-whiteframe-1dp whiteframe-wrapper" flex="90" flex-offset="5">
            <md-toolbar class="md-table-toolbar md-default" ng-hide="selected.length || filter.show">
              <div class="md-toolbar-tools">
                <h2 class="md-title"><?php echo sprintf( __( '%s &mdash; %s', 'angular_material' ), $AnguCrud->crudTitle, $AnguCrud->editLabel );?></h2>
                <div flex></div>
                <md-button href="<?php echo $AnguCrud->baseUrl;?>" aria-label="<?php echo $AnguCrud->showListLabel;?>"class="md-raised"><?php echo $AnguCrud->showListLabel;?></md-button>
                <md-button href="<?php echo $AnguCrud->baseUrl;?>/add_new" aria-label="<?php echo $AnguCrud->addNewLabel;?>"class="md-raised"><?php echo $AnguCrud->addNewLabel;?></md-button>
              </div>
            </md-toolbar>
            <md-divider></md-divider>
            <md-content layout-padding layout="row" layout-align="center center" ng-show="entryStatus" ng-hide="entryStatus == false">
                <md-progress-circular class="md-accent" md-diameter="100px"></md-progress-circular>
            </md-content>
            <md-content layout-padding ng-hide="true" ng-hide="entryStatus" ng-show="entryStatus ==  false">
                <form name="entriesForm">
                    <?php foreach( ( Array ) $AnguCrud->getColumns() as $key => $title ):?>
                        <?php if( ! in_array( $key, $AnguCrud->getShowOnListOnly() ) ):?>
                            <?php if( ! in_array( $key, array_keys( $AnguCrud->fieldsType ) ) ):?>
                                <?php include( dirname( __FILE__ ) . '/default-field.php' );?>
                            <?php else:?>
                                <?php include( dirname( __FILE__ ) . '/custom-fields.php' );?>
                            <?php endif;?>
                        <?php endif;?>
                    <?php endforeach;?>
                </form>
            </md-content>
            <md-divider></md-divider>
            <md-toolbar class="md-table-toolbar md-default">
              <div class="md-toolbar-tools">
                <div flex></div>
                <md-button ng-click="updateEntry(<?php echo end( $uri );?>)" class="md-accent md-raised"><?php echo _s( 'Update', 'angular_material' );?></md-button>
              </div>
            </md-toolbar>
        </div>
    </div>
</div>
<?php include_once( dirname( __FILE__ ) . '/general-css.php' );?>
