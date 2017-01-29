<!-- exact table from live demo -->
<div layout="column" ng-controller="anguCrud" ng-cloak style="min-height:{{ crudHeight }}px">
    <div style="height:150px;background:url(<?php echo module_url( 'angular_material' ) . 'images/mb-bg-fb-0' . rand(1,4) . '.jpg';?>)">
    </div>
    <div style="height:auto;" layout="row">
        <div  flex="90" flex-offset="5">
            <div class="md-whiteframe-1dp whiteframe-wrapper">
                <md-toolbar class="md-table-toolbar md-default" ng-hide="selected.length || filter.show">
                  <div class="md-toolbar-tools">
                    <h2 class="md-title"><?php echo sprintf( __( '%s &mdash; %s', 'angular_material' ), $AnguCrud->crudTitle, $AnguCrud->addNewLabel );?></h2>
                    <div flex></div>
                    <md-button href="<?php echo $AnguCrud->baseUrl;?>" aria-label="<?php echo $AnguCrud->showListLabel;?>"class="md-raised"><?php echo $AnguCrud->showListLabel;?></md-button>
                  </div>
                </md-toolbar>
                <md-divider></md-divider>
                <md-content layout-padding>
                    <form name="entriesForm" method="POST">
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
                <md-toolbar class="md-table-toolbar md-default" ng-hide="selected.length || filter.show">
                  <div class="md-toolbar-tools">
                    <div flex></div>
                    <md-button ng-click="submitEntry(entriesForm)" class="md-accent md-raised"><?php echo _s( 'Submit', 'angular_material' );?></md-button>
                  </div>
                </md-toolbar>
            </div>
        </div>
    </div>
</div>
<?php include_once( dirname( __FILE__ ) . '/general-css.php' );?>
