(function () {
  var app = angular.module("tagpos", []);

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
