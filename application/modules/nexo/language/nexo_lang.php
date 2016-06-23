<?php
// Deprecated
$lang[ 'license-expired' ]            =    tendoo_error(__('Votre licence a expiré. Veuillez contacter l\'administrateur pour renouveller votre license.', 'nexo'));
$lang[ 'invalid-activation-key' ]    =    tendoo_error(__('La licence que vous avez spécifiée est invalide. La précédente licence a été restaurée. Veuillez contacter l\'administrateur pour corriger le problème.', 'nexo'));
$lang[ 'unable-to-connect' ]        =    tendoo_error(__('Impossible d\'établir une connexion fiable vers le serveur. Verifiez votre connexion internet. <br>Vous devez vous connecter pour valider l\'authenticité de cette licence.', 'nexo'));
$lang[ 'license-activated' ]        =    tendoo_success(__('Votre licence à été activée. Merci d\'avoir renouvellé votre abonnement.', 'nexo'));
$lang[ 'license-has-expired' ]        =    tendoo_error(__('Cette licence n\'est plus valide. Veuillez fournir une licence valide.', 'nexo'));

// Quote Lang lines
$lang[ 'deleted-quotes-msg' ]        =    __('Les commandes suivantes ont été supprimées automatiquement pour expiration : %s Les produits ont été restauré dans la boutique.', 'nexo');
$lang[ 'deleted-quotes-title' ]        =    __('Suppression automatique des commandes devis', 'nexo');
$lang[ 'cant-delete-used-item' ]        =    __('Vous ne pouvez pas supprimer cet élément, car il est en cours d\'utilisation.', 'nexo');
$lang[ 'permission-denied' ]        =    __('Vous n\'avez pas l\'autorisation nécessaire pour effectuer cette action.', 'nexo');
$lang[ 'default-customer-required' ]    =    tendoo_warning(__('Vous devez définir un compte client par défaut, avant d\'effectuer une vente.', 'nexo'));
$lang[ 'order_edit_not_allowed' ]    =    tendoo_error(__('Vous ne pouvez pas modifier une commande complète ou ayant reçu une avance. Pour les commandes ayant reçu une avance, vous pouvez les compléter pour terminer le paiement.', 'nexo'));
$lang[ 'order_not_found' ]            =    tendoo_error(__('Cette commande est introuvable', 'nexo'));
$lang[ 'order_proceeded' ]            =    tendoo_success(__('La commande à été correctement été complétée', 'nexo'));
$lang[ 'nexo_order_complete' ]        =    __('Complète', 'nexo');
$lang[ 'nexo_order_advance' ]        =    __('Incomplète', 'nexo');
$lang[ 'nexo_order_quote' ]            =    __('Devis', 'nexo');
$lang[ 'disabled' ]                    =    __('Désactivée', 'nexo');
$lang[ 'nexo_flat_discount' ]        =    __('Remise à prix fixe', 'nexo');
$lang[ 'nexo_percentage_discount' ]    =    __('Remise au pourcentage', 'nexo');
$lang[ 'yes' ]                        =    __('Oui', 'nexo');
$lang[ 'no' ]                        =    __('Non', 'nexo');
$lang[ 'cash' ]                        =    __('Paiement en espèces', 'nexo');
$lang[ 'cheque' ]                    =    __('Chèque', 'nexo');
$lang[ 'bank_transfert' ]            =    __('Transfert Bancaire', 'nexo');
$lang[ 'only_cash_order_can_be_printed' ]    =    tendoo_error(__('Seules les commandes complètes peuvent être imprimées', 'nexo'));
$lang[ 'stripe']      = __('Stripe', 'nexo');
