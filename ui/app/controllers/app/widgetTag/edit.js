define(function () {
  return [
    '$scope', '$route', '$location', '$routeParams', '$timeout', 'path',  'api', 'util.Errors', 'util.Notif',
    function ($scope, $route, $location, $routeParams,$timeout,  path, api, Errors, Notif) {

      $scope.model = {};

      $scope.notif = new Notif;
      $scope.errors = new Errors;


      $scope.save = function () {
        api.put('widget-tags/'+$routeParams.widgetTagId, {data: $scope.model})
           .then(
              function (data) {
                $scope.model = data.resource;
                $scope.notif.setMode('success');
                $timeout(function () { $scope.notif.setMode('neutral'); }, 2000);
              },
              function (err) {
                $scope.notif.setMode('error');
                $scope.errors.set(err.data.errors);
              }
            );
      };

      $scope.setup = function () {
        api.get('widget-tags/'+$routeParams.widgetTagId)
           .then(function (data) {
              $scope.model = data.resource;
           })
      }

      $scope.setup();

      $scope.$on('$routeChangeSuccess', function () {
        if($route.current && $route.current.name.indexOf('widget-tags.edit') > -1) {
          $scope.setup();
        }
      });

    }
  ];
})