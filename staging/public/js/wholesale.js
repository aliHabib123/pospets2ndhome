(function () {
    var app = angular.module('tagpos', []);

    app.directive('commaNumber', function () {
        function unformat(value) {
            return (value || '').toString().replace(/,/g, '');
        }
        function format(value) {
            if (value === null || value === undefined || value === '') return '';
            var parts = value.toString().split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return parts.join('.');
        }
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, element, attrs, ctrl) {
                var el = element[0];
                ctrl.$parsers.push(function (viewValue) {
                    var plain = unformat(viewValue);

                    if (plain === '' || plain === '-') {
                        ctrl.$setValidity('commaNumber', true);
                        return plain === '' ? null : undefined;
                    }
                    if (!/^-?\d*\.?\d*$/.test(plain)) {
                        ctrl.$setValidity('commaNumber', false);
                        return undefined;
                    }
                    ctrl.$setValidity('commaNumber', true);

                    var formatted = format(plain);
                    if (formatted !== viewValue && el.selectionStart != null) {
                        var cursor = el.selectionStart;
                        var digitsBeforeCursor = unformat(viewValue.slice(0, cursor)).length;
                        el.value = formatted;
                        var pos = 0, count = 0;
                        while (pos < formatted.length && count < digitsBeforeCursor) {
                            if (formatted.charAt(pos) !== ',') count++;
                            pos++;
                        }
                        el.setSelectionRange(pos, pos);
                    }

                    return parseFloat(plain);
                });
                ctrl.$formatters.push(function (modelValue) {
                    return format(modelValue);
                });
            }
        };
    });

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

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.forms['wholessales'];
        var discountEl = document.getElementById('discount');
        if (form && discountEl) {
            form.addEventListener('submit', function () {
                discountEl.value = (discountEl.value || '').replace(/,/g, '');
            });
        }
    });
})();