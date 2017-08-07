<?php
$columns    =   $awesome_crud->getColumns();

?>
<div class="box" ng-controller="awesomeCrudController">
    <div class="box-header with-border">
        <div class="row">
            <div class="col-md-7">
                <h3 class="box-title">
                    <?php echo $awesome_crud->get( 'name' );?>
                </h3>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button"><?php echo __( 'Search', 'awesome-crud' );?></button>
                    </span>
                    <input type="text" class="form-control" placeholder="<?php echo __( 'Search', 'awesome-crud' );?>" aria-describedby="basic-addon1">
                </div>
            </div>
            <div class="div col-md-2">
                <div class="btn-group btn-group-sm pull-right btn-group-justified" role="group" aria-label="...">
                    <a type="button" class="btn btn-default" ng-href="<?php echo $awesome_crud->get( 'baseUrl' ) . '/' . $awesome_crud->get( 'addSlug' );?>"><i class="fa fa-plus"></i> <?php echo __( 'Add', 'awesome-crud' );?></a>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-default"><i class="fa fa-print"></i> <?php echo __( 'Print', 'awesome-crud' );?></button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pull-right">
            
            
        </div>
    </div>
    <div class="box-body table-responsive no-padding">
        <table class="table table-striped">
            <thead>
                <tr class="">
                    <td ng-show="showCheckboxes" width="5">
                        <i-check ng-model="checkboxToggle"></i-check>
                    </td>
        <?php
        $visibleColumn  =   0;
        foreach( $columns as $namespace => $column ) {
            $config     =   $awesome_crud->getConfig( $namespace );
            
            // skip if hidden
            if( @$config[ 'hideColumn' ] === true ) {
                break;
            }

            $visibleColumn++;
            $width      =   @$config[ 'width' ] ? 'width:' . $config[ 'width' ] . ';' : '';
            ?>
            <td ng-click="sortColumn( '<?php echo $namespace;?>' )" style="<?php echo $width;?>"><i class="pull-left fa fa-sort" style="margin-top: 3px;"></i><strong><?php echo $column;?></strong></td>
            <?php
        }
        ?>
                    <td ng-show="showActions">
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr ng-show="entries.length == 0">
                    <td class="text-center" colspan="{{ <?php echo $visibleColumn;?> + ( showActions ? 1 : 0 ) + ( showCheckboxes ? 1 : 0 ) }}">
                        <span ng-show="loadStatus == 'loading'">
                            <?php echo __( 'Loading', 'nexo' );?>
                        </span> 
                        <span ng-show="loadStatus == 'finished'"><?php echo __( 'Nothing to show', 'nexo' );?></span>
                    </td>
                </tr>
                <tr ng-repeat="entry in entries track by entry.<?php echo $awesome_crud->get( 'primaryKey' );?>">
                    <td ng-show="showCheckboxes" width="5">
                        <i-check ng-model="entriesList[ entry.<?php echo $awesome_crud->get( 'primaryKey' );?> ]" ng-click="toggleCheckBoxes()"></i-check>
                    </td>
                    <?php
                    foreach( $columns as $namespace => $column ) {
                        $filters        =   '';
                        $config         =   $awesome_crud->get( 'config' );

                        // skip if hidden
                        if( @$config[ 'hideColumn' ] === true ) {
                            break;
                        }
                        
                        $config         =   @$config[ $namespace ];
                        // if filters are supported
                        if( @$config[ 'filters' ] != null ) {

                            $filters    =   explode( ',', @$config[ 'filters' ] );
                            foreach( $filters as $index     =>  &$filter ) {
                                $filter     =   ' | acFilter: "' . $filter . '"';                           
                            }
                            $filters    =   implode( $filters );
                        }
                        
                        ?>
                        <td>{{ entry[ '<?php echo $namespace;?>' ] <?php echo $filters;?> }}</td>
                        <?php
                    }
                    ?>
                    <td ng-show="showActions" width="100">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Action
                            <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu" style="left: -99px;">
                                <li ng-click="action.callback( entry )" ng-repeat="action in actions"><a href="#">{{ action.name }}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="box-footer">
        <ul class="pagination pagination-sm no-margin pull-right">
            <li><a href="#">«</a></li>
            <li><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">»</a></li>
        </ul>
    </div>
    <style>
    div.checkbox{
        margin:0;
    }
    </style>
</div>