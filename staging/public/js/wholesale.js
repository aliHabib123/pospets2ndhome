(function () {
    var app = angular.module('tagpos', []);

    console.log(app);
    app.controller("SearchItemCtrl", ['$scope', '$http', function ($scope, $http) {

        $scope.items = [];
        $http.get('api/wholesaleitems').success(function (data) {
            $scope.items = data;
            //console.log($scope.items)

        }); 
        $scope.saletemp = [];
        $scope.saletempLenght = 0;

        $scope.newsaletemp = {};

        $scope.$watch('filteredItems', function(newItems, oldItems) {
            if (oldItems != null &&  newItems.length == 1 && $scope.items.lenght > 1) {

                $scope.addSaleTemp(  newItems[0]);
            }
        }, true);

        $http.get('api/wholesaletemp').success(function (data, status, headers, config) {
            $scope.saletemp = data;
            $scope.saletempLenght = data.length;
        });
        $scope.addSaleTemp = function (item) {
        	//console.log(item);
            if ((item.quantity > 0 && item.type_name == "product") || item.type_name == "service") {

                $http.post('api/wholesaletemp', {
                    item_id: item.id,
                    cost_price: item.cost_price,
                    wholesale_price: item.wholesale_price,
                    type: item.type_name
                }).success(function (data, status, headers, config) {
                  //  $scope.saletemp.push(data);
                    $http.get('api/wholesaletemp').success(function (data) {
                        $scope.saletemp = data;
                        $scope.saletempLenght = data.length;
                    });
                });
            }
        }
        $scope.updateSaleTemp = function (newsaletemp) {

            $http.put('api/wholesaletemp/' + newsaletemp.id, {
                quantity: newsaletemp.quantity, total_cost: newsaletemp.item.cost_price * newsaletemp.quantity,
                total_selling: newsaletemp.item.wholesale_price * newsaletemp.quantity
            }).success(function (data, status, headers, config) {
                if (data == 0) {
                    alert("Quantity exceed stock!");
                    newsaletemp.quantity = 1;
                }
            });
        }
        $scope.removeSaleTemp = function (id) {
        	console.log(id);
            $http.delete('api/wholesaletemp/' + id).success(function (data, status, headers, config) {
                $http.get('api/wholesaletemp').success(function (data) {
                    $scope.saletemp = data;
                    $scope.saletempLenght = data.length;
                });
            });
        }
        $scope.sum = function (list) {
            var total = 0;
            angular.forEach(list, function (newsaletemp) {
                total += parseFloat(newsaletemp.wholesale_price * newsaletemp.quantity);
            });
            return total;
        }


    }]);
})();