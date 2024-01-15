(function () {
  var app = angular.module("tagpos", []);

  app.controller("saleInvoice", [
    "$scope",
    "$http",
    function ($scope, $http) {
      $scope.mainUrl = mainUrl;
      $scope.refundRes = "";
      $scope.SaleItems = [];
      $scope.SaleInvoice = {};

      var id = $("#sale_id").val();

      $http.get($scope.mainUrl + "getSaleItems/" + id).success(function (data) {
        angular.forEach(data, function (data) {
          data.quantity = parseInt(data.quantity);
        });
        $scope.SaleItems = data;
        console.log($scope.SaleItems);
      });

      $http
        .get($scope.mainUrl + "getSaleInvoice/" + id)
        .success(function (data) {
          $scope.SaleInvoice = data;
        });

      $scope.updateItem = function (item) {
        item.total_selling = item.quantity * item.selling_price;
      };

      $scope.updateInvoice = function (SaleInvoice, SaleItems) {
        $http
          .post($scope.mainUrl + "update-sale-invoice", {
            invoice: SaleInvoice,
            items: SaleItems,
          })
          .success(function (data, status, headers, config) {
            location.href = $scope.mainUrl + "generalReports/sales";
          });
      };

      $scope.sum = function (SaleItems) {
        var total = 0;
        angular.forEach(SaleItems, function (SaleItems) {
          total += parseFloat(SaleItems.total_selling);
        });
        return total;
      };
    },
  ]);
})();
