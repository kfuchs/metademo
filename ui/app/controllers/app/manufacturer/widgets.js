define(function () {
  return [
    '$scope', '$route', '$routeParams', 'api',
    function ($scope, $route, $routeParams, api) {

      $scope.collection = [];

      $scope.setup = function () {
        var params = {for_manufacturer: $routeParams.manufacturerId};

        api.get('widgets', {data: {filters: params}})
           .then(function (data) {
              $scope.collection = data.collection;
            });
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