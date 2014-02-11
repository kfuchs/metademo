define(function () {
  return [
    function () {
      return {
        restrict: 'A',
        link: function ($scope, $el, attrs) {
          var size = attrs.box.split(',');
          var width = size[0];
          var height = size[1] ? size[1] : width;

          $el.css({
            width: width,
            height: height
          });
        }
      }
    }
  ]
})