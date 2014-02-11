define(function () {
  return [
    '$scope', '$location', 'path',  'api', 'util.Errors', 'util.Notif',
    function ($scope, $location, path, api, Errors, Notif) {

      $scope.model = {};

      $scope.errors = new Errors;
      $scope.notif = new Notif;

      $scope.save = function () {
        api.post('widget-tags', {data: $scope.model})
           .then(
              function (data) {
                $location.path(path.page('widget-tags/'+data.resource.id+'/edit'));
              },
              function (err) {
                $scope.notif.setMode('error');
                $scope.errors.set(err.data.errors);
              }
            )
      }

    }
  ]
})