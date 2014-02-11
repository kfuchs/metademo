define(function () {
  return [
    '$scope', '$location', '$route', 'auth', 'path',
    function ($scope, $location, $route, auth, path) {

      var setup = function () {
        $scope.headerTmpl = $route.current.headerTmpl;
        $scope.panelTmpl  = $route.current.panelTmpl;
      };

      setup();

      $scope.$on('$routeChangeSuccess', setup);
    }
  ];
})