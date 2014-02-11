define(function () {
  return [
    function () {
      return {
        restrict: 'A',

        link: function ($scope, $el, attrs) {
          $el.css({overflowY: 'auto'})
          var setHeight = function (h) {
            $el.css({height: h});
          };

          attrs.$observe('height', function (h) {
            if(h) {
              setHeight(h);
            }
          });
        }
      }
    }
  ];
})