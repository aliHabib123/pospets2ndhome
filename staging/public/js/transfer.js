(function () {
  var app = angular.module("tagpos", []);

  app.controller("SearchItemCtrl", [
    "$scope",
    "$http",
    function ($scope, $http) {
      $scope.items = [];
      $scope.buttonDisabled = true;
      $scope.isSubmitting = false;
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

      // Confirm transfer with quantity validation
      $scope.confirmTransfer = function(event) {
        event.preventDefault();
        
        if ($scope.transfertemp.length === 0) {
          alert('No items to transfer!');
          return false;
        }

        if ($scope.isSubmitting) {
          return false;
        }

        // Build confirmation message with all items and quantities
        var confirmMsg = 'Please confirm the transfer:\n\n';
        var totalItems = 0;
        
        for (var i = 0; i < $scope.transfertemp.length; i++) {
          var item = $scope.transfertemp[i];
          var itemName = item.item ? item.item.item_name : item.item_name;
          confirmMsg += '• ' + itemName + ': ' + item.quantity + ' units\n';
          totalItems += parseInt(item.quantity);
        }
        
        confirmMsg += '\nTotal: ' + $scope.transfertemp.length + ' item(s), ' + totalItems + ' units';
        confirmMsg += '\n\nAre you sure you want to proceed?';

        if (confirm(confirmMsg)) {
          $scope.isSubmitting = true;
          $scope.buttonDisabled = true;
          
          // Validate quantities against current stock before submitting
          $http.post('api/transfer/validate', {
            items: $scope.transfertemp
          }).then(function(response) {
            if (response.data.valid) {
              // Submit the form
              document.getElementById('transferForm').submit();
            } else {
              alert('Validation failed:\n' + response.data.message);
              $scope.isSubmitting = false;
              $scope.buttonDisabled = false;
            }
          }).catch(function(error) {
            // If validation endpoint doesn't exist, proceed with form submit
            document.getElementById('transferForm').submit();
          });
        }
        
        return false;
      };
    },
  ]);
})();
