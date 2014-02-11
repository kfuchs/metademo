define(
  ['jquery'],
  function ($) {
    return [
      'youtube.player',
      function (youtubePlayer) {
        return {
          restrict: 'E',
          template: '<div class="youtube-container"></div>',
          replace: true,

          link: function ($scope, $el, attrs) {

            var currentVideoId = null;

            $scope.youtubePlayer = {
              getCurrentVideoId: function () {
                return currentVideoId;
              },

              cleanup: function () {
                var $div = $($.parseHTML('<div>'));
                $el.html($div);  
              },

              playVideo: function (videoId) {
                if(this.getCurrentVideoId() === videoId) {
                  return;
                }

                currentVideoId = videoId;

                this.cleanup();

                var $div = $el.children(':first');

                youtubePlayer.playVideo($div.get(0), videoId);                
              }
            };
          }
        }
      }
    ]
  }
)