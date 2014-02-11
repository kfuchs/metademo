define(function () {
  return [
    '$q', '$rootScope',
    function ($q, $rootScope) {
      return {
        read: function (file) {
          var prepareReader = function (r, deferred) {
            r.onload = function (ev) {
              (function (ev) {
                $rootScope.$apply(function () { deferred.resolve(ev.target.result); });
              } (ev));
            }

            r.onerror = function (err) {
              (function (err) {
                $rootScope.$apply(function () { deferred.reject(err); });
              } (err));
            }
          };

          return {
            asArrayBuffer: function () {
              var r = new FileReader();
              var deferred = $q.defer();

              r.readAsArrayBuffer(file);
              prepareReader(r, deferred);

              this.asArrayBuffer.reader = r;

              return deferred.promise;
            },
            asBinaryString: function () {
              var r = new FileReader();
              var deferred = $q.defer();

              r.readAsBinaryString(file);
              prepareReader(r, deferred);

              this.asBinaryString.reader = r;

              return deferred.promise;
            },
            asDataURL: function () {
              var r = new FileReader();
              var deferred = $q.defer();

              r.readAsDataURL(file);
              prepareReader(r, deferred);

              this.asDataURL.reader = r;

              return deferred.promise;
            },
            asText: function () {
              var r = new FileReader();
              var deferred = $q.defer();

              r.readAsText(file);
              prepareReader(r, deferred);

              this.asText.reader = r;

              return deferred.promise;
            }
          };
        }
      }
    }
  ];
})