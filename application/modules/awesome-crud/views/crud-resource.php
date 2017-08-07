<script>
tendooApp.factory( 'AwesomeCrudFactory', [ '$resource', function( $resource ){
    return $resource( '<?php echo site_url([ 'rest', 'crud_api', 'select', $awesome_crud->get( 'table' ) ]);?>/:id', null, {
        query      :       {
            method      :   'GET',
            headers     :   {
                '<?php echo $this->config->item( 'rest_key_name' );?>' : '<?php echo get_option( 'rest_key' );?>'
            },
            isArray     :   true
        },
        get      :       {
            method      :   'GET',
            headers     :   {
                '<?php echo $this->config->item( 'rest_key_name' );?>' : '<?php echo get_option( 'rest_key' );?>'
            }
        },
        delete      :       {
            method      :   'DELETE',
            headers     :   {
                '<?php echo $this->config->item( 'rest_key_name' );?>' : '<?php echo get_option( 'rest_key' );?>'
            }
        }
    });
}]);
tendooApp.filter( 'acFilter', function(){
    return function( data, param ) {

        if( param == 'enablePlaceholders' ){
            return data.length == 0 ? '<?php echo __( 'N/A', 'awesome-crud' );?>' : data;
        }

        if( param == 'isPercentage' ) {
            return data + ' %';
        }
        
        return data;
    }
})
</script>