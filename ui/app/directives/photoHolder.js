define(
['jquery'],
function ($) {
  return [
    function () {
      return {
        restrict: 'E',
        template: '<div class="photo-holder"></div>',
        replace: true,
        link: function ($scope, $el, attrs) {

          var size = attrs.box ? attrs.box.split(',') : [];
          var width = parseInt(size[0]);
          var height = parseInt(size[1]);

          width = width !== NaN && width > 0 ? width : $el.parent().width();
          height = height !== NaN && height > 0 ? height : width;

          $el.css({
            background: '#e4e4e4',
            width: width, height: height
          });

          $el.setPhoto = function (photo) {
            this.empty();
            if(photo.src) {
              var $img = $($.parseHTML('<img src="'+photo.src+'">'));
              $img.css({width: '100%', height: '100%'});
              this.append($img);
            }
          }

          $scope.$on('photo-changed', function ($ev, photo) {
            if(photo.name === $el.attr('name'))
              $el.setPhoto(photo);
          });

          attrs.$observe('src', function (src) {
            $el.setPhoto(attrs);
          })
        }
      }
    }
  ];
})