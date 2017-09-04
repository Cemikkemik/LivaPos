<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

    $column_width = (int)(80/count($columns));

if (!empty($list)) : ?>

    <div class="box-body no-padding table-responsive">
        <table cellspacing="0" cellpadding="0" border="0" id="flex1" class="table table-striped">
            <thead>
                <tr>
                    <?php foreach ($columns as $column): ?>
                    <th>
                        <div class="text-left field-sorting <?php if (isset($order_by[0]) &&  $column->field_name == $order_by[0]) { ?><?php echo $order_by[1]?><?php } ?>"
                            rel='<?php echo $column->field_name?>'>
                            <?php echo $column->display_as?>
                        </div>
                    </th>
                    <?php endforeach; ?>
                    <?php if (!$unset_delete || !$unset_edit || !empty($actions)): ?>
                    <th align="left" abbr="tools" axis="col1" class="" width='20%'>
                        <div class="text-right">
                            <?php echo $this->l('list_actions');?> </div>
                    </th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $num_row => $row) : ?>
                    <?php
                    $item_class         =   get_instance()->events->apply_filters('grocery_crud_list_item_class', 'default', $row);
                    $row                =   get_instance()->events->apply_filters( 'grocery_filter_row', $row );
                    $temp_string        =   $row->delete_url;
                    $temp_string        =   explode("/", $temp_string);
                    $row_num            =   sizeof($temp_string)-1;
                    $rowID              =   $temp_string[$row_num]; 
                    ?>
                    <tr class="<?php echo ($num_row % 2 == 1) ? 'erow' : null;?> <?php echo $item_class;?>" id="custom_tr_<?php echo $rowID?>">
                        <?php foreach ($columns as $column):?>
                            <td class="<?php echo (isset($order_by[0]) &&  $column->field_name == $order_by[0]) ? 'sorted' : null ?>">
                                <div style="width: 100%;" class='text-left'>
                                    <?php echo $row->{$column->field_name};?>
                                </div>
                            </td>
                        <?php endforeach;?>
                        
                        <?php if (!$unset_delete || !$unset_edit || !empty($actions)):?>
                            <td align="left" width='20%'>
                                <div class='tools'>
                                    <?php if (!$unset_delete) { 
                                        ob_start();?>
                                        <a href='<?php echo $row->delete_url?>' title='<?php echo $this->l('list_delete')?> <?php echo $subject?>' class="delete-row"> <span class='fa fa-remove btn btn-danger'></span> </a>
                                        <?php echo get_instance()->events->apply_filters('grocery_filter_delete_button', ob_get_clean(), $row, $this->l('list_delete'), $subject);
                                    }

                                    if (!$unset_edit) {
                                        ob_start();
                                        ?>
                                        <a href='<?php echo $row->edit_url?>' title='<?php echo  @$this->l('list_edit')?> <?php echo $subject?>'> <span class='edit-icon fa fa-edit btn-default btn'></span> </a>
                                        <?php
                                        echo get_instance()->events->apply_filters('grocery_filter_edit_button', ob_get_clean(), $row, @$this->l('list_edit'), $subject);
                                    }

                                    if (!empty($row->action_urls)) {
                                        $data               =    get_instance()->events->apply_filters('grocery_filter_actions', [ $row->action_urls, $actions, $row ]);
                                        $row->action_urls   =   $data[0];
                                        $actions            =   $data[1];
                                        $row                =   $data[2];

                                        foreach ($row->action_urls as $action_unique_id => $action_url) {
                                            $action        = $actions[$action_unique_id];?>
                                            <a href="<?php echo $action_url;?>" data-item-id="<?php echo $row->ID;?>" class="<?php echo $action->css_class;?> crud-action" title="<?php echo $action->label?>">
                                            <?php
                                                echo @$action->text;
                                                if (!empty($action->image_url)):?>
                                                                                    <img src="<?php echo $action->image_url;
                                                ?>" alt="<?php echo $action->label?>" />
                                                <?php endif; ?>
                                            </a>
                                            <?php
                                        }
                                    }

                                    echo get_instance()->events->apply_filters('grocery_row_actions_output', '', $row, $this->l('list_edit'), $subject);?>
                                    <div class='clear'></div>
                                </div>
                            </td>
                        <?php endif;?>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
<?php else :?>
    <br/> 
    <?php echo $this->l('list_no_items');?> <br/>
    <br/>
<?php endif;?>