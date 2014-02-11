define(function () {
  return [
    '$scope', '$route', 'api',
    function ($scope, $route, api) {

      $scope.collection = [];

      $scope.setup = function () {
        api.get('manufacturers')
           .then(function (data) {
              $scope.collection = data.collection;
            });
      };

      $scope.setup();

      $scope.$on('$routeChangeSuccess', function () {
        if($route.current.name && $route.current.name.indexOf('manufacturers') > -1) {
          $scope.setup();
        }
      });

    }
  ]
})