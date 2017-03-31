<!-- exact table from live demo -->
<div layout="row" ng-controller="anguCrud" ng-cloak style="min-height:{{ crudHeight }}px">
    <div layout="row">
      <div flex>First item in row</div>
      <div flex>Second item in row</div>
    </div>
    <div layout="column">
      <div flex>First item in column</div>
      <div flex>Second item in column</div>
    </div>
</div>
<?php return;?>
<div layout="row" ng-controller="anguCrud" ng-cloak style="min-height:{{ crudHeight }}px">
    <div layout="column" flex="{{ mainContainerWidth }}">
        <div style="height:150px;background:url(<?php echo module_url( 'angular_material' ) . 'images/mb-bg-fb-0' . rand(1,4) . '.jpg';?>)">
        </div>
        <div style="height:auto;" layout="row">
            <div ng-init="getEntries()" flex="90" flex-offset="5">
                <div class="md-whiteframe-1dp whiteframe-wrapper" >
                    <md-toolbar class="md-table-toolbar md-default" ng-hide="selected.length || filter.show">
                      <div class="md-toolbar-tools">
                        <h2 class="md-title"><?php echo $AnguCrud->crudTitle;?></h2>
                        <div flex></div>
                        <md-button aria-label="<?php echo __( 'Search', 'angular_material' );?>" class="md-icon-button" ng-click="filter.show = true">
                          <i class="fa fa-search"></i>
                        </md-button>
                        <?php foreach( $AnguCrud->getDefaultButtons() as $button ):?>
                            <md-button href="<?php echo $button[ 'url' ];?>" aria-label="<?php echo $button[ 'text' ];?>"class="md-raised"><?php echo $button[ 'text' ];?></md-button>
                        <?php endforeach;?>
                        <md-button href="<?php echo $AnguCrud->baseUrl;?>/add_new" aria-label="<?php echo $AnguCrud->addNewLabel;?>"class="md-raised"><?php echo $AnguCrud->addNewLabel;?></md-button>
                        <md-button ng-json-export-excel data="entries" report-fields="exportFields" filename =" 'export-excel' " separator="," aria-label="<?php echo $AnguCrud->exportToCSV;?>"class="md-raised css-class"><i class="fa fa-archive"></i> <?php echo $AnguCrud->exportToCSV;?></md-button><!-- href="<?php echo $AnguCrud->baseUrl;?>/export_xls" -->
                        <md-button class="md-raised md-icon-button" aria-label="<?php echo __( 'Options', 'angular_material' );?>" ng-click="toggleOptions()">
                          <i class="fa fa-cogs"></i>
                        </md-button>
                      </div>
                    </md-toolbar>
                    <md-toolbar class="md-table-toolbar md-default" ng-show="filter.show && !selected.length">
                      <div class="md-toolbar-tools">
                        <i class="fa fa-search"></i>
                        <form flex name="filter.form">
                            <md-input-container md-no-float class="md-block" style="margin-top: 50px;margin-left: 20px;">
                              <input ng-model="query.filter" focus-on="filter.show" ng-model-options="{ debounce : 800 }" placeholder="<?php echo $AnguCrud->searchLabel;?>">
                            </md-input-container>
                        </form>
                        <md-button aria-label="<?php echo __( 'Close', 'angular_material' );?>" class="md-fab md-default md-mini" ng-click="closeSearch()">
                          <i class="fa fa-remove" style="font-size:15px;"></i>
                        </md-button>
                      </div>
                    </md-toolbar>
                    <md-toolbar class="md-table-toolbar alternate" ng-show="selected.length > 0">
                      <div class="md-toolbar-tools" layout-align="space-between center" layout="row">
                        <div>{{selected.length}} {{ selected.length > 1 ? '<?php echo $AnguCrud->itemsLabel;?>' : '<?php echo $AnguCrud->itemLabel;?>'}} selected</div>
                        <div flex></div>
                        <?php foreach( $AnguCrud->getSelectingButtons() as $button ):?>
                            <md-button <?php echo @$button[ 'allow_multiple' ] == true ? 'ng-show="selected.length >= 1"' : '';?> <?php echo @$button[ 'only_multiple' ] == true ? 'ng-show="selected.length > 1"' : '';?> <?php echo @$button[ 'only_unique' ] == true ? 'ng-show="selected.length == 1"' : '';?> aria-label="<?php echo __( 'Edit', 'angular_material' );?>" class="md-icon-button" ng-click="goToSelected( '<?php echo @$button[ 'url' ];?>' )">
                              <i class="fa fa-<?php echo @$button[ 'icon' ];?>"></i>
                            </md-button>
                        <?php endforeach;?>
                        <md-button ng-show="selected.length == 1" aria-label="<?php echo __( 'Edit', 'angular_material' );?>" class="md-icon-button" ng-click="editSelected( $event )">
                          <i class="fa fa-pencil"></i>
                        </md-button>
                        <md-button aria-label="<?php echo __( 'Delete', 'angular_material' );?>" class="md-icon-button" ng-click="deleteSelected( $event )">
                          <i class="fa fa-trash-o"></i>
                        </md-button>
                        <md-button aria-label="<?php echo __( 'Close', 'angular_material' );?>" class="md-icon-button" ng-click="closeSelected()">
                          <i class="fa fa-remove"></i>
                        </md-button>
                      </div>
                    </md-toolbar>
                    <md-divider></md-divider>
                    <md-content layout-fill>
                        <md-table-container>
                          <table md-table md-row-select multiple id="entries_table" ng-model="selected" md-progress="promise">
                            <thead md-head md-order="query.order" md-on-reorder="getEntries">
                              <tr md-row>
                                  <?php foreach( ( Array ) $AnguCrud->getColumns() as $key => $title ):?>
                                      <?php if( $key == '$AnguCrudActions' ):?>
                                          <th md-column><?php echo $title;?></th>
                                      <?php else:?>
                                          <th md-column ng-hide="hideColumn[ '<?php echo $key;?>' ].hide" md-order-by="<?php echo $key;?>"><span><?php echo $title;?></span></th>
                                      <?php endif;?>
                                  <?php endforeach;?>
                              </tr>
                            </thead>
                            <tbody md-body>
                              <tr md-row md-select="entry" md-select-id="name" md-auto-select ng-repeat="entry in entries">
                              <?php foreach( ( Array ) $AnguCrud->getColumns() as $key => $title ):?>
                                  <?php if( $key == '$AnguCrudActions' ):?>
                                      <td md-cell>
                                        <md-menu md-offset="0 -5">
                                            <md-button aria-label="<?php echo __( 'Options', 'angular_material' );?>" class="md-icon-button" ng-click="openMenu($mdOpenMenu, $event)">
                                                <i class="fa fa-bars"></i>
                                            </md-button>
                                            <md-menu-content width="4">
                                                <md-menu-item ng-repeat="(k,v) in menuActions">
                                                    <md-button aria-label="<?php echo __( 'Option', 'angular_material' );?>" ng-click="triggerAction(k,entry.<?php echo $AnguCrud->getPrimaryCol();?>,$event)"><i class="{{ v.icon }}"></i> <span md-menu-align-target>{{ v.label }}</span></md-button>
                                                </md-menu-item>
                                            </md-menu-content>
                                        </md-menu>
                                      </td>
                                  <?php else:?>
                                      <td md-cell ng-hide="hideColumn[ '<?php echo $key;?>' ].hide" class="text-center">{{entry.<?php echo $key;?>}}</td>
                                  <?php endif;?>
                              <?php endforeach;?>
                              </tr>
                              <tr class="hide-on-export" md-row md-auto-select ng-show="entries.length == 0">
                                  <td md-cell colspan="<?php echo count( $AnguCrud->getColumns() ) + 1;?>">
                                      <?php echo __( 'No entry available', 'angular_material' );?>
                                  </td>
                              </tr>
                            </tbody>
                          </table>
                        </md-table-container>
                    </md-content>
                    <md-toolbar class="md-table-toolbar md-default">
                        <md-table-pagination md-label="{ of : '<?php echo $AnguCrud->ofLabel;?>', page : '<?php echo $AnguCrud->pageLabel;?>', rowsPerPage : '<?php echo $AnguCrud->rowsPerPageLabel;?>' }" md-limit="query.limit" md-limit-options="<?php echo json_encode( ( Array ) @$AnguCrud->limit );?>" md-page="query.page" md-total="{{ totalEntries }}" md-on-paginate="getEntries" md-page-select></md-table-pagination>
                    </md-toolbar>
                </div>
                <style media="screen">
                    md-toolbar .label {
                        color : #333;
                    }
                    body {
                        /** top: 0px !important; **/
                    }
                </style>
            </div>
        </div>
    </div>
    <div layout="column" class="option-sidebar md-whiteframe-1dp" flex="{{ sidePanelWidth }}">
        <md-toolbar flex="5">

            <div class="md-toolbar-tools">
                <?php echo __( 'Options', 'angular_material' );?>
            </div>

        </md-toolbar>
        <md-content flex="95">
            <!-- ng-checked="sidebarOptionCheck(item, selected)" -->
            <div flex="100" >
                <md-list-item ng-repeat="column in hideColumn">
                    <p> {{ column.title }} </p>
                    <md-checkbox class="md-secondary" ng-model="column.hide"></md-checkbox>
                </md-list-item>
            </div>
        </md-content>
    </div>
</div>
<?php include_once( dirname( __FILE__ ) . '/general-css.php' );?>
