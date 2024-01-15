(function () {
    var app = angular.module('tagpos', []);

    app.controller("Reports", ['$scope', '$http', function ($scope, $http) {

        $scope.items = [];
        $scope.location = "0";
        $http.get('itemReportApi', {params: {location:$scope.location}}).success(function (data) {
            $scope.items = data;

        });

        $scope.locationChanged = function(event) {
            $http.get('itemReportApi', {params: {location:$scope.location}}).success(function (data) {
                $scope.items = data;
            });
        };

        $scope.sum = function (list) {
            var total = 0;
            angular.forEach(list, function (item) {
                total += parseFloat(item.cost_price * item.quantity);
            });
            return total;
        }

    }]);
})();