define(function () {
  return [
    '$scope', '$route', '$routeParams', '$timeout', 'path',  'api', 'fileReader', 'util.Errors', 'util.Notif',
    function ($scope, $route, $routeParams, $timeout, path, api, fileReader, Errors, Notif) {

      $scope.model = {};

      var formData = new FormData;
      var mergeDataWithFormData = function (data) {
        var type = function (val) { return Object.prototype.toString.call(val); } 
        var process = function (obj, prefix) {
          if(type(val) === '[object Object]' || type(val) === '[object Array]') {
            return;
          }

          for(var k in obj) {
            if(! prefix && (k === 'source' || k === 'resource')) {
              // ignore the "source" and "resource" field
              continue;
            }

            var key = prefix ? prefix+'['+k+']' : k;
            var val = obj[k];
            if(type(val) === '[object Object]' || type(val) === '[object Array]') {
              process(val, key);
            } else {
              // appending shit to formData when no longer processing
              formData.append(key, val);
            }
          }
        };

        process(data);
      };

      
      $scope.previewUrl = null;

      $scope.errors = new Errors;
      $scope.notif = new Notif;

      $scope.processFile = function (el) {
        var file = el.files[0];
        formData.append('source', file);
        formData.append('type', 'photo');
        
        fileReader.read(file).asDataURL()
                  .then(function (result) {
                      $scope.previewUrl = result;
                   });
      };

      $scope.save = function () {
        mergeDataWithFormData($scope.model);
        var options = {
          enctype: 'multipart/form-data', data: formData,
          processData: false, contentType: false
        };

        api.post('widgets/'+$routeParams.widgetId, options)
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
        api.get('widgets/'+$routeParams.widgetId)
           .then(function (data) {
              $scope.model = data.resource;
              $scope.previewUrl = $scope.model.photo;
           })
      };

      $scope.setup();

      $scope.$on('$routeChangeSuccess', function () {
        if($route.current.name && $route.current.name.indexOf('manufacturers.widgets.edit') > -1) {
          $scope.setup();
        }
      });

    }
  ]
})