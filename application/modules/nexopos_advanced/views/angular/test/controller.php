define([], function () {
    var myController = function ($scope) {
        $scope.message = "RequireJs Integrated successfully";
    };

    myController.$inject = ['$scope'];

    return myController;
});
