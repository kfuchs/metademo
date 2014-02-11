define(
  ['../config'],
  function (config) {
    return [
      '$rootScope', 'ajax', 'session', 'path',
      function ($rootScope, ajax, session, path) {
        var url = {
          login: path.auth('login'),
          logout: path.auth('logout'),
          account: path.auth('account')
        };

        return {
          check: function () {
            return ajax({type: 'GET', url: url.account})
          },

          login: function (email, password) {
            return ajax({
                type: 'POST',
                url: url.login,
                data: { email: email, password: password }
              }).then(function (res) {
                  session.setKey(res.xhr.getResponseHeader(config.apiKeyName));
                  return res.data
                }
              );
          },

          logout: function () {
            return ajax({type: 'POST', url: url.logout}).then(function () {
              $rootScope.$broadcast('logged-out')
            });
          },

          account: function () {
            return ajax.get({url: url.account});
          }
        };
      }
    ]
  }
)