<script>
tendooApp.filter( 'padNumber', function(){
    return function( num, zero ){
        alert( num );
        var s = num +"";
        while (s.length < zero ) s = "0" + s;
        return s;
    }
});
</script>
