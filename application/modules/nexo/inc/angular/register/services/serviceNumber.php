<script>
tendooApp.service( 'serviceNumber', function(){
	this.isInt		=	function(n){
    return Number(n) === n && n % 1 === 0;
	}
	
	this.isFloat 	=	function(n){
		return Number(n) === n && n % 1 !== 0;
	}
});
</script>