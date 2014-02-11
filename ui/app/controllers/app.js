define(function () {
  return [
    '$scope', '$location', '$route', 'auth', 'path',
    function ($scope, $location, $route, auth, path) {

      $scope.setup = function () {
        $scope.panelTmpl  = $route.current.panelTmpl;
      };

      $scope.navClass = function (slug) {
        return path.page(slug) === $location.path() ? 'active' : '';
      };

      $scope.setup();

      $scope.$on('$routeChangeSuccess', $scope.setup);
    }
  ];
})