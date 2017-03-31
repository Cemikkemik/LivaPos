<script>
tendooApp.service( 'textHelper', function(){
    this.toUrl      =   function(Text, remplacement, disableToLower ) {
        var remplacement =   angular.isUndefined( remplacement ) ? '-' : remplacement;
        Text        =   Text
        .replace( / /g, remplacement )
        .replace( /[^\w-]+/g, '' );

        return disableToLower === true ? Text : Text.toLowerCase()
    }

});
</script>
