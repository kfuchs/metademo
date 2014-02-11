define(
  ['../config'],
  function (config) {
    return [
      '$scope', '$location', 'path', 'auth',
      function ($scope, $location, path, auth) {
          
        $scope.email = null;
        $scope.password = null;

        $scope.showErrors = false;

        var loadHome = function () {
          $location.path(path.page(config.homePage));
        };

        auth.check().then(loadHome, function () { });

        $scope.closeErrorsBox = function () { 
          $scope.showErrors = false;
        };

        $scope.submit = function () {
          auth.login($scope.email, $scope.password)
              .then(
                function () { 
                  loadHome();
                },
                function () {
                  $scope.showErrors = true;
                  $scope.email = null;
                  $scope.password = null;
                }
              );
          };
      }
    ];
  }
)