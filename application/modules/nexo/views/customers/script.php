<?php
global $Options, $PageNow;
$this->load->config( 'rest' );
?>
<script>
let customersMain    =   function( 
    $scope, 
    $http,
    $compile,
    $rootScope
) {

    tendoo.loader.show();
    $scope.item                     =   new Object;
    $scope.rawCustomers             =   <?php echo json_encode( ( array ) $clients );?>;
    $scope.pageNow                  =   '<?php echo $PageNow;?>';

    $scope.tabs         =   [{
        name        :   '<?php echo _s( 'Basic informations', 'nexo' );?>',
        namespace   :   'basic'
    },{
        name        :   '<?php echo _s( 'Billing Informations', 'nexo' );?>',
        namespace   :   'billing'
    },{
        name        :   '<?php echo _s( 'Informations de livraison', 'nexo' );?>',
        namespace   :   'shipping'
    }];

    $scope.model        =   new Object;

    /**
     * enable tab
     * @param object current tab
     * @return void
    **/
    
    $scope.enableTab = function( tab ){
        _.each( $scope.tabs, ( _tab ) => {
            _tab.active     =   false;
        });
        
        tab.active  =   true;
    }

    // enable first tab
    $scope.enableTab( $scope.tabs[0] );

    
    $scope.submitForm           =   function() {

        if( $scope.form.$valid ) {
            let data    =   new Object;
            _.each( $scope.form, ( value, key ) => {
                if( key.substr( 0, 1 ) != '$' ) {
                    data[ key ]     =   ( typeof value.$modelValue == 'undefined' ) ? '' : value.$modelValue;
                }
            });

            if( _.keys( $scope.rawCustomers ).length > 0 ) {
                data.edited_on                  =   tendoo.now();
                data.author                     =   <?php echo User::id();?>;
                tendoo.loader.show();

                $http.put( '<?php echo site_url([ 'rest', 'nexo', 'customers', $client_id, store_get_param( '?' )]);?>', data, {
                    headers	:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                    }
                }).then( function( returned ){
                    $scope.model[ 'basic' ]         =   new Object;
                    $scope.model[ 'billing' ]       =   new Object;
                    $scope.model[ 'shipping' ]      =   new Object;  

                    // if we're creating a user from the POS screen
                    if( $scope.pageNow == 'nexo/registers/__use' ) {
                        // refresh customers
                        $scope.getCustomers();
                        $( '[data-bb-handler="ok"]' ).trigger( 'click' );
                    } else {
                        document.location   =   '<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'clients', 'lists', 'success', $client_id ]);?>';
                        tendoo.loader.hide();
                    }                
                }, ( returned ) => {
                    if( returned.data.status == 'failed' ) {
                        switch( returned.data.message ) {
                            case "email_used" : 
                                NexoAPI.Toast()( '<?php echo _s( 'L\'email de cet utilisateurs est déjà utilisé', 'nexo' );?>' );
                            break;
                        }
                    }

                    tendoo.loader.hide();
                });
            } else {
                data.created_on         =   tendoo.now();
                data.author             =   <?php echo User::id();?>;
                tendoo.loader.show();

                $http.post( '<?php echo site_url([ 'rest', 'nexo', 'customers', store_get_param( '?' )]);?>', data, {
                    headers	:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                    }
                }).then( function( returned ){
                    $scope.model[ 'basic' ]         =   new Object;
                    $scope.model[ 'billing' ]       =   new Object;
                    $scope.model[ 'shipping' ]      =   new Object;  
                    
                    // if we're creating a user from the POS screen
                    if( $scope.pageNow == 'nexo/registers/__use' ) {
                        // refresh customers
                        $scope.getCustomers();
                        $( '[data-bb-handler="ok"]' ).trigger( 'click' );
                    } else {
                        document.location   =   '<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'clients', 'lists', 'success' ]);?>';
                        tendoo.loader.hide();
                    }   
                }, ( returned ) => {
                    if( returned.data.status == 'failed' ) {
                        switch( returned.data.message ) {
                            case "email_used" : 
                                NexoAPI.Toast()( '<?php echo _s( 'L\'email de cet utilisateurs est déjà utilisé', 'nexo' );?>' );
                            break;
                        }
                    }
                    tendoo.loader.hide();
                });
            }
            
        } else {
            NexoAPI.Toast()( '<?php echo _s( 'Le formulaire contient une ou plusieurs erreurs', 'nexo' );?>' );
        }
    }
}

// inject dependencies
customersMain.$inject    =   [ '$scope', '$http', '$compile', '$rootScope' ];

tendooApp.directive( 'customersMain', function() {
    return {
        restrict    :   'E',
        templateUrl     :   '<?php echo site_url( [ 'dashboard', store_slug(), 'nexo_templates', 'customers_main' ]);?>',
        controller      :   customersMain
    }
});

/**
 * For directive
**/

