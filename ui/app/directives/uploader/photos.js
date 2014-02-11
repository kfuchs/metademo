define(
['helpers'],
function (helpers) {
  return [
    'path', 'fileReader',
    function (path, fileReader) {

      return {
        restrict: 'E',
        replace: true,
        templateUrl: 'directives.uploader.photos',

        link: function ($scope, $el, attrs) {

        },

        controller: [
          '$scope', '$attrs', '$timeout', 'api', 'util.Notif', 'util.Errors',
          function ($scope, attrs, $timeout, api, Notif, Errors) {

            // setup the common properties in the controller
            if(attrs.properties && $scope[attrs.properties]) {
              $scope.properties = $scope[attrs.properties];
            } else {
              $scope.properties = {};
            }

            $scope.uploader = {
              working: false,
              photos: [],
              processFiles: function (el) {
                for(var i=0;el.files[i] !== undefined;i++) {
                  var photo = el.files[i];
                  this.photos.push(photo);
                  
                  (function (photo) {
                    fileReader.read(photo).asDataURL()
                            .then(function (result) {
                              photo.src = result;
                              photo.status = 'queued';
                            });
                  } (el.files[i]))
                }
                
                $scope.$digest();
              },

              removePhoto: function (photo) {
                this.photos = this.photos.filter(function (p) { return p !== photo; });
              },

              prepareFormData: function (photo) {
                var formData = new FormData;
                formData.append('type', 'photo');
                formData.append('source', photo);
                formData.append('name', photo.name);

                var mergeDataWithFormData = function (data) {
                  var process = function (obj, prefix) {
                    if(helpers.type(val) === '[object Object]' || helpers.type(val) === '[object Array]') {
                      return;
                    }

                    for(var k in obj) {
                      if(! prefix && (k === 'source' || k === 'resource')) {
                        // ignore the "source" and "resource" field
                        continue;
                      }

                      var key = prefix ? prefix+'['+k+']' : k;
                      var val = obj[k];
                      if(helpers.type(val) === '[object Object]' || helpers.type(val) === '[object Array]') {
                        process(val, key);
                      } else {
                        // appending shit to formData when no longer processing
                        formData.append(key, val);
                      }
                    }
                  };

                  process(data);
                };

                mergeDataWithFormData($scope.properties);
                return formData;
              },

              clear: function () {
                this.photos = [];
              }
            }

          }
        ]
      }
    }
  ]
})