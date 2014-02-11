define(
['jquery', 'helpers'],
function ($, helpers) {
  return [
    'path', 'fileReader',
    function (path, fileReader) {

      return {
        restrict: 'E',
        replace: true,
        templateUrl: 'directives.uploader.videos',

        link: function ($scope, $el, attrs) {

        },

        controller: [
          '$scope', '$attrs', '$timeout', 'api', 'util.Notif', 'util.Errors', 'youtube.dataApi',
          function ($scope, attrs, $timeout, api, Notif, Errors, youtubeDataApi) {

            // setup the common properties in the controller
            if(attrs.properties && $scope[attrs.properties]) {
              $scope.properties = $scope[attrs.properties];
            } else {
              $scope.properties = {};
            }

            var nullYoutubeData = function () { return {url: '', meta: {}, type: null}; };
            $scope.youtube = nullYoutubeData();

            $scope.errors = new Errors;



            $scope.uploader = {
              videos: [],
              getVideoId: function (video) {
                switch(helpers.type(video.id)) {
                  case '[object Object]': return video.id.videoId;
                  case '[object String]': return video.id;
                }
              },

              processYoutubeUrl: function () {
                if(youtubeDataApi.guessUrlType($scope.youtube.url) === null) {
                  $scope.errors.set({youtube: ['Invalid Youtube Url']});
                } else {
                  $scope.youtube.meta = youtubeDataApi.extractUrlMeta($scope.youtube.url);
                  $scope.youtube.type = youtubeDataApi.guessUrlType($scope.youtube.url);
                  $scope.errors.set({});
                }
              },

              // this is for loading more and more videos
              // from the same channel. Things stored in it would be
              // id, total, nextPageToken
              activeChannel: {id: null, total: null, nextPageToken: null},

              fetchVideos: function () {
                if($scope.youtube.url === '' || ! $scope.errors.isEmpty()) {
                  return;
                }

                var self = this;
                var action = (function (data) {
                  switch (data.type) {
                    case 'channel': return youtubeDataApi.userVideos(data.meta.user, self.activeChannel.nextPageToken);
                    case 'video'  : return youtubeDataApi.videoData(data.meta.videoId);
                  }
                } ($scope.youtube));

                action.then(
                  function (result) {
                    if($scope.youtube.type === 'video') {
                      // process for single video
                      self.videos.push(result);
                      $scope.youtube = nullYoutubeData();
                    } else if($scope.youtube.type === 'channel') {
                      // concat channel videos
                      self.videos = result.videos;
                      // set activeChannel data 
                      self.activeChannel = {id: result.channelId, total: result.total, nextPageToken: result.nextPageToken};
                      $scope.youtube = nullYoutubeData();
                    }

                    self.videos.forEach(function (v) { v.status = 'queued'; });
                    $scope.errors.set({});
                  },
                  function (xhr) {
                    $scope.errors.set({youtube: ['cannot retrieve Youtube data']});
                  }
                );
              },

              loadMore: function () {
                var self = this;
                if(this.activeChannel.nextPageToken) {
                  youtubeDataApi.channelVideos(this.activeChannel.id, this.activeChannel.nextPageToken)
                                .then(function (result) {
                                  // concat channel videos
                                  self.videos = self.videos.concat(result.videos);
                                  // set activeChannel data 
                                  self.activeChannel = {
                                    id: result.channelId,
                                    total: result.total,
                                    nextPageToken: result.nextPageToken
                                  };
                                });
                }
              },

              removeVideo: function (video) {
                if($scope.youtubePlayer.getCurrentVideoId() === this.getVideoId(video)) {
                  $scope.youtubePlayer.cleanup();
                }

                this.videos = this.videos.filter(function (v) { return v !== video; });
              },

              clear: function () {
                $scope.youtubePlayer.cleanup();
                this.videos = [];
              }
            };

            // shit to do when modal is closing
            $scope.$on('modal-closing', function () {
              $scope.youtubePlayer.cleanup();
              
              // set some stuff here
              $scope.uploader.videos = [];
              $scope.uploader.activeChannel = {id: null, total: null, nextPageToken: null};
            });

          }
        ]
      }
    }
  ]
});