define(
  ['../config'],
  function (config) {
    return [
      '$scope', '$location', '$route', 'ajax', 'path', 'auth',
      function ($scope, $location, $route, ajax, path, auth) {
        
        // setting up page path helper so its
        // available in all the scopes
        $scope.page = path.page;

        $scope.navClass = function (slug) {
          return path.page(slug) === $location.path() ? 'active' : '';
        };

        $scope.ajaxInWorks = ajax.inWorks;

        var loadLogin = function () {
          $location.path(path.page(config.loginPage));
        };

        var setup = function () {
          $scope.title =  + $route.current.title;
          $scope.headTitle = config.appName + ' - ' + $scope.title;
          $scope.bodyTmpl  = $route.current.bodyTmpl;
        };

        auth.check().then(function () {}, loadLogin);
        
        $scope.$on('auth-failed', loadLogin);
        $scope.$on('logged-out', loadLogin);

        $scope.$on('$routeChangeSuccess', setup);
        
      }
    ]
  }
)