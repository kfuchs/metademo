define(
['jquery'],
function ($) {
  var activeBox = null;

  return [
    '$document',
    function ($document) {

      return {
        restrict: 'A',

        link: {
          pre: function ($scope, $el, attrs) {
            $el.addClass('preview-box');
            $el.children('button[preview-btn]:first').addClass('preview-btn');
            $el.children('div[preview-container]:first')
               .addClass('preview-container')
               .addClass('container')
          },

          post: function ($scope, $el, attrs) {
            // define container defaults
            var containerDefaults = {
              placement: 'top',
              width: 600,
              height: 300
            };

            // define something to get placementConfig
            var placementConfigGenerator = function (containerW, containerH, $previewBtn) {
              return {
                top:    { top: -1 * containerH, left: 0 },
                bottom: { top: ($previewBtn.position().top + $previewBtn.outerHeight()), left: 0 },
                align:  { top: (-1 * containerH/2 + $previewBtn.outerHeight()/2), left: 0 },
                center: { top: 0, left: (-1 * containerW/2 + $previewBtn.outerWidth())/2 },
                left:   { top: 0, left: -1 * containerW },
                right:  { top: 0, left: ($previewBtn.position().left + $previewBtn.outerWidth()) }
              };
            };

            // container opening and closing functions
            var openContainer = function ($previewContainer, containerW, containerH, placement) {
              $previewContainer.addClass('open');
              $previewContainer.css({
                width: containerW, height: containerH, left: placement.left, top: placement.top
              });
            };

            var closeContainer = function ($previewContainer) {
              $previewContainer.removeClass('open');
              $previewContainer.css({
                width: 0, height: 0, top: 0, left: 0
              });
            }



            // put the click event handler on preview-btn
            $el.children('button[preview-btn]:first').on('click', function (ev) {
              var $previewBtn = $(this);
              var $previewBox = $previewBtn.parent();
              var $previewContainer = $previewBtn.siblings('div[preview-container]:first');

              // check if container is the active container. if it is, then stop
              // propagation, and tackle things appropriately
              if(activeBox === null || $previewBox.get(0) === activeBox) {
                ev.stopPropagation();
              }

              // set active box acc to the action
              if($previewBox.hasClass('open')) {
                activeBox = null;
              } else {
                activeBox = $previewBox.get(0);
              }

              // take container config from the dom
              var containerW = $previewContainer.attr('width'),
                  containerH = $previewContainer.attr('height'),
                  containerPlacement = $previewContainer.attr('placement')
              ;

              // setup default container config if not defined
              // via the directive
              if(containerPlacement === undefined) containerPlacement = containerDefaults.placement;
              if(containerW === undefined) containerW = containerDefaults.width;
              if(containerH === undefined) containerH = containerDefaults.height;

              var placementConfig = placementConfigGenerator(containerW, containerH, $previewBtn);

              var placement = (function () {
                var keys = containerPlacement.split('-');
                var config = {top: 0, left: 0};
                for(var i = 0; i < keys.length; i++) {
                var key = keys[i];
                  config.top += placementConfig[key].top;
                  config.left += placementConfig[key].left;
                }

                return config;
              }());

              if($previewContainer.hasClass('open')) {
                // the action of closing the container
                closeContainer($previewContainer);
              } else {
                // the action of opening the container
                openContainer($previewContainer, containerW, containerH, placement);
              }

            });
            
            $el.children('div[preview-container]:first').on('click', function (ev) {
              ev.stopPropagation();
            });

            // handle document wide click for closing the container
            $document.on('click', function (ev) {
              var $target = $(ev.target);
              // check if target is a preview-btn
              if($target.attr('preview-btn') !== undefined) {
                var $box = $target.parent();
                // $box.get(0) !== activeBox check is unnecessarry
                // as if $document.on('click') reaches here,
                // then things are already happening outside the activeBox
                // we need to check that if($el.get(0) !== $box.get(0))
                // then close the container
                if($box.get(0) !== $el.get(0)) {
                  if($el.children('div[preview-container]:first').hasClass('open')) {
                    // the action of closing the container
                    $previewContainer = $el.children('div[preview-container]:first');
                    closeContainer($previewContainer);
                  }
                }
              } else {
                if($el.children('div[preview-container]:first').hasClass('open')) {
                  // the action of closing the container
                  $previewContainer = $el.children('div[preview-container]:first');
                  closeContainer($previewContainer);
                }

                if(activeBox !== null) {
                  activeBox = null;
                }
              }
            });
  
            // on dom changes, just check if the ev originated
            // from the activeBox, and set it to null if it did
            $document.on('DOMNodeRemoved', function (ev) {
              var targetBox = $(ev.target).parent().get(0);

              if(targetBox === activeBox) {
                activeBox = null;
              }
            });

          }
        }
      }
    }
  ]
})