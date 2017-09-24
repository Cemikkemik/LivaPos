<div class="row">
     <div class="col-md-6">
          <form action="" method="post">
               <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
               <div class="form-group <?php echo form_error( 'amount' ) != '' ? 'has-error': '';?>">
                    <div class="input-group">
                         <div class="input-group-addon"><?php echo __( 'Somme à verser', 'nexo_premium' );?></div>
                         <input value="<?php echo set_value( 'amount' );?>" name="amount" type="text" class="form-control" id="exampleInputAmount" placeholder="Amount">
                    </div>
                    <?php if( form_error( 'amount' ) != '' ):?>
                         <p class="help-block"><?php echo form_error( 'amount' );?></p>
                    <?php else:?>
                         <p class="help-block"><?php echo __( 'Définir la somme qui sera versé pour le compte du fournisseur.', 'nexo_premium' );?></p>
                    <?php endif;?>
               </div>

               <div class="form-group">
                    <textarea name="description" id="" cols="30" rows="10" class="form-control"><?php echo set_value( 'description' );?></textarea>
                    <p class="help-block"><?php echo __( 'Fournir des détails sur l\'opération', 'nexo_premium' );?></p>
               </div>

               <input type="submit" value="<?php echo __( 'Enregistrer le paiement', 'nexo_premium' );?>" class="btn btn-primary">         
               <a href="<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'fournisseurs', 'list']);?>" class="btn btn-default"><?php echo __( 'Annuler', 'nexo_premium' );?></a>
          </form>
     </div>
</div>