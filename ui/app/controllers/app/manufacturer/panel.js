define(function () {
  return [
    '$scope', '$route', '$routeParams', 'api',
    function ($scope, $route, $routeParams, api) {

      $scope.manufacturer = {};

      $scope.setup = function () {
        api.get('manufacturers/'+$routeParams.manufacturerId)
           .then(function (data) {
              $scope.manufacturer = data.resource;
           });

        $scope.subPanelTmpl = $route.current.subPanelTmpl;
      };

      $scope.setup();

      $scope.$on('$routeChangeSuccess', function () {
        if($route.current.name && $route.current.name.indexOf('manufacturers.widgets') > -1) {
          $scope.setup();
        }
      });

    }
  ]
})