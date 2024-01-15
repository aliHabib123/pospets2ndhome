(function () {
  var app = angular.module("tagpos", []);
  app.filter('ceil', function() {
    return function(input) {
      return Math.round(input/1000)*1000
    };
  });
  app.controller("SearchItemCtrl", [
    "$scope",
    "$http",
    function ($scope, $http) {
      $scope.buttonDisabled = true;
      $scope.items = [];
      $http.get("api/saleitems").success(function (data) {
        $scope.items = data;
      });
      $http.get("api/getDollarRate").success(function (data) {
        $scope.rate = data;
      });
      $scope.saletemp = [];
      $scope.saletempLenght = 0;

      $scope.newsaletemp = {};

      $scope.$watch(
        "filteredItems",
        function (newItems, oldItems) {
          if (
            oldItems != null &&
            newItems.length == 1 &&
            $scope.items.lenght > 1
          ) {
            $scope.addSaleTemp(newItems[0]);
          }
        },
        true
      );

      $http
        .get("api/saletemp")
        .success(function (data, status, headers, config) {
          $scope.saletemp = data;
          $scope.saletempLenght = data.length;
        });
      $scope.addSaleTemp = function (item) {
        $scope.buttonDisabled = true;
        if (
          (item.quantity > 0 && item.type_name == "product") ||
          item.type_name == "service"
        ) {
          $http
            .post("api/saletemp", {
              item_id: item.id,
              cost_price: item.cost_price,
              selling_price: item.selling_price,
              type: item.type_name,
            })
            .success(function (data, status, headers, config) {
              //  $scope.saletemp.push(data);
              $http.get("api/saletemp").success(function (data) {
                $scope.saletemp = data;
                $scope.saletempLenght = data.length;
                $scope.buttonDisabled = false;
              });
            });
        }
      };
      $scope.updateSaleTemp = function (newsaletemp) {
        $http
          .put("api/saletemp/" + newsaletemp.id, {
            quantity: newsaletemp.quantity,
            total_cost: newsaletemp.item.cost_price * newsaletemp.quantity,
            total_selling:
              newsaletemp.item.selling_price * newsaletemp.quantity,
          })
          .success(function (data, status, headers, config) {
            if (data == 0) {
              alert("Quantity exceed stock!");
              newsaletemp.quantity = 1;
            }
            $scope.buttonDisabled = false;
          });
      };
      $scope.removeSaleTemp = function (id) {
        $scope.buttonDisabled = true;
        $http
          .delete("api/saletemp/" + id)
          .success(function (data, status, headers, config) {
            $http.get("api/saletemp").success(function (data) {
              $scope.saletemp = data;
              $scope.saletempLenght = data.length;
              $scope.buttonDisabled = false;
            });
          });
      };
      $scope.sum = function (list) {
        var total = 0;
        angular.forEach(list, function (newsaletemp) {
          total += parseFloat(newsaletemp.selling_price * newsaletemp.quantity * $scope.rate);
        });
        return total;
      };
      $scope.disableButton = function(){
        $scope.buttonDisabled = true;
      };
    },
  ]);
})();
