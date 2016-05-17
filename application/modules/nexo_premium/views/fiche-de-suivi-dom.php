<?php
// Creating tabs
$Tabs                    =    array(
    'stock_par_category'    =>    array(
        'file_content'        =>    $this->load->module_view('nexo_premium', 'fiche-de-suivi/by-categories', array(), true),
        'title'                =>    __('Par catégorie', 'nexo_premium')
    )
);
?>
<div class="nav-tabs-custom">
  <ul class="nav nav-tabs">
  	<?php
    $_i        =    0;
    foreach ($Tabs as $namespace => $tab) {
        ?>
		<li <?php echo $_i == 0 ? 'class="active"': null;
        ?>>
        	<a href="#<?php echo $namespace;
        ?>" data-toggle="tab" aria-expanded="true"><?php echo $tab[ 'title' ];
        ?></a>
		</li>
        <?php
        $_i++;
    }
    ?>
  </ul>
  <div class="tab-content">
	<?php 
    $_i        =    0;
    foreach ($Tabs as $namespace => $tab):
    ?>
    <div class="tab-pane <?php echo $_i == 0 ? 'active': null;?>" id="<?php echo $namespace;?>">
    	<?php echo $tab[ 'file_content' ];?>
    </div>
    <?php 
    $_i++;
    endforeach;
    ?>
    <!-- /.tab-pane --> 
  </div>
  <!-- /.tab-content --> 
</div>