// taken from stackoverflow http://stackoverflow.com/questions/12843770/loading-youtube-iframe-api-with-requirejs
define(
  ['jquery'],
  function($) {
    return [
      function () {
        var player = {
          playVideo: function(container, videoId) {
            if (typeof(YT) == 'undefined' || typeof(YT.Player) == 'undefined') {
              window.onYouTubeIframeAPIReady = function() {
                player.loadPlayer(container, videoId);
              };

              $.getScript('//www.youtube.com/iframe_api');
            } else {
              player.loadPlayer(container, videoId);
            }
          },

          loadPlayer: function(container, videoId) {
            var $container = $(container);

            new YT.Player(container, {
              videoId: videoId,
              width: $container.width(),
              playerVars: {
                autoplay: 1,
                controls: 1,
                modestbranding: 1,
                rel: 0,
                showInfo: 0,
                iv_load_policy: 3
              }
            });
          }
        };

        return player;
      }
    ]
  }
);