tendooApp.directive( 'customersForm', function( $rootScope ){
    return {
        restrict    :   'E',
        controller  :   [ '$scope', '$http', '$compile', function( 
            $scope,
            $http,
            $compile
        ) {

            $scope.rawCustomers             =   <?php echo json_encode( ( array ) $clients );?>;
            $scope.rawGroups                =   <?php echo json_encode( ( array ) $groups );?>;
            
            $scope.model[ 'basic' ]         =   new Object;
            $scope.model[ 'billing' ]       =   new Object;
            $scope.model[ 'shipping' ]      =   new Object;

            if( _.keys( $scope.rawCustomers[0] ).length > 0 ) {
                _.each( $scope.rawCustomers[0], ( data, key ) => {
                    if( key.substr( 0, 9 ) == 'shipping_' ) {
                        $scope.model[ 'shipping' ][ key ]       =   data;
                    } else if( key.substr( 0, 8 ) == 'billing_' ) {
                        $scope.model[ 'billing' ][ key ]        =   data;
                    } else if( key == 'name' ) {
                        $scope.model[ key ]     =   data;
                    } else {
                        $scope.model[ 'basic' ][ key ]          =   data;
                    }
                });
            }

            $scope.rawToOptoions        =   function( raw, key, value_key ) {
                let data        =   {};
                _.each( raw, function( value, index ) {
                    data[ value[ key ] ]    =   value[ value_key ];
                });

                return data;
            }

            $scope.groups               =   $scope.rawToOptoions( $scope.rawGroups, 'ID', 'NAME' );

            $scope.schema               =   new Object;
            $scope.schema[ 'basic' ]    =   {
                type    :   'object',
                title   :   "<?php echo __( 'Informations de contact', 'nexo' );?>",
                properties  :   {
                    surname     :   {
                        type    :   "string"
                    },
                    birth_date  :   {
                        type    :   "string",
                    },
                    email     :   {
                        type    :   "string",
                        pattern     :   "^\\S+@\\S+$",
                    },
                    phone     :   {
                        type    :   "string",
                    },
                    country   :   {
                        type    :   "string"
                    },
                    city      :   {
                        type    :   "string"
                    },
                    state       :   {
                        type    :   "string"
                    },
                    description     :   {
                        type    :   'string'
                    },
                    ref_group       :   {
                        "title": '<?php echo _s( 'Assigner à un groupe', 'nexo' );?>',
                        "type": "string"
                    }
                }
            }

            _.each([ 'billing', 'shipping' ], ( tab ) => {
                let title   =   tab == 'billing' ?     "<?php echo __( 'Livraison', 'nexo' );?>" : "<?php echo __( 'Facturation', 'nexo' );?>"
                $scope.schema[ tab ]    =   {
                    type    :   'object',
                    title
                }

                $scope.schema[ tab ].properties     =   new Object;

                _.each( [ 'name', 'surname', 'city', 'country', 'state', 'address_1', 'address_2', 'pobox', 'enterprise' ], ( field ) => {
                    $scope.schema[ tab ].properties[ tab + '_' + field ]    =   {
                        type    :   "string"
                    }
                });                
            });
            
            $scope.form                 =   new Array;

            // Basic
            $scope.form[ 'basic' ]      =   [{
                key         :   'ref_group',
                type        :   'select',
                titleMap    :   $scope.groups
            },{
                key     :   "surname",
                title   :   "<?php echo _s( 'Prénom', 'nexo' );?>"
            },{
                key     :   "email",
                title   :   "<?php echo _s( 'Email', 'nexo' );?>",
                description     :   "<?php echo _s( 'Cet email pourra être utilisé pour envoyer des factures au client', 'nexo' );?>"
            },{
                key     :   "phone",
                title   :   "<?php echo _s( 'Téléphone', 'nexo' );?>",
                description     :   "<?php echo _s( 'Ce numéro pourra être utilisé pour envoyer des factures au client', 'nexo' );?>"
            },{
                key     :   "birth_date",
                title   :   "<?php echo _s( 'Date de naissance', 'nexo' );?>"
            },{
                key     :   "country",
                title   :   "<?php echo _s( 'Pays', 'nexo' );?>"
            },{
                key     :   "city",
                title   :   "<?php echo _s( 'Ville', 'nexo' );?>"
            },{
                key     :   "description",
                title   :   "<?php echo _s( 'Description', 'nexo' );?>"
            },{
                key     :   "state",
                title   :   "<?php echo _s( 'Etat / Région', 'nexo' );?>"
            },{
                key     :   "description",
                type    :   "textarea",
                title   :   "<?php echo _s( 'Description', 'nexo' );?>"
            }];

            _.each([ 'billing', 'shipping' ], ( tab ) => {
                $scope.form[ tab ]      =   [{
                    key     :   tab + '_' + "name",
                    title   :   "<?php echo _s( 'Nom', 'nexo' );?>"
                },{
                    key     :   tab + '_' + "surname",
                    title   :   "<?php echo _s( 'Prénom', 'nexo' );?>"
                },{
                    key     :   tab + '_' + "enterprise",
                    title   :   "<?php echo _s( 'Entreprise', 'nexo' );?>"
                },{
                    key     :   tab + '_' + "address_1",
                    title   :   "<?php echo _s( 'Addresse Ligne 1', 'nexo' );?>"
                },{
                    key     :   tab + '_' + "address_2",
                    title   :   "<?php echo _s( 'Addresse Ligne 2', 'nexo' );?>"
                },{
                    key     :   tab + '_' + "city",
                    title   :   "<?php echo _s( 'Ville', 'nexo' );?>"
                },{
                    key     :   tab + '_' + "pobox",
                    title   :   "<?php echo _s( 'Code Postal', 'nexo' );?>"
                },{
                    key     :   tab + '_' + "country",
                    title   :   "<?php echo _s( 'Pays', 'nexo' );?>"
                },{
                    key     :   tab + '_' + "state",
                    title   :   "<?php echo _s( 'Etat / Comté', 'nexo' );?>"
                }];
            });

            tendoo.loader.hide();
        }],
        templateUrl     :      ( element, attrs, scope ) => {
            let template    =   '<?php echo site_url([ 'dashboard', store_slug(), 'nexo_templates', 'customers_form' ]);?>';
            return template;
        }
    }
});
</script>