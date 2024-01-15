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
      $scope.customerId = $('select[name="customer_id"]')
        .children("option:selected")
        .val();
      $scope.customerPayment = "";
      $scope.paymentType = $('select[name="payment_type"]')
        .children("option:selected")
        .val();

      $scope.data = {
        paymentTypes: ["Cash", "Check", "Debit Card", "Credit Card"],
        selectedOption: "",
      };

      var id = $("#sale_id").val();

      $http
        .get($scope.mainUrl + "getWholesaleItems/" + id)
        .success(function (data) {
          angular.forEach(data, function (data) {
            data.quantity = parseInt(data.quantity);
          });
          $scope.SaleItems = data;
        });

      $http
        .get($scope.mainUrl + "getWholesaleInvoice/" + id)
        .success(function (data) {
          $scope.SaleInvoice = data;
        });

      $http
        .get($scope.mainUrl + "getCustomerInvoive/" + id)
        .success(function (data) {
          $scope.customerPayment = data;
          $scope.data.selectedOption = data.payment_type;
        });

      $scope.updateItem = function (item) {
        item.total_selling = item.quantity * item.selling_price;
      };

      $scope.updateWholesaleInvoice = function (SaleInvoice, SaleItems) {
        $http
          .post($scope.mainUrl + "update-wholesale-invoice", {
            invoice: SaleInvoice,
            items: SaleItems,
            paymentType: $scope.data.selectedOption,
            paymentAmount: $scope.customerPayment.amount_paid,
            customerId: $scope.customerId,
          })
          .success(function (data, status, headers, config) {
            console.log(data);
            if (data) {
              location.href = $scope.mainUrl + "generalReports/wholesales";
            }
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
