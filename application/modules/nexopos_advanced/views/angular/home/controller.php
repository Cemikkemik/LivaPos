define( [], function(){
    var ctrl    =   function( $scope ){
        $scope.message  =   'hello World';
    };

    ctrl.$inject        =   [ '$scope' ];
    return ctrl;
});
