<div class="container-fluid">
     <div class="row">
          <div class="col-md-6"></div>
          <div class="col-md-6"></div>
     </div>
     <div class="row">
          <div class="col-lg-8 col-lg-offset-2">
               <div class="box">
                    <div class="box-body no-padding">
                         <table class="table table-bordered">
                              <thead>
                                   <tr>
                                        <td><?php echo __( 'Nom', 'nexo' );?></td>
                                        <td><?php echo __( 'Coût', 'nexo' );?></td>
                                        <td><?php echo __( 'Date', 'nexo' );?></td>
                                        <td><?php echo __( 'Par', 'nexo' );?></td>
                                   </tr>
                              </thead>
                              <tbody>
                                   <?php foreach( $results as $result ):?>
                                   <?php
                                   switch( $result[ 'TYPE' ] ) {
                                        case 'payment' : 
                                             $class = 'success'; 
                                             $sign     =    'plus';
                                        break;
                                        case 'stock_purchase' : 
                                             $class = 'danger'; 
                                             $sign     =    'minus';
                                        break;
                                        default: 
                                             $class    =    '';
                                             $sign     =    '';
                                        break;
                                   }
                                   ?>
                                   <tr class="<?php echo $class;?>">
                                        <td><?php echo $operation[ $result[ 'TYPE' ] ];?></td>
                                        <td class="text-right"><i class="fa fa-<?php echo $sign;?>"></i> <?php echo $this->Nexo_Misc->cmoney_format( $result[ 'AMOUNT' ] );?></td>
                                        <td class="text-right"><?php echo $result[ 'DATE_CREATION' ];?></td>
                                        <td class="text-right"><?php echo ucwords( $result[ 'name' ] );?></td>
                                   </tr>
                                   <?php endforeach;?>
                                   <tr>
                                        <td><?php echo __( 'Solde du compte', 'nexo' );?></td>
                                        <td class="text-right"><?php echo $this->Nexo_Misc->cmoney_format( $provider[ 'PAYABLE' ] );?></td>
                                        <td class="text-right"></td>
                                        <td></td>
                                   </tr>
                              </tbody>
                         </table>
                         
                    </div>
               </div>
               <?php echo $pagination;?>
          </div>
     </div>
</div>