define(
  ['config'],
  function (config) {
    return [function () {
      var keyName = config.apiKeyName;
      var key = localStorage.getItem('sessionKey');

      return {
        keyName: function () {
          return keyName;
        },

        key: function () {
          return key;
        },

        setKey: function (val) {
          key = val;
          localStorage.setItem('sessionKey', key)
        }
      }
    }]
  }
)