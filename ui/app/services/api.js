define(function () {
  return [
    'ajax', 'path',
    function (ajax, path) {
      var factory = function (scope) {

        scope = scope === undefined ? '' : scope + '/';

        var api = {};

        var methods = ['get', 'put', 'post', 'delete'], l = methods.length, i = 0;

        for(;i<l;i++) {
          var m = methods[i];

          api[m] = (function (m) {
            return function (uri, options) {
              if(options === undefined)
                options = {};

              options.url = path.api(scope + uri);

              return ajax[m](options);
            }
          })(m);
        }

        api.scope = factory;

        return api;

      }

      return factory();
    }
  ];
})