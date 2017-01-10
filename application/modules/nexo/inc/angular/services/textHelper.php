<script>
tendooApp.service( 'textHelper', function(){
    this.toUrl      =   function(Text, remplacement ) {
        var remplacement =   angular.isUndefined( remplacement ) ? '-' : remplacement;
        return Text
        .toLowerCase()
        .replace( / /g, remplacement )
        .replace( /[^\w-]+/g, '' );
    }
});
</script>
