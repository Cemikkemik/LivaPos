<script>
tendooApp.directive( 'barDirective', function(){
    return {
        restrict    :   'A',
        controller  :   [ '$scope', function( $scope ) {
            $scope.widget.template = `
                
                <ul class="list-group">
                    <li class="list-group-item">{{ widget.json.foo }}</li>
                    <li class="list-group-item">Item 2</li>
                    <li class="list-group-item">Item 3</li>
                </ul>
                
            `;
        }]
    }
});
</script>