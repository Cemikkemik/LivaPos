<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

    $column_width = (int)(80/count($columns));

if (!empty($list)) : ?>

    <table cellspacing="0" cellpadding="0" border="0" id="flex1" class="table table-hovered">
        <thead>
            <tr class="active">
                <td style="width:20px">
                    <input type="checkbox" class="select_all">
                </td>
                <?php foreach ($columns as $column): ?>
                <th>
                    <div class="text-left field-sorting <?php if (isset($order_by[0]) &&  $column->field_name == $order_by[0]) { ?><?php echo $order_by[1]?><?php } ?>"
                        rel='<?php echo $column->field_name?>'>
                        <?php echo $column->display_as?>
                    </div>
                </th>
                <?php endforeach; ?>
                <?php if (!$unset_delete || !$unset_edit || !empty($actions)): ?>
                <th align="left" abbr="tools" axis="col1" class="" width='10%'>
                    <div class="text-right">
                        <?php echo $this->l('list_actions');?> 
                    </div>
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
                    <td style="width:20px">
                        <input type="checkbox" class="single_entry" name="entries[]" value="<?php echo $rowID;?>">
                    </td>
                    <?php foreach ($columns as $column):?>
                        <td class="<?php echo (isset($order_by[0]) &&  $column->field_name == $order_by[0]) ? 'sorted' : null ?>">
                            <div style="width: 100%;" class='text-left'>
                                <?php echo $row->{$column->field_name};?>
                            </div>
                        </td>
                    <?php endforeach;?>
                    
                    <?php if (!$unset_delete || !$unset_edit || !empty($actions)):?>
                        <td align="left" width='10%'>
                            <div class="dropdown">
                                <a class="btn btn-default dropdown-toggle btn-sm" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <?php echo get_instance()->config->item( 'options' );?>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                <?php if (!$unset_delete) { 
                                    ob_start();?><a href='<?php echo $row->delete_url?>' class="delete-row fa fa-remove"> <?php echo $this->l('list_delete')?> <?php echo $subject?></a><?php 
                                    echo '<li>' . get_instance()->events->apply_filters('grocery_filter_delete_button', ob_get_clean(), $row, $this->l('list_delete'), $subject) . '</li>';
                                }

                                if (!$unset_edit) {
                                    ob_start();
                                    ?>
                                    <a href='<?php echo $row->edit_url?>' class="fa fa-edit"> <?php echo $this->l('list_edit')?> <?php echo $subject?></a>
                                    <?php
                                    echo '<li>' . get_instance()->events->apply_filters('grocery_filter_edit_button', ob_get_clean(), $row, $this->l('list_edit'), $subject) . '</li>';
                                }

                                if (!empty($row->action_urls)) {
                                    $data               =    get_instance()->events->apply_filters('grocery_filter_actions', [ $row->action_urls, $actions, $row ]);
                                    $row->action_urls   =   $data[0];
                                    $actions            =   $data[1];
                                    $row                =   $data[2];

                                    foreach ($row->action_urls as $action_unique_id => $action_url) {
                                        $action        = $actions[$action_unique_id];?>
                                    <li>
                                        <a 
                                        href="<?php echo $action_url;?>" 
                                        data-item-id="<?php echo $row->ID;?>" 
                                        class="<?php echo $action->css_class;?> crud-action" >
                                            <?php echo $action->label?>
                                        </a>
                                    </li>
                                <?php
                                    }
                                }
                                echo get_instance()->events->apply_filters('grocery_row_actions_output', '', $row, $this->l('list_edit'), $subject);
                                ?>
                                </ul>
                            </div>
                        </td>
                    <?php endif;?>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
<?php else :?>
    <div class="box-body text-center">
        <h3 style="margin: 5px 0"><?php echo $this->l('list_no_items');?></h3>
    </div>
<?php endif;?>

<script>
$( document ).ready( function(){
    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });
    $( '.select_all' ).on( 'ifChecked', function() {
        $( '.single_entry' ).each( function() {
            $( this ).iCheck( 'check' );
        });
    });
    $( '.select_all' ).on( 'ifUnchecked', function() {
        $( '.single_entry' ).each( function() {
            $( this ).iCheck( 'uncheck' );
        });
    });

    $( '.delete_selected' ).bind( 'click', function(){
        let countSelected   =  $( '.single_entry:checked' ).length;
        
        if( countSelected == 0 ) {
            return alert( '<?php echo _s( 'You must selected at least one item');?>' );
        }

        let selectedEntries     =   [];
        $( '.single_entry:checked' ).each(function(){
            selectedEntries.push( $( this ).val() );
        });

        if( confirm( '<?php echo _s( 'Would you like to delete selected entries' );?>' ) ) {
            $.ajax({
                url     :   bulk_delete_url,
                data    :   Object.assign({},{
                    entries     :   selectedEntries,
                }, tendoo.csrf_data ),
                method      :   'POST',
                beforeSend  :   function(){

                },
                success     :   function( data ) {
                    $.notify({
                        title       :   '<?php echo _s( 'Successful' );?>',
                        message     :   '<?php echo _s( 'All entries has been deleted' );?>'
                    });
                    $( '#ajax_refresh_and_loading' ).trigger( 'click' );
                }
            });
        }        
    });
});
</script>