(function () {
  var app = angular.module("tagpos", []);

  app.controller("SearchItemCtrl", [
    "$scope",
    "$http",
    function ($scope, $http) {
      $scope.items = [];
      $scope.buttonDisabled = true;
      $http.get("api/item").success(function (data) {
        $scope.items = data;
      });
      $scope.receivingtemp = [];
      $scope.newreceivingtemp = {};
      $http
        .get("api/receivingtemp")
        .success(function (data, status, headers, config) {
          $scope.receivingtemp = data;
        });
      $scope.addReceivingTemp = function (item, newreceivingtemp) {
        $scope.buttonDisabled = true;
        $http
          .post("api/receivingtemp", {
            item_id: item.id,
            cost_price: item.cost_price,
            total_cost: item.cost_price,
            type: item.type,
          })
          .success(function (data, status, headers, config) {
            $http.get("api/receivingtemp").success(function (data) {
              $scope.receivingtemp = data;
              $scope.buttonDisabled = false;
            });
          });
      };
      $scope.updateReceivingTemp = function (newreceivingtemp) {
        $http
          .put("api/receivingtemp/" + newreceivingtemp.id, {
            quantity: newreceivingtemp.quantity,
            total_cost:
              newreceivingtemp.item.cost_price * newreceivingtemp.quantity,
          })
          .success(function (data, status, headers, config) {
            $scope.buttonDisabled = false;
          });
      };
      $scope.removeReceivingTemp = function (id) {
        $scope.buttonDisabled = true;
        $http
          .delete("api/receivingtemp/" + id)
          .success(function (data, status, headers, config) {
            $http.get("api/receivingtemp").success(function (data) {
              $scope.receivingtemp = data;
              $scope.buttonDisabled = false;
            });
          });
      };
      $scope.sum = function (list) {
        var total = 0;
        angular.forEach(list, function (newreceivingtemp) {
          total += parseFloat(
            newreceivingtemp.cost_price * newreceivingtemp.quantity
          );
        });
        return total;
      };
      $scope.disableButton = function () {
        $scope.buttonDisabled = true;
      };
    },
  ]);
})();
