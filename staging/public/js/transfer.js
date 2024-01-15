(function () {
  var app = angular.module("tagpos", []);

  app.controller("SearchItemCtrl", [
    "$scope",
    "$http",
    function ($scope, $http) {
      $scope.items = [];
      $scope.buttonDisabled = true;
      $http.get("api/transferitems").success(function (data) {
        $scope.items = data;
      });
      $scope.transfertemp = [];
      $scope.newtransfertemp = {};
      $http
        .get("api/transfertemp")
        .success(function (data, status, headers, config) {
          $scope.transfertemp = data;
        });
      $scope.addTransferTemp = function (item, newtransfertemp) {
        $scope.buttonDisabled = true;
        $http
          .post("api/transfertemp", {
            item_id: item.id,
            item_name: item.item_name,
          })
          .success(function (data, status, headers, config) {
            $scope.transfertemp.push(data);
            $http.get("api/transfertemp").success(function (data) {
              $scope.transfertemp = data;
              $scope.buttonDisabled = false;
            });
          });
      };
      $scope.updateTransferTemp = function (newtransfertemp) {
        $http
          .put("api/transfertemp/" + newtransfertemp.id, {
            quantity: newtransfertemp.quantity,
          })
          .success(function (data, status, headers, config) {
            if (data == 0) {
              alert("Quantity exceed stock!");
              newtransfertemp.quantity = 1;
            }
            $scope.buttonDisabled = false;
          });
      };
      $scope.removeTransferTemp = function (id) {
        $scope.buttonDisabled = true;
        $http
          .delete("api/transfertemp/" + id)
          .success(function (data, status, headers, config) {
            $http.get("api/transfertemp").success(function (data) {
              $scope.transfertemp = data;
              $scope.buttonDisabled = false;
            });
          });
      };
      $scope.disableButton = function(){
        $scope.buttonDisabled = true;
      };
    },
  ]);
})();
