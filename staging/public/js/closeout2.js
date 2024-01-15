(function () {
    var app = angular.module('tagpos', []);

    app.filter('roundup', function() {
        return function(input) {
            return Math.ceil(input);
        };
    });
    app.filter('rounddown', function() {
        return function(input) {
            return Math.floor(input);
        };
    });

    app.controller("Reports", ['$scope', '$http', function ($scope, $http) {

        $scope.closeout = [];
        $scope.date = "";
        $scope.location = "0";
        $scope.showSales = false;
        $scope.showWholesales = true;
        $scope.showervices = false;
        $scope.showReceiving = false;
        $scope.showExpenses = false;
        
        //get the data for 1 day
        $http.get('closeoutApi', {params: {daterange: $scope.date ,location:$scope.location}}).success(function (data) {
            $scope.closeout = data;
        });
        
        $scope.sum = function (list) {
            var total = 0;
            angular.forEach(list, function (item) {
                total += parseFloat(item.cost_price * item.quantity);
            });
            return total;
        }

        $scope.dateChanged = function(event) {
            $http.get('closeoutApi', {params: {daterange: $scope.date ,location:$scope.location}}).success(function (data) {
                $scope.closeout = data;
            });
        };

        $scope.locationChanged = function(event) {
            $http.get('closeoutApi', {params: {daterange: $scope.date,location:$scope.location }}).success(function (data) {
                $scope.closeout = data;
            });
        };
    }]);
})();