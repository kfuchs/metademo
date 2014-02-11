// interactions happen using youtube v3 api
define(
  ['config'],
  function (config) {
    return [
      '$q', '$rootScope', 'ajax',
      function ($q, $rootScope, ajax) {
        var youtubeApiBase = 'https://www.googleapis.com/youtube/v3';

        var url = {
          urlTypes: ['channel', 'video'],
          regex: {
            channel: /^(http|https):\/\/(www\.){0,1}youtube.com\/user\/([^\?\/&\s]+)\S*$/,
            video: /^(http|https):\/\/(www\.){0,1}youtube.com\/watch\?v=([^\?\/&\s]+)\S*$/
          },

          channel: function (user) {
            return youtubeApiBase + '/channels?forUsername='+user+'&part=contentDetails,id&key='+config.youtubeApiKey;
          },
          video: function (videoId) {
            return youtubeApiBase + '/videos?id='+videoId+'&part=id,snippet&key='+config.youtubeApiKey;
          },
          channelVideos: function (channelId, pageToken) {
            var url = youtubeApiBase+'/search?type=video&part=id,snippet&channelId='+channelId+'&key='+config.youtubeApiKey;
            if(pageToken) {
              url += '&pageToken='+pageToken;
            }
            return url;
          },

          guessType: function (url) {
            var result;

            result = this.regex.channel.exec(url);
            if(result) { return 'channel'; }

            result = this.regex.video.exec(url);
            if(result) { return 'video'; }

            return null;
          },

          extractMeta: function (url) {
            var type = this.guessType(url);
            if(type === null) { return null; }

            var regex = this.regex[type];
            var result = regex.exec(url);

            switch (type) {
              case 'channel' : return {user: result[3]};
              case 'video'   : return {videoId: result[3]};
            }
          }
        };

        // the hashmap to store all user => channel maps
        var userChannnelIdMap = {};

        var api = {
          videoData: function (videoId) {
            return ajax({type: 'GET', dataType: 'JSON', url: url.video(videoId)})
                      .then(function (res) {
                        return res.data.items[0];
                      });
          },

          channelFromUser: function (user) {
            return ajax({type: 'GET', dataType: 'JSON', url: url.channel(user)})
                      .then(function (res) {
                        return res.data.items[0];
                      });
          },

          channelVideos: function (channelId, pageToken) {
            return ajax({type: 'GET', dataType: 'JSON', url: url.channelVideos(channelId, pageToken)})
                      .then(function (res) {
                        return {
                          videos: res.data.items,
                          nextPageToken: res.data.nextPageToken,
                          total: res.data.pageInfo.totalResults,
                          perPage: res.data.pageInfo.resultsPerPage,
                          channelId: channelId,
                        };
                      });
          },

          userVideos: function (user, pageToken) {
            var channelId = userChannnelIdMap[user];

            if(channelId) {
              return this.channelVideos(channelId, pageToken);
            } else {
              var deferred = $q.defer();
              var self = this;

              this.channelFromUser(user)
                  .then(function (channel) {
                    userChannnelIdMap[user] = channel.id;
                    self.userVideos(user, pageToken)
                        .then(
                          function (data) {
                            deferred.resolve(data);
                          },
                          function (err) {
                            deferred.reject(err);
                          }
                        )
                  });

              return deferred.promise;
            }
          }
        };

        api.guessUrlType = function (_url) { return url.guessType(_url); };
        api.extractUrlMeta  = function (_url) { return url.extractMeta(_url); };

        return api;
        
      }
    ]
  }
)