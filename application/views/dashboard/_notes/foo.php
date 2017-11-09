<script>
tendooApp.directive( 'fooDirective', function(){
    return {
        restrict    :   'A',
        controller  :   [ '$scope', '$compile', function( $scope, $compile ) {
            console.log( $scope.widget.json );
            $scope.widget.template  =   `
            <ul class="list-group">
                <li ng-repeat="list in widget.json" class="list-group-item">{{ list.title }}</li>
            </ul>
            `;

            $scope.helloWorld       =   'Hello World';
        }]
    }
});
</script>