define(
  [
    'angular',

    'services/path',
    'services/session',
    'services/ajax',
    'services/auth',
    'services/api',
    'services/objContainer',

    'services/youtube/player',
    'services/youtube/dataApi',
    
    'services/util/Errors',
    'services/util/Notif',

    'services/fileReader'
  ],
  function (
    angular,

    path, session, ajax, auth, api, objContainer,

    youtubePlayer,
    youtubeDataApi,

    Errors, Notif,

    fileReader
  )
  {
    angular.module('demo.services', [])
           .factory('path', path)
           .factory('session', session) 
           .factory('ajax', ajax)
           .factory('auth', auth)
           .factory('api', api)
           .factory('objContainer', objContainer)

           .factory('youtube.player', youtubePlayer)
           .factory('youtube.dataApi', youtubeDataApi)

           .factory('util.Errors', Errors)
           .factory('util.Notif', Notif)

           .factory('fileReader', fileReader)
  }
)