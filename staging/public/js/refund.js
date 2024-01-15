(function () {
  var app = angular.module("tagpos", []);

  app.controller("refundInvoice", [
    "$scope",
    "$http",
    function ($scope, $http) {
      $scope.mainUrl = mainUrl;
      $scope.refundRes = "";

      $scope.ReceivingItems = [];
      var id = $("#invoice_id").val();
      $http
        .get($scope.mainUrl + "getReceivingItems/" + id)
        .success(function (data) {
          $scope.ReceivingItems = data;
          console.log($scope.ReceivingItems);
        });
      $scope.checkValue = function (item) {
        item.new_quantity = item.quantity - item.quantity_to_refund;
        item.total_cost = item.new_quantity * item.cost_price;
        return 0;
      };

      $scope.updateInvoice = function (ReceivingItems) {
        $http
          .post($scope.mainUrl + "update-invoice", { ReceivingItems })
          .success(function (data, status, headers, config) {
            //console.log(data.status);
            if (data.status) {
              location.href = $scope.mainUrl + "refund";
            } else {
              $("#refundRes").html("");
              $("#refundRes").append(
                '<div class="alert alert-danger alert-dismissible" role="alert"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
                  data.msg +
                  "</div>"
              );
            }
          });
      };
      $scope.updateReceivingTemp = function (newreceivingtemp) {
        $http
          .put("api/receivingtemp/" + newreceivingtemp.id, {
            quantity: newreceivingtemp.quantity,
            total_cost:
              newreceivingtemp.item.cost_price * newreceivingtemp.quantity,
          })
          .success(function (data, status, headers, config) {});
      };
      $scope.removeReceivingTemp = function (id) {
        $http
          .delete("api/receivingtemp/" + id)
          .success(function (data, status, headers, config) {
            $http.get("api/receivingtemp").success(function (data) {
              $scope.receivingtemp = data;
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
      $scope.max = function (a, b) {
        return Math.min(a, b);
      };
    },
  ]);
})();
