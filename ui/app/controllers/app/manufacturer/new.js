define(function () {
  return [
    '$scope', '$location', 'path',  'api', 'util.Errors', 'util.Notif',
    function ($scope, $location, path, api, Errors, Notif) {

      $scope.model = {};

      $scope.errors = new Errors;
      $scope.notif = new Notif;

      $scope.save = function () {
        api.post('manufacturers', {data: $scope.model})
           .then(
              function (data) {
                $location.path(path.page('manufacturers/'+data.resource.id+'/edit'));
              },
              function (err) {
                $scope.notif.setMode('error');
                $scope.errors.set(err.data.errors);
              }
            )
      };

    }
  ]
})