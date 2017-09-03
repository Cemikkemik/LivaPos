<div class="row">
     <?php if( get_option( 'updater_validated', 'no' ) == 'no' ):?>
     <div class="col-md-6">
          <p><?php echo __( 'Thank you for choosing to use NexoPOS. 
          We hope it will help you manage your business better. 
          To take advantage of priority support and updates, we invite you to provide your purchase permit. 
          You will find this license in your purchases on CodeCanyon.', 'nexo-updater' );?></p>
          <form method="POST" ng-non-bindable>
               <div class="form-group">
                    <label for="licence-field"><?php echo __( 'Licence Code', 'nexo' );?></label>
                    <input class="form-control" name="licence-code">
                    <p class="help">
                    <?php echo sprintf( 
                         __( 'If you don\'t know where to get your purchase code, <a href="%s">read this.</a>', 'nexo-updater' ), 
                         'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-' 
                    );?></p>
               </div>
               <?php
               $csrf = array(
                    'name' => $this->security->get_csrf_token_name(),
                    'hash' => $this->security->get_csrf_hash()
               );
               ?>
               <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
               <button class="btn btn-primary" type="submit"><?php echo __( 'Save Licence Key', 'nexo' );?></button>
          </form>
     </div>
     <?php else:?>
          <div class="container-fluid">
               <?php echo tendoo_success( __( 'Your licence has been validated.', 'nexo' ) );?>
               <!-- <h4><?php echo __( 'Historique des mises à jour', 'nexo' );?></h4> -->
          </div>
     <?php endif;?>
</div>