define(function () {
  return [
    '$scope', '$route', '$routeParams', '$timeout', 'api', 'util.Errors', 'util.Notif',
    function ($scope, $route, $routeParams, $timeout, api, Errors, Notif) {

      $scope.model = {};

      $scope.notif = new Notif;
      $scope.errors = new Errors;


      $scope.save = function () {
        api.put('manufacturers/'+$routeParams.manufacturerId, {data: $scope.model})
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
        api.get('manufacturers/'+$routeParams.manufacturerId)
           .then(function (data) {
              $scope.model = data.resource;
           })
      };

      $scope.setup();

      $scope.$on('$routeChangeSuccess', function () {
        if($route.current.name && $route.current.name.indexOf('manufacturers.edit') > -1) {
          $scope.setup();
        }
      });

    }
  ];
})