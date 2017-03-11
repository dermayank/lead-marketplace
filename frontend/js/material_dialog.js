var app = angular.module('MyApp',['ngMaterial', 'ngMessages', 'material.svgAssetsCache']);
app.controller('AppCtrl', function($scope, $mdDialog) {
  $scope.openFromLeft = function(msg) {
    $mdDialog.show(
      $mdDialog.alert()
        .clickOutsideToClose(true)
        .title('Warning:')
        .textContent(msg)
        .ok('close')
        // You can specify either sting with query selector
        .openFrom('#left')
        // or an element
        .closeTo(angular.element(document.querySelector('#right')))
    );
  };
});


function onlo() {
  document.getElementById('clickme').click();
}
